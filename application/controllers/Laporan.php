<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laporan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Cek login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }

        $this->load->model('Guru_model');
        $this->load->model('Sekolah_model');
        $this->load->model('Kelas_model');
        $this->load->model('Mapel_model');
        $this->load->model('Laporan_model');
        $this->load->helper(array('pdf_helper', 'date'));
        $this->load->helper('tanggal');
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

        // Ambil data jurnal per bulan
        $data_jurnal = $this->Laporan_model->get_jurnal_by_bulan_tahun($bulan, $tahun);

        // Load DomPDF
        $this->load->library('dompdf');

        $pdf = new Dompdf();
        $pdf->setPaper('A4', 'portrait');

        $hari_cetak = format_hari_indo(date('Y-m-d'));
        $tanggal_cetak = format_tanggal_indo(date('Y-m-d'));

        // Build HTML
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

    /* Tabel utama laporan */
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
.lampiran {
    margin-top: 20px;
    page-break-inside: avoid;
    border-bottom: 1px dashed #aaa;
    padding-bottom: 15px;
}

img {
    margin-top: 10px;
  
    padding: 5px;
}
</style>
</head>
<body>';

        // Header
        $html .= generate_pdf_header($pdf, 'LAPORAN JURNAL BULANAN');

        // Kotak informasi laporan
        $html .= '
<div class="info-box">
    <table style="width:100%; border:none !important; border-collapse:collapse !important;">
        <tr>
            <td style="border:none !important;width:20%;">Periode</td>
            <td style="border:none !important;width:2%;">:</td>
            <td style="border:none !important;width:78%;"><b>' . $this->get_nama_bulan($bulan) . ' ' . $tahun . '</b></td>
        </tr>
        <tr>
            <td style="border:none !important;">Hari Cetak</td>
            <td style="border:none !important;">:</td>
            <td style="border:none !important;">' . $hari_cetak . '</td>
        </tr>
        <tr>
            <td style="border:none !important;">Tanggal Cetak</td>
            <td style="border:none !important;">:</td>
            <td style="border:none !important;">' . $tanggal_cetak . '</td>
        </tr>
        <tr>
            <td style="border:none !important;">Total Jurnal</td>
            <td style="border:none !important;">:</td>
            <td style="border:none !important;"><b>' . count($data_jurnal) . ' Data</b></td>
        </tr>
    </table>
</div>';

        // Tabel utama laporan (tanpa kolom siswa)
        $headers = ['No', 'Tanggal', 'Kelas', 'Mapel', 'Guru', 'Materi', 'Penginput'];
        $table_data = [];
        $no = 1;

        foreach ($data_jurnal as $jurnal) {
            $table_data[] = [
                $no++,
                date('d/m/Y', strtotime($jurnal->tanggal)),
                $jurnal->nama_kelas,
                $jurnal->nama_mapel,
                $jurnal->nama_guru,
                $jurnal->materi,
                $jurnal->nama_penginput
            ];
        }

        // Panggil generate table dengan class khusus
        $html .= generate_table_html($headers, $table_data, [10, 20, 25, 25, 30, 60, 25], 'main-table');

        // Footer + QR Code
        $html .= generate_pdf_footer($pdf, 'Srono', $tanggal_cetak);

        // ================== LAMPIRAN FOTO ==================
        $html .= '<div style="page-break-before: always;"></div>';

        $html .= '<h3 class="text-center">LAMPIRAN BUKTI JURNAL</h3>';
        $html .= '<p class="text-center">Periode: <b>' . $this->get_nama_bulan($bulan) . ' ' . $tahun . '</b></p>';

        $ada_foto = false;

        foreach ($data_jurnal as $jurnal) {

            if (!empty($jurnal->foto_bukti)) {

                $path = FCPATH . 'assets/uploads/foto_kegiatan/' . $jurnal->foto_bukti;

                if (file_exists($path)) {

                    $ada_foto = true;

                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

                    $html .= '<div class="lampiran">';

                    $html .= '<table style="width:100%; border:none !important;">';
                    $html .= '<tr><td style="border:none !important;width:25%;"><b>Tanggal</b></td><td style="border:none !important;">: ' . date('d/m/Y', strtotime($jurnal->tanggal)) . '</td></tr>';
                    $html .= '<tr><td style="border:none !important;"><b>Guru</b></td><td style="border:none !important;">: ' . $jurnal->nama_guru . '</td></tr>';
                    $html .= '<tr><td style="border:none !important;"><b>Kelas</b></td><td style="border:none !important;">: ' . $jurnal->nama_kelas . '</td></tr>';
                    $html .= '<tr><td style="border:none !important;"><b>Mapel</b></td><td style="border:none !important;">: ' . $jurnal->nama_mapel . '</td></tr>';
                    $html .= '<tr><td style="border:none !important;"><b>Materi</b></td><td style="border:none !important;">: ' . $jurnal->materi . '</td></tr>';
                    $html .= '</table>';

                    $html .= '<div style="margin-top:10px;">';
                    $html .= '<img src="' . $base64 . '" style="max-width: 450px; max-height: 650px;" />';
                    $html .= '</div>';

                    $html .= '</div>';
                }
            }
        }

        if (!$ada_foto) {
            $html .= '<p class="text-center">Tidak ada lampiran foto pada periode ini.</p>';
        }

        $html .= '</body></html>';

        // Render PDF
        $pdf->loadHtml($html);
        $pdf->render();

        // Output
        $filename = 'laporan_jurnal_bulanan_' . $bulan . '_' . $tahun . '.pdf';

        $pdf->stream($filename, ['Attachment' => 0]);
    }



    public function cetak_laporan_guru()
    {
        $id_guru = $this->input->get('id_guru');
        $bulan   = $this->input->get('bulan');
        $tahun   = $this->input->get('tahun');

        if (!$id_guru || !$bulan || !$tahun) {
            show_error('Parameter tidak lengkap');
        }

        // Ambil data jurnal per guru
        $data_jurnal = $this->Laporan_model->get_jurnal_by_guru($id_guru, $bulan, $tahun);

        // Ambil info guru
        $guru_info = $this->db->get_where('bimbel_guru', ['id_guru' => $id_guru])->row();

        if (!$guru_info) {
            show_error('Data guru tidak ditemukan');
        }

        // Load DomPDF
        $this->load->library('dompdf');

        $pdf = new Dompdf();
        $pdf->setPaper('A4', 'portrait');

        $hari_cetak = format_hari_indo(date('Y-m-d'));
        $tanggal_cetak = format_tanggal_indo(date('Y-m-d'));

        // Build HTML
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

    /* Tabel utama laporan */
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
.lampiran {
    margin-top: 20px;
    page-break-inside: avoid;
    border-bottom: 1px dashed #aaa;
    padding-bottom: 15px;
}
img {
    margin-top: 10px;
  
    padding: 5px;
}
</style>
</head>
<body>';

        // Header sekolah
        $html .= generate_pdf_header($pdf, 'LAPORAN JURNAL GURU');

        // Kotak informasi laporan (tanpa border tabel)
        $html .= '
<div class="info-box">
    <table style="width:100%; border:none !important; border-collapse:collapse !important;">
        <tr>
            <td style="border:none !important;width:20%;">Nama Guru</td>
            <td style="border:none !important;width:2%;">:</td>
            <td style="border:none !important;width:78%;"><b>' . $guru_info->nama_guru . '</b></td>
        </tr>
        <tr>
            <td style="border:none !important;">NIP</td>
            <td style="border:none !important;">:</td>
            <td style="border:none !important;">' . $guru_info->nip . '</td>
        </tr>
        <tr>
            <td style="border:none !important;">Periode</td>
            <td style="border:none !important;">:</td>
            <td style="border:none !important;">' . $this->get_nama_bulan($bulan) . ' ' . $tahun . '</td>
        </tr>
        <tr>
            <td style="border:none !important;">Hari Cetak</td>
            <td style="border:none !important;">:</td>
            <td style="border:none !important;">' . $hari_cetak . '</td>
        </tr>
        <tr>
            <td style="border:none !important;">Tanggal Cetak</td>
            <td style="border:none !important;">:</td>
            <td style="border:none !important;">' . $tanggal_cetak . '</td>
        </tr>
        <tr>
            <td style="border:none !important;">Total Jurnal</td>
            <td style="border:none !important;">:</td>
            <td style="border:none !important;"><b>' . count($data_jurnal) . ' Data</b></td>
        </tr>
    </table>
</div>';

        // Tabel utama laporan
        $headers = ['No', 'Tanggal', 'Kelas', 'Mapel', 'Materi', 'Penginput'];
        $table_data = [];
        $no = 1;

        foreach ($data_jurnal as $jurnal) {
            $table_data[] = [
                $no++,
                date('d/m/Y', strtotime($jurnal->tanggal)),
                $jurnal->nama_kelas,
                $jurnal->nama_mapel,
                $jurnal->materi,
                $jurnal->nama_penginput
            ];
        }

        // Pakai class main-table biar border hanya untuk tabel utama
        $html .= generate_table_html($headers, $table_data, [10, 20, 25, 25, 60, 30], 'main-table');

        // Footer + QR Code
        $html .= generate_pdf_footer($pdf, 'Srono', $tanggal_cetak);

        // ================== LAMPIRAN FOTO ==================
        $html .= '<div style="page-break-before: always;"></div>';

        $html .= '<h3 class="text-center">LAMPIRAN BUKTI JURNAL</h3>';
        $html .= '<p class="text-center">Guru: <b>' . $guru_info->nama_guru . '</b></p>';
        $html .= '<p class="text-center">Periode: ' . $this->get_nama_bulan($bulan) . ' ' . $tahun . '</p>';

        $ada_foto = false;

        foreach ($data_jurnal as $jurnal) {

            if (!empty($jurnal->foto_bukti)) {

                $path = FCPATH . 'assets/uploads/foto_kegiatan/' . $jurnal->foto_bukti;

                if (file_exists($path)) {

                    $ada_foto = true;

                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

                    $html .= '<div class="lampiran">';

                    // Info foto tanpa border
                    $html .= '<table style="width:100%; border:none !important;">';
                    $html .= '<tr><td style="border:none !important;width:25%;"><b>Tanggal</b></td><td style="border:none !important;">: ' . date('d/m/Y', strtotime($jurnal->tanggal)) . '</td></tr>';
                    $html .= '<tr><td style="border:none !important;"><b>Kelas</b></td><td style="border:none !important;">: ' . $jurnal->nama_kelas . '</td></tr>';
                    $html .= '<tr><td style="border:none !important;"><b>Mapel</b></td><td style="border:none !important;">: ' . $jurnal->nama_mapel . '</td></tr>';
                    $html .= '<tr><td style="border:none !important;"><b>Materi</b></td><td style="border:none !important;">: ' . $jurnal->materi . '</td></tr>';
                    $html .= '</table>';

                    $html .= '<div style="margin-top:10px;">';
                    $html .= '<img src="' . $base64 . '" style="max-width: 450px; max-height: 650px;" />';
                    $html .= '</div>';

                    $html .= '</div>';
                }
            }
        }

        if (!$ada_foto) {
            $html .= '<p class="text-center">Tidak ada lampiran foto pada periode ini.</p>';
        }

        $html .= '</body></html>';

        // Render PDF
        $pdf->loadHtml($html);
        $pdf->render();

        // Output
        $filename = 'laporan_guru_' . str_replace(' ', '_', strtolower($guru_info->nama_guru)) . '_' . $bulan . '_' . $tahun . '.pdf';

        $pdf->stream($filename, ['Attachment' => 0]);
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
        $html .= generate_pdf_footer($pdf, 'Srono', format_tanggal_indo(date('Y-m-d')));

        // ===== BAGIAN LAMPIRAN FOTO =====
        $html .= '<div style="page-break-before: always;"></div>';

        $html .= '<h3 class="text-center">LAMPIRAN BUKTI JURNAL</h3>';
        $html .= '<p class="text-center">Periode: ' . $this->get_nama_bulan($bulan) . ' ' . $tahun . '</p>';

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
        $html .= generate_pdf_footer($pdf, 'Srono', format_tanggal_indo(date('Y-m-d')));

        // ===== BAGIAN LAMPIRAN FOTO =====
        $html .= '<div style="page-break-before: always;"></div>';

        $html .= '<h3 class="text-center">LAMPIRAN BUKTI JURNAL</h3>';
        $html .= '<p class="text-center">Periode: ' . $this->get_nama_bulan($bulan) . ' ' . $tahun . '</p>';

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
        $html .= generate_pdf_footer($pdf, 'Srono', format_tanggal_indo(date('Y-m-d')));

        $html .= '</body></html>';

        // Load HTML to DomPDF
        $pdf->loadHtml($html);
        $pdf->render();

        // Output PDF
        $pdf->stream('rekap_kehadiran_' . $bulan . '_' . $tahun . '.pdf', ['Attachment' => 0]);
    }

    // Helper function untuk nama bulan
    private function get_nama_bulan($bulan)
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
