<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

    /**
     * Mendapatkan data user berdasarkan username
     * @param string $username
     * @return object
     */
    public function get_user_by_username($username)
    {
        return $this->db
            ->where('username', $username)
            ->get('bimbel_users')
            ->row();
    }

    /**
     * Mendapatkan data user berdasarkan ID
     * @param int $id_user
     * @return object
     */
    public function get_user_by_id($id_user)
    {
        return $this->db
            ->where('id_user', $id_user)
            ->get('bimbel_users')
            ->row();
    }

    /**
     * Update last login user
     * @param int $id_user
     * @return bool
     */
    public function update_last_login($id_user)
    {
        $this->db->where('id_user', $id_user);
        return $this->db->update('bimbel_users', ['last_login' => date('Y-m-d H:i:s')]);
    }
}