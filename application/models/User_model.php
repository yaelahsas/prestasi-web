<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // Get all users
    public function get_all_users()
    {
        return $this->db->get('bimbel_users')->result();
    }

    // Get user by id
    public function get_user_by_id($id)
    {
        return $this->db->get_where('bimbel_users', array('id_user' => $id))->row();
    }

    // Get user by username
    public function get_user_by_username($username)
    {
        return $this->db->get_where('bimbel_users', array('username' => $username))->row();
    }

    // Insert new user
    public function insert_user($data)
    {
        return $this->db->insert('bimbel_users', $data);
    }

    // Update user
    public function update_user($id, $data)
    {
        $this->db->where('id_user', $id);
        return $this->db->update('bimbel_users', $data);
    }

    // Delete user
    public function delete_user($id)
    {
        $this->db->where('id_user', $id);
        return $this->db->delete('bimbel_users');
    }

    // Count total users
    public function count_users()
    {
        return $this->db->count_all('bimbel_users');
    }

    // Count users by role
    public function count_users_by_role($role)
    {
        $this->db->where('role', $role);
        return $this->db->count_all_results('bimbel_users');
    }

    // Count users by status
    public function count_users_by_status($status)
    {
        $this->db->where('status', $status);
        return $this->db->count_all_results('bimbel_users');
    }

    // Check if username exists (for validation)
    public function username_exists($username, $exclude_id = null)
    {
        $this->db->where('username', $username);
        if ($exclude_id) {
            $this->db->where('id_user !=', $exclude_id);
        }
        return $this->db->count_all_results('bimbel_users') > 0;
    }

    // Get users with pagination
    public function get_users_with_pagination($limit, $offset)
    {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('bimbel_users', $limit, $offset)->result();
    }

    // Search users
    public function search_users($keyword)
    {
        $this->db->like('nama', $keyword);
        $this->db->or_like('username', $keyword);
        $this->db->or_like('role', $keyword);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('bimbel_users')->result();
    }

    /**
     * Toggle status user (aktif/nonaktif)
     * @param int $id_user
     * @param string $status
     * @return boolean
     */
    public function toggle_status($id_user, $status)
    {
        $this->db->where('id_user', $id_user);
        $this->db->update('bimbel_users', ['status' => $status]);
        return $this->db->affected_rows() > 0;
    }
}