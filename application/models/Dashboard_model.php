<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

    /**
     * Mendapatkan total jumlah guru aktif
     * @return int
     */
    public function get_total_guru()
    {
        return $this->db
            ->where('status', 'aktif')
            ->count_all_results('bimbel_guru');
    }

    /**
     * Mendapatkan total jumlah kelas aktif
     * @return int
     */
    public function get_total_kelas()
    {
        return $this->db
            ->where('status', 'aktif')
            ->count_all_results('bimbel_kelas');
    }

    /**
     * Mendapatkan total jurnal bulan ini
     * @return int
     */
    public function get_total_jurnal_bulan_ini()
    {
        $this->db->where('MONTH(tanggal)', date('m'));
        $this->db->where('YEAR(tanggal)', date('Y'));
        return $this->db->count_all_results('bimbel_jurnal');
    }

    /**
     * Mendapatkan total jurnal hari ini
     * @return int
     */
    public function get_total_jurnal_hari_ini()
    {
        $hari_ini = date('Y-m-d');
        return $this->db
            ->where('tanggal', $hari_ini)
            ->count_all_results('bimbel_jurnal');
    }

    /**
     * Mendapatkan total jumlah mapel aktif
     * @return int
     */
    public function get_total_mapel()
    {
        return $this->db
            ->where('status', 'aktif')
            ->count_all_results('bimbel_mapel');
    }

    /**
     * Mendapatkan total jumlah users aktif
     * @return int
     */
    public function get_total_users()
    {
        return $this->db
            ->where('status', 'aktif')
            ->count_all_results('bimbel_users');
    }

    /**
     * Mendapatkan total sekolah
     * @return int
     */
    public function get_total_sekolah()
    {
        return $this->db->count_all('bimbel_sekolah');
    }

    /**
     * Mendapatkan total jurnal keseluruhan
     * @return int
     */
    public function get_total_jurnal()
    {
        return $this->db->count_all_results('bimbel_jurnal');
    }

    /**
     * Mendapatkan total siswa yang dibimbing bulan ini
     * @return int
     */
    public function get_total_siswa_bulan_ini()
    {
        $this->db->select_sum('jumlah_siswa');
        $this->db->where('MONTH(tanggal)', date('m'));
        $this->db->where('YEAR(tanggal)', date('Y'));
        $result = $this->db->get('bimbel_jurnal')->row();
        return $result ? (int)$result->jumlah_siswa : 0;
    }

    /**
     * Mendapatkan 5 jurnal terbaru
     * @return array
     */
    public function get_jurnal_terbaru($limit = 5)
    {
        $this->db->select('j.id_jurnal, j.tanggal, j.materi, j.jumlah_siswa, g.nama_guru, k.nama_kelas, m.nama_mapel');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->order_by('j.tanggal', 'DESC');
        $this->db->order_by('j.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan data jurnal per bulan untuk chart (12 bulan terakhir)
     * @return array
     */
    public function get_jurnal_per_bulan()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan = date('m', strtotime("-$i months"));
            $tahun = date('Y', strtotime("-$i months"));
            $label = date('M Y', strtotime("-$i months"));

            $this->db->where('MONTH(tanggal)', $bulan);
            $this->db->where('YEAR(tanggal)', $tahun);
            $count = $this->db->count_all_results('bimbel_jurnal');

            $data[] = [
                'label' => $label,
                'count' => $count
            ];
        }
        return $data;
    }

    /**
     * Mendapatkan distribusi jurnal per mapel (top 5)
     * @return array
     */
    public function get_jurnal_per_mapel()
    {
        $this->db->select('m.nama_mapel, COUNT(j.id_jurnal) as total');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->group_by('j.id_mapel');
        $this->db->order_by('total', 'DESC');
        $this->db->limit(5);
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan distribusi jurnal per kelas (top 5)
     * @return array
     */
    public function get_jurnal_per_kelas()
    {
        $this->db->select('k.nama_kelas, COUNT(j.id_jurnal) as total');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->group_by('j.id_kelas');
        $this->db->order_by('total', 'DESC');
        $this->db->limit(5);
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan guru paling aktif (berdasarkan jumlah jurnal bulan ini)
     * @return array
     */
    public function get_guru_teraktif($limit = 5)
    {
        $this->db->select('g.nama_guru, COUNT(j.id_jurnal) as total_jurnal, SUM(j.jumlah_siswa) as total_siswa');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->where('MONTH(j.tanggal)', date('m'));
        $this->db->where('YEAR(j.tanggal)', date('Y'));
        $this->db->group_by('j.id_guru');
        $this->db->order_by('total_jurnal', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan data ringkasan dashboard
     * @return array
     */
    public function get_ringkasan_dashboard()
    {
        return [
            'total_guru'             => $this->get_total_guru(),
            'total_kelas'            => $this->get_total_kelas(),
            'total_mapel'            => $this->get_total_mapel(),
            'total_users'            => $this->get_total_users(),
            'total_jurnal'           => $this->get_total_jurnal(),
            'total_jurnal_bulan_ini' => $this->get_total_jurnal_bulan_ini(),
            'total_jurnal_hari_ini'  => $this->get_total_jurnal_hari_ini(),
            'total_siswa_bulan_ini'  => $this->get_total_siswa_bulan_ini(),
        ];
    }

    /**
     * Mendapatkan semua data untuk dashboard (ringkasan + chart + tabel)
     * @return array
     */
    public function get_dashboard_data()
    {
        return [
            'ringkasan'       => $this->get_ringkasan_dashboard(),
            'jurnal_terbaru'  => $this->get_jurnal_terbaru(5),
            'jurnal_per_bulan'=> $this->get_jurnal_per_bulan(),
            'jurnal_per_mapel'=> $this->get_jurnal_per_mapel(),
            'jurnal_per_kelas'=> $this->get_jurnal_per_kelas(),
            'guru_teraktif'   => $this->get_guru_teraktif(5),
        ];
    }

    /**
     * Mendapatkan data sekolah
     * @return object
     */
    public function get_sekolah()
    {
        return $this->db->get('bimbel_sekolah')->row();
    }
}
