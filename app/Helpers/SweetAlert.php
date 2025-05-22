<?php

namespace App\Helpers;

class SweetAlert
{
    public static function success($title, $message = null)
    {
        session()->flash('swal', [
            'type' => 'success',
            'title' => $title,
            'text' => $message
        ]);
    }

    public static function error($title, $message = null)
    {
        session()->flash('swal', [
            'type' => 'error',
            'title' => $title,
            'text' => $message
        ]);
    }

    public static function warning($title, $message = null)
    {
        session()->flash('swal', [
            'type' => 'warning',
            'title' => $title,
            'text' => $message
        ]);
    }

    public static function info($title, $message = null)
    {
        session()->flash('swal', [
            'type' => 'info',
            'title' => $title,
            'text' => $message
        ]);
    }
}