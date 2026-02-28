<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil semua session WhatsApp
     */
    public function get_all_sessions()
    {
        $this->db->select('ws.*, u.nama as created_by_name');
        $this->db->from('whatsapp_sessions ws');
        $this->db->join('bimbel_users u', 'ws.created_by = u.id_user', 'left');
        $this->db->order_by('ws.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Ambil session berdasarkan session_id
     */
    public function get_session_by_id($session_id)
    {
        return $this->db->get_where('whatsapp_sessions', ['session_id' => $session_id])->row();
    }

    /**
     * Simpan session baru
     */
    public function save_session($data)
    {
        return $this->db->insert('whatsapp_sessions', $data);
    }

    /**
     * Update data session
     */
    public function update_session($session_id, $data)
    {
        $this->db->where('session_id', $session_id);
        return $this->db->update('whatsapp_sessions', $data);
    }

    /**
     * Update status session
     */
    public function update_session_status($session_id, $status)
    {
        $this->db->where('session_id', $session_id);
        return $this->db->update('whatsapp_sessions', [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Hapus session
     */
    public function delete_session($session_id)
    {
        $this->db->where('session_id', $session_id);
        return $this->db->delete('whatsapp_sessions');
    }

    /**
     * Log pesan yang dikirim
     */
    public function log_message($data)
    {
        return $this->db->insert('whatsapp_message_logs', $data);
    }

    /**
     * Ambil log pesan
     */
    public function get_message_logs($session_id = null, $limit = 50)
    {
        $this->db->select('wml.*, u.nama as sent_by_name');
        $this->db->from('whatsapp_message_logs wml');
        $this->db->join('bimbel_users u', 'wml.sent_by = u.id_user', 'left');

        if ($session_id) {
            $this->db->where('wml.session_id', $session_id);
        }

        $this->db->order_by('wml.sent_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Hitung total pesan terkirim hari ini
     */
    public function count_messages_today($session_id = null)
    {
        $this->db->where('DATE(sent_at)', date('Y-m-d'));
        $this->db->where('status', 'sent');
        if ($session_id) {
            $this->db->where('session_id', $session_id);
        }
        return $this->db->count_all_results('whatsapp_message_logs');
    }

    /**
     * Hitung total session aktif
     */
    public function count_active_sessions()
    {
        return $this->db->where('status', 'connected')->count_all_results('whatsapp_sessions');
    }
}
