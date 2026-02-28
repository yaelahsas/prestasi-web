<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Guru Portal Controller
 * Self-service portal untuk guru: input jurnal, lihat riwayat, absensi
 */
class Guru_portal extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->requireLogin();
        $this->requireRole('guru', 'dashboard');

        $this->load->model('Jurnal_model');
        $this->load->model('Absensi_model');
        $this->load->model('Guru_model');
    }

    /**
     * Dashboard guru - ringkasan jurnal milik sendiri
     */
    public function index()
    {
        $id_guru = $this->session->userdata('id_guru');

        $data['user']    = $this->session->userdata();
        $data['guru']    = $this->Guru_model->get_guru_by_id($id_guru);

        // Statistik jurnal milik guru ini
        $data['total_jurnal']        = $this->_count_jurnal_guru($id_guru);
        $data['total_jurnal_bulan']  = $this->_count_jurnal_guru_bulan($id_guru);
        $data['total_jurnal_hari']   = $this->_count_jurnal_guru_hari($id_guru);
        $data['jurnal_terbaru']      = $this->_get_jurnal_terbaru_guru($id_guru, 5);
        $data['jurnal_per_bulan']    = $this->_get_jurnal_per_bulan_guru($id_guru);

        // Absensi bulan ini
        $data['absensi_bulan_ini']   = $this->Absensi_model->get_absensi_guru_bulan($id_guru, date('m'), date('Y'));
        $data['rekap_absensi']       = $this->Absensi_model->get_rekap_absensi_guru($id_guru, date('m'), date('Y'));

        $this->load->view('guru_portal/dashboard', $data);
    }

    /**
     * Halaman jurnal guru - CRUD jurnal milik sendiri
     */
    public function jurnal()
    {
        $id_guru = $this->session->userdata('id_guru');
        $data['user']  = $this->session->userdata();
        $data['guru']  = $this->Guru_model->get_guru_by_id($id_guru);
        $data['total'] = $this->_count_jurnal_guru($id_guru);
        $this->load->view('guru_portal/jurnal', $data);
    }

    /**
     * Get data jurnal milik guru ini (untuk DataTable)
     */
    public function get_jurnal_data()
    {
        $id_guru = $this->session->userdata('id_guru');
        $jurnal  = $this->_get_all_jurnal_guru($id_guru);

        $rows = [];
        foreach ($jurnal as $j) {
            $row   = [];
            $row[] = $j->id_jurnal;
            $row[] = date('d/m/Y', strtotime($j->tanggal));
            $row[] = $j->nama_kelas;
            $row[] = $j->nama_mapel;
            $row[] = substr($j->materi, 0, 60) . (strlen($j->materi) > 60 ? '...' : '');
            $row[] = $j->jumlah_siswa ?? '-';
            $row[] = $j->foto_bukti
                ? '<img src="' . base_url('assets/uploads/foto_kegiatan/' . $j->foto_bukti) . '" class="w-12 h-12 object-cover rounded-lg cursor-pointer" onclick="viewImage(\'' . $j->foto_bukti . '\')">'
                : '<span class="text-gray-400 text-xs">Tidak ada</span>';
            $row[] = '<div class="flex gap-1">
                        <button onclick="editJurnal(' . $j->id_jurnal . ')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteJurnal(' . $j->id_jurnal . ')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs">
                            <i class="fas fa-trash"></i>
                        </button>
                      </div>';
            $rows[] = $row;
        }

        echo json_encode(['data' => $rows]);
    }

    /**
     * Simpan jurnal (tambah/edit) - hanya untuk jurnal milik guru sendiri
     */
    public function save_jurnal()
    {
        $id_guru = $this->session->userdata('id_guru');

        $this->form_validation->set_rules('tanggal',     'Tanggal',      'required|trim');
        $this->form_validation->set_rules('id_kelas',    'Kelas',        'required');
        $this->form_validation->set_rules('id_mapel',    'Mata Pelajaran','required');
        $this->form_validation->set_rules('materi',      'Materi',       'required|trim|max_length[500]');
        $this->form_validation->set_rules('jumlah_siswa','Jumlah Siswa', 'required|integer|greater_than[0]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => [
                    'tanggal'      => form_error('tanggal'),
                    'id_kelas'     => form_error('id_kelas'),
                    'id_mapel'     => form_error('id_mapel'),
                    'materi'       => form_error('materi'),
                    'jumlah_siswa' => form_error('jumlah_siswa'),
                ]
            ]);
            return;
        }

        $id_jurnal = $this->input->post('id_jurnal');

        // Jika edit, pastikan jurnal milik guru ini
        if ($id_jurnal) {
            $existing = $this->Jurnal_model->get_jurnal_by_id($id_jurnal);
            if (!$existing || (int)$existing->id_guru !== (int)$id_guru) {
                echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
                return;
            }
        }

        $data = [
            'tanggal'      => $this->input->post('tanggal'),
            'id_guru'      => $id_guru,
            'id_kelas'     => $this->input->post('id_kelas'),
            'id_mapel'     => $this->input->post('id_mapel'),
            'materi'       => $this->input->post('materi'),
            'jumlah_siswa' => $this->input->post('jumlah_siswa'),
            'keterangan'   => $this->input->post('keterangan') ?: null,
            'created_by'   => $this->session->userdata('id_user'),
        ];

        // Upload foto bukti
        if (!empty($_FILES['foto_bukti']['name'])) {
            $foto = $this->Jurnal_model->upload_foto_bukti('foto_bukti');
            if ($foto) {
                $data['foto_bukti'] = $foto;
            }
        }

        if ($id_jurnal) {
            $result  = $this->Jurnal_model->update_jurnal($id_jurnal, $data);
            $message = 'Jurnal berhasil diperbarui';
        } else {
            $result  = $this->Jurnal_model->insert_jurnal($data);
            $message = 'Jurnal berhasil ditambahkan';
        }

        $this->logActivity($id_jurnal ? 'UPDATE' : 'INSERT', 'bimbel_jurnal', $id_jurnal ?: $this->db->insert_id(), $message);

        echo json_encode([
            'status'  => $result ? 'success' : 'error',
            'message' => $result ? $message : 'Gagal menyimpan jurnal'
        ]);
    }

    /**
     * Hapus jurnal - hanya milik guru sendiri
     */
    public function delete_jurnal($id)
    {
        $id_guru  = $this->session->userdata('id_guru');
        $existing = $this->Jurnal_model->get_jurnal_by_id($id);

        if (!$existing || (int)$existing->id_guru !== (int)$id_guru) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
            return;
        }

        $result = $this->Jurnal_model->delete_jurnal($id);
        $this->logActivity('DELETE', 'bimbel_jurnal', $id, 'Hapus jurnal oleh guru');

        echo json_encode([
            'status'  => $result ? 'success' : 'error',
            'message' => $result ? 'Jurnal berhasil dihapus' : 'Gagal menghapus jurnal'
        ]);
    }

    /**
     * Get jurnal by ID (hanya milik guru sendiri)
     */
    public function get_jurnal_by_id($id)
    {
        $id_guru  = $this->session->userdata('id_guru');
        $jurnal   = $this->Jurnal_model->get_jurnal_by_id($id);

        if (!$jurnal || (int)$jurnal->id_guru !== (int)$id_guru) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
            return;
        }

        echo json_encode(['status' => 'success', 'data' => $jurnal]);
    }

    /**
     * Halaman absensi guru
     */
    public function absensi()
    {
        $id_guru = $this->session->userdata('id_guru');
        $data['user']  = $this->session->userdata();
        $data['guru']  = $this->Guru_model->get_guru_by_id($id_guru);
        $data['bulan'] = $this->input->get('bulan') ?: date('m');
        $data['tahun'] = $this->input->get('tahun') ?: date('Y');
        $data['absensi_list'] = $this->Absensi_model->get_absensi_guru_bulan($id_guru, $data['bulan'], $data['tahun']);
        $data['rekap']        = $this->Absensi_model->get_rekap_absensi_guru($id_guru, $data['bulan'], $data['tahun']);
        $this->load->view('guru_portal/absensi', $data);
    }

    /**
     * Profil guru
     */
    public function profil()
    {
        $id_guru = $this->session->userdata('id_guru');
        $data['user'] = $this->session->userdata();
        $data['guru'] = $this->Guru_model->get_guru_by_id($id_guru);
        $this->load->view('guru_portal/profil', $data);
    }

    /**
     * Ganti password guru
     */
    public function ganti_password()
    {
        $this->form_validation->set_rules('password_lama',  'Password Lama',  'required');
        $this->form_validation->set_rules('password_baru',  'Password Baru',  'required|min_length[6]');
        $this->form_validation->set_rules('konfirmasi',     'Konfirmasi',     'required|matches[password_baru]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }

        $id_user = $this->session->userdata('id_user');
        $this->load->model('Auth_model');
        $user = $this->Auth_model->get_user_by_id($id_user);

        if (!password_verify($this->input->post('password_lama'), $user->password)) {
            echo json_encode(['status' => 'error', 'message' => 'Password lama tidak sesuai']);
            return;
        }

        $this->db->where('id_user', $id_user);
        $this->db->update('bimbel_users', [
            'password' => password_hash($this->input->post('password_baru'), PASSWORD_DEFAULT)
        ]);

        $this->logActivity('UPDATE', 'bimbel_users', $id_user, 'Ganti password');

        echo json_encode(['status' => 'success', 'message' => 'Password berhasil diubah']);
    }

    // =============================================
    // PRIVATE HELPER METHODS
    // =============================================

    private function _count_jurnal_guru($id_guru)
    {
        $this->db->where('id_guru', $id_guru);
        return $this->db->count_all_results('bimbel_jurnal');
    }

    private function _count_jurnal_guru_bulan($id_guru)
    {
        $this->db->where('id_guru', $id_guru);
        $this->db->where('MONTH(tanggal)', date('m'));
        $this->db->where('YEAR(tanggal)', date('Y'));
        return $this->db->count_all_results('bimbel_jurnal');
    }

    private function _count_jurnal_guru_hari($id_guru)
    {
        $this->db->where('id_guru', $id_guru);
        $this->db->where('DATE(tanggal)', date('Y-m-d'));
        return $this->db->count_all_results('bimbel_jurnal');
    }

    private function _get_jurnal_terbaru_guru($id_guru, $limit = 5)
    {
        $this->db->select('j.*, k.nama_kelas, m.nama_mapel');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->where('j.id_guru', $id_guru);
        $this->db->order_by('j.tanggal', 'DESC');
        $this->db->order_by('j.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    private function _get_all_jurnal_guru($id_guru)
    {
        $this->db->select('j.*, k.nama_kelas, m.nama_mapel, u.nama as nama_penginput');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->join('bimbel_users u', 'j.created_by = u.id_user');
        $this->db->where('j.id_guru', $id_guru);
        $this->db->order_by('j.tanggal', 'DESC');
        $this->db->order_by('j.created_at', 'DESC');
        return $this->db->get()->result();
    }

    private function _get_jurnal_per_bulan_guru($id_guru)
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = date('m', strtotime("-$i months"));
            $tahun = date('Y', strtotime("-$i months"));
            $label = date('M Y', strtotime("-$i months"));

            $this->db->where('id_guru', $id_guru);
            $this->db->where('MONTH(tanggal)', $bulan);
            $this->db->where('YEAR(tanggal)', $tahun);
            $count = $this->db->count_all_results('bimbel_jurnal');

            $data[] = ['label' => $label, 'count' => $count];
        }
        return $data;
    }
}
