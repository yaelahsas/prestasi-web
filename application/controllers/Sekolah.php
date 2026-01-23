<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sekolah extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Load input library (core library that can't be autoloaded)

        
        // Cek jika user belum login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        // Load model
        $this->load->model('Sekolah_model');
        $this->load->model('Dashboard_model');
    }

    /**
     * Halaman sekolah utama
     * @return void
     */
    public function index()
    {
        $data['user'] = $this->session->userdata();
        $data['total_sekolah'] = $this->Dashboard_model->get_total_sekolah();
        $this->load->view('sekolah/index', $data);
    }

    /**
     * Get data sekolah via Ajax untuk datatable
     * @return void
     */
    public function get_sekolah_data()
    {
        try {
            $sekolah = $this->Sekolah_model->get_all_sekolah();
            
            $data = [];
            foreach ($sekolah as $s) {
                $row = [];
                $row[] = $s->id_sekolah;
                $row[] = $s->nama_sekolah;
                $row[] = $s->alamat ? substr($s->alamat, 0, 50) . '...' : '-';
                $row[] = $s->kepala_sekolah ? $s->kepala_sekolah : '-';
                $row[] = $s->nip_kepsek ? $s->nip_kepsek : '-';
                $row[] = $s->logo ? '<img src="' . base_url('assets/uploads/logo/' . $s->logo) . '" alt="Logo" class="w-12 h-12 object-cover rounded">' : '-';
                $row[] = '<div class="flex gap-1">
                            <button onclick="editSekolah('.$s->id_sekolah.')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteSekolah('.$s->id_sekolah.')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                          </div>';
                $data[] = $row;
            }

            $output = [
                "status" => "success",
                "data" => $data
            ];

            echo json_encode($output);
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Gagal memuat data sekolah: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Get data sekolah by ID via Ajax
     * @param int $id
     * @return void
     */
    public function get_sekolah_by_id($id)
    {
        try {
            $sekolah = $this->Sekolah_model->get_sekolah_by_id($id);
            
            if ($sekolah) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $sekolah
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Data sekolah tidak ditemukan'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal memuat data sekolah: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Simpan data sekolah (tambah/edit)
     * @return void
     */
    public function save_sekolah()
    {
        try {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('nama_sekolah', 'Nama Sekolah', 'required|trim|max_length[150]');
            $this->form_validation->set_rules('alamat', 'Alamat', 'trim|max_length[500]');
            $this->form_validation->set_rules('kepala_sekolah', 'Kepala Sekolah', 'trim|max_length[100]');
            $this->form_validation->set_rules('nip_kepsek', 'NIP Kepala Sekolah', 'trim|max_length[30]|callback_nip_check');
            
            if ($this->form_validation->run() == FALSE) {
                $errors = [
                    'nama_sekolah' => form_error('nama_sekolah'),
                    'alamat' => form_error('alamat'),
                    'kepala_sekolah' => form_error('kepala_sekolah'),
                    'nip_kepsek' => form_error('nip_kepsek')
                ];
                
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $errors
                ]);
                return;
            }
            
            $id_sekolah = $this->input->post('id_sekolah');
            $data = [
                'nama_sekolah' => $this->input->post('nama_sekolah'),
                'alamat' => $this->input->post('alamat') ? $this->input->post('alamat') : null,
                'kepala_sekolah' => $this->input->post('kepala_sekolah') ? $this->input->post('kepala_sekolah') : null,
                'nip_kepsek' => $this->input->post('nip_kepsek') ? $this->input->post('nip_kepsek') : null
            ];
            
            // Handle logo upload
            if (!empty($_FILES['logo']['name'])) {
                $config['upload_path'] = './assets/uploads/logo/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['max_size'] = 2048; // 2MB
                $config['file_name'] = time() . '_' . $_FILES['logo']['name'];
                
                $this->load->library('upload', $config);
                
                if ($this->upload->do_upload('logo')) {
                    $upload_data = $this->upload->data();
                    $data['logo'] = $upload_data['file_name'];
                    
                    // Delete old logo if exists
                    if ($id_sekolah) {
                        $old_sekolah = $this->Sekolah_model->get_sekolah_by_id($id_sekolah);
                        if ($old_sekolah && $old_sekolah->logo && file_exists('./assets/uploads/logo/' . $old_sekolah->logo)) {
                            unlink('./assets/uploads/logo/' . $old_sekolah->logo);
                        }
                    }
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Upload gagal: ' . $this->upload->display_errors()
                    ]);
                    return;
                }
            }
            
            if ($id_sekolah) {
                // Update
                $result = $this->Sekolah_model->update_sekolah($id_sekolah, $data);
                $message = 'Data sekolah berhasil diperbarui';
            } else {
                // Insert
                $result = $this->Sekolah_model->insert_sekolah($data);
                $message = 'Data sekolah berhasil ditambahkan';
            }
            
            echo json_encode([
                'status' => $result ? 'success' : 'error',
                'message' => $result ? $message : 'Terjadi kesalahan saat menyimpan data'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Hapus data sekolah
     * @param int $id
     * @return void
     */
    public function delete_sekolah($id)
    {
        try {
            // Get sekolah data to delete logo file
            $sekolah = $this->Sekolah_model->get_sekolah_by_id($id);
            
            if (!$sekolah) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Data sekolah tidak ditemukan'
                ]);
                return;
            }
            
            $result = $this->Sekolah_model->delete_sekolah($id);
            
            if ($result && $sekolah && $sekolah->logo) {
                // Delete logo file
                if (file_exists('./assets/uploads/logo/' . $sekolah->logo)) {
                    unlink('./assets/uploads/logo/' . $sekolah->logo);
                }
            }
            
            echo json_encode([
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'Data sekolah berhasil dihapus' : 'Gagal menghapus data sekolah'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Cari sekolah
     * @return void
     */
    public function search()
    {
        try {
            $keyword = $this->input->get('keyword');
            $sekolah = $this->Sekolah_model->search_sekolah($keyword);
            
            echo json_encode([
                'status' => 'success',
                'data' => $sekolah
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
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
     * Get total sekolah for API
     * @return void
     */
    public function get_total_sekolah()
    {
        try {
            $total = $this->Sekolah_model->count_sekolah();
            
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'total' => $total
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API endpoint to get sekolah info for laporan
     * @return void
     */
    public function api_get_sekolah()
    {
        // Get the first sekolah record (assuming there's only one)
        $sekolah = $this->Sekolah_model->get_first_sekolah();
        
        if ($sekolah) {
            // Add logo URL if logo exists
            if ($sekolah->logo) {
                $sekolah->logo_url = base_url('assets/uploads/logo/' . $sekolah->logo);
            }
            
            echo json_encode($sekolah);
        } else {
            echo json_encode([
                'error' => true,
                'message' => 'Data sekolah tidak ditemukan'
            ]);
        }
    }
}