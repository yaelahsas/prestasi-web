<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Cek jika user belum login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        // Load model
        $this->load->model('Dashboard_model');
    }

    /**
     * Halaman dashboard utama
     * @return void
     */
    public function index()
    {
        $data['user'] = $this->session->userdata();
        $data['ringkasan'] = $this->Dashboard_model->get_ringkasan_dashboard();
        $this->load->view('dashboard/index', $data);
    }
    
    /**
     * Get stats via Ajax
     * @return void
     */
    public function get_stats()
    {
        $stats = $this->Dashboard_model->get_ringkasan_dashboard();
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => $stats
        ]);
    }
}