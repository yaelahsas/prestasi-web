<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kalender extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->requireLogin();
        $this->load->model('Jurnal_model');
    }

    /**
     * Halaman kalender jurnal
     */
    public function index()
    {
        $data['user'] = $this->session->userdata();
        $this->load->view('kalender/index', $data);
    }

    /**
     * Get events untuk FullCalendar (JSON)
     */
    public function get_events()
    {
        $start = $this->input->get('start');
        $end   = $this->input->get('end');

        // Filter berdasarkan role
        $id_guru = null;
        if ($this->isGuru()) {
            $id_guru = $this->session->userdata('id_guru');
        }

        $this->db->select('
            j.id_jurnal, j.tanggal, j.materi, j.jumlah_siswa,
            g.nama_guru, k.nama_kelas, m.nama_mapel
        ');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');

        if ($start) $this->db->where('j.tanggal >=', $start);
        if ($end)   $this->db->where('j.tanggal <=', $end);
        if ($id_guru) $this->db->where('j.id_guru', $id_guru);

        $jurnal = $this->db->get()->result();

        // Kelompokkan per tanggal untuk menghitung jumlah
        $events_by_date = [];
        foreach ($jurnal as $j) {
            $date = $j->tanggal;
            if (!isset($events_by_date[$date])) {
                $events_by_date[$date] = [
                    'count'   => 0,
                    'details' => []
                ];
            }
            $events_by_date[$date]['count']++;
            $events_by_date[$date]['details'][] = $j->nama_guru . ' - ' . $j->nama_kelas . ' (' . $j->nama_mapel . ')';
        }

        // Format untuk FullCalendar
        $events = [];
        foreach ($events_by_date as $date => $info) {
            $count = $info['count'];
            $color = $count >= 5 ? '#16a34a' : ($count >= 3 ? '#22c55e' : '#86efac');
            $events[] = [
                'id'              => $date,
                'title'           => $count . ' Jurnal',
                'start'           => $date,
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#fff',
                'extendedProps'   => [
                    'count'   => $count,
                    'details' => implode("\n", array_slice($info['details'], 0, 5))
                ]
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($events);
    }

    /**
     * Get detail jurnal untuk tanggal tertentu
     */
    public function get_detail_by_date()
    {
        $tanggal = $this->input->get('tanggal');
        if (!$tanggal) {
            echo json_encode(['status' => 'error', 'message' => 'Tanggal diperlukan']);
            return;
        }

        $id_guru = $this->isGuru() ? $this->session->userdata('id_guru') : null;

        $this->db->select('j.*, g.nama_guru, k.nama_kelas, m.nama_mapel');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->where('DATE(j.tanggal)', $tanggal);
        if ($id_guru) $this->db->where('j.id_guru', $id_guru);
        $this->db->order_by('j.created_at', 'ASC');

        $jurnal = $this->db->get()->result();

        echo json_encode(['status' => 'success', 'data' => $jurnal, 'tanggal' => $tanggal]);
    }
}
