<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->requireLogin();
        $this->requireRole(['admin', 'tim'], 'guru_portal');

        $this->load->model('Absensi_model');
        $this->load->model('Guru_model');
        $this->load->model('Kelas_model');
        $this->load->model('Mapel_model');
    }

    /**
     * Halaman utama absensi
     */
    public function index()
    {
        $data['user']  = $this->session->userdata();
        $data['guru']  = $this->Guru_model->get_all_guru();
        $data['bulan'] = $this->input->get('bulan') ?: date('m');
        $data['tahun'] = $this->input->get('tahun') ?: date('Y');
        $data['rekap'] = $this->Absensi_model->get_rekap_semua_guru($data['bulan'], $data['tahun']);
        $data['total_absensi'] = $this->Absensi_model->get_total_absensi_dashboard($data['bulan'], $data['tahun']);
        $this->load->view('absensi/index', $data);
    }

    /**
     * Get data absensi untuk DataTable
     */
    public function get_absensi_data()
    {
        $filter = [
            'id_guru' => $this->input->get('id_guru'),
            'bulan'   => $this->input->get('bulan') ?: date('m'),
            'tahun'   => $this->input->get('tahun') ?: date('Y'),
            'status'  => $this->input->get('status'),
        ];

        $absensi = $this->Absensi_model->get_all_absensi($filter);

        $rows = [];
        $status_colors = [
            'hadir' => 'bg-green-100 text-green-800',
            'izin'  => 'bg-blue-100 text-blue-800',
            'sakit' => 'bg-yellow-100 text-yellow-800',
            'alpha' => 'bg-red-100 text-red-800',
        ];

        foreach ($absensi as $a) {
            $color = $status_colors[$a->status] ?? 'bg-gray-100 text-gray-800';
            $row   = [];
            $row[] = $a->id_absensi;
            $row[] = date('d/m/Y', strtotime($a->tanggal));
            $row[] = $a->nama_guru;
            $row[] = $a->nama_kelas ?? '-';
            $row[] = $a->nama_mapel ?? '-';
            $row[] = '<span class="px-2 py-1 rounded-full text-xs font-medium ' . $color . '">' . ucfirst($a->status) . '</span>';
            $row[] = $a->keterangan ?? '-';
            $row[] = '<div class="flex gap-1">
                        <button onclick="editAbsensi(' . $a->id_absensi . ')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteAbsensi(' . $a->id_absensi . ')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs">
                            <i class="fas fa-trash"></i>
                        </button>
                      </div>';
            $rows[] = $row;
        }

        echo json_encode(['data' => $rows]);
    }

    /**
     * Get absensi by ID
     */
    public function get_absensi_by_id($id)
    {
        $absensi = $this->Absensi_model->get_absensi_by_id($id);
        echo json_encode([
            'status' => $absensi ? 'success' : 'error',
            'data'   => $absensi
        ]);
    }

    /**
     * Simpan absensi (tambah/edit)
     */
    public function save_absensi()
    {
        $this->form_validation->set_rules('tanggal',  'Tanggal', 'required|trim');
        $this->form_validation->set_rules('id_guru',  'Guru',    'required');
        $this->form_validation->set_rules('status',   'Status',  'required|in_list[hadir,izin,sakit,alpha]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => [
                    'tanggal' => form_error('tanggal'),
                    'id_guru' => form_error('id_guru'),
                    'status'  => form_error('status'),
                ]
            ]);
            return;
        }

        $id_absensi = $this->input->post('id_absensi');
        $id_guru    = $this->input->post('id_guru');
        $tanggal    = $this->input->post('tanggal');

        // Cek duplikat
        if ($this->Absensi_model->is_absensi_exists($id_guru, $tanggal, $id_absensi)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Absensi untuk guru ini pada tanggal tersebut sudah ada'
            ]);
            return;
        }

        $data = [
            'tanggal'    => $tanggal,
            'id_guru'    => $id_guru,
            'id_kelas'   => $this->input->post('id_kelas') ?: null,
            'id_mapel'   => $this->input->post('id_mapel') ?: null,
            'status'     => $this->input->post('status'),
            'keterangan' => $this->input->post('keterangan') ?: null,
            'created_by' => $this->session->userdata('id_user'),
        ];

        if ($id_absensi) {
            $result  = $this->Absensi_model->update_absensi($id_absensi, $data);
            $message = 'Absensi berhasil diperbarui';
        } else {
            $result  = $this->Absensi_model->insert_absensi($data);
            $message = 'Absensi berhasil ditambahkan';
        }

        $this->logActivity($id_absensi ? 'UPDATE' : 'INSERT', 'bimbel_absensi', $id_absensi ?: $this->db->insert_id(), $message);

        echo json_encode([
            'status'  => $result ? 'success' : 'error',
            'message' => $result ? $message : 'Gagal menyimpan absensi'
        ]);
    }

    /**
     * Hapus absensi
     */
    public function delete_absensi($id)
    {
        $result = $this->Absensi_model->delete_absensi($id);
        $this->logActivity('DELETE', 'bimbel_absensi', $id, 'Hapus absensi');

        echo json_encode([
            'status'  => $result ? 'success' : 'error',
            'message' => $result ? 'Absensi berhasil dihapus' : 'Gagal menghapus absensi'
        ]);
    }

    /**
     * Get rekap absensi per guru (untuk laporan)
     */
    public function get_rekap()
    {
        $bulan = $this->input->get('bulan') ?: date('m');
        $tahun = $this->input->get('tahun') ?: date('Y');
        $rekap = $this->Absensi_model->get_rekap_semua_guru($bulan, $tahun);

        echo json_encode([
            'status' => 'success',
            'data'   => $rekap
        ]);
    }

    /**
     * Get data guru untuk dropdown
     */
    public function get_guru()
    {
        $guru = $this->Guru_model->get_all_guru();
        echo json_encode(['status' => 'success', 'data' => $guru]);
    }
}
