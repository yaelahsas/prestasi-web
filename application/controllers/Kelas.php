<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Cek jika user belum login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        // Load model
        $this->load->model('Kelas_model');
        $this->load->model('Dashboard_model');
    }

    /**
     * Halaman kelas utama
     * @return void
     */
    public function index()
    {
        $data['user'] = $this->session->userdata();
        $data['total_kelas'] = $this->Dashboard_model->get_total_kelas();
        $this->load->view('kelas/index', $data);
    }

    /**
     * Get data kelas via Ajax untuk datatable
     * @return void
     */
    public function get_kelas_data()
    {
        $kelas = $this->Kelas_model->get_all_kelas();
        
        $data = [];
        foreach ($kelas as $k) {
            $row = [];
            $row[] = $k->id_kelas;
            $row[] = $k->nama_kelas;
            $row[] = $k->tingkat;
            $row[] = '<span class="px-3 py-1 rounded-full text-xs font-medium ' . 
                    ($k->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') . 
                    '">' . ucfirst($k->status) . '</span>';
            $row[] = '<div class="flex gap-1">
                        <button onclick="editKelas('.$k->id_kelas.')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteKelas('.$k->id_kelas.')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button onclick="toggleStatus('.$k->id_kelas.', \''.$k->status.'\')" class="px-3 py-1 ' . 
                        ($k->status == 'aktif' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600') . 
                        ' text-white rounded transition-colors">
                            <i class="fas ' . ($k->status == 'aktif' ? 'fa-eye-slash' : 'fa-eye') . '"></i>
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
     * Get data kelas by ID via Ajax
     * @param int $id
     * @return void
     */
    public function get_kelas_by_id($id)
    {
        $kelas = $this->Kelas_model->get_kelas_by_id($id);
        
        echo json_encode([
            'status' => 'success',
            'data' => $kelas
        ]);
    }

    /**
     * Simpan data kelas (tambah/edit)
     * @return void
     */
    public function save_kelas()
    {
        $this->form_validation->set_rules('nama_kelas', 'Nama Kelas', 'required|trim|max_length[20]');
        $this->form_validation->set_rules('tingkat', 'Tingkat', 'required|trim|max_length[5]');
        $this->form_validation->set_rules('status', 'Status', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $errors = [
                'nama_kelas' => form_error('nama_kelas'),
                'tingkat' => form_error('tingkat'),
                'status' => form_error('status')
            ];
            
            echo json_encode([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $errors
            ]);
            return;
        }
        
        $id_kelas = $this->input->post('id_kelas');
        $data = [
            'nama_kelas' => $this->input->post('nama_kelas'),
            'tingkat' => $this->input->post('tingkat'),
            'status' => $this->input->post('status')
        ];
        
        if ($id_kelas) {
            // Update
            $result = $this->Kelas_model->update_kelas($id_kelas, $data);
            $message = 'Data kelas berhasil diperbarui';
        } else {
            // Insert
            $result = $this->Kelas_model->insert_kelas($data);
            $message = 'Data kelas berhasil ditambahkan';
        }
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? $message : 'Terjadi kesalahan saat menyimpan data'
        ]);
    }

    /**
     * Hapus data kelas
     * @param int $id
     * @return void
     */
    public function delete_kelas($id)
    {
        $result = $this->Kelas_model->delete_kelas($id);
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Data kelas berhasil dihapus' : 'Gagal menghapus data. Kelas mungkin memiliki guru atau jurnal terkait.'
        ]);
    }

    /**
     * Toggle status kelas
     * @param int $id
     * @return void
     */
    public function toggle_status($id)
    {
        $kelas = $this->Kelas_model->get_kelas_by_id($id);
        $new_status = $kelas->status == 'aktif' ? 'nonaktif' : 'aktif';
        $result = $this->Kelas_model->toggle_status($id, $new_status);
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Status kelas berhasil diubah' : 'Gagal mengubah status kelas'
        ]);
    }

    /**
     * Cari kelas
     * @return void
     */
    public function search()
    {
        $keyword = $this->input->get('keyword');
        $kelas = $this->Kelas_model->search_kelas($keyword);
        
        echo json_encode([
            'status' => 'success',
            'data' => $kelas
        ]);
    }

    /**
     * API method untuk mendapatkan total kelas aktif
     * @return void
     */
    public function get_total_kelas_aktif()
    {
        $total = $this->Kelas_model->get_total_kelas_aktif();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total' => $total
            ]
        ]);
    }
}