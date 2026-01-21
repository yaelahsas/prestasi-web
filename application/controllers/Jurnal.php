<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jurnal extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Load input library (core library that can't be autoloaded)
        
        // Cek jika user belum login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        // Load model
        $this->load->model('Jurnal_model');
        $this->load->model('Dashboard_model');
    }

    /**
     * Halaman jurnal utama
     * @return void
     */
    public function index()
    {
        $data['user'] = $this->session->userdata();
        $data['total_jurnal'] = $this->Jurnal_model->get_total_jurnal();
        $data['total_hari'] = $this->Jurnal_model->get_total_jurnal_hari_ini();
        $data['total_bulan'] = $this->Jurnal_model->get_total_jurnal_bulan_ini();
        $this->load->view('jurnal/index', $data);
    }

    /**
     * Get data jurnal via Ajax untuk datatable
     * @return void
     */
    public function get_jurnal_data()
    {
        $jurnal = $this->Jurnal_model->get_all_jurnal();
        
        $data = [];
        foreach ($jurnal as $j) {
            $row = [];
            $row[] = $j->id_jurnal;
            $row[] = date('d/m/Y', strtotime($j->tanggal));
            $row[] = $j->nama_guru;
            $row[] = $j->nama_kelas;
            $row[] = $j->nama_mapel;
            $row[] = substr($j->materi, 0, 50) . (strlen($j->materi) > 50 ? '...' : '');
            $row[] = $j->jumlah_siswa;
            $row[] = $j->foto_bukti ? '<img src="' . base_url('assets/uploads/foto_kegiatan/' . $j->foto_bukti) . '" alt="Foto Bukti" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" onclick="viewImage(\'' . $j->foto_bukti . '\')" style="cursor: pointer;">' : '<span class="text-gray-400">Tidak ada</span>';
            $row[] = '<div class="flex gap-1">
                        <button onclick="editJurnal('.$j->id_jurnal.')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteJurnal('.$j->id_jurnal.')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button onclick="viewJurnal('.$j->id_jurnal.')" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                            <i class="fas fa-eye"></i>
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
     * Get data jurnal by ID via Ajax
     * @param int $id
     * @return void
     */
    public function get_jurnal_by_id($id)
    {
        $jurnal = $this->Jurnal_model->get_jurnal_by_id($id);
        
        echo json_encode([
            'status' => 'success',
            'data' => $jurnal
        ]);
    }

    /**
     * Get data guru untuk dropdown
     * @return void
     */
    public function get_guru()
    {
        $guru = $this->Jurnal_model->get_guru();
        
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
        $kelas = $this->Jurnal_model->get_kelas();
        
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
        $mapel = $this->Jurnal_model->get_mapel();
        
        echo json_encode([
            'status' => 'success',
            'data' => $mapel
        ]);
    }

    /**
     * Simpan data jurnal (tambah/edit)
     * @return void
     */
    public function save_jurnal()
    {
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required|trim');
        $this->form_validation->set_rules('id_guru', 'Guru', 'required');
        $this->form_validation->set_rules('id_kelas', 'Kelas', 'required');
        $this->form_validation->set_rules('id_mapel', 'Mata Pelajaran', 'required');
        $this->form_validation->set_rules('materi', 'Materi', 'required|trim|max_length[500]');
        $this->form_validation->set_rules('jumlah_siswa', 'Jumlah Siswa', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|max_length[500]');
        
        if ($this->form_validation->run() == FALSE) {
            $errors = [
                'tanggal' => form_error('tanggal'),
                'id_guru' => form_error('id_guru'),
                'id_kelas' => form_error('id_kelas'),
                'id_mapel' => form_error('id_mapel'),
                'materi' => form_error('materi'),
                'jumlah_siswa' => form_error('jumlah_siswa'),
                'keterangan' => form_error('keterangan')
            ];
            
            echo json_encode([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $errors
            ]);
            return;
        }
        
        $id_jurnal = $this->input->post('id_jurnal');
        $data = [
            'tanggal' => $this->input->post('tanggal'),
            'id_guru' => $this->input->post('id_guru'),
            'id_kelas' => $this->input->post('id_kelas'),
            'id_mapel' => $this->input->post('id_mapel'),
            'materi' => $this->input->post('materi'),
            'jumlah_siswa' => $this->input->post('jumlah_siswa'),
            'keterangan' => $this->input->post('keterangan') ? $this->input->post('keterangan') : null,
            'created_by' => $this->session->userdata('id_user')
        ];
        
        // Handle upload foto bukti
        if (!empty($_FILES['foto_bukti']['name'])) {
            $foto_bukti = $this->Jurnal_model->upload_foto_bukti('foto_bukti');
            if ($foto_bukti) {
                $data['foto_bukti'] = $foto_bukti;
            }
        }
        
        if ($id_jurnal) {
            // Update
            $result = $this->Jurnal_model->update_jurnal($id_jurnal, $data);
            $message = 'Data jurnal berhasil diperbarui';
        } else {
            // Insert
            $result = $this->Jurnal_model->insert_jurnal($data);
            $message = 'Data jurnal berhasil ditambahkan';
        }
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? $message : 'Terjadi kesalahan saat menyimpan data'
        ]);
    }

    /**
     * Hapus data jurnal
     * @param int $id
     * @return void
     */
    public function delete_jurnal($id)
    {
        $result = $this->Jurnal_model->delete_jurnal($id);
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Data jurnal berhasil dihapus' : 'Gagal menghapus data jurnal'
        ]);
    }

    /**
     * Cari jurnal
     * @return void
     */
    public function search()
    {
        $keyword = $this->input->get('keyword');
        $jurnal = $this->Jurnal_model->search_jurnal($keyword);
        
        echo json_encode([
            'status' => 'success',
            'data' => $jurnal
        ]);
    }

    /**
     * Filter jurnal berdasarkan tanggal
     * @return void
     */
    public function filter_by_tanggal()
    {
        $tanggal_awal = $this->input->get('tanggal_awal');
        $tanggal_akhir = $this->input->get('tanggal_akhir');
        
        if (!$tanggal_awal || !$tanggal_akhir) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Tanggal awal dan tanggal akhir harus diisi'
            ]);
            return;
        }
        
        $jurnal = $this->Jurnal_model->get_jurnal_by_tanggal($tanggal_awal, $tanggal_akhir);
        
        echo json_encode([
            'status' => 'success',
            'data' => $jurnal
        ]);
    }
}