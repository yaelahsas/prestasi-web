<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Image Helper
 * Fungsi-fungsi untuk kompresi dan manipulasi gambar
 */

if (!function_exists('compress_image')) {
    /**
     * Kompres gambar menggunakan GD Library
     *
     * @param string $source_path  Path file sumber (absolut)
     * @param string $dest_path    Path file tujuan (absolut), null = overwrite sumber
     * @param int    $max_width    Lebar maksimum (px), 0 = tidak resize
     * @param int    $max_height   Tinggi maksimum (px), 0 = tidak resize
     * @param int    $quality      Kualitas JPEG (1-100), default 75
     * @return bool
     */
    function compress_image($source_path, $dest_path = null, $max_width = 1200, $max_height = 1200, $quality = 75)
    {
        if (!file_exists($source_path)) {
            log_message('error', 'compress_image: file tidak ditemukan: ' . $source_path);
            return false;
        }

        if ($dest_path === null) {
            $dest_path = $source_path;
        }

        // Deteksi tipe gambar
        $image_info = @getimagesize($source_path);
        if (!$image_info) {
            log_message('error', 'compress_image: bukan file gambar valid: ' . $source_path);
            return false;
        }

        $mime_type = $image_info['mime'];
        $orig_width  = $image_info[0];
        $orig_height = $image_info[1];

        // Load gambar berdasarkan tipe
        switch ($mime_type) {
            case 'image/jpeg':
            case 'image/jpg':
                $source_image = @imagecreatefromjpeg($source_path);
                break;
            case 'image/png':
                $source_image = @imagecreatefrompng($source_path);
                break;
            case 'image/gif':
                $source_image = @imagecreatefromgif($source_path);
                break;
            case 'image/webp':
                $source_image = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source_path) : false;
                break;
            default:
                log_message('error', 'compress_image: tipe gambar tidak didukung: ' . $mime_type);
                return false;
        }

        if (!$source_image) {
            log_message('error', 'compress_image: gagal membaca gambar: ' . $source_path);
            return false;
        }

        // Hitung dimensi baru (proportional resize)
        $new_width  = $orig_width;
        $new_height = $orig_height;

        if ($max_width > 0 || $max_height > 0) {
            $ratio = $orig_width / $orig_height;

            if ($max_width > 0 && $orig_width > $max_width) {
                $new_width  = $max_width;
                $new_height = (int)round($max_width / $ratio);
            }

            if ($max_height > 0 && $new_height > $max_height) {
                $new_height = $max_height;
                $new_width  = (int)round($max_height * $ratio);
            }
        }

        // Buat canvas baru
        $dest_image = imagecreatetruecolor($new_width, $new_height);

        // Handle transparansi untuk PNG dan GIF
        if ($mime_type === 'image/png' || $mime_type === 'image/gif') {
            imagealphablending($dest_image, false);
            imagesavealpha($dest_image, true);
            $transparent = imagecolorallocatealpha($dest_image, 255, 255, 255, 127);
            imagefilledrectangle($dest_image, 0, 0, $new_width, $new_height, $transparent);
        } else {
            // Background putih untuk JPEG
            $white = imagecolorallocate($dest_image, 255, 255, 255);
            imagefilledrectangle($dest_image, 0, 0, $new_width, $new_height, $white);
        }

        // Resize gambar
        imagecopyresampled(
            $dest_image, $source_image,
            0, 0, 0, 0,
            $new_width, $new_height,
            $orig_width, $orig_height
        );

        // Simpan sebagai JPEG (semua format dikonversi ke JPEG untuk efisiensi)
        $result = imagejpeg($dest_image, $dest_path, $quality);

        // Bersihkan memory
        imagedestroy($source_image);
        imagedestroy($dest_image);

        if ($result) {
            log_message('debug', sprintf(
                'compress_image: %s -> %s (%dx%d -> %dx%d, quality=%d, size=%s KB)',
                basename($source_path),
                basename($dest_path),
                $orig_width, $orig_height,
                $new_width, $new_height,
                $quality,
                round(filesize($dest_path) / 1024, 1)
            ));
        }

        return $result;
    }
}

if (!function_exists('compress_image_from_string')) {
    /**
     * Kompres gambar dari binary string (untuk upload base64 dari API)
     *
     * @param string $image_data   Binary data gambar
     * @param string $dest_path    Path file tujuan (absolut)
     * @param int    $max_width    Lebar maksimum (px)
     * @param int    $max_height   Tinggi maksimum (px)
     * @param int    $quality      Kualitas JPEG (1-100)
     * @return bool
     */
    function compress_image_from_string($image_data, $dest_path, $max_width = 1200, $max_height = 1200, $quality = 75)
    {
        // Tulis ke file temp dulu
        $temp_path = sys_get_temp_dir() . '/img_' . uniqid() . '.tmp';
        if (file_put_contents($temp_path, $image_data) === false) {
            return false;
        }

        $result = compress_image($temp_path, $dest_path, $max_width, $max_height, $quality);

        // Hapus file temp
        if (file_exists($temp_path)) {
            @unlink($temp_path);
        }

        return $result;
    }
}

if (!function_exists('get_image_size_kb')) {
    /**
     * Mendapatkan ukuran file gambar dalam KB
     *
     * @param string $filepath
     * @return float
     */
    function get_image_size_kb($filepath)
    {
        if (!file_exists($filepath)) return 0;
        return round(filesize($filepath) / 1024, 1);
    }
}
