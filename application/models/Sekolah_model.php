<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sekolah_model extends CI_Model {

    /**
     * Mendapatkan semua data sekolah
     * @return array
     */
    public function get_all_sekolah()
    {
        try {
            $this->db->order_by('nama_sekolah', 'ASC');
            return $this->db->get('bimbel_sekolah')->result();
        } catch (Exception $e) {
            log_message('error', 'Error getting all sekolah: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Mendapatkan data sekolah berdasarkan ID
     * @param int $id_sekolah
     * @return object
     */
    public function get_sekolah_by_id($id_sekolah)
    {
        try {
            $this->db->where('id_sekolah', $id_sekolah);
            return $this->db->get('bimbel_sekolah')->row();
        } catch (Exception $e) {
            log_message('error', 'Error getting sekolah by ID: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Menambahkan data sekolah baru
     * @param array $data
     * @return boolean
     */
    public function insert_sekolah($data)
    {
        try {
            $this->db->insert('bimbel_sekolah', $data);
            return $this->db->affected_rows() > 0;
        } catch (Exception $e) {
            log_message('error', 'Error inserting sekolah: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengupdate data sekolah
     * @param int $id_sekolah
     * @param array $data
     * @return boolean
     */
    public function update_sekolah($id_sekolah, $data)
    {
        try {
            $this->db->where('id_sekolah', $id_sekolah);
            $this->db->update('bimbel_sekolah', $data);
            return $this->db->affected_rows() > 0;
        } catch (Exception $e) {
            log_message('error', 'Error updating sekolah: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghapus data sekolah
     * @param int $id_sekolah
     * @return boolean
     */
    public function delete_sekolah($id_sekolah)
    {
        try {
            $this->db->where('id_sekolah', $id_sekolah);
            $this->db->delete('bimbel_sekolah');
            return $this->db->affected_rows() > 0;
        } catch (Exception $e) {
            log_message('error', 'Error deleting sekolah: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mendapatkan total sekolah
     * @return int
     */
    public function count_sekolah()
    {
        try {
            return $this->db->count_all('bimbel_sekolah');
        } catch (Exception $e) {
            log_message('error', 'Error counting sekolah: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mencari sekolah berdasarkan nama atau alamat
     * @param string $keyword
     * @return array
     */
    public function search_sekolah($keyword)
    {
        try {
            $this->db->group_start();
            $this->db->like('nama_sekolah', $keyword);
            $this->db->or_like('alamat', $keyword);
            $this->db->or_like('kepala_sekolah', $keyword);
            $this->db->group_end();
            $this->db->order_by('nama_sekolah', 'ASC');
            return $this->db->get('bimbel_sekolah')->result();
        } catch (Exception $e) {
            log_message('error', 'Error searching sekolah: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Mendapatkan data sekolah pertama (untuk laporan)
     * @return object
     */
    public function get_first_sekolah()
    {
        $this->db->limit(1);
        return $this->db->get('bimbel_sekolah')->row();
    }

    /**
     * Mendapatkan data sekolah untuk PDF
     * @return array
     */
    public function get_sekolah_for_pdf()
    {
        $this->db->limit(1);
        $result = $this->db->get('bimbel_sekolah')->row();
        
        if ($result) {
            return [
                'id_sekolah' => $result->id_sekolah,
                'nama_sekolah' => $result->nama_sekolah,
                'alamat' => $result->alamat,
                'logo' => $result->logo,
                'kepala_sekolah' => $result->kepala_sekolah,
                'nip_kepsek' => $result->nip_kepsek
            ];
        }
        
        return null;
    }
}