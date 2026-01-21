<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sekolah_model extends CI_Model {

    /**
     * Mendapatkan semua data sekolah
     * @return array
     */
    public function get_all_sekolah()
    {
        $this->db->order_by('nama_sekolah', 'ASC');
        return $this->db->get('bimbel_sekolah')->result();
    }

    /**
     * Mendapatkan data sekolah berdasarkan ID
     * @param int $id_sekolah
     * @return object
     */
    public function get_sekolah_by_id($id_sekolah)
    {
        $this->db->where('id_sekolah', $id_sekolah);
        return $this->db->get('bimbel_sekolah')->row();
    }

    /**
     * Menambahkan data sekolah baru
     * @param array $data
     * @return boolean
     */
    public function insert_sekolah($data)
    {
        $this->db->insert('bimbel_sekolah', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mengupdate data sekolah
     * @param int $id_sekolah
     * @param array $data
     * @return boolean
     */
    public function update_sekolah($id_sekolah, $data)
    {
        $this->db->where('id_sekolah', $id_sekolah);
        $this->db->update('bimbel_sekolah', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Menghapus data sekolah
     * @param int $id_sekolah
     * @return boolean
     */
    public function delete_sekolah($id_sekolah)
    {
        $this->db->where('id_sekolah', $id_sekolah);
        $this->db->delete('bimbel_sekolah');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mendapatkan total sekolah
     * @return int
     */
    public function count_sekolah()
    {
        return $this->db->count_all('bimbel_sekolah');
    }

    /**
     * Mencari sekolah berdasarkan nama atau alamat
     * @param string $keyword
     * @return array
     */
    public function search_sekolah($keyword)
    {
        $this->db->group_start();
        $this->db->like('nama_sekolah', $keyword);
        $this->db->or_like('alamat', $keyword);
        $this->db->or_like('kepala_sekolah', $keyword);
        $this->db->group_end();
        $this->db->order_by('nama_sekolah', 'ASC');
        return $this->db->get('bimbel_sekolah')->result();
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