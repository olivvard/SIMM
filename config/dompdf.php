<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DomPDF Options
    |--------------------------------------------------------------------------
    */
    'show_warnings'        => false,
    'orientation'          => 'landscape',
    'defines'              => [],
    'dpi'                  => 96,
    'defaultFont'          => 'DejaVu Sans',
    'font_height_ratio'    => 1.1,
    'enable_php'           => false,
    'enable_remote'        => false,
    'enable_css_float'     => true,
    'enable_html5_parser'  => true,
    'log_output_file'      => null,
    'temp_dir'             => sys_get_temp_dir(),
    'chroot'               => realpath(base_path()),
    'allowed_protocols'    => [
        'data://'  => ['rules' => []],
        'file://'  => ['rules' => []],
        'http://'  => ['rules' => []],
        'https://' => ['rules' => []],
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Paper
    |--------------------------------------------------------------------------
    */
    'paper'    => env('DOMPDF_PAPER', 'a4'),

    /*
    |--------------------------------------------------------------------------
    | Public Storage Path (for embedded images)
    |--------------------------------------------------------------------------
    */
    'public_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Autorender on view
    |--------------------------------------------------------------------------
    */
    'convert_entities' => true,

];
