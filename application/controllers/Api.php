<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Load model
        $this->load->model('Jurnal_model');
        $this->load->model('Guru_model');
        $this->load->model('Kelas_model');
        $this->load->model('Mapel_model');
        $this->load->model('User_model');
        $this->load->model('Laporan_model');
        $this->load->model('Sekolah_model');
        $this->load->helper(array('pdf_helper', 'date'));

        // Set response header to JSON
        header('Content-Type: application/json');
    }

    /**
     * API Authentication using API Key
     * @return bool
     */
    private function _authenticate()
    {
        $api_key = $this->input->server('HTTP_X_API_KEY');

        // You can store API keys in database or config file
        // For now, using a simple hardcoded key
        $valid_api_keys = ['whatsapp_bot_key_2024', 'prestasi_api_key'];

        if (!$api_key || !in_array($api_key, $valid_api_keys)) {
            $this->_send_error_response('Unauthorized', 401);
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Send standardized error response
     * @param string $message
     * @param int $status_code
     * @return void
     */
    private function _send_error_response($message, $status_code = 400)
    {
        $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => $message,
                'timestamp' => date('Y-m-d H:i:s')
            ]));
    }

    /**
     * Send standardized success response
     * @param array $data
     * @param string $message
     * @return void
     */
    private function _send_success_response($data = [], $message = 'Success')
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Create jurnal via API (for WhatsApp bot)
     * @return void
     */
    public function create_jurnal()
    {
        // Authenticate API request
        if (!$this->_authenticate()) {
            return;
        }

        // Only accept POST requests
        if ($this->input->method() !== 'post') {
            $this->_send_error_response('Method not allowed', 405);
            return;
        }

        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), TRUE);

        // If JSON is empty, try to get from POST data
        if (empty($input)) {
            $input = $this->input->post();
        }

        // Validate required fields - only no_telpon is required now
        // if (empty($input['no_telpon'])) {
        //     $this->_send_error_response("Field 'no_telpon' is required");
        //     return;
        // }

        // Validate phone number format
        // if (!preg_match('/^(0[0-9]{9,14}|(\+62)[0-9]{9,14}|628[0-9]{8,12})$/', $input['no_telpon'])) {
        //     $this->_send_error_response('Format nomor telepon tidak valid. Gunakan format: 08xxxxxxxxxx, +62xxxxxxxxxx, atau 628xxxxxxxxxx');
        //     return;
        // }

        // Get guru information by phone number
        $guru = $this->Guru_model->get_guru_by_lid($input['no_lid']);
        if (!$guru) {
            $this->_send_error_response('Guru with LID ' . $input['no_lid'] . ' not found');
            return;
        }

        // Check if kelas exists
        $kelas = $this->db->get_where('bimbel_kelas', ['id_kelas' => $guru->id_kelas])->row();
        if (!$kelas) {
            $this->_send_error_response('Kelas not found');
            return;
        }

        // Check if mapel exists
        $mapel = $this->db->get_where('bimbel_mapel', ['id_mapel' => $guru->id_mapel])->row();
        if (!$mapel) {
            $this->_send_error_response('Mata Pelajaran not found');
            return;
        }

        // Update guru data if no_lid is provided
        // $guru_updated = false;
        // $guru_update_data = [];

        // Check if no_lid is provided and update guru if needed
        // if (isset($input['no_lid']) && !empty($input['no_lid'])) {
        //     // Update guru's LID if it's empty
        //     if (empty($guru->no_lid)) {
        //         $guru_update_data['no_lid'] = $input['no_lid'];
        //         $guru_updated = true;
        //     }
        // }

        // // Update guru data if needed
        // if ($guru_updated) {
        //     $this->db->where('id_guru', $guru->id_guru);
        //     $this->db->update('bimbel_guru', $guru_update_data);
        // }

        if (!empty($input['tanggal'])) {

            // Validasi format tanggal dari input
            $tanggal_input = $input['tanggal'];


            // Cek format YYYY-MM-DD
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_input)) {
                $tanggal = $tanggal_input;
            } else {
                // Jika format salah, fallback ke hari ini
                $tanggal = date('Y-m-d');
            }
        } else {
            // Default jika tidak dikirim
            $tanggal = date('Y-m-d');
        }
        // Cegah tanggal masa depan
        if (strtotime($tanggal) > strtotime(date('Y-m-d'))) {
            $this->_send_error_response('Tanggal jurnal tidak boleh lebih dari hari ini');
            return;
        }


        // Prepare data for insertion
        $data = [
            'tanggal' => $tanggal, // Use current date automatically
            'id_guru' => $guru->id_guru,
            'id_kelas' => $guru->id_kelas,
            'id_mapel' => $guru->id_mapel,
            'materi' => $mapel->nama_mapel, // Use subject name as materi
            'jumlah_siswa' => null, // Set to null as requested
            'keterangan' => isset($input['keterangan']) ? $input['keterangan'] : null,
            'created_by' => isset($input['created_by']) ? $input['created_by'] : 2, // Default to admin if not specified
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Handle foto (alias for foto_bukti) if provided as base64
        $foto_field = isset($input['foto']) ? 'foto' : 'foto_bukti';
        if (isset($input[$foto_field]) && !empty($input[$foto_field])) {
            // Check if it's a base64 string
            if (preg_match('/^data:image\/(\w+);base64,/', $input[$foto_field])) {
                $foto_bukti = $this->_upload_base64_image($input[$foto_field]);
                if ($foto_bukti) {
                    $data['foto_bukti'] = $foto_bukti;
                }
            }
        }

        // Insert jurnal
        $result = $this->Jurnal_model->insert_jurnal($data);

        if ($result) {
            // Get the inserted jurnal with related data
            $jurnal_id = $this->db->insert_id();
            $jurnal = $this->Jurnal_model->get_jurnal_by_id($jurnal_id);

            $response_data = [
                'id_jurnal' => $jurnal_id,
                'jurnal_data' => $jurnal
            ];

            // Add guru update info if applicable
            // if ($guru_updated) {
            //     $response_data['guru_updated'] = true;
            //     $response_data['updated_fields'] = array_keys($guru_update_data);
            // }

            $this->_send_success_response($response_data, 'Jurnal created successfully');
        } else {
            $this->_send_error_response('Failed to create jurnal');
        }
    }

    /**
     * Upload base64 image
     * @param string $base64_string
     * @return string|null
     */
    private function _upload_base64_image($base64_string)
    {
        try {
            // Extract file extension
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $matches)) {
                $image_type = $matches[1];
                $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
                $base64_string = base64_decode($base64_string);

                if ($base64_string === false) {
                    return null;
                }

                // Generate unique filename
                $filename = uniqid() . '.' . $image_type;
                $filepath = './assets/uploads/foto_kegiatan/' . $filename;

                // Save file
                if (file_put_contents($filepath, $base64_string)) {
                    return $filename;
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error uploading base64 image: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get list of guru for API
     * @return void
     */
    public function get_guru()
    {
        if (!$this->_authenticate()) {
            return;
        }

        $guru = $this->Jurnal_model->get_guru();
        $this->_send_success_response($guru, 'Guru list retrieved successfully');
    }

    /**
     * Get list of kelas for API
     * @return void
     */
    public function get_kelas()
    {
        if (!$this->_authenticate()) {
            return;
        }

        $kelas = $this->Jurnal_model->get_kelas();
        $this->_send_success_response($kelas, 'Kelas list retrieved successfully');
    }

    /**
     * Get list of mapel for API
     * @return void
     */
    public function get_mapel()
    {
        if (!$this->_authenticate()) {
            return;
        }

        $mapel = $this->Jurnal_model->get_mapel();
        $this->_send_success_response($mapel, 'Mapel list retrieved successfully');
    }

    /**
     * Get jurnal by ID for API
     * @param int $id
     * @return void
     */
    public function get_jurnal($id = null)
    {
        if (!$this->_authenticate()) {
            return;
        }

        if (!$id) {
            $this->_send_error_response('Jurnal ID is required');
            return;
        }

        $jurnal = $this->Jurnal_model->get_jurnal_by_id($id);

        if ($jurnal) {
            $this->_send_success_response($jurnal, 'Jurnal retrieved successfully');
        } else {
            $this->_send_error_response('Jurnal not found', 404);
        }
    }

    /**
     * Get all jurnal with pagination for API
     * @return void
     */
    public function get_all_jurnal()
    {
        if (!$this->_authenticate()) {
            return;
        }

        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $limit = $this->input->get('limit') ? (int)$this->input->get('limit') : 10;
        $offset = ($page - 1) * $limit;

        // Get total count
        $total = $this->db->count_all_results('bimbel_jurnal');

        // Get jurnal with pagination
        $this->db->select('j.*, g.nama_guru, g.nip, k.nama_kelas, m.nama_mapel, u.nama as nama_penginput');
        $this->db->from('bimbel_jurnal j');
        $this->db->join('bimbel_guru g', 'j.id_guru = g.id_guru');
        $this->db->join('bimbel_kelas k', 'j.id_kelas = k.id_kelas');
        $this->db->join('bimbel_mapel m', 'j.id_mapel = m.id_mapel');
        $this->db->join('bimbel_users u', 'j.created_by = u.id_user');
        $this->db->order_by('j.tanggal', 'DESC');
        $this->db->order_by('j.created_at', 'DESC');
        $this->db->limit($limit, $offset);
        $jurnal = $this->db->get()->result();

        $this->_send_success_response([
            'jurnal' => $jurnal,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ], 'Jurnal list retrieved successfully');
    }

    /**
     * Search jurnal for API
     * @return void
     */
    public function search_jurnal()
    {
        if (!$this->_authenticate()) {
            return;
        }

        $keyword = $this->input->get('keyword');

        if (empty($keyword)) {
            $this->_send_error_response('Search keyword is required');
            return;
        }

        $jurnal = $this->Jurnal_model->search_jurnal($keyword);
        $this->_send_success_response($jurnal, 'Search results retrieved successfully');
    }

    /**
     * Generate PDF report for API
     * @return void
     */
    public function get_laporan_pdf()
    {
        // Authenticate API request
        if (!$this->_authenticate()) {
            return;
        }

        // Get parameters
        $tipe_laporan = $this->input->get('tipe_laporan'); // bulanan, guru, kelas, mapel, rekap_kehadiran
        $bulan = $this->input->get('bulan') ? $this->input->get('bulan') : date('m');
        $tahun = $this->input->get('tahun') ? $this->input->get('tahun') : date('Y');
        $id = $this->input->get('id'); // ID for guru, kelas, or mapel
        $no_lid = $this->input->get('no_lid'); // no_lid for guru (alternative to id)

        // Validate tipe_laporan
        $valid_tipe = ['bulanan', 'guru', 'kelas', 'mapel', 'rekap_kehadiran'];
        if (!in_array($tipe_laporan, $valid_tipe)) {
            $this->_send_error_response('Tipe laporan tidak valid. Pilih: bulanan, guru, kelas, mapel, rekap_kehadiran');
            return;
        }

        // Validate required ID for specific report types
        if (in_array($tipe_laporan, ['guru', 'kelas', 'mapel']) && empty($id) && ($tipe_laporan !== 'guru' || empty($no_lid))) {
            if ($tipe_laporan === 'guru') {
                $this->_send_error_response("Parameter 'id' atau 'no_lid' diperlukan untuk tipe laporan guru");
            } else {
                $this->_send_error_response("Parameter 'id' diperlukan untuk tipe laporan ini");
            }
            return;
        }

        // If no_lid is provided for guru report, get the guru ID
        if ($tipe_laporan === 'guru' && !empty($no_lid)) {
            $guru = $this->Guru_model->get_guru_by_lid($no_lid);
            if (!$guru) {
                $this->_send_error_response("Guru dengan no_lid '{$no_lid}' tidak ditemukan");
                return;
            }
            $id = $guru->id_guru;
        }

        try {
            // Generate PDF based on type
            $pdf_content = $this->_generate_pdf_content($tipe_laporan, $bulan, $tahun, $id);

            if ($pdf_content) {
                // Set headers for PDF download
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="laporan_' . $tipe_laporan . '_' . $bulan . '_' . $tahun . '.pdf"');
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');

                echo $pdf_content;
            } else {
                $this->_send_error_response('Gagal menghasilkan PDF');
            }
        } catch (Exception $e) {
            $this->_send_error_response('Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF content based on report type
     * @param string $tipe_laporan
     * @param string $bulan
     * @param string $tahun
     * @param int $id
     * @return string|null
     */
    private function _generate_pdf_content($tipe_laporan, $bulan, $tahun, $id = null)
    {
        // Load DomPDF library
        $this->load->library('dompdf');

        // Create new PDF document
        $pdf = new Dompdf();
        $pdf->setPaper('A4', 'portrait');

        // Get data based on report type
        switch ($tipe_laporan) {
            case 'bulanan':
                return $this->_generate_pdf_bulanan($pdf, $bulan, $tahun);
            case 'guru':
                return $this->_generate_pdf_guru($pdf, $id, $bulan, $tahun);
            case 'kelas':
                return $this->_generate_pdf_kelas($pdf, $id, $bulan, $tahun);
            case 'mapel':
                return $this->_generate_pdf_mapel($pdf, $id, $bulan, $tahun);
            case 'rekap_kehadiran':
                return $this->_generate_pdf_rekap_kehadiran($pdf, $bulan, $tahun);
            default:
                return null;
        }
    }

    /**
     * Generate PDF for monthly report
     * @param object $pdf
     * @param string $bulan
     * @param string $tahun
     * @return string
     */
    private function _generate_pdf_bulanan($pdf, $bulan, $tahun)
    {
        // Get data jurnal per bulan
        $data_jurnal = $this->Laporan_model->get_jurnal_by_bulan_tahun($bulan, $tahun);

        // Build HTML content
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Jurnal Bulanan</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            margin: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 5px;
            font-size: 8px;
        }
        th {
            text-align: center;
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .bold {
            font-weight: bold;
        }
        .margin-top-20 {
            margin-top: 20px;
        }
        .margin-top-50 {
            margin-top: 50px;
        }
    </style>
</head>
<body>';

        // Generate header with sekolah data
        $html .= generate_pdf_header($pdf, 'LAPORAN JURNAL BULANAN');

        // Add periode information
        $html .= '<p class="text-center bold">Periode: ' . $this->_get_nama_bulan($bulan) . ' ' . $tahun . '</p>';

        // Prepare table data
        $headers = ['No', 'Tanggal', 'Kelas', 'Mapel', 'Guru', 'Materi', 'Siswa', 'Penginput'];
        $table_data = [];
        $no = 1;

        foreach ($data_jurnal as $jurnal) {
            $table_data[] = [
                $no++,
                date('d/m/Y', strtotime($jurnal->tanggal)),
                $jurnal->nama_kelas,
                $jurnal->nama_mapel,
                $jurnal->nama_guru,
                substr($jurnal->materi, 0, 30),
                $jurnal->jumlah_siswa,
                $jurnal->nama_penginput
            ];
        }

        // Generate table HTML
        $html .= generate_table_html($headers, $table_data, [10, 20, 25, 25, 30, 50, 15, 25]);

        // Add summary
        $html .= '<p class="bold margin-top-20">Total Jurnal: ' . count($data_jurnal) . '</p>';

        // Generate footer with signature
        $html .= generate_pdf_footer($pdf, 'Srono', format_tanggal_indo(date('Y-m-d')));

        // ===== BAGIAN LAMPIRAN FOTO =====
        $html .= '<div style="page-break-before: always;"></div>';

        $html .= '<h3 class="text-center">LAMPIRAN BUKTI JURNAL</h3>';
        $html .= '<p class="text-center">Periode: ' . $this->_get_nama_bulan($bulan) . ' ' . $tahun . '</p>';

        foreach ($data_jurnal as $jurnal) {

            if (!empty($jurnal->foto_bukti)) {

                $path = FCPATH . 'assets/uploads/foto_kegiatan/' . $jurnal->foto_bukti;

                if (file_exists($path)) {

                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

                    $html .= '<div style="margin-top:20px; page-break-inside: avoid;">';

                    $html .= '<p><b>Tanggal:</b> ' . date('d/m/Y', strtotime($jurnal->tanggal)) . '</p>';
                    $html .= '<p><b>Guru:</b> ' . $jurnal->nama_guru . '</p>';
                    $html .= '<p><b>Kelas:</b> ' . $jurnal->nama_kelas . '</p>';
                    $html .= '<p><b>Mapel:</b> ' . $jurnal->nama_mapel . '</p>';

                    $html .= '<img src="' . $base64 . '" style="max-width: 500px; max-height: 700px;" />';

                    $html .= '</div>';
                }
            }
        }


        $html .= '</body></html>';
        // Load HTML to DomPDF
        $pdf->loadHtml($html);
        $pdf->render();

        // Return PDF content
        return $pdf->output();
    }

    /**
     * Generate PDF for guru report
     * @param object $pdf
     * @param int $id_guru
     * @param string $bulan
     * @param string $tahun
     * @return string
     */
    private function _generate_pdf_guru($pdf, $id_guru, $bulan, $tahun)
    {
        // Get data jurnal per guru
        $data_jurnal = $this->Laporan_model->get_jurnal_by_guru($id_guru, $bulan, $tahun);

        // Get guru info
        $guru_info = $this->db->get_where('bimbel_guru', array('id_guru' => $id_guru))->row();

        // Build HTML content
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Jurnal Guru</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            margin: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 5px;
            font-size: 8px;
        }
        th {
            text-align: center;
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .bold {
            font-weight: bold;
        }
        .margin-top-20 {
            margin-top: 20px;
        }
        .margin-top-50 {
            margin-top: 50px;
        }
    </style>
</head>
<body>';

        // Generate header with sekolah data
        $html .= generate_pdf_header($pdf, 'LAPORAN JURNAL GURU');

        // Add guru information
        $html .= '<p class="bold">Nama Guru: ' . $guru_info->nama_guru . '</p>';
        $html .= '<p class="bold">NIP: ' . $guru_info->nip . '</p>';
        $html .= '<p class="bold">Periode: ' . $this->_get_nama_bulan($bulan) . ' ' . $tahun . '</p>';
        $html .= '<div class="margin-top-20"></div>';

        // Prepare table data
        $headers = ['No', 'Tanggal', 'Kelas', 'Mapel', 'Materi', 'Siswa', 'Penginput'];
        $table_data = [];
        $no = 1;

        foreach ($data_jurnal as $jurnal) {
            $table_data[] = [
                $no++,
                date('d/m/Y', strtotime($jurnal->tanggal)),
                $jurnal->nama_kelas,
                $jurnal->nama_mapel,
                substr($jurnal->materi, 0, 30),
                $jurnal->jumlah_siswa,
                $jurnal->nama_penginput
            ];
        }

        // Generate table HTML
        $html .= generate_table_html($headers, $table_data, [10, 20, 25, 25, 50, 15, 25]);

        // Add summary
        $html .= '<p class="bold margin-top-20">Total Jurnal: ' . count($data_jurnal) . '</p>';

        // Generate footer with signature
        $html .= generate_pdf_footer($pdf, 'Srono', format_tanggal_indo(date('Y-m-d')));

        // ===== BAGIAN LAMPIRAN FOTO =====
        $html .= '<div style="page-break-before: always;"></div>';

        $html .= '<h3 class="text-center">LAMPIRAN BUKTI JURNAL</h3>';
        $html .= '<p class="text-center">Periode: ' . $this->_get_nama_bulan($bulan) . ' ' . $tahun . '</p>';

        foreach ($data_jurnal as $jurnal) {

            if (!empty($jurnal->foto_bukti)) {

                $path = FCPATH . 'assets/uploads/foto_kegiatan/' . $jurnal->foto_bukti;

                if (file_exists($path)) {

                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

                    $html .= '<div style="margin-top:20px; page-break-inside: avoid;">';

                    $html .= '<p><b>Tanggal:</b> ' . date('d/m/Y', strtotime($jurnal->tanggal)) . '</p>';
                    $html .= '<p><b>Guru:</b> ' . $jurnal->nama_guru . '</p>';
                    $html .= '<p><b>Kelas:</b> ' . $jurnal->nama_kelas . '</p>';
                    $html .= '<p><b>Mapel:</b> ' . $jurnal->nama_mapel . '</p>';

                    $html .= '<img src="' . $base64 . '" style="max-width: 500px; max-height: 700px;" />';

                    $html .= '</div>';
                }
            }
        }


        $html .= '</body></html>';
        // Load HTML to DomPDF
        $pdf->loadHtml($html);
        $pdf->render();

        // Return PDF content
        return $pdf->output();
    }

    /**
     * Generate PDF for kelas report
     * @param object $pdf
     * @param int $id_kelas
     * @param string $bulan
     * @param string $tahun
     * @return string
     */
    private function _generate_pdf_kelas($pdf, $id_kelas, $bulan, $tahun)
    {
        // Get data jurnal per kelas
        $data_jurnal = $this->Laporan_model->get_jurnal_by_kelas($id_kelas, $bulan, $tahun);

        // Get kelas info
        $kelas_info = $this->db->get_where('bimbel_kelas', array('id_kelas' => $id_kelas))->row();

        // Build HTML content
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Jurnal Kelas</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            margin: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 5px;
            font-size: 8px;
        }
        th {
            text-align: center;
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .bold {
            font-weight: bold;
        }
        .margin-top-20 {
            margin-top: 20px;
        }
        .margin-top-50 {
            margin-top: 50px;
        }
    </style>
</head>
<body>';

        // Generate header with sekolah data
        $html .= generate_pdf_header($pdf, 'LAPORAN JURNAL KELAS');

        // Add kelas information
        $html .= '<p class="bold">Kelas: ' . $kelas_info->nama_kelas . '</p>';
        $html .= '<p class="bold">Tingkat: ' . $kelas_info->tingkat . '</p>';
        $html .= '<p class="bold">Periode: ' . $this->_get_nama_bulan($bulan) . ' ' . $tahun . '</p>';
        $html .= '<div class="margin-top-20"></div>';

        // Prepare table data
        $headers = ['No', 'Tanggal', 'Guru', 'Mapel', 'Materi', 'Siswa', 'Penginput'];
        $table_data = [];
        $no = 1;

        foreach ($data_jurnal as $jurnal) {
            $table_data[] = [
                $no++,
                date('d/m/Y', strtotime($jurnal->tanggal)),
                $jurnal->nama_guru,
                $jurnal->nama_mapel,
                substr($jurnal->materi, 0, 30),
                $jurnal->jumlah_siswa,
                $jurnal->nama_penginput
            ];
        }

        // Generate table HTML
        $html .= generate_table_html($headers, $table_data, [10, 20, 30, 25, 50, 15, 25]);

        // Add summary
        $html .= '<p class="bold margin-top-20">Total Jurnal: ' . count($data_jurnal) . '</p>';

        // Generate footer with signature
        $html .= generate_pdf_footer($pdf, 'Jakarta', format_tanggal_indo(date('Y-m-d')));

        $html .= '</body></html>';

        // Load HTML to DomPDF
        $pdf->loadHtml($html);
        $pdf->render();

        // Return PDF content
        return $pdf->output();
    }

    /**
     * Generate PDF for mapel report
     * @param object $pdf
     * @param int $id_mapel
     * @param string $bulan
     * @param string $tahun
     * @return string
     */
    private function _generate_pdf_mapel($pdf, $id_mapel, $bulan, $tahun)
    {
        // Get data jurnal per mapel
        $data_jurnal = $this->Laporan_model->get_jurnal_by_mapel($id_mapel, $bulan, $tahun);

        // Get mapel info
        $mapel_info = $this->db->get_where('bimbel_mapel', array('id_mapel' => $id_mapel))->row();

        // Build HTML content
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Jurnal Mata Pelajaran</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            margin: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 5px;
            font-size: 8px;
        }
        th {
            text-align: center;
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .bold {
            font-weight: bold;
        }
        .margin-top-20 {
            margin-top: 20px;
        }
        .margin-top-50 {
            margin-top: 50px;
        }
    </style>
</head>
<body>';

        // Generate header with sekolah data
        $html .= generate_pdf_header($pdf, 'LAPORAN JURNAL MATA PELAJARAN');

        // Add mapel information
        $html .= '<p class="bold">Mata Pelajaran: ' . $mapel_info->nama_mapel . '</p>';
        $html .= '<p class="bold">Periode: ' . $this->_get_nama_bulan($bulan) . ' ' . $tahun . '</p>';
        $html .= '<div class="margin-top-20"></div>';

        // Prepare table data
        $headers = ['No', 'Tanggal', 'Guru', 'Kelas', 'Materi', 'Siswa', 'Penginput'];
        $table_data = [];
        $no = 1;

        foreach ($data_jurnal as $jurnal) {
            $table_data[] = [
                $no++,
                date('d/m/Y', strtotime($jurnal->tanggal)),
                $jurnal->nama_guru,
                $jurnal->nama_kelas,
                substr($jurnal->materi, 0, 30),
                $jurnal->jumlah_siswa,
                $jurnal->nama_penginput
            ];
        }

        // Generate table HTML
        $html .= generate_table_html($headers, $table_data, [10, 20, 30, 25, 50, 15, 25]);

        // Add summary
        $html .= '<p class="bold margin-top-20">Total Jurnal: ' . count($data_jurnal) . '</p>';

        // Generate footer with signature
        $html .= generate_pdf_footer($pdf, 'Jakarta', format_tanggal_indo(date('Y-m-d')));

        $html .= '</body></html>';

        // Load HTML to DomPDF
        $pdf->loadHtml($html);
        $pdf->render();

        // Return PDF content
        return $pdf->output();
    }

    /**
     * Generate PDF for rekap kehadiran
     * @param object $pdf
     * @param string $bulan
     * @param string $tahun
     * @return string
     */
    private function _generate_pdf_rekap_kehadiran($pdf, $bulan, $tahun)
    {
        // Get data rekap kehadiran
        $data_rekap = $this->Laporan_model->get_rekap_kehadiran_guru($bulan, $tahun);

        // Build HTML content
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Kehadiran Guru</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            margin: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 5px;
            font-size: 8px;
        }
        th {
            text-align: center;
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .bold {
            font-weight: bold;
        }
        .margin-top-20 {
            margin-top: 20px;
        }
        .margin-top-50 {
            margin-top: 50px;
        }
    </style>
</head>
<body>';

        // Generate header with sekolah data
        $html .= generate_pdf_header($pdf, 'REKAP KEHADIRAN GURU');

        // Add periode information
        $html .= '<p class="bold">Periode: ' . $this->_get_nama_bulan($bulan) . ' ' . $tahun . '</p>';
        $html .= '<div class="margin-top-20"></div>';

        // Prepare table data
        $headers = ['No', 'Nama Guru', 'NIP', 'Total Jurnal', 'Total Siswa'];
        $table_data = [];
        $no = 1;

        foreach ($data_rekap as $rekap) {
            $table_data[] = [
                $no++,
                $rekap->nama_guru,
                $rekap->nip,
                $rekap->total_jurnal,
                $rekap->total_siswa
            ];
        }

        // Generate table HTML
        $html .= generate_table_html($headers, $table_data, [10, 50, 30, 25, 25]);

        // Generate footer with signature
        $html .= generate_pdf_footer($pdf, 'Jakarta', format_tanggal_indo(date('Y-m-d')));

        $html .= '</body></html>';

        // Load HTML to DomPDF
        $pdf->loadHtml($html);
        $pdf->render();

        // Return PDF content
        return $pdf->output();
    }

    /**
     * Helper function untuk nama bulan
     * @param string $bulan
     * @return string
     */
    private function _get_nama_bulan($bulan)
    {
        $nama_bulan = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        );

        return isset($nama_bulan[$bulan]) ? $nama_bulan[$bulan] : '';
    }
}
