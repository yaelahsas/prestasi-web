<?php
// defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
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

if (!function_exists('optimize_image_for_pdf')) {
    /**
     * Optimize image for PDF by resizing and compressing
     * @param string $image_path Path to the image file
     * @param int $max_width Maximum width (default 400px)
     * @param int $max_height Maximum height (default 300px)
     * @param int $quality JPEG quality (0-100, default 75)
     * @return string Base64 encoded optimized image
     */
    function optimize_image_for_pdf($image_path, $max_width = 400, $max_height = 300, $quality = 75)
    {
        if (!file_exists($image_path)) {
            return '';
        }

        try {
            // Get image info
            $image_info = getimagesize($image_path);
            if (!$image_info) {
                return '';
            }

            $mime_type = $image_info['mime'];
            $width = $image_info[0];
            $height = $image_info[1];

            // Calculate new dimensions
            $ratio = min($max_width / $width, $max_height / $height);
            $new_width = (int)($width * $ratio);
            $new_height = (int)($height * $ratio);

            // Create image resource based on mime type
            switch ($mime_type) {
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($image_path);
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($image_path);
                    break;
                case 'image/gif':
                    $source = imagecreatefromgif($image_path);
                    break;
                default:
                    // If unsupported type, just return base64 of original
                    $data = file_get_contents($image_path);
                    return 'data:image/jpeg;base64,' . base64_encode($data);
            }

            if (!$source) {
                return '';
            }

            // Create new image
            $new_image = imagecreatetruecolor($new_width, $new_height);

            // Handle transparency for PNG
            if ($mime_type == 'image/png') {
                imagealphablending($new_image, false);
                imagesavealpha($new_image, true);
                $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
                imagefilledrectangle($new_image, 0, 0, $new_width, $new_height, $transparent);
            }

            // Resize image
            imagecopyresampled($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            // Capture output
            ob_start();
            imagejpeg($new_image, null, $quality);
            $image_data = ob_get_contents();
            ob_end_clean();

            // Clean up
            imagedestroy($source);
            imagedestroy($new_image);

            return 'data:image/jpeg;base64,' . base64_encode($image_data);
        } catch (Exception $e) {
            // Fallback to original image if optimization fails
            $data = file_get_contents($image_path);
            return 'data:image/jpeg;base64,' . base64_encode($data);
        }
    }
}

if (!function_exists('generate_image_grid_html')) {
    /**
     * Generate HTML for 6-image grid layout with page breaks
     * @param array $images Array of image data with info
     * @return string HTML for image grid
     */
    function generate_image_grid_html($images)
    {
        $html = '';
        $image_count = count($images);

        if ($image_count == 0) {
            return '';
        }

        // Process images in groups of 6 with page breaks
        for ($i = 0; $i < $image_count; $i += 6) {
            // Add page break except for first page
            if ($i > 0) {
                $html .= '<div style="page-break-before: always;"></div>';
            }

            // Start grid container for this page
            $html .= '<div style="margin: 15px 0;">';

            // Create 3x2 grid for 6 images
            $html .= '<table style="width: 100%; border-collapse: collapse; height: 750px;">';

            // Process 3 rows with 2 images each
            for ($row = 0; $row < 3; $row++) {
                $html .= '<tr style="height: 33.33%;">';

                // Process 2 images per row
                for ($col = 0; $col < 2; $col++) {
                    $img_index = $i + ($row * 2) + $col;

                    if ($img_index < $image_count) {
                        $img = $images[$img_index];
                        $html .= '<td style="width: 50%; vertical-align: middle; padding: 8px; text-align: center; border: 1px solid #ddd;">';

                        // Image info
                        $html .= '<div style="font-size: 8px; margin-bottom: 6px; line-height: 1.1;">';
                        $html .= '<b>' . date('d/m/Y', strtotime($img->tanggal)) . '</b><br>';
                        $html .= (isset($img->nama_guru) ? $img->nama_guru : 'N/A') . '<br>';
                        $html .= (isset($img->nama_kelas) ? $img->nama_kelas : 'N/A') . '<br>';
                        $html .= substr(isset($img->materi) ? $img->materi : 'N/A', 0, 20) . (strlen(isset($img->materi) ? $img->materi : 'N/A') > 20 ? '...' : '');
                        $html .= '</div>';

                        // Optimized image with larger size
                        $path = FCPATH . 'assets/uploads/foto_kegiatan/' . $img->foto_bukti;
                        $optimized_image = optimize_image_for_pdf($path, 280, 200, 75);

                        if ($optimized_image) {
                            $html .= '<img src="' . $optimized_image . '" style="max-width: 280px; max-height: 200px; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" />';
                        }

                        $html .= '</td>';
                    } else {
                        // Empty cell
                        $html .= '<td style="width: 50%; padding: 8px; border: 1px solid #ddd;"></td>';
                    }
                }



                $html .= '</tr>';
            }

            $html .= '</table>';

            $html .= '</div>';
        }

        return $html;
    }
}
if (!function_exists('generate_pdf_copyright')) {
    /**
     * Generate advanced secured PDF footer
     * @param string $printed_by
     * @param string $document_id
     * @param string $system_version
     * @return string
     */
    function generate_pdf_copyright($printed_by = 'System', $document_id = '', $system_version = 'v1.0')
    {
        $year        = date('Y');
        $timestamp   = date('d-m-Y H:i:s');
        $hash        = strtoupper(substr(hash('sha256', $document_id . $timestamp . $printed_by), 0, 12));

        $html = '
        <div style="
            position: fixed;
            bottom: 12px;
            left: 0;
            right: 0;
            font-size: 8px;
            color: #777;
            border-top: 0.5px solid #ddd;
            padding: 6px 20px 0 20px;
            font-family: Arial, sans-serif;
        ">
            <table width="100%" style="border-collapse: collapse;">
                <tr>
                    <td style="text-align: left;">
                        🔒 <strong>Sistem Prestasi</strong> © ' . $year . '
                    </td>
                    <td style="text-align: center;">
                        Dicetak: ' . $timestamp . ' | Oleh: ' . $printed_by . '
                    </td>
                    <td style="text-align: right;">
                        Versi: ' . $system_version . ' | ID: ' . $hash . '
                    </td>
                </tr>
            </table>
        </div>';

        return $html;
    }
}
