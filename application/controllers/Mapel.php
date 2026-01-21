<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mapel extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Load input library (core library that can't be autoloaded)

        
        // Cek jika user belum login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        // Load model
        $this->load->model('Mapel_model');
        $this->load->model('Dashboard_model');
    }

    /**
     * Halaman mapel utama
     * @return void
     */
    public function index()
    {
        $data['user'] = $this->session->userdata();
        $data['total_mapel'] = $this->Dashboard_model->get_total_mapel();
        $this->load->view('mapel/index', $data);
    }

    /**
     * Get data mapel via Ajax untuk datatable
     * @return void
     */
    public function get_mapel_data()
    {
        $mapel = $this->Mapel_model->get_all_mapel();
        
        $data = [];
        foreach ($mapel as $m) {
            $row = [];
            $row[] = $m->id_mapel;
            $row[] = $m->nama_mapel;
            $row[] = '<span class="px-3 py-1 rounded-full text-xs font-medium ' . 
                    ($m->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') . 
                    '">' . ucfirst($m->status) . '</span>';
            $row[] = '<div class="flex gap-1">
                        <button onclick="editMapel('.$m->id_mapel.')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteMapel('.$m->id_mapel.')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button onclick="toggleStatus('.$m->id_mapel.', \''.$m->status.'\')" class="px-3 py-1 ' . 
                        ($m->status == 'aktif' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600') . 
                        ' text-white rounded transition-colors">
                            <i class="fas ' . ($m->status == 'aktif' ? 'fa-eye-slash' : 'fa-eye') . '"></i>
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
     * Get data mapel by ID via Ajax
     * @param int $id
     * @return void
     */
    public function get_mapel_by_id($id)
    {
        $mapel = $this->Mapel_model->get_mapel_by_id($id);
        
        echo json_encode([
            'status' => 'success',
            'data' => $mapel
        ]);
    }

    /**
     * Simpan data mapel (tambah/edit)
     * @return void
     */
    public function save_mapel()
    {
        $this->form_validation->set_rules('nama_mapel', 'Nama Mata Pelajaran', 'required|trim|max_length[50]');
        $this->form_validation->set_rules('status', 'Status', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $errors = [
                'nama_mapel' => form_error('nama_mapel'),
                'status' => form_error('status')
            ];
            
            echo json_encode([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $errors
            ]);
            return;
        }
        
        $id_mapel = $this->input->post('id_mapel');
        $data = [
            'nama_mapel' => $this->input->post('nama_mapel'),
            'status' => $this->input->post('status')
        ];
        
        if ($id_mapel) {
            // Update
            $result = $this->Mapel_model->update_mapel($id_mapel, $data);
            $message = 'Data mata pelajaran berhasil diperbarui';
        } else {
            // Insert
            $result = $this->Mapel_model->insert_mapel($data);
            $message = 'Data mata pelajaran berhasil ditambahkan';
        }
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? $message : 'Terjadi kesalahan saat menyimpan data'
        ]);
    }

    /**
     * Hapus data mapel
     * @param int $id
     * @return void
     */
    public function delete_mapel($id)
    {
        $result = $this->Mapel_model->delete_mapel($id);
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Data mata pelajaran berhasil dihapus' : 'Gagal menghapus data. Mata pelajaran mungkin memiliki guru atau jurnal terkait.'
        ]);
    }

    /**
     * Toggle status mapel
     * @param int $id
     * @return void
     */
    public function toggle_status($id)
    {
        $mapel = $this->Mapel_model->get_mapel_by_id($id);
        $new_status = $mapel->status == 'aktif' ? 'nonaktif' : 'aktif';
        $result = $this->Mapel_model->toggle_status($id, $new_status);
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Status mata pelajaran berhasil diubah' : 'Gagal mengubah status mata pelajaran'
        ]);
    }

    /**
     * Cari mapel
     * @return void
     */
    public function search()
    {
        $keyword = $this->input->get('keyword');
        $mapel = $this->Mapel_model->search_mapel($keyword);
        
        echo json_encode([
            'status' => 'success',
            'data' => $mapel
        ]);
    }

    /**
     * Get total mapel aktif for API
     * @return void
     */
    public function get_total_mapel_aktif()
    {
        $total = $this->Mapel_model->get_total_mapel_aktif();
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'total' => $total
            ]
        ]);
    }
}