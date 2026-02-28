<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model');
    }

    /**
     * Halaman login
     * @return void
     */
    public function index()
    {
        // Cek jika user sudah login, redirect sesuai role
        if ($this->session->userdata('logged_in')) {
            $this->_redirect_by_role($this->session->userdata('role'));
        }
        
        $this->load->view('auth/login');
    }

    /**
     * Proses login menggunakan AJAX
     * @return json
     */
    public function login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // Validasi input
        if (empty($username) || empty($password)) {
            echo json_encode([
                'status' => false,
                'message' => 'Username dan password harus diisi'
            ]);
            return;
        }

        // Cek user di database
        $user = $this->Auth_model->get_user_by_username($username);

        if ($user) {
            // Verifikasi password
            if (password_verify($password, $user->password)) {
                // Cek status user
                if ($user->status !== 'aktif') {
                    echo json_encode([
                        'status' => false,
                        'message' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'
                    ]);
                    return;
                }

                // Set session data
                $session_data = [
                    'id_user'  => $user->id_user,
                    'nama'     => $user->nama,
                    'username' => $user->username,
                    'role'     => $user->role,
                    'logged_in'=> TRUE
                ];

                // Jika role guru, simpan juga id_guru ke session
                if ($user->role === 'guru' && !empty($user->id_guru)) {
                    $session_data['id_guru'] = $user->id_guru;
                }

                $this->session->set_userdata($session_data);

                // Update last login
                $this->Auth_model->update_last_login($user->id_user);

                // Tentukan redirect URL berdasarkan role
                $redirect_url = $this->_get_redirect_url($user->role);

                echo json_encode([
                    'status'       => true,
                    'message'      => 'Login berhasil',
                    'redirect_url' => $redirect_url,
                    'data'         => [
                        'role' => $user->role,
                        'nama' => $user->nama
                    ]
                ]);
            } else {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Password salah'
                ]);
            }
        } else {
            echo json_encode([
                'status'  => false,
                'message' => 'Username tidak ditemukan'
            ]);
        }
    }

    /**
     * Proses logout
     * @return void
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }

    /**
     * Redirect berdasarkan role
     * @param string $role
     * @return void
     */
    private function _redirect_by_role($role)
    {
        redirect($this->_get_redirect_url($role));
    }

    /**
     * Dapatkan URL redirect berdasarkan role
     * @param string $role
     * @return string
     */
    private function _get_redirect_url($role)
    {
        switch ($role) {
            case 'guru':
                return base_url('guru_portal');
            case 'admin':
            case 'tim':
            default:
                return base_url('dashboard');
        }
    }
}
