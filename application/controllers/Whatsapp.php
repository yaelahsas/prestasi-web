<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Cek jika user belum login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        $this->load->model('Whatsapp_model');
    }

    /**
     * Halaman utama manajemen WhatsApp Bot
     */
    public function index()
    {
        $data['user']     = $this->session->userdata();
        $data['sessions'] = $this->Whatsapp_model->get_all_sessions();
        $this->load->view('whatsapp/index', $data);
    }

    /**
     * Get semua session via AJAX
     */
    public function get_sessions()
    {
        header('Content-Type: application/json');
        $sessions = $this->Whatsapp_model->get_all_sessions();
        echo json_encode(['status' => 'success', 'data' => $sessions]);
    }

    /**
     * Get status session dari Baileys API
     */
    public function get_status($session_id = null)
    {
        header('Content-Type: application/json');

        if (!$session_id) {
            echo json_encode(['status' => 'error', 'message' => 'Session ID diperlukan']);
            return;
        }

        $baileys_url = $this->_get_baileys_url();
        $result = $this->_call_baileys_api('GET', $baileys_url . '/session/status/' . $session_id);

        echo json_encode($result);
    }

    /**
     * Get QR Code untuk session
     */
    public function get_qr($session_id = null)
    {
        header('Content-Type: application/json');

        if (!$session_id) {
            echo json_encode(['status' => 'error', 'message' => 'Session ID diperlukan']);
            return;
        }

        $baileys_url = $this->_get_baileys_url();
        $result = $this->_call_baileys_api('GET', $baileys_url . '/session/qr/' . $session_id);

        echo json_encode($result);
    }

    /**
     * Tambah / buat session baru
     */
    public function add_session()
    {
        header('Content-Type: application/json');

        if ($this->input->method() !== 'post') {
            echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), TRUE);
        if (empty($input)) {
            $input = $this->input->post();
        }

        $session_id   = isset($input['session_id'])   ? trim($input['session_id'])   : '';
        $session_name = isset($input['session_name']) ? trim($input['session_name']) : '';
        $description  = isset($input['description'])  ? trim($input['description'])  : '';

        if (empty($session_id) || empty($session_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Session ID dan Nama wajib diisi']);
            return;
        }

        // Validasi format session_id (hanya huruf, angka, underscore, dash)
        if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $session_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Session ID hanya boleh berisi huruf, angka, underscore, dan dash']);
            return;
        }

        // Cek apakah session_id sudah ada di DB
        $existing = $this->Whatsapp_model->get_session_by_id($session_id);
        if ($existing) {
            echo json_encode(['status' => 'error', 'message' => 'Session ID sudah digunakan']);
            return;
        }

        // Simpan ke database
        $data = [
            'session_id'   => $session_id,
            'session_name' => $session_name,
            'description'  => $description,
            'status'       => 'disconnected',
            'created_by'   => $this->session->userdata('id_user'),
            'created_at'   => date('Y-m-d H:i:s'),
        ];

        $saved = $this->Whatsapp_model->save_session($data);

        if ($saved) {
            // Inisiasi session ke Baileys API
            $baileys_url = $this->_get_baileys_url();
            $baileys_result = $this->_call_baileys_api('GET', $baileys_url . '/session/add/' . $session_id);

            echo json_encode([
                'status'  => 'success',
                'message' => 'Session berhasil dibuat',
                'data'    => [
                    'session_id'    => $session_id,
                    'baileys_result' => $baileys_result,
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan session ke database']);
        }
    }

    /**
     * Hapus session
     */
    public function delete_session($session_id = null)
    {
        header('Content-Type: application/json');

        if (!$session_id) {
            echo json_encode(['status' => 'error', 'message' => 'Session ID diperlukan']);
            return;
        }

        // Hapus dari Baileys API
        $baileys_url = $this->_get_baileys_url();
        $this->_call_baileys_api('DELETE', $baileys_url . '/session/delete/' . $session_id);

        // Hapus dari database
        $deleted = $this->Whatsapp_model->delete_session($session_id);

        if ($deleted) {
            echo json_encode(['status' => 'success', 'message' => 'Session berhasil dihapus']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus session']);
        }
    }

    /**
     * Logout / disconnect session
     */
    public function logout_session($session_id = null)
    {
        header('Content-Type: application/json');

        if (!$session_id) {
            echo json_encode(['status' => 'error', 'message' => 'Session ID diperlukan']);
            return;
        }

        $baileys_url = $this->_get_baileys_url();
        $result = $this->_call_baileys_api('DELETE', $baileys_url . '/session/logout/' . $session_id);

        // Update status di database
        $this->Whatsapp_model->update_session_status($session_id, 'disconnected');

        echo json_encode([
            'status'  => 'success',
            'message' => 'Session berhasil di-logout',
            'data'    => $result,
        ]);
    }

    /**
     * Kirim pesan teks via bot
     */
    public function send_message()
    {
        header('Content-Type: application/json');

        if ($this->input->method() !== 'post') {
            echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), TRUE);
        if (empty($input)) {
            $input = $this->input->post();
        }

        $session_id = isset($input['session_id']) ? trim($input['session_id']) : '';
        $receiver   = isset($input['receiver'])   ? trim($input['receiver'])   : '';
        $message    = isset($input['message'])    ? trim($input['message'])    : '';

        if (empty($session_id) || empty($receiver) || empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Session ID, penerima, dan pesan wajib diisi']);
            return;
        }

        $baileys_url = $this->_get_baileys_url();
        $result = $this->_call_baileys_api('POST', $baileys_url . '/message/send', [
            'session_id' => $session_id,
            'receiver'   => $receiver,
            'message'    => ['text' => $message],
        ]);

        // Log pesan ke database
        $this->Whatsapp_model->log_message([
            'session_id'  => $session_id,
            'receiver'    => $receiver,
            'message'     => $message,
            'type'        => 'text',
            'status'      => isset($result['status']) && $result['status'] === 'success' ? 'sent' : 'failed',
            'sent_by'     => $this->session->userdata('id_user'),
            'sent_at'     => date('Y-m-d H:i:s'),
        ]);

        echo json_encode($result);
    }

    /**
     * Get log pesan
     */
    public function get_message_logs()
    {
        header('Content-Type: application/json');

        $session_id = $this->input->get('session_id');
        $limit      = $this->input->get('limit') ? (int)$this->input->get('limit') : 50;

        $logs = $this->Whatsapp_model->get_message_logs($session_id, $limit);
        echo json_encode(['status' => 'success', 'data' => $logs]);
    }

    /**
     * Update status session (dipanggil dari webhook Baileys)
     */
    public function webhook_status()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), TRUE);

        if (empty($input) || empty($input['session_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
            return;
        }

        $session_id = $input['session_id'];
        $status     = isset($input['status']) ? $input['status'] : 'disconnected';
        $phone      = isset($input['phone'])  ? $input['phone']  : null;

        $update_data = ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')];
        if ($phone) {
            $update_data['phone_number'] = $phone;
        }

        $this->Whatsapp_model->update_session($session_id, $update_data);

        echo json_encode(['status' => 'success', 'message' => 'Status diperbarui']);
    }

    /**
     * Get URL Baileys API dari config atau env
     */
    private function _get_baileys_url()
    {
        // Ambil dari config (application/config/config.php)
        $url = $this->config->item('baileys_url');
        if (!$url) {
            // Fallback ke environment variable atau default
            $url = getenv('BAILEYS_API_URL') ?: 'http://localhost:3000';
        }
        return rtrim($url, '/');
    }

    /**
     * Helper untuk memanggil Baileys API
     */
    private function _call_baileys_api($method, $url, $data = null)
    {
        $ch = curl_init();

        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ];

        if ($method === 'POST') {
            $options[CURLOPT_POST]       = TRUE;
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        } elseif ($method === 'DELETE') {
            $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            if ($data) {
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
            }
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error     = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'status'  => 'error',
                'message' => 'Tidak dapat terhubung ke Baileys API: ' . $error,
                'code'    => 0,
            ];
        }

        $decoded = json_decode($response, TRUE);
        if ($decoded === null) {
            return [
                'status'   => $http_code >= 200 && $http_code < 300 ? 'success' : 'error',
                'message'  => $response,
                'code'     => $http_code,
            ];
        }

        return $decoded;
    }
}
