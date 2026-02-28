<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Ensure core classes are loaded as properties
        $this->load->library('session');
        $this->load->library('form_validation');
        
        // Make sure input is available
        if (!isset($this->input)) {
            $this->input =& load_class('Input', 'core');
        }
    }

    /**
     * Cek apakah user sudah login
     * @return void
     */
    protected function requireLogin()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    /**
     * Cek apakah user memiliki role tertentu
     * @param string|array $roles - role yang diizinkan
     * @param string $redirect_to - URL redirect jika tidak punya akses
     * @return void
     */
    protected function requireRole($roles, $redirect_to = 'dashboard')
    {
        $this->requireLogin();

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $user_role = $this->session->userdata('role');

        if (!in_array($user_role, $roles)) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            redirect($redirect_to);
        }
    }

    /**
     * Cek apakah user adalah admin
     * @return bool
     */
    protected function isAdmin()
    {
        return $this->session->userdata('role') === 'admin';
    }

    /**
     * Cek apakah user adalah guru
     * @return bool
     */
    protected function isGuru()
    {
        return $this->session->userdata('role') === 'guru';
    }

    /**
     * Cek apakah user adalah tim (operator)
     * @return bool
     */
    protected function isTim()
    {
        return $this->session->userdata('role') === 'tim';
    }

    /**
     * Cek apakah user adalah admin atau tim
     * @return bool
     */
    protected function isAdminOrTim()
    {
        return in_array($this->session->userdata('role'), ['admin', 'tim']);
    }

    /**
     * Log aktivitas user ke tabel bimbel_activity_log
     * @param string $action
     * @param string $table_name
     * @param int|null $record_id
     * @param string|null $description
     * @return void
     */
    protected function logActivity($action, $table_name = null, $record_id = null, $description = null)
    {
        try {
            $this->db->insert('bimbel_activity_log', [
                'id_user'    => $this->session->userdata('id_user'),
                'action'     => $action,
                'table_name' => $table_name,
                'record_id'  => $record_id,
                'description'=> $description,
                'ip_address' => $this->input->ip_address(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            // Jangan sampai error log mengganggu operasi utama
            log_message('error', 'Activity log error: ' . $e->getMessage());
        }
    }
}
