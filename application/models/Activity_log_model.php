<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_log_model extends CI_Model {

    /**
     * Mendapatkan semua log aktivitas dengan filter
     * @param array $filter
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_logs($filter = [], $limit = 50, $offset = 0)
    {
        $this->db->select('l.*, u.nama as nama_user, u.role');
        $this->db->from('bimbel_activity_log l');
        $this->db->join('bimbel_users u', 'l.id_user = u.id_user', 'left');

        if (!empty($filter['id_user'])) {
            $this->db->where('l.id_user', $filter['id_user']);
        }
        if (!empty($filter['action'])) {
            $this->db->where('l.action', $filter['action']);
        }
        if (!empty($filter['table_name'])) {
            $this->db->where('l.table_name', $filter['table_name']);
        }
        if (!empty($filter['tanggal_awal'])) {
            $this->db->where('DATE(l.created_at) >=', $filter['tanggal_awal']);
        }
        if (!empty($filter['tanggal_akhir'])) {
            $this->db->where('DATE(l.created_at) <=', $filter['tanggal_akhir']);
        }

        $this->db->order_by('l.created_at', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    /**
     * Hitung total log
     * @param array $filter
     * @return int
     */
    public function count_logs($filter = [])
    {
        $this->db->from('bimbel_activity_log l');
        $this->db->join('bimbel_users u', 'l.id_user = u.id_user', 'left');

        if (!empty($filter['id_user']))     $this->db->where('l.id_user', $filter['id_user']);
        if (!empty($filter['action']))      $this->db->where('l.action', $filter['action']);
        if (!empty($filter['table_name']))  $this->db->where('l.table_name', $filter['table_name']);
        if (!empty($filter['tanggal_awal'])) $this->db->where('DATE(l.created_at) >=', $filter['tanggal_awal']);
        if (!empty($filter['tanggal_akhir'])) $this->db->where('DATE(l.created_at) <=', $filter['tanggal_akhir']);

        return $this->db->count_all_results();
    }

    /**
     * Insert log aktivitas
     * @param array $data
     * @return bool
     */
    public function insert_log($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('bimbel_activity_log', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Hapus log lama (lebih dari N hari)
     * @param int $days
     * @return bool
     */
    public function purge_old_logs($days = 90)
    {
        $this->db->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        $this->db->delete('bimbel_activity_log');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Mendapatkan statistik aktivitas per user
     * @param string $tanggal_awal
     * @param string $tanggal_akhir
     * @return array
     */
    public function get_stats_per_user($tanggal_awal = null, $tanggal_akhir = null)
    {
        $this->db->select('u.nama, u.role, COUNT(l.id_log) as total_aktivitas,
            SUM(CASE WHEN l.action = "INSERT" THEN 1 ELSE 0 END) as total_insert,
            SUM(CASE WHEN l.action = "UPDATE" THEN 1 ELSE 0 END) as total_update,
            SUM(CASE WHEN l.action = "DELETE" THEN 1 ELSE 0 END) as total_delete
        ');
        $this->db->from('bimbel_activity_log l');
        $this->db->join('bimbel_users u', 'l.id_user = u.id_user');

        if ($tanggal_awal)  $this->db->where('DATE(l.created_at) >=', $tanggal_awal);
        if ($tanggal_akhir) $this->db->where('DATE(l.created_at) <=', $tanggal_akhir);

        $this->db->group_by('l.id_user');
        $this->db->order_by('total_aktivitas', 'DESC');
        return $this->db->get()->result();
    }
}
