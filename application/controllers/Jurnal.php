<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jurnal extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->requireLogin();

        // Load model
        $this->load->model('Jurnal_model');
        $this->load->model('Dashboard_model');
        $this->load->model('Guru_model');
        $this->load->model('Kelas_model');
        $this->load->model('Mapel_model');
    }

    /**
     * Halaman jurnal utama
     * @return void
     */
    public function index()
    {
        $data['user']         = $this->session->userdata();
        $data['total_jurnal'] = $this->Jurnal_model->get_total_jurnal();
        $data['total_hari']   = $this->Jurnal_model->get_total_jurnal_hari_ini();
        $data['total_bulan']  = $this->Jurnal_model->get_total_jurnal_bulan_ini();
        // Data untuk filter dropdown
        $data['guru_list']    = $this->Guru_model->get_all_guru();
        $data['kelas_list']   = $this->Kelas_model->get_all_kelas();
        $data['mapel_list']   = $this->Mapel_model->get_all_mapel();
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
        $tanggal_awal  = $this->input->get('tanggal_awal');
        $tanggal_akhir = $this->input->get('tanggal_akhir');
        
        if (!$tanggal_awal || !$tanggal_akhir) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Tanggal awal dan tanggal akhir harus diisi'
            ]);
            return;
        }
        
        $jurnal = $this->Jurnal_model->get_jurnal_by_tanggal($tanggal_awal, $tanggal_akhir);
        
        echo json_encode([
            'status' => 'success',
            'data'   => $jurnal
        ]);
    }

    /**
     * Filter lanjutan jurnal (kombinasi tanggal + guru + kelas + mapel)
     * @return void
     */
    public function filter_lanjutan()
    {
        $tanggal_awal  = $this->input->get('tanggal_awal');
        $tanggal_akhir = $this->input->get('tanggal_akhir');
        $id_guru       = $this->input->get('id_guru');
        $id_kelas      = $this->input->get('id_kelas');
        $id_mapel      = $this->input->get('id_mapel');
        $keyword       = $this->input->get('keyword');

        $this->db->select('j.*, g.nama_guru, g.nip, k.nama_kelas, m.nama_mapel, u.nama as nama_penginput');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->join('bimbel_users u', 'j.created_by = u.id_user');

        if ($tanggal_awal)  $this->db->where('j.tanggal >=', $tanggal_awal);
        if ($tanggal_akhir) $this->db->where('j.tanggal <=', $tanggal_akhir);
        if ($id_guru)       $this->db->where('j.id_guru', $id_guru);
        if ($id_kelas)      $this->db->where('j.id_kelas', $id_kelas);
        if ($id_mapel)      $this->db->where('j.id_mapel', $id_mapel);

        if ($keyword) {
            $this->db->group_start();
            $this->db->like('j.materi', $keyword);
            $this->db->or_like('g.nama_guru', $keyword);
            $this->db->or_like('k.nama_kelas', $keyword);
            $this->db->or_like('m.nama_mapel', $keyword);
            $this->db->group_end();
        }

        $this->db->order_by('j.tanggal', 'DESC');
        $this->db->order_by('j.created_at', 'DESC');
        $jurnal = $this->db->get()->result();

        $data = [];
        foreach ($jurnal as $j) {
            $row   = [];
            $row[] = $j->id_jurnal;
            $row[] = date('d/m/Y', strtotime($j->tanggal));
            $row[] = $j->nama_guru;
            $row[] = $j->nama_kelas;
            $row[] = $j->nama_mapel;
            $row[] = substr($j->materi, 0, 50) . (strlen($j->materi) > 50 ? '...' : '');
            $row[] = $j->jumlah_siswa;
            $row[] = $j->foto_bukti
                ? '<img src="' . base_url('assets/uploads/foto_kegiatan/' . $j->foto_bukti) . '" style="width:50px;height:50px;object-fit:cover;border-radius:8px;" onclick="viewImage(\'' . $j->foto_bukti . '\')" style="cursor:pointer;">'
                : '<span class="text-gray-400">Tidak ada</span>';
            $row[] = '<div class="flex gap-1">
                        <button onclick="editJurnal(' . $j->id_jurnal . ')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteJurnal(' . $j->id_jurnal . ')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors"><i class="fas fa-trash"></i></button>
                        <button onclick="viewJurnal(' . $j->id_jurnal . ')" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors"><i class="fas fa-eye"></i></button>
                      </div>';
            $data[] = $row;
        }

        echo json_encode(['data' => $data, 'total' => count($data)]);
    }

    /**
     * Export jurnal ke CSV
     * @return void
     */
    public function export_csv()
    {
        $tanggal_awal  = $this->input->get('tanggal_awal');
        $tanggal_akhir = $this->input->get('tanggal_akhir');
        $id_guru       = $this->input->get('id_guru');
        $id_kelas      = $this->input->get('id_kelas');
        $id_mapel      = $this->input->get('id_mapel');

        $this->db->select('j.tanggal, g.nama_guru, g.nip, k.nama_kelas, m.nama_mapel, j.materi, j.jumlah_siswa, j.keterangan, u.nama as nama_penginput, j.created_at');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->join('bimbel_users u', 'j.created_by = u.id_user');

        if ($tanggal_awal)  $this->db->where('j.tanggal >=', $tanggal_awal);
        if ($tanggal_akhir) $this->db->where('j.tanggal <=', $tanggal_akhir);
        if ($id_guru)       $this->db->where('j.id_guru', $id_guru);
        if ($id_kelas)      $this->db->where('j.id_kelas', $id_kelas);
        if ($id_mapel)      $this->db->where('j.id_mapel', $id_mapel);

        $this->db->order_by('j.tanggal', 'DESC');
        $jurnal = $this->db->get()->result();

        $filename = 'export_jurnal_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // BOM untuk Excel UTF-8
        fputs($output, "\xEF\xBB\xBF");

        // Header CSV
        fputcsv($output, ['Tanggal', 'Nama Guru', 'NIP', 'Kelas', 'Mata Pelajaran', 'Materi', 'Jumlah Siswa', 'Keterangan', 'Penginput', 'Waktu Input']);

        foreach ($jurnal as $j) {
            fputcsv($output, [
                date('d/m/Y', strtotime($j->tanggal)),
                $j->nama_guru,
                $j->nip,
                $j->nama_kelas,
                $j->nama_mapel,
                $j->materi,
                $j->jumlah_siswa,
                $j->keterangan,
                $j->nama_penginput,
                date('d/m/Y H:i', strtotime($j->created_at)),
            ]);
        }

        fclose($output);
        exit;
    }
}