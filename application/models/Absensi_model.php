<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_model extends CI_Model {

    /**
     * Mendapatkan semua absensi dengan filter
     * @param array $filter
     * @return array
     */
    public function get_all_absensi($filter = [])
    {
        $this->db->select('a.*, g.nama_guru, g.nip, k.nama_kelas, m.nama_mapel, u.nama as nama_penginput');
        $this->db->from('bimbel_absensi a');
        $this->db->join('bimbel_guru g', 'a.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'a.id_kelas = k.id_kelas', 'left');
        $this->db->join('bimbel_mapel m', 'a.id_mapel = m.id_mapel', 'left');
        $this->db->join('bimbel_users u', 'a.created_by = u.id_user', 'left');

        if (!empty($filter['id_guru'])) {
            $this->db->where('a.id_guru', $filter['id_guru']);
        }
        if (!empty($filter['bulan'])) {
            $this->db->where('MONTH(a.tanggal)', $filter['bulan']);
        }
        if (!empty($filter['tahun'])) {
            $this->db->where('YEAR(a.tanggal)', $filter['tahun']);
        }
        if (!empty($filter['status'])) {
            $this->db->where('a.status', $filter['status']);
        }

        $this->db->order_by('a.tanggal', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan absensi berdasarkan ID
     * @param int $id
     * @return object
     */
    public function get_absensi_by_id($id)
    {
        $this->db->select('a.*, g.nama_guru, k.nama_kelas, m.nama_mapel');
        $this->db->from('bimbel_absensi a');
        $this->db->join('bimbel_guru g', 'a.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'a.id_kelas = k.id_kelas', 'left');
        $this->db->join('bimbel_mapel m', 'a.id_mapel = m.id_mapel', 'left');
        $this->db->where('a.id_absensi', $id);
        return $this->db->get()->row();
    }

    /**
     * Mendapatkan absensi guru untuk bulan tertentu
     * @param int $id_guru
     * @param string $bulan
     * @param string $tahun
     * @return array
     */
    public function get_absensi_guru_bulan($id_guru, $bulan, $tahun)
    {
        $this->db->select('a.*, k.nama_kelas, m.nama_mapel');
        $this->db->from('bimbel_absensi a');
        $this->db->join('bimbel_kelas k', 'a.id_kelas = k.id_kelas', 'left');
        $this->db->join('bimbel_mapel m', 'a.id_mapel = m.id_mapel', 'left');
        $this->db->where('a.id_guru', $id_guru);
        $this->db->where('MONTH(a.tanggal)', $bulan);
        $this->db->where('YEAR(a.tanggal)', $tahun);
        $this->db->order_by('a.tanggal', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Mendapatkan rekap absensi guru (hadir/izin/sakit/alpha)
     * @param int $id_guru
     * @param string $bulan
     * @param string $tahun
     * @return object
     */
    public function get_rekap_absensi_guru($id_guru, $bulan, $tahun)
    {
        $this->db->select('
            COUNT(*) as total,
            SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN status = "izin"  THEN 1 ELSE 0 END) as izin,
            SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
            SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as alpha
        ');
        $this->db->from('bimbel_absensi');
        $this->db->where('id_guru', $id_guru);
        $this->db->where('MONTH(tanggal)', $bulan);
        $this->db->where('YEAR(tanggal)', $tahun);
        return $this->db->get()->row();
    }

    /**
     * Mendapatkan rekap absensi semua guru untuk bulan tertentu
     * @param string $bulan
     * @param string $tahun
     * @return array
     */
    public function get_rekap_semua_guru($bulan, $tahun)
    {
        $this->db->select('
            g.id_guru, g.nama_guru, g.nip,
            COUNT(a.id_absensi) as total,
            SUM(CASE WHEN a.status = "hadir" THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN a.status = "izin"  THEN 1 ELSE 0 END) as izin,
            SUM(CASE WHEN a.status = "sakit" THEN 1 ELSE 0 END) as sakit,
            SUM(CASE WHEN a.status = "alpha" THEN 1 ELSE 0 END) as alpha
        ');
        $this->db->from('bimbel_guru g');
        $this->db->join('bimbel_absensi a', 'g.id_guru = a.id_guru AND MONTH(a.tanggal) = ' . (int)$bulan . ' AND YEAR(a.tanggal) = ' . (int)$tahun, 'left');
        $this->db->where('g.status', 'aktif');
        $this->db->group_by('g.id_guru');
        $this->db->order_by('g.nama_guru', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Insert absensi baru
     * @param array $data
     * @return bool
     */
    public function insert_absensi($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('bimbel_absensi', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Update absensi
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_absensi($id, $data)
    {
        $this->db->where('id_absensi', $id);
        $this->db->update('bimbel_absensi', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Hapus absensi
     * @param int $id
     * @return bool
     */
    public function delete_absensi($id)
    {
        $this->db->where('id_absensi', $id);
        $this->db->delete('bimbel_absensi');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Cek apakah absensi sudah ada untuk guru pada tanggal tertentu
     * @param int $id_guru
     * @param string $tanggal
     * @param int|null $exclude_id
     * @return bool
     */
    public function is_absensi_exists($id_guru, $tanggal, $exclude_id = null)
    {
        $this->db->where('id_guru', $id_guru);
        $this->db->where('tanggal', $tanggal);
        if ($exclude_id) {
            $this->db->where('id_absensi !=', $exclude_id);
        }
        return $this->db->count_all_results('bimbel_absensi') > 0;
    }

    /**
     * Bulk insert absensi (untuk input massal)
     * @param array $rows
     * @return bool
     */
    public function bulk_insert($rows)
    {
        if (empty($rows)) return false;
        return $this->db->insert_batch('bimbel_absensi', $rows);
    }

    /**
     * Mendapatkan total absensi per status untuk dashboard
     * @param string $bulan
     * @param string $tahun
     * @return object
     */
    public function get_total_absensi_dashboard($bulan, $tahun)
    {
        $this->db->select('
            COUNT(*) as total,
            SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN status = "izin"  THEN 1 ELSE 0 END) as izin,
            SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
            SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as alpha
        ');
        $this->db->from('bimbel_absensi');
        $this->db->where('MONTH(tanggal)', $bulan);
        $this->db->where('YEAR(tanggal)', $tahun);
        return $this->db->get()->row();
    }
}
