<?php

if (!function_exists('indo_full_date')) {
    function indo_full_date($date)
    {
        if (!$date || $date == "0000-00-00") {
            return "-";
        }

        // Hari dalam bahasa Indonesia
        $days = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        ];

        // Bulan bahasa Indonesia
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $timestamp = strtotime($date);
        $dayName   = $days[date('l', $timestamp)];
        $day       = date('d', $timestamp);
        $month     = $months[(int) date('m', $timestamp)];
        $year      = date('Y', $timestamp);

        return "$dayName, $day $month $year";
    }
}
