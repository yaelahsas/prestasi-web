<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Load input library (core library that can't be autoloaded)
   
        $this->load->model('Auth_model');
    }

    /**
     * Halaman login
     * @return void
     */
    public function index()
    {
        // Cek jika user sudah login, redirect ke dashboard
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
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
                    'id_user' => $user->id_user,
                    'nama' => $user->nama,
                    'username' => $user->username,
                    'role' => $user->role,
                    'logged_in' => TRUE
                ];
                $this->session->set_userdata($session_data);

                echo json_encode([
                    'status' => true,
                    'message' => 'Login berhasil',
                    'redirect_url' => base_url('dashboard'),
                    'data' => [
                        'role' => $user->role,
                        'nama' => $user->nama
                    ]
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => 'Password salah'
                ]);
            }
        } else {
            echo json_encode([
                'status' => false,
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
}