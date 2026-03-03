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

            // Add journal title
            $html .= '<h1 style="margin: 5px 0; font-weight: bold; font-size: 16px;">JURNAL BIMBINGAN KELAS UNGGULAN AKADEMIK</h1>';

            // Add school name
            $html .= '<h2 style="margin: 5px 0; font-weight: bold; font-size: 14px;">MTsN 3 BANYUWANGI</h2>';

            // Add academic year (automatic calculation)
            $current_year = date('Y');
            $next_year = $current_year + 1;
            $html .= '<p style="margin: 5px 0; font-size: 12px;">Tahun Pelajaran ' . $current_year . '/' . $next_year . '</p>';

            // Add line
            $html .= '<hr style="margin: 10px 0;" />';

            // Add document title if provided
            if ($title) {
                $html .= '<h3 style="margin: 10px 0; font-weight: bold;">' . strtoupper($title) . '</h3>';
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
                <td style="width:50%; border:none; vertical-align:top;">';
            $html .= '</td>';
            $html .= '<td style="width:50%; border:none; vertical-align:top; text-align: right;">';

            // ===== BAGIAN LOKASI & TANGGAL =====
            if (!empty($location) || !empty($date)) {
                $html .= '<div style="text-align: right; margin-bottom: 10px; margin-right: 5px;">';

                if (!empty($location) && !empty($date)) {
                    $html .= '<p style="margin: 5px 0;">' . $location . ', ' . $date . '</p>';
                } else if (!empty($location)) {
                    $html .= '<p style="margin: 3px 0;">' . $location . '</p>';
                } else if (!empty($date)) {
                    $html .= '<p style="margin: 3px 0;">' . $date . '</p>';
                }

                $html .= '</div>';
            }

            // ===== BAGIAN TANDA TANGAN =====
            $html .= '<div style="display: inline-block; text-align: left;">';
            $html .= '<p style="margin: 5px 0;">Mengetahui,</p>';
            $html .= '<p style="margin: 5px 0;">Pengurus</p>';

            $html .= '<div style="height: 60px;"></div>';

            // ===== NAMA & NIP =====
            $html .= '<p style="margin: 5px 0; font-weight: bold;">Iffatul Hasanah, S.Pd.</p>';
            $html .= '<p style="margin: 5px 0; font-size: 9px;">NIP. - </p>';

            $html .= '</div>';

            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';
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

