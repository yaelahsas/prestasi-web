<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('format_hari_indo')) {
    function format_hari_indo($tanggal)
    {
        $hari = date('l', strtotime($tanggal));

        $nama_hari = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        ];

        return $nama_hari[$hari] ?? $hari;
    }
}

if (!function_exists('format_tanggal_indo')) {
    function format_tanggal_indo($tanggal)
    {
        $bulan = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $pecah = explode('-', $tanggal);

        return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
    }
}
