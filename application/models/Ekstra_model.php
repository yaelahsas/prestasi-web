<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ekstra_model extends CI_Model {

    /**
     * Mendapatkan semua data ekstra dengan daftar guru
     * @return array
     */
    public function get_all_ekstra()
    {
        $this->db->select('e.*, 
            GROUP_CONCAT(DISTINCT CONCAT(g.nama_guru, " (", g.nip, ")") ORDER BY g.nama_guru ASC SEPARATOR ", ") as daftar_guru,
            COUNT(DISTINCT eg.id_guru) as jumlah_guru');
        $this->db->from('bimbel_ekstra e');
        $this->db->join('bimbel_ekstra_guru eg', 'e.id_ekstra = eg.id_ekstra', 'left');
        $this->db->join('bimbel_guru g', 'eg.id_guru = g.id_guru AND g.status = "aktif"', 'left');
        $this->db->group_by('e.id_ekstra');
        $this->db->order_by('e.nama_ekstra', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan data ekstra berdasarkan ID
     * @param int $id_ekstra
     * @return object
     */
    public function get_ekstra_by_id($id_ekstra)
    {
        $this->db->where('id_ekstra', $id_ekstra);
        return $this->db->get('bimbel_ekstra')->row();
    }

    /**
     * Mendapatkan data ekstra dengan detail guru berdasarkan ID
     * @param int $id_ekstra
     * @return object
     */
    public function get_ekstra_with_guru($id_ekstra)
    {
        $this->db->select('e.*, g.id_guru, g.nama_guru, g.nip, g.no_telpon');
        $this->db->from('bimbel_ekstra e');
        $this->db->join('bimbel_ekstra_guru eg', 'e.id_ekstra = eg.id_ekstra', 'left');
        $this->db->join('bimbel_guru g', 'eg.id_guru = g.id_guru', 'left');
        $this->db->where('e.id_ekstra', $id_ekstra);
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan daftar guru yang mengajar ekstra tertentu
     * @param int $id_ekstra
     * @return array
     */
    public function get_guru_by_ekstra($id_ekstra)
    {
        $this->db->select('g.id_guru, g.nama_guru, g.nip');
        $this->db->from('bimbel_guru g');
        $this->db->join('bimbel_ekstra_guru eg', 'g.id_guru = eg.id_guru');
        $this->db->where('eg.id_ekstra', $id_ekstra);
        $this->db->where('g.status', 'aktif');
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan daftar guru yang belum mengajar ekstra tertentu
     * @param int $id_ekstra
     * @return array
     */
    public function get_guru_not_in_ekstra($id_ekstra)
    {
        $this->db->select('g.id_guru, g.nama_guru, g.nip');
        $this->db->from('bimbel_guru g');
        $this->db->where('g.status', 'aktif');
        $this->db->where_not_in('g.id_guru', 
            "SELECT id_guru FROM bimbel_ekstra_guru WHERE id_ekstra = $id_ekstra", FALSE
        );
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan semua guru aktif untuk dropdown
     * @return array
     */
    public function get_all_guru()
    {
        $this->db->select('id_guru, nama_guru, nip');
        $this->db->where('status', 'aktif');
        $this->db->order_by('nama_guru', 'ASC');
        return $this->db->get('bimbel_guru')->result();
    }

    /**
     * Menambahkan data ekstra baru
     * @param array $data
     * @return int|boolean
     */
    public function insert_ekstra($data)
    {
        $this->db->insert('bimbel_ekstra', $data);
        return $this->db->insert_id();
    }

    /**
     * Menambahkan relasi ekstra-guru
     * @param int $id_ekstra
     * @param int $id_guru
     * @return boolean
     */
    public function insert_ekstra_guru($id_ekstra, $id_guru)
    {
        $data = [
            'id_ekstra' => $id_ekstra,
            'id_guru' => $id_guru
        ];
        $this->db->insert('bimbel_ekstra_guru', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Menambahkan banyak guru ke ekstra sekaligus
     * @param int $id_ekstra
     * @param array $guru_ids
     * @return boolean
     */
    public function insert_ekstra_guru_batch($id_ekstra, $guru_ids)
    {
        if (empty($guru_ids)) {
            return true;
        }

        $data = [];
        foreach ($guru_ids as $id_guru) {
            $data[] = [
                'id_ekstra' => $id_ekstra,
                'id_guru' => $id_guru
            ];
        }

        try {
            $this->db->insert_batch('bimbel_ekstra_guru', $data);
            // Return true if insert was attempted (even if no rows affected due to duplicates)
            return true;
        } catch (Exception $e) {
            // Log error but don't fail completely
            log_message('error', 'Error inserting ekstra guru batch: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengupdate data ekstra
     * @param int $id_ekstra
     * @param array $data
     * @return boolean
     */
    public function update_ekstra($id_ekstra, $data)
    {
        $this->db->where('id_ekstra', $id_ekstra);
        $this->db->update('bimbel_ekstra', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mengupdate guru yang mengajar ekstra
     * @param int $id_ekstra
     * @param array $guru_ids
     * @return boolean
     */
    public function update_ekstra_guru($id_ekstra, $guru_ids)
    {
        // Hapus semua relasi lama
        $this->db->where('id_ekstra', $id_ekstra);
        $this->db->delete('bimbel_ekstra_guru');

        // Tambahkan relasi baru
        return $this->insert_ekstra_guru_batch($id_ekstra, $guru_ids);
    }

    /**
     * Menghapus data ekstra
     * @param int $id_ekstra
     * @return boolean
     */
    public function delete_ekstra($id_ekstra)
    {
        // Hapus relasi ekstra-guru terlebih dahulu
        $this->db->where('id_ekstra', $id_ekstra);
        $this->db->delete('bimbel_ekstra_guru');

        // Hapus ekstra
        $this->db->where('id_ekstra', $id_ekstra);
        $this->db->delete('bimbel_ekstra');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mengubah status ekstra (aktif/nonaktif)
     * @param int $id_ekstra
     * @param string $status
     * @return boolean
     */
    public function toggle_status($id_ekstra, $status)
    {
        $this->db->where('id_ekstra', $id_ekstra);
        $this->db->update('bimbel_ekstra', ['status' => $status]);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mendapatkan total ekstra aktif
     * @return int
     */
    public function get_total_ekstra_aktif()
    {
        $this->db->where('status', 'aktif');
        return $this->db->count_all_results('bimbel_ekstra');
    }

    /**
     * Mendapatkan total ekstra nonaktif
     * @return int
     */
    public function get_total_ekstra_nonaktif()
    {
        $this->db->where('status', 'nonaktif');
        return $this->db->count_all_results('bimbel_ekstra');
    }

    /**
     * Mencari ekstra berdasarkan nama
     * @param string $keyword
     * @return array
     */
    public function search_ekstra($keyword)
    {
        $this->db->like('nama_ekstra', $keyword);
        $this->db->or_like('deskripsi', $keyword);
        $this->db->order_by('nama_ekstra', 'ASC');
        return $this->db->get('bimbel_ekstra')->result();
    }

    /**
     * Mendapatkan semua ekstra yang diajar oleh guru tertentu
     * @param int $id_guru
     * @return array
     */
    public function get_ekstra_by_guru($id_guru)
    {
        $this->db->select('e.*');
        $this->db->from('bimbel_ekstra e');
        $this->db->join('bimbel_ekstra_guru eg', 'e.id_ekstra = eg.id_ekstra');
        $this->db->where('eg.id_guru', $id_guru);
        $this->db->where('e.status', 'aktif');
        $this->db->order_by('e.nama_ekstra', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan ekstra dengan jumlah gurunya
     * @return array
     */
    public function get_ekstra_with_count()
    {
        $this->db->select('e.*, COUNT(eg.id_guru) as jumlah_guru');
        $this->db->from('bimbel_ekstra e');
        $this->db->join('bimbel_ekstra_guru eg', 'e.id_ekstra = eg.id_ekstra', 'left');
        $this->db->group_by('e.id_ekstra');
        $this->db->order_by('e.nama_ekstra', 'ASC');
        return $this->db->get()->result();
    }
}
