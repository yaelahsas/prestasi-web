<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guru_model extends CI_Model {

    /**
     * Mendapatkan semua data guru dengan relasi ke kelas dan mapel
     * @return array
     */
    public function get_all_guru()
    {
        $this->db->select('g.*, k.nama_kelas, m.nama_mapel');
        $this->db->from('bimbel_guru g');
        $this->db->join('bimbel_kelas k', 'g.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'g.id_mapel = m.id_mapel');
        $this->db->order_by('g.nama_guru', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan data guru berdasarkan ID
     * @param int $id_guru
     * @return object
     */
    public function get_guru_by_id($id_guru)
    {
        $this->db->select('g.*, k.nama_kelas, m.nama_mapel');
        $this->db->from('bimbel_guru g');
        $this->db->join('bimbel_kelas k', 'g.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'g.id_mapel = m.id_mapel');
        $this->db->where('g.id_guru', $id_guru);
        return $this->db->get()->row();
    }

    /**
     * Mendapatkan data kelas untuk dropdown
     * @return array
     */
    public function get_kelas()
    {
        $this->db->select('id_kelas, nama_kelas');
        $this->db->from('bimbel_kelas');
        $this->db->where('status', 'aktif');
        $this->db->order_by('nama_kelas', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan data mapel untuk dropdown
     * @return array
     */
    public function get_mapel()
    {
        $this->db->select('id_mapel, nama_mapel');
        $this->db->from('bimbel_mapel');
        $this->db->where('status', 'aktif');
        $this->db->order_by('nama_mapel', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Menambahkan data guru baru
     * @param array $data
     * @return boolean
     */
    public function insert_guru($data)
    {
        $this->db->insert('bimbel_guru', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mengupdate data guru
     * @param int $id_guru
     * @param array $data
     * @return boolean
     */
    public function update_guru($id_guru, $data)
    {
        $this->db->where('id_guru', $id_guru);
        $this->db->update('bimbel_guru', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Menghapus data guru
     * @param int $id_guru
     * @return boolean
     */
    public function delete_guru($id_guru)
    {
        // Cek apakah guru memiliki jurnal terkait
        $this->db->where('id_guru', $id_guru);
        $jurnal_count = $this->db->count_all_results('bimbel_jurnal');
        
        if ($jurnal_count > 0) {
            // Jika ada jurnal terkait, jangan hapus
            return false;
        }
        
        $this->db->where('id_guru', $id_guru);
        $this->db->delete('bimbel_guru');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mengubah status guru (aktif/nonaktif)
     * @param int $id_guru
     * @param string $status
     * @return boolean
     */
    public function toggle_status($id_guru, $status)
    {
        $this->db->where('id_guru', $id_guru);
        $this->db->update('bimbel_guru', ['status' => $status]);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mendapatkan total guru aktif
     * @return int
     */
    public function get_total_guru_aktif()
    {
        $this->db->where('status', 'aktif');
        return $this->db->count_all_results('bimbel_guru');
    }

    /**
     * Mendapatkan total guru nonaktif
     * @return int
     */
    public function get_total_guru_nonaktif()
    {
        $this->db->where('status', 'nonaktif');
        return $this->db->count_all_results('bimbel_guru');
    }

    /**
     * Mencari guru berdasarkan nama atau NIP
     * @param string $keyword
     * @return array
     */
    public function search_guru($keyword)
    {
        $this->db->select('g.*, k.nama_kelas, m.nama_mapel');
        $this->db->from('bimbel_guru g');
        $this->db->join('bimbel_kelas k', 'g.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'g.id_mapel = m.id_mapel');
        $this->db->group_start();
        $this->db->like('g.nama_guru', $keyword);
        $this->db->or_like('g.nip', $keyword);
        $this->db->group_end();
        $this->db->order_by('g.nama_guru', 'ASC');
        return $this->db->get()->result();
    }
}