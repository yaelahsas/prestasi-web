<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Load input library (core library that can't be autoloaded)

        
        // Cek jika user belum login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        // Load model
        $this->load->model('User_model');
        $this->load->model('Dashboard_model');
    }

    /**
     * Halaman users utama
     * @return void
     */
    public function index()
    {
        $data['user'] = $this->session->userdata();
        $data['total_users'] = $this->Dashboard_model->get_total_users();
        $this->load->view('users/index', $data);
    }

    /**
     * Get data users via Ajax untuk datatable
     * @return void
     */
    public function get_users_data()
    {
        $users = $this->User_model->get_all_users();
        
        $data = [];
        foreach ($users as $u) {
            $row = [];
            $row[] = $u->id_user;
            $row[] = $u->nama;
            $row[] = $u->username;
            $row[] = '<span class="px-3 py-1 rounded-full text-xs font-medium ' . 
                    ($u->role == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800') . 
                    '">' . ucfirst($u->role) . '</span>';
            $row[] = '<span class="px-3 py-1 rounded-full text-xs font-medium ' . 
                    ($u->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') . 
                    '">' . ucfirst($u->status) . '</span>';
            $row[] = date('d/m/Y H:i', strtotime($u->created_at));
            $row[] = '<div class="flex gap-1">
                        <button onclick="editUser('.$u->id_user.')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteUser('.$u->id_user.')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button onclick="toggleStatus('.$u->id_user.', \''.$u->status.'\')" class="px-3 py-1 ' . 
                        ($u->status == 'aktif' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600') . 
                        ' text-white rounded transition-colors">
                            <i class="fas ' . ($u->status == 'aktif' ? 'fa-eye-slash' : 'fa-eye') . '"></i>
                        </button>
                      </div>';
            $data[] = $row;
        }

        $output = [
            "data" => $data
        ];

        echo json_encode($output);
    }

    /**
     * Get data user by ID via Ajax
     * @param int $id
     * @return void
     */
    public function get_user_by_id($id)
    {
        $user = $this->User_model->get_user_by_id($id);
        
        echo json_encode([
            'status' => 'success',
            'data' => $user
        ]);
    }

    /**
     * Simpan data user (tambah/edit)
     * @return void
     */
    public function save_user()
    {
        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|max_length[50]|callback_username_check');
        $this->form_validation->set_rules('role', 'Role', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');
        
        // Password validation hanya untuk user baru
        $id_user = $this->input->post('id_user');
        if (!$id_user) {
            $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[6]');
        } else {
            // Password opsional untuk edit, tapi jika diisi minimal 6 karakter
            $this->form_validation->set_rules('password', 'Password', 'trim|min_length[6]');
        }
        
        if ($this->form_validation->run() == FALSE) {
            $errors = [
                'nama' => form_error('nama'),
                'username' => form_error('username'),
                'password' => form_error('password'),
                'role' => form_error('role'),
                'status' => form_error('status')
            ];
            
            echo json_encode([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $errors
            ]);
            return;
        }
        
        $data = [
            'nama' => $this->input->post('nama'),
            'username' => $this->input->post('username'),
            'role' => $this->input->post('role'),
            'status' => $this->input->post('status')
        ];
        
        // Jika password diisi, tambahkan ke data
        $password = $this->input->post('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        if ($id_user) {
            // Update
            $result = $this->User_model->update_user($id_user, $data);
            $message = 'Data user berhasil diperbarui';
        } else {
            // Insert
            $result = $this->User_model->insert_user($data);
            $message = 'Data user berhasil ditambahkan';
        }
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? $message : 'Terjadi kesalahan saat menyimpan data'
        ]);
    }

    /**
     * Hapus data user
     * @param int $id
     * @return void
     */
    public function delete_user($id)
    {
        // Cek apakah user memiliki jurnal terkait
        $this->db->where('created_by', $id);
        $jurnal_count = $this->db->count_all_results('bimbel_jurnal');
        
        if ($jurnal_count > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menghapus data. User memiliki jurnal terkait.'
            ]);
            return;
        }
        
        $result = $this->User_model->delete_user($id);
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Data user berhasil dihapus' : 'Gagal menghapus data user'
        ]);
    }

    /**
     * Toggle status user
     * @param int $id
     * @return void
     */
    public function toggle_status($id)
    {
        $user = $this->User_model->get_user_by_id($id);
        $new_status = $user->status == 'aktif' ? 'nonaktif' : 'aktif';
        $result = $this->User_model->update_user($id, ['status' => $new_status]);
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Status user berhasil diubah' : 'Gagal mengubah status user'
        ]);
    }

    /**
     * Cari user
     * @return void
     */
    public function search()
    {
        $keyword = $this->input->get('keyword');
        $users = $this->User_model->search_users($keyword);
        
        echo json_encode([
            'status' => 'success',
            'data' => $users
        ]);
    }

    /**
     * Custom validation untuk username
     * @param string $username
     * @return bool
     */
    public function username_check($username)
    {
        $id_user = $this->input->post('id_user');
        
        if ($this->User_model->username_exists($username, $id_user)) {
            $this->form_validation->set_message('username_check', 'Username sudah digunakan');
            return FALSE;
        }
        
        return TRUE;
    }

    /**
     * Get total users aktif for API
     * @return void
     */
    public function get_total_users_aktif()
    {
        $total = $this->User_model->count_users_by_status('aktif');
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'total' => $total
            ]
        ]);
    }

    /**
     * Get total users nonaktif for API
     * @return void
     */
    public function get_total_users_nonaktif()
    {
        $total = $this->User_model->count_users_by_status('nonaktif');
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'total' => $total
            ]
        ]);
    }
}