<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jurnal_model extends CI_Model {

    /**
     * Mendapatkan semua data jurnal dengan relasi ke guru, kelas, mapel, dan user
     * @return array
     */
    public function get_all_jurnal()
    {
        $this->db->select('j.*, g.nama_guru, g.nip, k.nama_kelas, m.nama_mapel, u.nama as nama_penginput');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->join('bimbel_users u', 'j.created_by = u.id_user');
        $this->db->order_by('j.tanggal', 'DESC');
        $this->db->order_by('j.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan data jurnal berdasarkan ID
     * @param int $id_jurnal
     * @return object
     */
    public function get_jurnal_by_id($id_jurnal)
    {
        $this->db->select('j.*, g.nama_guru, g.nip, k.nama_kelas, m.nama_mapel, u.nama as nama_penginput');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->join('bimbel_users u', 'j.created_by = u.id_user');
        $this->db->where('j.id_jurnal', $id_jurnal);
        return $this->db->get()->row();
    }

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
     * Menambahkan data jurnal baru
     * @param array $data
     * @return boolean
     */
    public function insert_jurnal($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('bimbel_jurnal', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mengupdate data jurnal
     * @param int $id_jurnal
     * @param array $data
     * @return boolean
     */
    public function update_jurnal($id_jurnal, $data)
    {
        $this->db->where('id_jurnal', $id_jurnal);
        $this->db->update('bimbel_jurnal', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Menghapus data jurnal
     * @param int $id_jurnal
     * @return boolean
     */
    public function delete_jurnal($id_jurnal)
    {
        // Hapus foto bukti jika ada
        $jurnal = $this->get_jurnal_by_id($id_jurnal);
        if ($jurnal && $jurnal->foto_bukti) {
            $file_path = './assets/uploads/foto_kegiatan/' . $jurnal->foto_bukti;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        $this->db->where('id_jurnal', $id_jurnal);
        $this->db->delete('bimbel_jurnal');
        return $this->db->affected_rows() > 0;
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
     * Mendapatkan total jurnal hari ini
     * @return int
     */
    public function get_total_jurnal_hari_ini()
    {
        $this->db->where('DATE(tanggal)', date('Y-m-d'));
        return $this->db->count_all_results('bimbel_jurnal');
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
     * Mencari jurnal berdasarkan tanggal, guru, atau materi
     * @param string $keyword
     * @return array
     */
    public function search_jurnal($keyword)
    {
        $this->db->select('j.*, g.nama_guru, g.nip, k.nama_kelas, m.nama_mapel, u.nama as nama_penginput');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->join('bimbel_users u', 'j.created_by = u.id_user');
        $this->db->group_start();
        $this->db->like('j.tanggal', $keyword);
        $this->db->or_like('g.nama_guru', $keyword);
        $this->db->or_like('j.materi', $keyword);
        $this->db->or_like('k.nama_kelas', $keyword);
        $this->db->or_like('m.nama_mapel', $keyword);
        $this->db->group_end();
        $this->db->order_by('j.tanggal', 'DESC');
        $this->db->order_by('j.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan data jurnal berdasarkan rentang tanggal
     * @param string $tanggal_awal
     * @param string $tanggal_akhir
     * @return array
     */
    public function get_jurnal_by_tanggal($tanggal_awal, $tanggal_akhir)
    {
        $this->db->select('j.*, g.nama_guru, g.nip, k.nama_kelas, m.nama_mapel, u.nama as nama_penginput');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->join('bimbel_users u', 'j.created_by = u.id_user');
        $this->db->where('j.tanggal >=', $tanggal_awal);
        $this->db->where('j.tanggal <=', $tanggal_akhir);
        $this->db->order_by('j.tanggal', 'DESC');
        $this->db->order_by('j.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Upload foto bukti
     * @param string $field_name
     * @return string|null
     */
    public function upload_foto_bukti($field_name)
    {
        $config['upload_path'] = './assets/uploads/foto_kegiatan/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size'] = 2048; // 2MB
        $config['encrypt_name'] = TRUE;
        
        $this->load->library('upload', $config);
        
        if (!$this->upload->do_upload($field_name)) {
            return null;
        } else {
            $upload_data = $this->upload->data();
            return $upload_data['file_name'];
        }
    }
}