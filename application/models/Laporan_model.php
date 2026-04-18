<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // Get jurnal by bulan dan tahun
    public function get_jurnal_by_bulan_tahun($bulan, $tahun)
    {
        $this->db->select('j.*, g.nama_guru, k.nama_kelas, m.nama_mapel, u.nama as nama_penginput');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->join('bimbel_users u', 'j.created_by = u.id_user');
        $this->db->where('MONTH(j.tanggal)', $bulan);
        $this->db->where('YEAR(j.tanggal)', $tahun);
        $this->db->order_by('j.tanggal', 'ASC');
        
        return $this->db->get()->result();
    }

    // Get jurnal by guru dan periode
    public function get_jurnal_by_guru($id_guru, $bulan = null, $tahun = null)
    {
        $this->db->select('j.*, g.nama_guru, k.nama_kelas, m.nama_mapel, u.nama as nama_penginput');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->join('bimbel_users u', 'j.created_by = u.id_user');
        $this->db->where('j.id_guru', $id_guru);
        
        if ($bulan) {
            $this->db->where('MONTH(j.tanggal)', $bulan);
        }
        if ($tahun) {
            $this->db->where('YEAR(j.tanggal)', $tahun);
        }
        
        $this->db->order_by('j.tanggal', 'ASC');
        
        return $this->db->get()->result();
    }

    // Get jurnal by kelas dan periode
    public function get_jurnal_by_kelas($id_kelas, $bulan = null, $tahun = null)
    {
        $this->db->select('j.*, g.nama_guru, m.nama_mapel, u.nama as nama_penginput');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->join('bimbel_users u', 'j.created_by = u.id_user');
        $this->db->where('j.id_kelas', $id_kelas);
        
        if ($bulan) {
            $this->db->where('MONTH(j.tanggal)', $bulan);
        }
        if ($tahun) {
            $this->db->where('YEAR(j.tanggal)', $tahun);
        }
        
        $this->db->order_by('j.tanggal', 'ASC');
        
        return $this->db->get()->result();
    }

    // Get jurnal by mapel dan periode
    public function get_jurnal_by_mapel($id_mapel, $bulan = null, $tahun = null)
    {
        $this->db->select('j.*, g.nama_guru, k.nama_kelas, u.nama as nama_penginput');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_users u', 'j.created_by = u.id_user');
        $this->db->where('j.id_mapel', $id_mapel);
        
        if ($bulan) {
            $this->db->where('MONTH(j.tanggal)', $bulan);
        }
        if ($tahun) {
            $this->db->where('YEAR(j.tanggal)', $tahun);
        }
        
        $this->db->order_by('j.tanggal', 'ASC');
        
        return $this->db->get()->result();
    }

    // Get rekap kegiatan per mapel per bulan
    public function get_rekap_kegiatan_mapel($bulan, $tahun)
    {
        $this->db->select('m.id_mapel, m.nama_mapel, COUNT(j.id_jurnal) as total_jurnal, SUM(j.jumlah_siswa) as total_siswa');
        $this->db->from('bimbel_mapel m');
        $this->db->join('bimbel_jurnal j', 'm.id_mapel = j.id_mapel AND MONTH(j.tanggal) = ' . $bulan . ' AND YEAR(j.tanggal) = ' . $tahun, 'left');
        $this->db->where('m.status', 'aktif');
        $this->db->group_by('m.id_mapel, m.nama_mapel');
        $this->db->order_by('m.nama_mapel', 'ASC');
        
        return $this->db->get()->result();
    }

    // Get rekap kehadiran guru per bulan
    public function get_rekap_kehadiran_guru($bulan, $tahun)
    {
        $this->db->select('g.id_guru, g.nama_guru, g.nip, COUNT(j.id_jurnal) as total_jurnal, SUM(j.jumlah_siswa) as total_siswa');
        $this->db->from('bimbel_guru g');
        $this->db->join('bimbel_jurnal j', 'g.id_guru = j.id_guru AND MONTH(j.tanggal) = ' . $bulan . ' AND YEAR(j.tanggal) = ' . $tahun, 'left');
        $this->db->where('g.status', 'aktif');
        $this->db->group_by('g.id_guru, g.nama_guru, g.nip');
        $this->db->order_by('g.nama_guru', 'ASC');
        
        return $this->db->get()->result();
    }

    // Get rekap kegiatan per kelas per bulan
    public function get_rekap_kegiatan_kelas($bulan, $tahun)
    {
        $this->db->select('k.id_kelas, k.nama_kelas, k.tingkat, COUNT(j.id_jurnal) as total_jurnal, SUM(j.jumlah_siswa) as total_siswa');
        $this->db->from('bimbel_kelas k');
        $this->db->join('bimbel_jurnal j', 'k.id_kelas = j.id_kelas AND MONTH(j.tanggal) = ' . $bulan . ' AND YEAR(j.tanggal) = ' . $tahun, 'left');
        $this->db->where('k.status', 'aktif');
        $this->db->group_by('k.id_kelas, k.nama_kelas, k.tingkat');
        $this->db->order_by('k.tingkat, k.nama_kelas', 'ASC');
        
        return $this->db->get()->result();
    }

    // Get statistik jurnal per bulan
    public function get_statistik_jurnal($bulan, $tahun)
    {
        $this->db->select('COUNT(*) as total_jurnal, SUM(jumlah_siswa) as total_siswa, COUNT(DISTINCT id_guru) as total_guru, COUNT(DISTINCT id_kelas) as total_kelas, COUNT(DISTINCT id_mapel) as total_mapel');
        $this->db->from('bimbel_jurnal');
        $this->db->where('MONTH(tanggal)', $bulan);
        $this->db->where('YEAR(tanggal)', $tahun);
        
        return $this->db->get()->row();
    }

    // Get data guru untuk filter
    public function get_all_guru()
    {
        $this->db->select('id_guru, nama_guru, nip');
        $this->db->from('bimbel_guru');
        $this->db->where('status', 'aktif');
        $this->db->order_by('nama_guru', 'ASC');
        
        return $this->db->get()->result();
    }

    // Get data kelas untuk filter
    public function get_all_kelas()
    {
        $this->db->select('id_kelas, nama_kelas, tingkat');
        $this->db->from('bimbel_kelas');
        $this->db->where('status', 'aktif');
        $this->db->order_by('tingkat, nama_kelas', 'ASC');
        
        return $this->db->get()->result();
    }

    // Get data mapel untuk filter
    public function get_all_mapel()
    {
        $this->db->select('id_mapel, nama_mapel');
        $this->db->from('bimbel_mapel');
        $this->db->where('status', 'aktif');
        $this->db->order_by('nama_mapel', 'ASC');
        
        return $this->db->get()->result();
    }

    // Get billing data untuk laporan bulanan (semua guru aktif)
    public function get_billing_bulanan($bulan, $tahun)
    {
        // Get semua guru aktif
        $this->db->select('g.id_guru, g.nama_guru, g.nip, g.status');
        $this->db->from('bimbel_guru g');
        $this->db->where('g.status', 'aktif');
        $this->db->order_by('g.nama_guru', 'ASC');
        $guru_list = $this->db->get()->result();
        
        // Get billing data untuk periode ini
        $this->db->from('v_billing_summary');
        $this->db->where('bulan', $bulan);
        $this->db->where('tahun', $tahun);
        $billing_data = $this->db->get()->result();
        
        // Create array indexed by id_guru untuk lookup cepat
        $billing_by_guru = [];
        foreach ($billing_data as $billing) {
            $billing_by_guru[$billing->id_guru] = $billing;
        }
        
        // Merge data: semua guru aktif dengan billing data (jika ada)
        $result = [];
        foreach ($guru_list as $guru) {
            if (isset($billing_by_guru[$guru->id_guru])) {
                // Guru punya billing data
                $result[] = $billing_by_guru[$guru->id_guru];
            } else {
                // Guru tidak punya billing data (tidak ada jurnal)
                $result[] = (object)[
                    'id_billing' => null,
                    'id_guru' => $guru->id_guru,
                    'nama_guru' => $guru->nama_guru,
                    'nip' => $guru->nip,
                    'total_jurnal' => 0,
                    'total_honor' => 0,
                    'status' => null
                ];
            }
        }
        
        // Sort berdasarkan total_honor descending (terbesar ke terkecil)
        usort($result, function($a, $b) {
            return $b->total_honor - $a->total_honor;
        });
        
        return $result;
    }

    // Get billing detail untuk laporan bulanan
    public function get_billing_detail_bulanan($id_billing)
    {
        if (!$id_billing) {
            return [];
        }
        
        $this->db->from('v_billing_detail_lengkap');
        $this->db->where('id_billing', $id_billing);
        $this->db->order_by('jenis_kegiatan', 'ASC');
        
        return $this->db->get()->result();
    }
}