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
        $dashboard = $this->Dashboard_model->get_dashboard_data();

        $data['user']             = $this->session->userdata();
        $data['ringkasan']        = $dashboard['ringkasan'];
        $data['jurnal_terbaru']   = $dashboard['jurnal_terbaru'];
        $data['jurnal_per_bulan'] = $dashboard['jurnal_per_bulan'];
        $data['jurnal_per_mapel'] = $dashboard['jurnal_per_mapel'];
        $data['jurnal_per_kelas'] = $dashboard['jurnal_per_kelas'];
        $data['guru_teraktif']    = $dashboard['guru_teraktif'];

        $this->load->view('dashboard/index', $data);
    }

    /**
     * Get stats via Ajax (untuk refresh)
     * @return void
     */
    public function get_stats()
    {
        $dashboard = $this->Dashboard_model->get_dashboard_data();

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data'   => [
                'ringkasan'       => $dashboard['ringkasan'],
                'jurnal_terbaru'  => $dashboard['jurnal_terbaru'],
                'jurnal_per_bulan'=> $dashboard['jurnal_per_bulan'],
                'jurnal_per_mapel'=> $dashboard['jurnal_per_mapel'],
                'jurnal_per_kelas'=> $dashboard['jurnal_per_kelas'],
                'guru_teraktif'   => $dashboard['guru_teraktif'],
            ]
        ]);
    }
}
