<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Cek login
        if(!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        
        $this->load->model('Guru_model');
        $this->load->model('Sekolah_model');
        $this->load->model('Kelas_model');
        $this->load->model('Mapel_model');
        $this->load->model('Laporan_model');
        $this->load->helper(array('pdf_helper', 'date'));
    }

    // Halaman utama laporan
    public function index()
    {
        $data['user'] = $this->session->userdata();

        $data['title'] = 'Laporan';
        $data['guru'] = $this->Guru_model->get_all_guru();
        $data['kelas'] = $this->Kelas_model->get_all_kelas();
        $data['mapel'] = $this->Mapel_model->get_all_mapel();
        
        $this->load->view('laporan/index', $data);
    }

    // Cetak laporan jurnal bulanan
    public function cetak_jurnal_bulanan()
    {
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        
        if (!$bulan || !$tahun) {
            $bulan = date('m');
            $tahun = date('Y');
        }
        
        // Get data jurnal per bulan
        $data_jurnal = $this->Laporan_model->get_jurnal_by_bulan_tahun($bulan, $tahun);
        $sekolah = $this->Sekolah_model->get_sekolah_for_pdf();
        
        // Load DomPDF library
        $this->load->library('dompdf');
        
        // Create new PDF document
        $pdf = new Dompdf();
        $pdf->setPaper('A4', 'portrait');
        
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
        $html .= '<p class="text-center bold">Periode: ' . $this->get_nama_bulan($bulan) . ' ' . $tahun . '</p>';
        
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
        $html .= generate_pdf_footer($pdf, 'Jakarta', format_tanggal_indo(date('Y-m-d')));
        
        $html .= '</body></html>';
        
        // Load HTML to DomPDF
        $pdf->loadHtml($html);
        $pdf->render();
        
        // Output PDF
        $pdf->stream('laporan_jurnal_' . $bulan . '_' . $tahun . '.pdf', ['Attachment' => 0]);
    }

    // Cetak laporan per guru
    public function cetak_laporan_guru()
    {
        $id_guru = $this->input->get('id_guru');
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        
        if (!$id_guru || !$bulan || !$tahun) {
            show_error('Parameter tidak lengkap');
        }
        
        // Get data jurnal per guru
        $data_jurnal = $this->Laporan_model->get_jurnal_by_guru($id_guru, $bulan, $tahun);
        $sekolah = $this->Sekolah_model->get_sekolah_for_pdf();
        
        // Get guru info
        $guru_info = $this->db->get_where('bimbel_guru', array('id_guru' => $id_guru))->row();
        
        // Load DomPDF library
        $this->load->library('dompdf');
        
        // Create new PDF document
        $pdf = new Dompdf();
        $pdf->setPaper('A4', 'portrait');
        
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
        $html .= '<p class="bold">Periode: ' . $this->get_nama_bulan($bulan) . ' ' . $tahun . '</p>';
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
        $html .= generate_pdf_footer($pdf, 'Jakarta', format_tanggal_indo(date('Y-m-d')));
        
        $html .= '</body></html>';
        
        // Load HTML to DomPDF
        $pdf->loadHtml($html);
        $pdf->render();
        
        // Output PDF
        $pdf->stream('laporan_guru_' . $id_guru . '_' . $bulan . '_' . $tahun . '.pdf', ['Attachment' => 0]);
    }

    // Cetak laporan per kelas
    public function cetak_laporan_kelas()
    {
        $id_kelas = $this->input->get('id_kelas');
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        
        if (!$id_kelas || !$bulan || !$tahun) {
            show_error('Parameter tidak lengkap');
        }
        
        // Get data jurnal per kelas
        $data_jurnal = $this->Laporan_model->get_jurnal_by_kelas($id_kelas, $bulan, $tahun);
        $sekolah = $this->Sekolah_model->get_sekolah_for_pdf();
        
        // Get kelas info
        $kelas_info = $this->db->get_where('bimbel_kelas', array('id_kelas' => $id_kelas))->row();
        
        // Load DomPDF library
        $this->load->library('dompdf');
        
        // Create new PDF document
        $pdf = new Dompdf();
        $pdf->setPaper('A4', 'portrait');
        
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
        $html .= '<p class="bold">Periode: ' . $this->get_nama_bulan($bulan) . ' ' . $tahun . '</p>';
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
        
        // Output PDF
        $pdf->stream('laporan_kelas_' . $id_kelas . '_' . $bulan . '_' . $tahun . '.pdf', ['Attachment' => 0]);
    }

    // Cetak laporan per mapel
    public function cetak_laporan_mapel()
    {
        $id_mapel = $this->input->get('id_mapel');
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        
        if (!$id_mapel || !$bulan || !$tahun) {
            show_error('Parameter tidak lengkap');
        }
        
        // Get data jurnal per mapel
        $data_jurnal = $this->Laporan_model->get_jurnal_by_mapel($id_mapel, $bulan, $tahun);
        $sekolah = $this->Sekolah_model->get_sekolah_for_pdf();
        
        // Get mapel info
        $mapel_info = $this->db->get_where('bimbel_mapel', array('id_mapel' => $id_mapel))->row();
        
        // Load DomPDF library
        $this->load->library('dompdf');
        
        // Create new PDF document
        $pdf = new Dompdf();
        $pdf->setPaper('A4', 'portrait');
        
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
        $html .= '<p class="bold">Periode: ' . $this->get_nama_bulan($bulan) . ' ' . $tahun . '</p>';
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
        
        // Output PDF
        $pdf->stream('laporan_mapel_' . $id_mapel . '_' . $bulan . '_' . $tahun . '.pdf', ['Attachment' => 0]);
    }

    // Cetak rekap kehadiran guru
    public function cetak_rekap_kehadiran()
    {
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        
        if (!$bulan || !$tahun) {
            show_error('Parameter tidak lengkap');
        }
        
        // Get data rekap kehadiran
        $data_rekap = $this->Laporan_model->get_rekap_kehadiran_guru($bulan, $tahun);
        $sekolah = $this->Sekolah_model->get_sekolah_for_pdf();
        
        // Load DomPDF library
        $this->load->library('dompdf');
        
        // Create new PDF document
        $pdf = new Dompdf();
        $pdf->setPaper('A4', 'portrait');
        
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
        $html .= '<p class="bold">Periode: ' . $this->get_nama_bulan($bulan) . ' ' . $tahun . '</p>';
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
        
        // Output PDF
        $pdf->stream('rekap_kehadiran_' . $bulan . '_' . $tahun . '.pdf', ['Attachment' => 0]);
    }

    // Helper function untuk nama bulan
    private function get_nama_bulan($bulan) {
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

    // API method untuk statistik jurnal
    public function get_statistik_jurnal()
    {
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        
        if (!$bulan || !$tahun) {
            $bulan = date('m');
            $tahun = date('Y');
        }
        
        $statistik = $this->Laporan_model->get_statistik_jurnal($bulan, $tahun);
        
        echo json_encode([
            'success' => true,
            'data' => $statistik
        ]);
    }
}