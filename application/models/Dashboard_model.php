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
        $bulan_ini = date('Y-m');
        return $this->db
            ->like('tanggal', $bulan_ini, 'after')
            ->count_all_results('bimbel_jurnal');
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
     * Mendapatkan total jurnal
     * @return int
     */
    public function get_total_jurnal()
    {
        return $this->db->count_all_results('bimbel_jurnal');
    }

    /**
     * Mendapatkan data ringkasan dashboard
     * @return array
     */
    public function get_ringkasan_dashboard()
    {
        return [
            'total_guru' => $this->get_total_guru(),
            'total_kelas' => $this->get_total_kelas(),
            'total_jurnal_bulan_ini' => $this->get_total_jurnal_bulan_ini(),
            'total_jurnal_hari_ini' => $this->get_total_jurnal_hari_ini()
        ];
    }
}