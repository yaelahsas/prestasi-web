<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Billing_model extends CI_Model {

    // ============================================
    // BILLING PERIODS FUNCTIONS
    // ============================================

    /**
     * Mendapatkan semua data periode billing
     * @return array
     */
    public function get_all_periods()
    {
        $this->db->select('p.*, u.nama as nama_creator');
        $this->db->from('bimbel_billing_periods p');
        $this->db->join('bimbel_users u', 'p.created_by = u.id_user', 'left');
        $this->db->order_by('p.tahun', 'DESC');
        $this->db->order_by('p.bulan', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan periode aktif
     * @return array
     */
    public function get_active_periods()
    {
        $this->db->where('status', 'aktif');
        $this->db->order_by('tahun', 'DESC');
        $this->db->order_by('bulan', 'DESC');
        return $this->db->get('bimbel_billing_periods')->result();
    }

    /**
     * Mendapatkan periode berdasarkan ID
     * @param int $id_period
     * @return object
     */
    public function get_period_by_id($id_period)
    {
        $this->db->where('id_period', $id_period);
        return $this->db->get('bimbel_billing_periods')->row();
    }

    /**
     * Menambahkan periode baru
     * @param array $data
     * @return boolean
     */
    public function insert_period($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('bimbel_billing_periods', $data);
        return $this->db->insert_id();
    }

    /**
     * Mengupdate periode
     * @param int $id_period
     * @param array $data
     * @return boolean
     */
    public function update_period($id_period, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id_period', $id_period);
        $this->db->update('bimbel_billing_periods', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Menghapus periode
     * @param int $id_period
     * @return boolean
     */
    public function delete_period($id_period)
    {
        $this->db->where('id_period', $id_period);
        $this->db->delete('bimbel_billing_periods');
        return $this->db->affected_rows() > 0;
    }

    // ============================================
    // BILLING TARIF FUNCTIONS
    // ============================================

    /**
     * Mendapatkan semua data tarif
     * @return array
     */
    public function get_all_tarif()
    {
        $this->db->order_by('jenis_kegiatan', 'ASC');
        return $this->db->get('bimbel_billing_tarif')->result();
    }

    /**
     * Mendapatkan tarif aktif
     * @return array
     */
    public function get_tarif_aktif()
    {
        $this->db->where('status', 'aktif');
        $this->db->order_by('jenis_kegiatan', 'ASC');
        return $this->db->get('bimbel_billing_tarif')->result();
    }

    /**
     * Mendapatkan tarif berdasarkan jenis kegiatan
     * @param string $jenis_kegiatan
     * @return object
     */
    public function get_tarif_by_jenis($jenis_kegiatan)
    {
        $this->db->where('jenis_kegiatan', $jenis_kegiatan);
        $this->db->where('status', 'aktif');
        return $this->db->get('bimbel_billing_tarif')->row();
    }

    /**
     * Mendapatkan tarif berdasarkan ID
     * @param int $id_tarif
     * @return object
     */
    public function get_tarif_by_id($id_tarif)
    {
        $this->db->where('id_tarif', $id_tarif);
        return $this->db->get('bimbel_billing_tarif')->row();
    }

    /**
     * Menambahkan tarif baru
     * @param array $data
     * @return boolean
     */
    public function insert_tarif($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('bimbel_billing_tarif', $data);
        return $this->db->insert_id();
    }

    /**
     * Mengupdate tarif
     * @param int $id_tarif
     * @param array $data
     * @return boolean
     */
    public function update_tarif($id_tarif, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id_tarif', $id_tarif);
        $this->db->update('bimbel_billing_tarif', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Menghapus tarif
     * @param int $id_tarif
     * @return boolean
     */
    public function delete_tarif($id_tarif)
    {
        $this->db->where('id_tarif', $id_tarif);
        $this->db->delete('bimbel_billing_tarif');
        return $this->db->affected_rows() > 0;
    }

    // ============================================
    // BILLING FUNCTIONS
    // ============================================

    /**
     * Mendapatkan semua data billing
     * @return array
     */
    public function get_all_billing()
    {
        return $this->db->get('v_billing_summary')->result();
    }

    /**
     * Mendapatkan billing dengan filter
     * @param int $bulan
     * @param int $tahun
     * @param string $status
     * @return array
     */
    public function get_billing_filtered($bulan = null, $tahun = null, $status = null)
    {
        $this->db->from('v_billing_summary');
        
        if ($bulan) {
            $this->db->where('bulan', $bulan);
        }
        
        if ($tahun) {
            $this->db->where('tahun', $tahun);
        }
        
        if ($status) {
            $this->db->where('status', $status);
        }
        
        $this->db->order_by('tahun', 'DESC');
        $this->db->order_by('bulan', 'DESC');
        $this->db->order_by('nama_guru', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan billing berdasarkan ID
     * @param int $id_billing
     * @return object
     */
    public function get_billing_by_id($id_billing)
    {
        $this->db->from('v_billing_summary');
        $this->db->where('id_billing', $id_billing);
        return $this->db->get()->row();
    }

    /**
     * Mendapatkan billing berdasarkan kode
     * @param string $kode_billing
     * @return object
     */
    public function get_billing_by_kode($kode_billing)
    {
        $this->db->from('v_billing_summary');
        $this->db->where('kode_billing', $kode_billing);
        return $this->db->get()->row();
    }

    /**
     * Mendapatkan billing berdasarkan periode dan guru
     * @param int $id_period
     * @param int $id_guru
     * @return object
     */
    public function get_billing_by_period_guru($id_period, $id_guru)
    {
        $this->db->where('id_period', $id_period);
        $this->db->where('id_guru', $id_guru);
        return $this->db->get('bimbel_billing')->row();
    }

    /**
     * Menambahkan billing baru
     * @param array $data
     * @return int
     */
    public function insert_billing($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('bimbel_billing', $data);
        return $this->db->insert_id();
    }

    /**
     * Mengupdate billing
     * @param int $id_billing
     * @param array $data
     * @return boolean
     */
    public function update_billing($id_billing, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id_billing', $id_billing);
        $this->db->update('bimbel_billing', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Menghapus billing
     * @param int $id_billing
     * @return boolean
     */
    public function delete_billing($id_billing)
    {
        $this->db->where('id_billing', $id_billing);
        $this->db->delete('bimbel_billing');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Generate kode billing unik
     * @param int $bulan
     * @param int $tahun
     * @return string
     */
    public function generate_kode_billing($bulan, $tahun)
    {
        $prefix = 'BLG-' . $tahun . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-';
        
        $this->db->like('kode_billing', $prefix, 'after');
        $this->db->order_by('kode_billing', 'DESC');
        $this->db->limit(1);
        $last_billing = $this->db->get('bimbel_billing')->row();
        
        if ($last_billing) {
            $last_number = (int)substr($last_billing->kode_billing, -3);
            $new_number = $last_number + 1;
        } else {
            $new_number = 1;
        }
        
        return $prefix . str_pad($new_number, 3, '0', STR_PAD_LEFT);
    }

    // ============================================
    // BILLING DETAILS FUNCTIONS
    // ============================================

    /**
     * Mendapatkan detail billing berdasarkan ID billing
     * @param int $id_billing
     * @return array
     */
    public function get_billing_details($id_billing)
    {
        $this->db->from('v_billing_detail_lengkap');
        $this->db->where('id_billing', $id_billing);
        $this->db->order_by('jenis_kegiatan', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan detail billing berdasarkan ID
     * @param int $id_detail
     * @return object
     */
    public function get_billing_detail_by_id($id_detail)
    {
        $this->db->where('id_detail', $id_detail);
        return $this->db->get('bimbel_billing_details')->row();
    }

    /**
     * Menambahkan detail billing
     * @param array $data
     * @return int
     */
    public function insert_billing_detail($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('bimbel_billing_details', $data);
        return $this->db->insert_id();
    }

    /**
     * Mengupdate detail billing
     * @param int $id_detail
     * @param array $data
     * @return boolean
     */
    public function update_billing_detail($id_detail, $data)
    {
        $this->db->where('id_detail', $id_detail);
        $this->db->update('bimbel_billing_details', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Menghapus detail billing berdasarkan ID billing
     * @param int $id_billing
     * @return boolean
     */
    public function delete_billing_details($id_billing)
    {
        $this->db->where('id_billing', $id_billing);
        $this->db->delete('bimbel_billing_details');
        return $this->db->affected_rows() > 0;
    }

    // ============================================
    // BILLING JURNAL FUNCTIONS
    // ============================================

    /**
     * Mendapatkan jurnal yang sudah dihitung dalam billing
     * @param int $id_billing
     * @return array
     */
    public function get_billing_jurnal($id_billing)
    {
        $this->db->select('bj.*, j.tanggal, j.materi, k.nama_kelas, m.nama_mapel');
        $this->db->from('bimbel_billing_jurnal bj');
        $this->db->join('bimbel_jurnal j', 'bj.id_jurnal = j.id_jurnal');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->where('bj.id_billing', $id_billing);
        $this->db->order_by('j.tanggal', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Menambahkan relasi billing jurnal
     * @param array $data
     * @return int
     */
    public function insert_billing_jurnal($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('bimbel_billing_jurnal', $data);
        return $this->db->insert_id();
    }

    /**
     * Menghapus relasi billing jurnal berdasarkan ID billing
     * @param int $id_billing
     * @return boolean
     */
    public function delete_billing_jurnal($id_billing)
    {
        $this->db->where('id_billing', $id_billing);
        $this->db->delete('bimbel_billing_jurnal');
        return $this->db->affected_rows() > 0;
    }

    // ============================================
    // GENERATE BILLING FUNCTIONS
    // ============================================

    /**
     * Menghitung jurnal per guru per periode
     * @param int $id_guru
     * @param string $tanggal_awal
     * @param string $tanggal_akhir
     * @return array
     */
    public function hitung_jurnal_guru($id_guru, $tanggal_awal, $tanggal_akhir)
    {
        $this->db->select('j.id_jurnal, j.tanggal, j.materi, j.id_kelas, j.id_mapel, j.is_daring, k.nama_kelas, k.tingkat, m.nama_mapel');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->where('j.id_guru', $id_guru);
        $this->db->where('j.tanggal >=', $tanggal_awal);
        $this->db->where('j.tanggal <=', $tanggal_akhir);
        $this->db->order_by('j.tanggal', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Tentukan jenis kegiatan berdasarkan jurnal
     * @param object $jurnal
     * @return string
     */
    private function tentukan_jenis_kegiatan($jurnal)
    {
        // Cek olimpiade
        if ($jurnal->tingkat == 'olim') {
            return 'olimpiade';
        }
        
        // Cek daring/luring
        if ($jurnal->is_daring == 1) {
            return 'daring';
        } else {
            return 'luring';
        }
    }

    /**
     * Generate billing untuk semua guru dalam periode
     * @param int $id_period
     * @return boolean
     */
    public function generate_billing_all($id_period)
    {
        // Get periode
        $period = $this->get_period_by_id($id_period);
        if (!$period) {
            return false;
        }

        // Cek apakah periode aktif
        if ($period->status != 'aktif') {
            return false;
        }

        // Get semua guru aktif
        $this->db->select('id_guru');
        $this->db->where('status', 'aktif');
        $guru_list = $this->db->get('bimbel_guru')->result();
        
        if (empty($guru_list)) {
            return false;
        }
        
        $success_count = 0;
        
        // Loop setiap guru dan generate billing
        foreach ($guru_list as $guru) {
            $result = $this->generate_billing_guru($id_period, $guru->id_guru);
            if ($result) {
                $success_count++;
            }
        }
        
        return $success_count > 0;
    }

    /**
     * Generate billing untuk guru tertentu dalam periode
     * @param int $id_period
     * @param int $id_guru
     * @return boolean
     */
    public function generate_billing_guru($id_period, $id_guru)
    {
        // Get periode
        $period = $this->get_period_by_id($id_period);
        if (!$period) {
            return false;
        }

        // Cek apakah billing sudah ada
        $existing_billing = $this->get_billing_by_period_guru($id_period, $id_guru);
        if ($existing_billing) {
            return false; // Billing sudah ada
        }
        
        // Hitung jurnal guru
        $jurnal_list = $this->hitung_jurnal_guru($id_guru, $period->tanggal_mulai, $period->tanggal_selesai);
        
        if (empty($jurnal_list)) {
            return false; // Tidak ada jurnal
        }
        
        // Generate kode billing
        $kode_billing = $this->generate_kode_billing($period->bulan, $period->tahun);
        
        // Insert billing header
        $billing_data = array(
            'id_period' => $id_period,
            'id_guru' => $id_guru,
            'kode_billing' => $kode_billing,
            'total_jurnal' => count($jurnal_list),
            'total_honor' => 0,
            'status' => 'draft',
            'created_by' => $this->session->userdata('id_user')
        );
        
        $id_billing = $this->insert_billing($billing_data);
        
        if (!$id_billing) {
            return false;
        }
        
        // Group jurnal berdasarkan jenis kegiatan
        $jurnal_by_jenis = array(
            'reguler' => array(),
            'olimpiade' => array(),
            'luring' => array(),
            'daring' => array()
        );
        
        foreach ($jurnal_list as $jurnal) {
            $jenis_kegiatan = $this->tentukan_jenis_kegiatan($jurnal);
            $jurnal_by_jenis[$jenis_kegiatan][] = $jurnal;
        }
        
        $total_honor = 0;
        
        // Insert detail billing untuk setiap jenis kegiatan
        foreach ($jurnal_by_jenis as $jenis_kegiatan => $jurnals) {
            if (empty($jurnals)) {
                continue;
            }
            
            // Get tarif untuk jenis kegiatan ini
            $tarif = $this->get_tarif_by_jenis($jenis_kegiatan);
            $tarif_per_jurnal = $tarif ? $tarif->tarif : 0;
            
            $jumlah_jurnal = count($jurnals);
            $subtotal_honor = $jumlah_jurnal * $tarif_per_jurnal;
            
            // Insert detail billing
            $detail_data = array(
                'id_billing' => $id_billing,
                'jenis_kegiatan' => $jenis_kegiatan,
                'jumlah_jurnal' => $jumlah_jurnal,
                'tarif_per_jurnal' => $tarif_per_jurnal,
                'subtotal_honor' => $subtotal_honor
            );
            
            $this->insert_billing_detail($detail_data);
            
            // Insert relasi billing jurnal
            foreach ($jurnals as $jurnal) {
                $billing_jurnal_data = array(
                    'id_billing' => $id_billing,
                    'id_jurnal' => $jurnal->id_jurnal,
                    'jenis_kegiatan' => $jenis_kegiatan
                );
                $this->insert_billing_jurnal($billing_jurnal_data);
            }
            
            $total_honor += $subtotal_honor;
        }
        
        // Update total honor
        $update_data = array(
            'total_honor' => $total_honor
        );
        
        return $this->update_billing($id_billing, $update_data);
    }

    // ============================================
    // HELPER FUNCTIONS
    // ============================================

    /**
     * Mendapatkan data guru untuk dropdown
     * @return array
     */
    public function get_guru()
    {
        $this->db->select('id_guru, nama_guru');
        $this->db->from('bimbel_guru');
        $this->db->where('status', 'aktif');
        $this->db->order_by('nama_guru', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan data jenis kegiatan untuk dropdown
     * @return array
     */
    public function get_jenis_kegiatan()
    {
        return [
            ['value' => 'reguler', 'label' => 'Reguler'],
            ['value' => 'olimpiade', 'label' => 'Olimpiade'],
            ['value' => 'luring', 'label' => 'Luring'],
            ['value' => 'daring', 'label' => 'Daring']
        ];
    }

    /**
     * Mendapatkan nama bulan
     * @param int $bulan
     * @return string
     */
    public function get_nama_bulan($bulan)
    {
        $nama_bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        
        return isset($nama_bulan[$bulan]) ? $nama_bulan[$bulan] : '';
    }
}
