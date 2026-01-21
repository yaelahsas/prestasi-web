<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Load model
        $this->load->model('Jurnal_model');
        $this->load->model('Guru_model');
        $this->load->model('Kelas_model');
        $this->load->model('Mapel_model');
        $this->load->model('User_model');
        
        // Set response header to JSON
        header('Content-Type: application/json');
    }

    /**
     * API Authentication using API Key
     * @return bool
     */
    private function _authenticate()
    {
        $api_key = $this->input->server('HTTP_X_API_KEY');
        
        // You can store API keys in database or config file
        // For now, using a simple hardcoded key
        $valid_api_keys = ['whatsapp_bot_key_2024', 'prestasi_api_key'];
        
        if (!$api_key || !in_array($api_key, $valid_api_keys)) {
            $this->_send_error_response('Unauthorized', 401);
            return FALSE;
        }
        
        return TRUE;
    }

    /**
     * Send standardized error response
     * @param string $message
     * @param int $status_code
     * @return void
     */
    private function _send_error_response($message, $status_code = 400)
    {
        $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => $message,
                'timestamp' => date('Y-m-d H:i:s')
            ]));
    }

    /**
     * Send standardized success response
     * @param array $data
     * @param string $message
     * @return void
     */
    private function _send_success_response($data = [], $message = 'Success')
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if (!empty($data)) {
            $response['data'] = $data;
        }
        
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Create jurnal via API (for WhatsApp bot)
     * @return void
     */
    public function create_jurnal()
    {
        // Authenticate API request
        if (!$this->_authenticate()) {
            return;
        }
        
        // Only accept POST requests
        if ($this->input->method() !== 'post') {
            $this->_send_error_response('Method not allowed', 405);
            return;
        }
        
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), TRUE);
        
        // If JSON is empty, try to get from POST data
        if (empty($input)) {
            $input = $this->input->post();
        }
        
        // Validate required fields
        $required_fields = ['tanggal', 'id_guru', 'id_kelas', 'id_mapel', 'materi', 'jumlah_siswa'];
        foreach ($required_fields as $field) {
            if (empty($input[$field])) {
                $this->_send_error_response("Field '{$field}' is required");
                return;
            }
        }
        
        // Validate data
        if (!strtotime($input['tanggal'])) {
            $this->_send_error_response('Invalid date format for tanggal');
            return;
        }
        
        if (!is_numeric($input['jumlah_siswa']) || $input['jumlah_siswa'] <= 0) {
            $this->_send_error_response('Jumlah siswa must be a positive number');
            return;
        }
        
        // Check if guru exists
        $guru = $this->db->get_where('bimbel_guru', ['id_guru' => $input['id_guru']])->row();
        if (!$guru) {
            $this->_send_error_response('Guru not found');
            return;
        }
        
        // Check if kelas exists
        $kelas = $this->db->get_where('bimbel_kelas', ['id_kelas' => $input['id_kelas']])->row();
        if (!$kelas) {
            $this->_send_error_response('Kelas not found');
            return;
        }
        
        // Check if mapel exists
        $mapel = $this->db->get_where('bimbel_mapel', ['id_mapel' => $input['id_mapel']])->row();
        if (!$mapel) {
            $this->_send_error_response('Mata Pelajaran not found');
            return;
        }
        
        // Prepare data for insertion
        $data = [
            'tanggal' => date('Y-m-d', strtotime($input['tanggal'])),
            'id_guru' => $input['id_guru'],
            'id_kelas' => $input['id_kelas'],
            'id_mapel' => $input['id_mapel'],
            'materi' => $input['materi'],
            'jumlah_siswa' => $input['jumlah_siswa'],
            'keterangan' => isset($input['keterangan']) ? $input['keterangan'] : null,
            'created_by' => isset($input['created_by']) ? $input['created_by'] : 1, // Default to admin if not specified
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Handle foto_bukti if provided as base64
        if (isset($input['foto_bukti']) && !empty($input['foto_bukti'])) {
            // Check if it's a base64 string
            if (preg_match('/^data:image\/(\w+);base64,/', $input['foto_bukti'])) {
                $foto_bukti = $this->_upload_base64_image($input['foto_bukti']);
                if ($foto_bukti) {
                    $data['foto_bukti'] = $foto_bukti;
                }
            }
        }
        
        // Insert jurnal
        $result = $this->Jurnal_model->insert_jurnal($data);
        
        if ($result) {
            // Get the inserted jurnal with related data
            $jurnal_id = $this->db->insert_id();
            $jurnal = $this->Jurnal_model->get_jurnal_by_id($jurnal_id);
            
            $this->_send_success_response([
                'id_jurnal' => $jurnal_id,
                'jurnal_data' => $jurnal
            ], 'Jurnal created successfully');
        } else {
            $this->_send_error_response('Failed to create jurnal');
        }
    }

    /**
     * Upload base64 image
     * @param string $base64_string
     * @return string|null
     */
    private function _upload_base64_image($base64_string)
    {
        try {
            // Extract file extension
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $matches)) {
                $image_type = $matches[1];
                $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
                $base64_string = base64_decode($base64_string);
                
                if ($base64_string === false) {
                    return null;
                }
                
                // Generate unique filename
                $filename = uniqid() . '.' . $image_type;
                $filepath = './assets/uploads/foto_kegiatan/' . $filename;
                
                // Save file
                if (file_put_contents($filepath, $base64_string)) {
                    return $filename;
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error uploading base64 image: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Get list of guru for API
     * @return void
     */
    public function get_guru()
    {
        if (!$this->_authenticate()) {
            return;
        }
        
        $guru = $this->Jurnal_model->get_guru();
        $this->_send_success_response($guru, 'Guru list retrieved successfully');
    }

    /**
     * Get list of kelas for API
     * @return void
     */
    public function get_kelas()
    {
        if (!$this->_authenticate()) {
            return;
        }
        
        $kelas = $this->Jurnal_model->get_kelas();
        $this->_send_success_response($kelas, 'Kelas list retrieved successfully');
    }

    /**
     * Get list of mapel for API
     * @return void
     */
    public function get_mapel()
    {
        if (!$this->_authenticate()) {
            return;
        }
        
        $mapel = $this->Jurnal_model->get_mapel();
        $this->_send_success_response($mapel, 'Mapel list retrieved successfully');
    }

    /**
     * Get jurnal by ID for API
     * @param int $id
     * @return void
     */
    public function get_jurnal($id = null)
    {
        if (!$this->_authenticate()) {
            return;
        }
        
        if (!$id) {
            $this->_send_error_response('Jurnal ID is required');
            return;
        }
        
        $jurnal = $this->Jurnal_model->get_jurnal_by_id($id);
        
        if ($jurnal) {
            $this->_send_success_response($jurnal, 'Jurnal retrieved successfully');
        } else {
            $this->_send_error_response('Jurnal not found', 404);
        }
    }

    /**
     * Get all jurnal with pagination for API
     * @return void
     */
    public function get_all_jurnal()
    {
        if (!$this->_authenticate()) {
            return;
        }
        
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $limit = $this->input->get('limit') ? (int)$this->input->get('limit') : 10;
        $offset = ($page - 1) * $limit;
        
        // Get total count
        $total = $this->db->count_all_results('bimbel_jurnal');
        
        // Get jurnal with pagination
        $this->db->select('j.*, g.nama_guru, g.nip, k.nama_kelas, m.nama_mapel, u.nama as nama_penginput');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->join('bimbel_users u', 'j.created_by = u.id_user');
        $this->db->order_by('j.tanggal', 'DESC');
        $this->db->order_by('j.created_at', 'DESC');
        $this->db->limit($limit, $offset);
        $jurnal = $this->db->get()->result();
        
        $this->_send_success_response([
            'jurnal' => $jurnal,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ], 'Jurnal list retrieved successfully');
    }

    /**
     * Search jurnal for API
     * @return void
     */
    public function search_jurnal()
    {
        if (!$this->_authenticate()) {
            return;
        }
        
        $keyword = $this->input->get('keyword');
        
        if (empty($keyword)) {
            $this->_send_error_response('Search keyword is required');
            return;
        }
        
        $jurnal = $this->Jurnal_model->search_jurnal($keyword);
        $this->_send_success_response($jurnal, 'Search results retrieved successfully');
    }
}