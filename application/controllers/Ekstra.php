<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ekstra extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Load input library (core library that can't be autoloaded)


        // Cek jika user belum login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        // Load model
        $this->load->model('Ekstra_model');
        $this->load->model('Dashboard_model');
    }

    /**
     * Halaman ekstra utama
     * @return void
     */
    public function index()
    {
        $data['user'] = $this->session->userdata();
        $data['total_ekstra'] = $this->Ekstra_model->get_total_ekstra_aktif();
        $this->load->view('ekstra/index', $data);
    }

    /**
     * Get data ekstra via Ajax untuk datatable
     * @return void
     */
    public function get_data()
    {
        $ekstra = $this->Ekstra_model->get_all_ekstra();

        $data = [];
        $no = 1;
        foreach ($ekstra as $e) {
            $row = [];
            $row[] = $no++; // No urut
            $row[] = $e->nama_ekstra;
            
            // Show only teacher count with click handler for details
            $jumlah_guru = isset($e->jumlah_guru) ? $e->jumlah_guru : 0;
            $row[] = '<button onclick="showGuruDetails(' . $e->id_ekstra . ')" class="px-3 py-1 rounded-full text-xs font-medium ' .
                ($jumlah_guru > 0 ? 'bg-blue-100 text-blue-800 hover:bg-blue-200 cursor-pointer' : 'bg-gray-100 text-gray-400 cursor-not-allowed') .
                ' transition-colors" ' . ($jumlah_guru > 0 ? '' : 'disabled') . '>
                <i class="fas fa-users mr-1"></i>' . $jumlah_guru . ' Guru
            </button>';
            
            $row[] = '<span class="px-3 py-1 rounded-full text-xs font-medium ' .
                ($e->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') .
                '">' . ucfirst($e->status) . '</span>';
            $row[] = '<div class="flex gap-1">
                        <button onclick="editEkstra(' . $e->id_ekstra . ')" class="px-2 py-1 sm:px-3 sm:py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-xs sm:text-sm">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteEkstra(' . $e->id_ekstra . ')" class="px-2 py-1 sm:px-3 sm:py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs sm:text-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button onclick="toggleStatus(' . $e->id_ekstra . ', \'' . $e->status . '\')" class="px-2 py-1 sm:px-3 sm:py-1 ' .
                ($e->status == 'aktif' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600') .
                ' text-white rounded transition-colors text-xs sm:text-sm">
                            <i class="fas ' . ($e->status == 'aktif' ? 'fa-eye-slash' : 'fa-eye') . '"></i>
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
     * Get data ekstra by ID via Ajax
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        $ekstra = $this->Ekstra_model->get_ekstra_by_id($id);
        $guru_list = $this->Ekstra_model->get_guru_by_ekstra($id);

        // Get guru IDs array
        $guru_ids = [];
        foreach ($guru_list as $g) {
            $guru_ids[] = $g->id_guru;
        }

        echo json_encode([
            'status' => 'success',
            'data' => $ekstra,
            'guru_ids' => $guru_ids
        ]);
    }

    /**
     * Get data guru untuk dropdown
     * @return void
     */
    public function get_guru()
    {
        $guru = $this->Ekstra_model->get_all_guru();

        echo json_encode([
            'status' => 'success',
            'data' => $guru
        ]);
    }

    /**
     * Simpan data ekstra (tambah/edit)
     * @return void
     */
    public function save()
    {
        // Get raw input data
        $input = json_decode(file_get_contents('php://input'), true);
        
        // If JSON decode fails, try POST data
        if ($input === null) {
            $input = $this->input->post();
        }

        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('nama_ekstra', 'Nama Ekstra', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() == FALSE) {
            $errors = [
                'nama_ekstra' => form_error('nama_ekstra'),
                'status' => form_error('status')
            ];

            echo json_encode([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $errors
            ]);
            return;
        }

        $id_ekstra = isset($input['id_ekstra']) ? $input['id_ekstra'] : null;
        $data = [
            'nama_ekstra' => $input['nama_ekstra'],
            'deskripsi' => isset($input['deskripsi']) ? $input['deskripsi'] : null,
            'status' => $input['status']
        ];

        // Get guru IDs from input
        $guru_ids = isset($input['guru_ids']) ? $input['guru_ids'] : [];

        // Handle guru_ids as array
        if (is_array($guru_ids)) {
            // Filter out empty values
            $guru_ids = array_filter($guru_ids, function($value) {
                return !empty($value);
            });
            // Re-index array
            $guru_ids = array_values($guru_ids);
        } else {
            $guru_ids = [];
        }

        // Validate at least one guru is selected
        if (empty($guru_ids)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Harap pilih minimal satu guru',
                'errors' => ['guru_ids' => 'Harap pilih minimal satu guru']
            ]);
            return;
        }

        if ($id_ekstra) {
            // Update
            $update_result = $this->Ekstra_model->update_ekstra($id_ekstra, $data);
            // Update guru relations
            $guru_result = $this->Ekstra_model->update_ekstra_guru($id_ekstra, $guru_ids);
            
            // Consider success if guru update succeeded (even if data update has no changes)
            $result = $guru_result;
            $message = 'Data ekstra berhasil diperbarui';
        } else {
            // Insert
            $id_ekstra = $this->Ekstra_model->insert_ekstra($data);
            if ($id_ekstra) {
                // Insert guru relations
                $guru_result = $this->Ekstra_model->insert_ekstra_guru_batch($id_ekstra, $guru_ids);
                // Consider success if ekstra was created and guru insert was attempted
                $result = ($id_ekstra > 0);
            } else {
                $result = false;
            }
            $message = 'Data ekstra berhasil ditambahkan';
        }

        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? $message : 'Terjadi kesalahan saat menyimpan data'
        ]);
    }

    /**
     * Hapus data ekstra
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        $result = $this->Ekstra_model->delete_ekstra($id);

        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Data ekstra berhasil dihapus' : 'Gagal menghapus data ekstra'
        ]);
    }

    /**
     * Toggle status ekstra
     * @param int $id
     * @return void
     */
    public function toggle_status($id)
    {
        $ekstra = $this->Ekstra_model->get_ekstra_by_id($id);
        $new_status = $ekstra->status == 'aktif' ? 'nonaktif' : 'aktif';
        $result = $this->Ekstra_model->toggle_status($id, $new_status);

        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Status ekstra berhasil diubah' : 'Gagal mengubah status ekstra'
        ]);
    }

    /**
     * Cari ekstra
     * @return void
     */
    public function search()
    {
        $keyword = $this->input->get('keyword');
        $ekstra = $this->Ekstra_model->search_ekstra($keyword);

        echo json_encode([
            'status' => 'success',
            'data' => $ekstra
        ]);
    }

    /**
     * Get total ekstra aktif for API
     * @return void
     */
    public function get_stats()
    {
        $aktif = $this->Ekstra_model->get_total_ekstra_aktif();
        $nonaktif = $this->Ekstra_model->get_total_ekstra_nonaktif();

        echo json_encode([
            'status' => 'success',
            'data' => [
                'aktif' => $aktif,
                'nonaktif' => $nonaktif
            ]
        ]);
    }

    /**
     * Get guru details for specific ekstra
     * @param int $id
     * @return void
     */
    public function get_guru_details($id)
    {
        $guru_list = $this->Ekstra_model->get_guru_by_ekstra($id);
        $ekstra = $this->Ekstra_model->get_ekstra_by_id($id);

        echo json_encode([
            'status' => 'success',
            'data' => [
                'ekstra' => $ekstra,
                'guru_list' => $guru_list
            ]
        ]);
    }
}
