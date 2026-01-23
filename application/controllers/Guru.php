<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guru extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Load input library (core library that can't be autoloaded)

        
        // Cek jika user belum login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        // Load model
        $this->load->model('Guru_model');
        $this->load->model('Dashboard_model');
    }

    /**
     * Halaman guru utama
     * @return void
     */
    public function index()
    {
        $data['user'] = $this->session->userdata();
        $data['total_guru'] = $this->Dashboard_model->get_total_guru();
        $this->load->view('guru/index', $data);
    }

    /**
     * Get data guru via Ajax untuk datatable
     * @return void
     */
    public function get_guru_data()
    {
        $guru = $this->Guru_model->get_all_guru();
        
        $data = [];
        foreach ($guru as $g) {
            $row = [];
            $row[] = $g->id_guru;
            $row[] = $g->nama_guru;
            $row[] = $g->nip ? $g->nip : '-';
            $row[] = $g->no_telpon ? $g->no_telpon : '-';
            $row[] = $g->no_lid ? $g->no_lid : '-';
            $row[] = $g->nama_kelas;
            $row[] = $g->nama_mapel;
            $row[] = '<span class="px-3 py-1 rounded-full text-xs font-medium ' .
                    ($g->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') .
                    '">' . ucfirst($g->status) . '</span>';
            $row[] = '<div class="flex gap-1">
                        <button onclick="editGuru('.$g->id_guru.')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteGuru('.$g->id_guru.')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button onclick="toggleStatus('.$g->id_guru.', \''.$g->status.'\')" class="px-3 py-1 ' .
                        ($g->status == 'aktif' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600') .
                        ' text-white rounded transition-colors">
                            <i class="fas ' . ($g->status == 'aktif' ? 'fa-eye-slash' : 'fa-eye') . '"></i>
                        </button>
                      </div>';
            $data[] = $row;
        }

        $output = [
            "data" => $data
        ];

        echo json_encode($output);
    }

    /**
     * Get data guru by ID via Ajax
     * @param int $id
     * @return void
     */
    public function get_guru_by_id($id)
    {
        $guru = $this->Guru_model->get_guru_by_id($id);
        
        echo json_encode([
            'status' => 'success',
            'data' => $guru
        ]);
    }

    /**
     * Get data kelas untuk dropdown
     * @return void
     */
    public function get_kelas()
    {
        $kelas = $this->Guru_model->get_kelas();
        
        echo json_encode([
            'status' => 'success',
            'data' => $kelas
        ]);
    }

    /**
     * Get data mapel untuk dropdown
     * @return void
     */
    public function get_mapel()
    {
        $mapel = $this->Guru_model->get_mapel();
        
        echo json_encode([
            'status' => 'success',
            'data' => $mapel
        ]);
    }

    /**
     * Simpan data guru (tambah/edit)
     * @return void
     */
    public function save_guru()
    {
        $this->form_validation->set_rules('nama_guru', 'Nama Guru', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('nip', 'NIP', 'trim|max_length[30]|callback_nip_check');
        $this->form_validation->set_rules('no_telpon', 'No. Telepon', 'trim|max_length[15]|callback_phone_check');
        $this->form_validation->set_rules('no_lid', 'No. LID', 'trim|max_length[30]');
        $this->form_validation->set_rules('id_kelas', 'Kelas', 'required');
        $this->form_validation->set_rules('id_mapel', 'Mata Pelajaran', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $errors = [
                'nama_guru' => form_error('nama_guru'),
                'nip' => form_error('nip'),
                'no_telpon' => form_error('no_telpon'),
                'no_lid' => form_error('no_lid'),
                'id_kelas' => form_error('id_kelas'),
                'id_mapel' => form_error('id_mapel'),
                'status' => form_error('status')
            ];
            
            echo json_encode([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $errors
            ]);
            return;
        }
        
        $id_guru = $this->input->post('id_guru');
        $data = [
            'nama_guru' => $this->input->post('nama_guru'),
            'nip' => $this->input->post('nip') ? $this->input->post('nip') : null,
            'no_telpon' => $this->input->post('no_telpon') ? $this->input->post('no_telpon') : null,
            'no_lid' => $this->input->post('no_lid') ? $this->input->post('no_lid') : null,
            'id_kelas' => $this->input->post('id_kelas'),
            'id_mapel' => $this->input->post('id_mapel'),
            'status' => $this->input->post('status')
        ];
        
        if ($id_guru) {
            // Update
            $result = $this->Guru_model->update_guru($id_guru, $data);
            $message = 'Data guru berhasil diperbarui';
        } else {
            // Insert
            $result = $this->Guru_model->insert_guru($data);
            $message = 'Data guru berhasil ditambahkan';
        }
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? $message : 'Terjadi kesalahan saat menyimpan data'
        ]);
    }

    /**
     * Hapus data guru
     * @param int $id
     * @return void
     */
    public function delete_guru($id)
    {
        $result = $this->Guru_model->delete_guru($id);
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Data guru berhasil dihapus' : 'Gagal menghapus data. Guru mungkin memiliki jurnal terkait.'
        ]);
    }

    /**
     * Toggle status guru
     * @param int $id
     * @return void
     */
    public function toggle_status($id)
    {
        $guru = $this->Guru_model->get_guru_by_id($id);
        $new_status = $guru->status == 'aktif' ? 'nonaktif' : 'aktif';
        $result = $this->Guru_model->toggle_status($id, $new_status);
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Status guru berhasil diubah' : 'Gagal mengubah status guru'
        ]);
    }

    /**
     * Cari guru
     * @return void
     */
    public function search()
    {
        $keyword = $this->input->get('keyword');
        $guru = $this->Guru_model->search_guru($keyword);
        
        echo json_encode([
            'status' => 'success',
            'data' => $guru
        ]);
    }

    /**
     * Custom validation untuk NIP
     * @param string $nip
     * @return bool
     */
    public function nip_check($nip)
    {
        if (empty($nip)) {
            return TRUE;
        }
        
        if (!preg_match('/^[0-9]{18}$/', $nip)) {
            $this->form_validation->set_message('nip_check', 'Format NIP tidak valid (harus 18 digit angka)');
            return FALSE;
        }
        
        return TRUE;
    }

    /**
     * Custom validation untuk No. Telepon
     * @param string $no_telpon
     * @return bool
     */
    public function phone_check($no_telpon)
    {
        if (empty($no_telpon)) {
            return TRUE;
        }
        
        // Validasi format nomor telepon Indonesia (dimulai dengan 0, +62, atau 628)
        if (!preg_match('/^(0[0-9]{9,14}|(\+62)[0-9]{9,14}|628[0-9]{8,12})$/', $no_telpon)) {
            $this->form_validation->set_message('phone_check', 'Format nomor telepon tidak valid. Gunakan format: 08xxxxxxxxxx, +62xxxxxxxxxx, atau 628xxxxxxxxxx');
            return FALSE;
        }
        
        return TRUE;
    }

    /**
     * API method untuk mendapatkan total guru aktif
     * @return void
     */
    public function get_total_guru_aktif()
    {
        $total = $this->Guru_model->get_total_guru_aktif();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total' => $total
            ]
        ]);
    }
}