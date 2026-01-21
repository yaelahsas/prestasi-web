<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas_model extends CI_Model {

    /**
     * Mendapatkan semua data kelas
     * @return array
     */
    public function get_all_kelas()
    {
        $this->db->from('bimbel_kelas');
        $this->db->order_by('tingkat', 'ASC');
        $this->db->order_by('nama_kelas', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan data kelas berdasarkan ID
     * @param int $id_kelas
     * @return object
     */
    public function get_kelas_by_id($id_kelas)
    {
        $this->db->from('bimbel_kelas');
        $this->db->where('id_kelas', $id_kelas);
        return $this->db->get()->row();
    }

    /**
     * Menambahkan data kelas baru
     * @param array $data
     * @return boolean
     */
    public function insert_kelas($data)
    {
        $this->db->insert('bimbel_kelas', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mengupdate data kelas
     * @param int $id_kelas
     * @param array $data
     * @return boolean
     */
    public function update_kelas($id_kelas, $data)
    {
        $this->db->where('id_kelas', $id_kelas);
        $this->db->update('bimbel_kelas', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Menghapus data kelas
     * @param int $id_kelas
     * @return boolean
     */
    public function delete_kelas($id_kelas)
    {
        // Cek apakah kelas memiliki guru terkait
        $this->db->where('id_kelas', $id_kelas);
        $guru_count = $this->db->count_all_results('bimbel_guru');
        
        if ($guru_count > 0) {
            // Jika ada guru terkait, jangan hapus
            return false;
        }
        
        // Cek apakah kelas memiliki jurnal terkait
        $this->db->where('id_kelas', $id_kelas);
        $jurnal_count = $this->db->count_all_results('bimbel_jurnal');
        
        if ($jurnal_count > 0) {
            // Jika ada jurnal terkait, jangan hapus
            return false;
        }
        
        $this->db->where('id_kelas', $id_kelas);
        $this->db->delete('bimbel_kelas');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mengubah status kelas (aktif/nonaktif)
     * @param int $id_kelas
     * @param string $status
     * @return boolean
     */
    public function toggle_status($id_kelas, $status)
    {
        $this->db->where('id_kelas', $id_kelas);
        $this->db->update('bimbel_kelas', ['status' => $status]);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mendapatkan total kelas aktif
     * @return int
     */
    public function get_total_kelas_aktif()
    {
        $this->db->where('status', 'aktif');
        return $this->db->count_all_results('bimbel_kelas');
    }

    /**
     * Mendapatkan total kelas nonaktif
     * @return int
     */
    public function get_total_kelas_nonaktif()
    {
        $this->db->where('status', 'nonaktif');
        return $this->db->count_all_results('bimbel_kelas');
    }

    /**
     * Mencari kelas berdasarkan nama atau tingkat
     * @param string $keyword
     * @return array
     */
    public function search_kelas($keyword)
    {
        $this->db->from('bimbel_kelas');
        $this->db->group_start();
        $this->db->like('nama_kelas', $keyword);
        $this->db->or_like('tingkat', $keyword);
        $this->db->group_end();
        $this->db->order_by('tingkat', 'ASC');
        $this->db->order_by('nama_kelas', 'ASC');
        return $this->db->get()->result();
    }
}