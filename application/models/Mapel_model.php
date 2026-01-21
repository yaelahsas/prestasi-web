<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mapel_model extends CI_Model {

    /**
     * Mendapatkan semua data mapel
     * @return array
     */
    public function get_all_mapel()
    {
        $this->db->order_by('nama_mapel', 'ASC');
        return $this->db->get('bimbel_mapel')->result();
    }

    /**
     * Mendapatkan data mapel berdasarkan ID
     * @param int $id_mapel
     * @return object
     */
    public function get_mapel_by_id($id_mapel)
    {
        $this->db->where('id_mapel', $id_mapel);
        return $this->db->get('bimbel_mapel')->row();
    }

    /**
     * Menambahkan data mapel baru
     * @param array $data
     * @return boolean
     */
    public function insert_mapel($data)
    {
        $this->db->insert('bimbel_mapel', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mengupdate data mapel
     * @param int $id_mapel
     * @param array $data
     * @return boolean
     */
    public function update_mapel($id_mapel, $data)
    {
        $this->db->where('id_mapel', $id_mapel);
        $this->db->update('bimbel_mapel', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Menghapus data mapel
     * @param int $id_mapel
     * @return boolean
     */
    public function delete_mapel($id_mapel)
    {
        // Cek apakah mapel memiliki guru terkait
        $this->db->where('id_mapel', $id_mapel);
        $guru_count = $this->db->count_all_results('bimbel_guru');
        
        if ($guru_count > 0) {
            // Jika ada guru terkait, jangan hapus
            return false;
        }
        
        // Cek apakah mapel memiliki jurnal terkait
        $this->db->where('id_mapel', $id_mapel);
        $jurnal_count = $this->db->count_all_results('bimbel_jurnal');
        
        if ($jurnal_count > 0) {
            // Jika ada jurnal terkait, jangan hapus
            return false;
        }
        
        $this->db->where('id_mapel', $id_mapel);
        $this->db->delete('bimbel_mapel');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mengubah status mapel (aktif/nonaktif)
     * @param int $id_mapel
     * @param string $status
     * @return boolean
     */
    public function toggle_status($id_mapel, $status)
    {
        $this->db->where('id_mapel', $id_mapel);
        $this->db->update('bimbel_mapel', ['status' => $status]);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mendapatkan total mapel aktif
     * @return int
     */
    public function get_total_mapel_aktif()
    {
        $this->db->where('status', 'aktif');
        return $this->db->count_all_results('bimbel_mapel');
    }

    /**
     * Mendapatkan total mapel nonaktif
     * @return int
     */
    public function get_total_mapel_nonaktif()
    {
        $this->db->where('status', 'nonaktif');
        return $this->db->count_all_results('bimbel_mapel');
    }

    /**
     * Mencari mapel berdasarkan nama
     * @param string $keyword
     * @return array
     */
    public function search_mapel($keyword)
    {
        $this->db->like('nama_mapel', $keyword);
        $this->db->order_by('nama_mapel', 'ASC');
        return $this->db->get('bimbel_mapel')->result();
    }
}