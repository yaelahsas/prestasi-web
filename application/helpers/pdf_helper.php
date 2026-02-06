<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('get_sekolah_data')) {
    /**
     * Get sekolah data for PDF header
     * @return array|null
     */
    function get_sekolah_data()
    {
        $CI = &get_instance();
        $CI->load->model('Sekolah_model');
        return $CI->Sekolah_model->get_sekolah_for_pdf();
    }
}

if (!function_exists('generate_pdf_header')) {
    /**
     * Generate PDF header with sekolah data
     * @param object $pdf PDF object (TCPDF/FPDF/etc)
     * @param string $title Document title
     * @return string HTML content for DomPDF
     */
    function generate_pdf_header($pdf, $title = '')
    {
        $sekolah = get_sekolah_data();
        $html = '';

        if ($sekolah) {
            $html .= '<div style="text-align: center; margin-bottom: 20px;">';

            // Add logo if exists
            if ($sekolah['logo']) {
                $logo_path = base_url() . 'assets/uploads/logo/' . $sekolah['logo'];
                $html .= '<img src="' . $logo_path . '" style="width: 60px; height: auto; margin-bottom: 10px;" />';
            }

            // Add school name
            $html .= '<h2 style="margin: 5px 0; font-weight: bold;">' . $sekolah['nama_sekolah'] . '</h2>';

            // Add address
            $html .= '<p style="margin: 5px 0; font-size: 10px;">' . $sekolah['alamat'] . '</p>';

            // Add line
            $html .= '<hr style="margin: 10px 0;" />';

            // Add document title
            if ($title) {
                $html .= '<h3 style="margin: 10px 0; font-weight: bold;">' . $title . '</h3>';
            }

            $html .= '</div>';
        }

        return $html;
    }
}

if (!function_exists('generate_pdf_footer')) {
    /**
     * Generate PDF footer with tanda tangan
     * @param object $pdf PDF object (TCPDF/FPDF/etc)
     * @param string $location Location for signature
     * @param string $date Date for signature
     * @return string HTML content for DomPDF
     */
    function generate_pdf_footer($pdf, $location = '', $date = '')
    {
        $sekolah = get_sekolah_data();
        $html = '';

        if ($sekolah) {

            $html .= '<div style="margin-top: 40px;">';

            $html .= '
        <table style="
            width:100%;
            border:none;
            border-collapse: collapse;
        ">
            <tr>
                <td style="width:60%; border:none; vertical-align:top;">';

            // ===== BAGIAN LOKASI & TANGGAL =====
            if (!empty($location) || !empty($date)) {
                $html .= '<div style="text-align: right; margin-bottom: 10px;">';

                if (!empty($location) && !empty($date)) {
                    $html .= '<p style="margin: 3px 0;">' . $location . ', ' . $date . '</p>';
                } else if (!empty($location)) {
                    $html .= '<p style="margin: 3px 0;">' . $location . '</p>';
                } else if (!empty($date)) {
                    $html .= '<p style="margin: 3px 0;">' . $date . '</p>';
                }

                $html .= '</div>';
            }

            // ===== GENERATE QR CODE =====
            $qr_text = "QR Code ini merupakan penanda keaslian laporan.\nTanggal Cetak: " . format_tanggal_indo(date('Y-m-d')) . " " . date('H:i') . ".\nSistem dikembangkan oleh Sastra.";

            $qr_base64 = generate_simple_qr_base64($qr_text);

            // ===== BAGIAN TANDA TANGAN =====
            $html .= '<table style="width: 100%;  border:none;">';
            $html .= '<tr>';
            $html .= '<td style="width: 60%;"></td>';

            $html .= '<td style="width: 40%; text-align: right; vertical-align: top;">';
            $html .= '<p style="margin: 5px 0;">Mengetahui,</p>';
            $html .= '<p style="margin: 5px 0;">Kepala Sekolah</p>';

            // ===== TAMPILKAN QR CODE =====
            if (!empty($qr_base64)) {
                $html .= '<div style="margin: 10px 0;">';
                $html .= '<img src="' . $qr_base64 . '" style="width: 100px; height: 100px;" />';
                $html .= '</div>';
            } else {
                // fallback kalau gagal generate QR
                $html .= '<div style="height: 100px;"></div>';
            }

            // ===== NAMA & NIP =====
            $html .= '<p style="margin: 5px 0; font-weight: bold;">' . $sekolah['kepala_sekolah'] . '</p>';
            $html .= '<p style="margin: 5px 0; font-size: 9px;">NIP. ' . $sekolah['nip_kepsek'] . '</p>';

            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $html .= '</div>';
        }

        return $html;
    }
}

if (!function_exists('format_tanggal_indo')) {
    /**
     * Format tanggal ke bahasa Indonesia
     * @param string $tanggal Tanggal dalam format Y-m-d
     * @return string Tanggal dalam format Indonesia
     */
    function format_tanggal_indo($tanggal)
    {
        if ($tanggal == '0000-00-00' || empty($tanggal)) {
            return '';
        }

        $bulan = array(
            1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );

        $pecahkan = explode('-', $tanggal);

        return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
    }
}
function generate_simple_qr_base64($text)
{
    $text = urlencode($text);
    $api = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={$text}";

    $data = @file_get_contents($api);

    if ($data !== false) {
        return 'data:image/png;base64,' . base64_encode($data);
    }

    return '';
}


if (!function_exists('generate_table_html')) {
    /**
     * Generate HTML table for DomPDF
     * @param array $headers Table headers
     * @param array $data Table data
     * @param array $widths Column widths (optional)
     * @return string HTML table
     */
    function generate_table_html($headers, $data, $widths = [])
    {
        $html = '<table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">';

        // Table headers
        $html .= '<tr>';
        foreach ($headers as $i => $header) {
            $width = isset($widths[$i]) ? 'width: ' . $widths[$i] . 'px;' : '';
            $html .= '<th style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold; font-size: 9px; ' . $width . '">' . $header . '</th>';
        }
        $html .= '</tr>';

        // Table data
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $i => $cell) {
                $align = is_numeric($cell) ? 'text-align: center;' : 'text-align: left;';
                $html .= '<td style="border: 1px solid #000; padding: 4px; font-size: 8px; ' . $align . '">' . $cell . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }
}
