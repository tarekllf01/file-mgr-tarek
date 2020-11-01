@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('/css/file-manager.css')}}">
@endsection
@section('content')
    @php
        defined('FM_SHOW_HIDDEN') || define('FM_SHOW_HIDDEN', $show_hidden_files);
        defined('FM_ROOT_PATH') || define('FM_ROOT_PATH', $root_path);
        defined('FM_LANG') || define('FM_LANG', $lang);
        defined('FM_FILE_EXTENSION') || define('FM_FILE_EXTENSION', $allowed_file_extensions);
        defined('FM_UPLOAD_EXTENSION') || define('FM_UPLOAD_EXTENSION', $allowed_upload_extensions);
        defined('FM_EXCLUDE_ITEMS') || define('FM_EXCLUDE_ITEMS', $exclude_items);
        defined('FM_DOC_VIEWER') || define('FM_DOC_VIEWER', $online_viewer);
        define('FM_READONLY', $use_auth && !empty($readonly_users) && isset($_SESSION[FM_SESSION_ID]['logged']) && in_array($_SESSION[FM_SESSION_ID]['logged'], $readonly_users));
        define('FM_IS_WIN', DIRECTORY_SEPARATOR == '\\');
        define('MAX_UPLOAD_SIZE', $max_upload_size_bytes);
        define('FM_THEME', $theme);

        if (!isset($_GET['p']) && empty($_FILES)) {
        fm_redirect(FM_SELF_URL . '?p=');
        }
        // get path
        $p = isset($_GET['p']) ? $_GET['p'] : (isset($_POST['p']) ? $_POST['p'] : '');
        // clean path
        $p = fm_clean_path($p);
        // for ajax request - save
        $input = file_get_contents('php://input');
        $_POST = (strpos($input, 'ajax') != FALSE && strpos($input, 'save') != FALSE) ? json_decode($input, true) : $_POST;
        // instead globals vars
        define('FM_PATH', $p);
        define('FM_USE_AUTH', $use_auth);
        define('FM_EDIT_FILE', $edit_files);
        defined('FM_ICONV_INPUT_ENC') || define('FM_ICONV_INPUT_ENC', $iconv_input_encoding);
        defined('FM_USE_HIGHLIGHTJS') || define('FM_USE_HIGHLIGHTJS', $use_highlightjs);
        defined('FM_HIGHLIGHTJS_STYLE') || define('FM_HIGHLIGHTJS_STYLE', 'vs');
        defined('FM_DATETIME_FORMAT') || define('FM_DATETIME_FORMAT', $datetime_format);
        unset($p, $use_auth, $iconv_input_encoding, $use_highlightjs);
    @endphp
    <div class="container-fluid">

    </div>
@endsection

@section('scripts')


@endsection
