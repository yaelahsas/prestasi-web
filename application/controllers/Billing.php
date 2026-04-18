<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Billing extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Cek jika user belum login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        
        // Load model
        $this->load->model('Billing_model');
        $this->load->model('Dashboard_model');
        $this->load->helper(array('pdf_helper', 'date'));

    }

    // ============================================
    // HALAMAN UTAMA
    // ============================================

    /**
     * Halaman billing utama
     * @return void
     */
    public function index()
    {
        $data['user'] = $this->session->userdata();
        $data['total_billing'] = $this->Billing_model->get_all_billing();
        $this->load->view('billing/index', $data);
    }

    // ============================================
    // PERIODE FUNCTIONS
    // ============================================

    /**
     * Get data periode via Ajax untuk datatable
     * @return void
     */
    public function get_period_data()
    {
        $periods = $this->Billing_model->get_all_periods();
        
        $data = [];
        foreach ($periods as $p) {
            $row = [];
            $row[] = $p->id_period;
            $row[] = $p->nama_period;
            $row[] = date('d/m/Y', strtotime($p->tanggal_mulai));
            $row[] = date('d/m/Y', strtotime($p->tanggal_selesai));
            
            // Status badge
            $status_class = '';
            switch ($p->status) {
                case 'aktif':
                    $status_class = 'bg-green-100 text-green-800';
                    break;
                case 'selesai':
                    $status_class = 'bg-blue-100 text-blue-800';
                    break;
                case 'draft':
                    $status_class = 'bg-gray-100 text-gray-800';
                    break;
            }
            $row[] = '<span class="px-2 py-1 ' . $status_class . ' rounded-full text-xs font-semibold">' . ucfirst($p->status) . '</span>';
            
            $row[] = '<div class="flex gap-1">
                        <button onclick="editPeriod('.$p->id_period.')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deletePeriod('.$p->id_period.')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                      </div>';
            $data[] = $row;
        }

        echo json_encode(['data' => $data]);
    }

    /**
     * Get data periode aktif via Ajax
     * @return void
     */
    public function get_active_periods()
    {
        $periods = $this->Billing_model->get_active_periods();
        
        echo json_encode([
            'status' => 'success',
            'data' => $periods
        ]);
    }

    /**
     * Get data periode by ID via Ajax
     * @param int $id
     * @return void
     */
    public function get_period_by_id($id)
    {
        $period = $this->Billing_model->get_period_by_id($id);
        
        echo json_encode([
            'status' => 'success',
            'data' => $period
        ]);
    }

    /**
     * Simpan data periode (tambah/edit)
     * @return void
     */
    public function save_period()
    {
        $this->form_validation->set_rules('bulan', 'Bulan', 'required|integer|greater_than[0]|less_than[13]');
        $this->form_validation->set_rules('tahun', 'Tahun', 'required|integer|greater_than[2020]|less_than[2100]');
        $this->form_validation->set_rules('tanggal_mulai', 'Tanggal Mulai', 'required');
        $this->form_validation->set_rules('tanggal_selesai', 'Tanggal Selesai', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[draft,aktif,selesai]');
        
        if ($this->form_validation->run() == FALSE) {
            $errors = [
                'bulan' => form_error('bulan'),
                'tahun' => form_error('tahun'),
                'tanggal_mulai' => form_error('tanggal_mulai'),
                'tanggal_selesai' => form_error('tanggal_selesai'),
                'status' => form_error('status')
            ];
            
            echo json_encode([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $errors
            ]);
            return;
        }
        
        $id_period = $this->input->post('id_period');
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');
        
        $nama_period = $this->Billing_model->get_nama_bulan($bulan) . ' ' . $tahun;
        
        $data = [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'nama_period' => $nama_period,
            'tanggal_mulai' => $this->input->post('tanggal_mulai'),
            'tanggal_selesai' => $this->input->post('tanggal_selesai'),
            'status' => $this->input->post('status'),
            'created_by' => $this->session->userdata('id_user')
        ];
        
        if ($id_period) {
            // Update
            $result = $this->Billing_model->update_period($id_period, $data);
            $message = 'Data periode berhasil diperbarui';
        } else {
            // Insert
            $result = $this->Billing_model->insert_period($data);
            $message = 'Data periode berhasil ditambahkan';
        }
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? $message : 'Terjadi kesalahan saat menyimpan data'
        ]);
    }

    /**
     * Hapus data periode
     * @param int $id
     * @return void
     */
    public function delete_period($id)
    {
        $result = $this->Billing_model->delete_period($id);
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Data periode berhasil dihapus' : 'Gagal menghapus data periode'
        ]);
    }

    // ============================================
    // TARIF FUNCTIONS
    // ============================================

    /**
     * Get data tarif via Ajax untuk datatable
     * @return void
     */
    public function get_tarif_data()
    {
        $tarif = $this->Billing_model->get_all_tarif();
        
        $data = [];
        foreach ($tarif as $t) {
            $row = [];
            $row[] = $t->id_tarif;
            
            // Jenis kegiatan badge
            $jenis_class = '';
            switch ($t->jenis_kegiatan) {
                case 'reguler':
                    $jenis_class = 'bg-blue-100 text-blue-800';
                    break;
                case 'olimpiade':
                    $jenis_class = 'bg-purple-100 text-purple-800';
                    break;
                case 'luring':
                    $jenis_class = 'bg-orange-100 text-orange-800';
                    break;
                case 'daring':
                    $jenis_class = 'bg-green-100 text-green-800';
                    break;
            }
            $row[] = '<span class="px-2 py-1 ' . $jenis_class . ' rounded-full text-xs font-semibold">' . ucfirst($t->jenis_kegiatan) . '</span>';
            
            $row[] = 'Rp ' . number_format($t->tarif, 0, ',', '.');
            $row[] = $t->keterangan ?: '-';
            $row[] = $t->status == 'aktif'
                ? '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Aktif</span>'
                : '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Nonaktif</span>';
            $row[] = '<div class="flex gap-1">
                        <button onclick="editTarif('.$t->id_tarif.')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteTarif('.$t->id_tarif.')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                      </div>';
            $data[] = $row;
        }

        echo json_encode(['data' => $data]);
    }

    /**
     * Get data tarif aktif via Ajax
     * @return void
     */
    public function get_tarif_aktif()
    {
        $tarif = $this->Billing_model->get_tarif_aktif();
        
        echo json_encode([
            'status' => 'success',
            'data' => $tarif
        ]);
    }

    /**
     * Get data tarif by ID via Ajax
     * @param int $id
     * @return void
     */
    public function get_tarif_by_id($id)
    {
        $tarif = $this->Billing_model->get_tarif_by_id($id);
        
        echo json_encode([
            'status' => 'success',
            'data' => $tarif
        ]);
    }

    /**
     * Simpan data tarif (tambah/edit)
     * @return void
     */
    public function save_tarif()
    {
        $this->form_validation->set_rules('jenis_kegiatan', 'Jenis Kegiatan', 'required|in_list[reguler,olimpiade,luring,daring]');
        $this->form_validation->set_rules('tarif', 'Tarif', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[aktif,nonaktif]');
        
        if ($this->form_validation->run() == FALSE) {
            $errors = [
                'jenis_kegiatan' => form_error('jenis_kegiatan'),
                'tarif' => form_error('tarif'),
                'status' => form_error('status')
            ];
            
            echo json_encode([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $errors
            ]);
            return;
        }
        
        $id_tarif = $this->input->post('id_tarif');
        
        $data = [
            'jenis_kegiatan' => $this->input->post('jenis_kegiatan'),
            'tarif' => $this->input->post('tarif'),
            'keterangan' => $this->input->post('keterangan') ?: null,
            'status' => $this->input->post('status')
        ];
        
        if ($id_tarif) {
            // Update
            $result = $this->Billing_model->update_tarif($id_tarif, $data);
            $message = 'Data tarif berhasil diperbarui';
        } else {
            // Insert
            $result = $this->Billing_model->insert_tarif($data);
            $message = 'Data tarif berhasil ditambahkan';
        }
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? $message : 'Terjadi kesalahan saat menyimpan data'
        ]);
    }

    /**
     * Hapus data tarif
     * @param int $id
     * @return void
     */
    public function delete_tarif($id)
    {
        $result = $this->Billing_model->delete_tarif($id);
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Data tarif berhasil dihapus' : 'Gagal menghapus data tarif'
        ]);
    }

    // ============================================
    // BILLING FUNCTIONS
    // ============================================

    /**
     * Get data billing via Ajax untuk datatable
     * @return void
     */
    public function get_billing_data()
    {
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        $status = $this->input->get('status');
        
        $billing = $this->Billing_model->get_billing_filtered($bulan, $tahun, $status);
        
        $data = [];
        foreach ($billing as $b) {
            $row = [];
            $row[] = $b->id_billing;
            $row[] = $b->kode_billing;
            $row[] = $b->nama_guru;
            $row[] = $b->nip ?: '-';
            $row[] = $b->nama_period;
            $row[] = $b->total_jurnal . ' Jurnal';
            $row[] = 'Rp ' . number_format($b->total_honor, 0, ',', '.');
            
            // Status badge
            $status_class = '';
            switch ($b->status) {
                case 'selesai':
                    $status_class = 'bg-green-100 text-green-800';
                    break;
                case 'diproses':
                    $status_class = 'bg-blue-100 text-blue-800';
                    break;
                case 'dibayar':
                    $status_class = 'bg-purple-100 text-purple-800';
                    break;
                case 'draft':
                    $status_class = 'bg-gray-100 text-gray-800';
                    break;
            }
            $row[] = '<span class="px-2 py-1 ' . $status_class . ' rounded-full text-xs font-semibold">' . ucfirst($b->status) . '</span>';
            
            $row[] = '<div class="flex gap-1">
                        <button onclick="printBillingPdf('.$b->id_billing.')" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors" title="Cetak PDF">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button onclick="viewBilling('.$b->id_billing.')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors" title="Lihat Detail">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                        <button onclick="deleteBilling('.$b->id_billing.')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                      </div>';
            $data[] = $row;
        }

        echo json_encode(['data' => $data]);
    }

    /**
     * Get data billing untuk dropdown via Ajax
     * @return void
     */
    public function get_billing_dropdown()
    {
        $billing = $this->Billing_model->get_billing_filtered();
        
        echo json_encode([
            'status' => 'success',
            'data' => $billing
        ]);
    }

    /**
     * Get data billing by ID via Ajax
     * @param int $id
     * @return void
     */
    public function get_billing_by_id($id)
    {
        $billing = $this->Billing_model->get_billing_by_id($id);
        
        echo json_encode([
            'status' => 'success',
            'data' => $billing
        ]);
    }

    /**
     * Get detail billing by ID via Ajax
     * @param int $id
     * @return void
     */
    public function get_billing_detail($id)
    {
        $detail = $this->Billing_model->get_billing_details($id);
        
        echo json_encode([
            'status' => 'success',
            'data' => $detail
        ]);
    }

    /**
     * Get jurnal billing by ID via Ajax
     * @param int $id
     * @return void
     */
    public function get_billing_jurnal($id)
    {
        $jurnal = $this->Billing_model->get_billing_jurnal($id);
        
        echo json_encode([
            'status' => 'success',
            'data' => $jurnal
        ]);
    }

    /**
     * Generate billing untuk semua guru dalam periode
     * @return void
     */
    public function generate_billing_all()
    {
        $this->form_validation->set_rules('id_period', 'Periode', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => [
                    'id_period' => form_error('id_period')
                ]
            ]);
            return;
        }
        
        $id_period = $this->input->post('id_period');
        $result = $this->Billing_model->generate_billing_all($id_period);
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Billing berhasil digenerate untuk semua guru' : 'Gagal generate billing'
        ]);
    }

    /**
     * Generate billing untuk guru tertentu
     * @return void
     */
    public function generate_billing_guru()
    {
        $this->form_validation->set_rules('id_period', 'Periode', 'required');
        $this->form_validation->set_rules('id_guru', 'Guru', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => [
                    'id_period' => form_error('id_period'),
                    'id_guru' => form_error('id_guru')
                ]
            ]);
            return;
        }
        
        $id_period = $this->input->post('id_period');
        $id_guru = $this->input->post('id_guru');
        $result = $this->Billing_model->generate_billing_guru($id_period, $id_guru);
        
        if ($result === false) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal generate billing. Mungkin billing sudah ada atau tidak ada jurnal.'
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'Billing guru berhasil digenerate'
            ]);
        }
    }

    /**
     * Update status billing
     * @return void
     */
    public function update_billing_status()
    {
        $this->form_validation->set_rules('id_billing', 'Billing', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[draft,diproses,selesai,dibayar]');
        
        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => [
                    'id_billing' => form_error('id_billing'),
                    'status' => form_error('status')
                ]
            ]);
            return;
        }
        
        $id_billing = $this->input->post('id_billing');
        $status = $this->input->post('status');
        
        $result = $this->Billing_model->update_billing($id_billing, ['status' => $status]);
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Status billing berhasil diperbarui' : 'Gagal memperbarui status billing'
        ]);
    }

    /**
     * Hapus data billing
     * @param int $id
     * @return void
     */
    public function delete_billing($id)
    {
        $result = $this->Billing_model->delete_billing($id);
        
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Data billing berhasil dihapus' : 'Gagal menghapus data billing'
        ]);
    }

    // ============================================
    // HELPER FUNCTIONS
    // ============================================

    /**
     * Get data guru untuk dropdown
     * @return void
     */
    public function get_guru()
    {
        $guru = $this->Billing_model->get_guru();
        
        echo json_encode([
            'status' => 'success',
            'data' => $guru
        ]);
    }

    /**
     * Get data jenis kegiatan untuk dropdown
     * @return void
     */
    public function get_jenis_kegiatan()
    {
        $jenis_kegiatan = $this->Billing_model->get_jenis_kegiatan();
        
        echo json_encode([
            'status' => 'success',
            'data' => $jenis_kegiatan
        ]);
    }

    // ============================================
    // PDF GENERATION FUNCTIONS
    // ============================================

    /**
     * Generate PDF untuk billing tertentu
     * @param int $id_billing
     * @return void
     */
    public function print_billing_pdf($id_billing)
    {
        try {
            // Load helpers
            $this->load->helper(array('pdf_helper', 'tanggal'));
            
            // Load DomPDF library
            $this->load->library('dompdf');
            
            // Get data billing
            $billing = $this->Billing_model->get_billing_by_id($id_billing);
            
            if (!$billing) {
                show_error('Data billing tidak ditemukan');
                return;
            }
            
            // Get detail billing
            $details = $this->Billing_model->get_billing_details($id_billing);
            
            if (empty($details)) {
                show_error('Detail billing tidak ditemukan');
                return;
            }
            
            // Get jurnal billing
            $jurnals = $this->Billing_model->get_billing_jurnal($id_billing);
            
            // Get data sekolah
            $sekolah = $this->Dashboard_model->get_sekolah();
            
            // Create PDF object
            $pdf = $this->dompdf;
            $pdf->setPaper('A4', 'portrait');
            
            // Format dates
            $hari_cetak = format_hari_indo(date('Y-m-d'));
            $tanggal_cetak = format_tanggal_indo(date('Y-m-d'));
            
            // Build HTML
            $html = '<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Billing Honor Guru</title>
<style>
    body {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 10px;
        margin: 15px;
    }

    table.main-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    table.main-table th,
    table.main-table td {
        border: 1px solid #000;
        padding: 5px;
        font-size: 9px;
    }

    table.main-table th {
        text-align: center;
        font-weight: bold;
        background-color: #f0f0f0;
    }

    .text-center { text-align: center; }
    .bold { font-weight: bold; }

    .info-box {
        border: 1px solid #000;
        padding: 10px;
        margin-bottom: 10px;
    }
</style>
</head>
<body>';

            // Add copyright notice
            $user_nama = $this->session->userdata('nama') ? $this->session->userdata('nama') : 'System';
            $html .= generate_pdf_copyright($user_nama, $billing->kode_billing, 'v1.0');

            // Header
            $html .= generate_pdf_header($pdf, 'BILLING HONOR GURU');

            // Info box
            $html .= '
<div class="info-box">
    <table style="width:100%; border:none !important; border-collapse:collapse !important;">
        <tr>
            <td style="border:none !important;width:20%;">Kode Billing</td>
            <td style="border:none !important;width:2%;">:</td>
            <td style="border:none !important;width:78%;"><b>' . $billing->kode_billing . '</b></td>
        </tr>
        <tr>
            <td style="border:none !important;">Nama Guru</td>
            <td style="border:none !important;">:</td>
            <td style="border:none !important;"><b>' . htmlspecialchars($billing->nama_guru) . '</b></td>
        </tr>
        <tr>
            <td style="border:none !important;">NIP</td>
            <td style="border:none !important;">:</td>
            <td style="border:none !important;">' . htmlspecialchars($billing->nip ?: '-') . '</td>
        </tr>
        <tr>
            <td style="border:none !important;">Periode</td>
            <td style="border:none !important;">:</td>
            <td style="border:none !important;">' . htmlspecialchars($billing->nama_period) . '</td>
        </tr>
        <tr>
            <td style="border:none !important;">Tanggal</td>
            <td style="border:none !important;">:</td>
            <td style="border:none !important;">' . date('d/m/Y', strtotime($billing->tanggal_mulai)) . ' - ' . date('d/m/Y', strtotime($billing->tanggal_selesai)) . '</td>
        </tr>
        <tr>
            <td style="border:none !important;">Status</td>
            <td style="border:none !important;">:</td>
            <td style="border:none !important;"><b>' . htmlspecialchars(ucfirst($billing->status)) . '</b></td>
        </tr>
        <tr>
            <td style="border:none !important;">Total Jurnal</td>
            <td style="border:none !important;">:</td>
            <td style="border:none !important;"><b>' . intval($billing->total_jurnal) . ' Jurnal</b></td>
        </tr>
        <tr>
            <td style="border:none !important;">Total Honor</td>
            <td style="border:none !important;">:</td>
            <td style="border:none !important;"><b>Rp ' . number_format(floatval($billing->total_honor), 0, ',', '.') . '</b></td>
        </tr>
    </table>
</div>';

            // Rincian Honor per Jenis Kegiatan
            $html .= '<h3 class="bold" style="margin: 15px 0 10px 0;">Rincian Honor per Jenis Kegiatan</h3>';
            
            $headers = ['No', 'Jenis Kegiatan', 'Jumlah Jurnal', 'Tarif per Jurnal', 'Subtotal Honor'];
            $table_data = [];
            $no = 1;
            
            foreach ($details as $detail) {
                $table_data[] = [
                    $no++,
                    htmlspecialchars(ucfirst($detail->jenis_kegiatan)),
                    intval($detail->jumlah_jurnal),
                    'Rp ' . number_format(floatval($detail->tarif_per_jurnal), 0, ',', '.'),
                    'Rp ' . number_format(floatval($detail->subtotal_honor), 0, ',', '.')
                ];
            }
            
            $html .= generate_table_html($headers, $table_data, [10, 30, 20, 30, 30], 'main-table');

            // Daftar Jurnal
            $html .= '<h3 class="bold" style="margin: 15px 0 10px 0;">Daftar Jurnal yang Dihitung</h3>';
            
            $headers_jurnal = ['No', 'Tanggal', 'Kelas', 'Mata Pelajaran', 'Materi', 'Jenis Kegiatan'];
            $table_data_jurnal = [];
            $no = 1;
            
            foreach ($jurnals as $jurnal) {
                $table_data_jurnal[] = [
                    $no++,
                    date('d/m/Y', strtotime($jurnal->tanggal)),
                    htmlspecialchars($jurnal->nama_kelas),
                    htmlspecialchars($jurnal->nama_mapel),
                    htmlspecialchars($jurnal->materi ?: '-'),
                    htmlspecialchars(ucfirst($jurnal->jenis_kegiatan))
                ];
            }
            
            $html .= generate_table_html($headers_jurnal, $table_data_jurnal, [10, 20, 25, 25, 60, 25], 'main-table');

            // Footer
            $html .= generate_pdf_footer($pdf, 'Srono', $tanggal_cetak);

            $html .= '</body></html>';

            // // Render PDF
            $pdf->loadHtml($html);
            $pdf->render();

            // // Output
            $filename = 'Billing_' . $billing->kode_billing . '_' . str_replace(' ', '_', strtolower($billing->nama_guru)) . '.pdf';
            $pdf->stream($filename, ['Attachment' => 0]);
        } catch (Exception $e) {
            show_error('Error generating PDF: ' . $e->getMessage());
        }
    }
}
