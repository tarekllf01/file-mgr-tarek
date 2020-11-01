<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;

class FileManagerController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
        $this->rootPath = $_SERVER['DOCUMENT_ROOT'].'/files';
        $this->rootUrl = $_SERVER['DOCUMENT_ROOT'].'/files';
    }

    public function index (Request $request) {
        if (!$request->hasAny('p') && empty($_FILES)) {
            // fm_redirect(FM_SELF_URL . '?p=');
            $url = route('files.manager');
            return redirect($url.'?p=');
        }
        // get path
        $p = $request->p ? $request->p :  '';
        // clean path
        $input = file_get_contents('php://input');
        $_POST = (strpos($input, 'ajax') != FALSE && strpos($input, 'save') != FALSE) ? json_decode($input, true) : $_POST;

        $data['dateFormat'] = "d.m.y H:i";
        $data['fmPath'] = $this->fmCleanManager($p);
        $data['rootPath'] = $_SERVER['DOCUMENT_ROOT'].'/files';
        $data['rootUrl'] = $_SERVER['DOCUMENT_ROOT'].'/files';
        $data['path'] = $data['rootPath'] . '/'. $data['fmPath']; // get current path
        $data['parent'] = $this->fmGetParentPath($data['path']);
        $objects = is_readable($data['path']) ? scandir($data['path']) : array();
        $folders = array();
        $files = array();
        $currentPath = array_slice(explode("/", $data['path']), -1)[0];
        if (is_array($objects) && $this->fmIsExcludesItems($currentPath)) {
            foreach ($objects as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if ( substr($file, 0, 1) === '.') {
                    continue;
                }
                $newPath = $data['path'] . '/' . $file;
                if (@is_file($newPath) && $this->fmIsExcludesItems($file)) {
                    $files[] = $file;
                } elseif (@is_dir($newPath) && $file != '.' && $file != '..' && $this->fmIsExcludesItems($file)) {
                    $folders[] = $file;
                }
            }
        } else {
            return "Excluded Item";
        }

        if (!empty($files)) {
            natcasesort($files);
        }
        if (!empty($folders)) {
            natcasesort($folders);
        }
        $data['files'] = $files;
        $data['folders'] = $folders;
        $data['numFiles'] = count($files);
        $date['numFolders'] = count($folders);

        return view('files.manager',$data);
    }

    public function rename (Request $request) {
        $request->validate([
            'ren' => 'required',
            'to' => 'required',
        ]);
        // old name
        $old = $request->ren;
        $old = $this->fmCleanPath($old);
        $old = str_replace('/', '', $old);
        // new name
        $new = $request->to;
        $new = $this->fmCleanPath(strip_tags($new));
        $new = str_replace('/', '', $new);
        // path
        $path = $this->rootPath;
        $fmPath = $this->getFmPath($request);
        if ($fmPath != '') {
            $path .= '/' . $fmPath;
        }
        // rename
        if ($this->isValidFileName($new) && $old != '' && $new != '') {
            $rename = $this->fileRename($path . '/' . $old, $path . '/' . $new);
            if ($rename) {
                // return "success";
                return back()->with(['message'=>'Renamed File','alert-type'=>'success']);
                // fm_set_msg(sprintf('Renamed from <b>%s</b> to <b>%s</b>', fm_enc($old), fm_enc($new)));
            }
            return "Error while renaming check file is allowed or note";

            return back()->with(['message'=>'Error while renaming check file is allowed or note','alert-type'=>'error']);
        }
        return "Invalid characters in file name";
        return back()->with(['message'=>'Invalid characters in file name','alert-type'=>'error']);
        // fm_redirect(FM_SELF_URL . '?p=' . urlencode($fmPath));

    }


    public function uploader (Request $request) {
        if (!$request->hasAny('p') && empty($_FILES)) {
            // fm_redirect(FM_SELF_URL . '?p=');
            $url = route('files.uploader');
            return redirect($url.'?p=');
        }
        // get path
        $p = $request->p ? $request->p :  '';
        // clean path
        $input = file_get_contents('php://input');
        $_POST = (strpos($input, 'ajax') != FALSE && strpos($input, 'save') != FALSE) ? json_decode($input, true) : $_POST;

        $data['dateFormat'] = "d.m.y H:i";
        $data['fmPath'] = $this->fmCleanManager($p);
        $data['rootPath'] = $_SERVER['DOCUMENT_ROOT'].'/files';
        $data['rootUrl'] = $_SERVER['DOCUMENT_ROOT'].'/files';
        $data['path'] = $data['rootPath'] . '/'. $data['fmPath']; // get current path
        $data['parent'] = $this->fmGetParentPath($data['path']);
        $data['extString'] = $this->getAllowedExtensionWithDots();
        $data['maxUpload'] = $this->maxUploadSIze();

        return view('files.uploader',$data);
    }

    public function uploaderAjax (Request $request) {
        if (!$request->hasAny('p') && empty($_FILES)) {
            // fm_redirect(FM_SELF_URL . '?p=');
            $url = route('files.uploader');
            return redirect($url.'?p=');
        }
        // get path
        $p = $request->p ? $request->p :  '';
        // clean path
        $input = file_get_contents('php://input');
        $_POST = (strpos($input, 'ajax') != FALSE && strpos($input, 'save') != FALSE) ? json_decode($input, true) : $_POST;

        if (!empty($_FILES) ) {
            $overrideFileName = true;
            $f = $_FILES;
            $path = $_SERVER['DOCUMENT_ROOT'].'/files';
            $ds = "\\";
            $fmPath = $this->fmCleanManager($p);
            if ($fmPath != '') {
                $path .= '/' . $fmPath;
            }

            $errors = 0;
            $uploads = 0;
            $response = array(
                'status' => 'error',
                'info'   => 'Oops! Try again'
            );

            $filename = $f['file']['name'];
            $tmp_name = $f['file']['tmp_name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $isFileAllowed = $this->isValidExt($ext);

            $targetPath = $path . $ds;
            if (is_writable($targetPath)) {
                $fullPath = $path . '/' . $_REQUEST['fullpath'];
                $folder = substr($fullPath, 0, strrpos($fullPath, "/"));

                if (file_exists($fullPath) && !$overrideFileName) {
                    $ext_1 = $ext ? '.' . $ext : '';
                    $fullPath = str_replace($ext_1, '', $fullPath) . '_' . date('ymdHis') . $ext_1;
                }

                if (!is_dir($folder)) {
                    $old = umask(0);
                    mkdir($folder, 0777, true);
                    umask($old);
                }

                if (empty($f['file']['error']) && !empty($tmp_name) && $tmp_name != 'none' && $isFileAllowed) {
                    if (move_uploaded_file($tmp_name, $fullPath)) {
                        // Be sure that the file has been uploaded
                        if (file_exists($fullPath)) {
                            $response = array(
                                'status'    => 'success',
                                'info' => "file upload successful"
                            );
                        } else {
                            $response = array(
                                'status' => 'error',
                                'info'   => 'Couldn\'t upload the requested file.'
                            );
                        }
                    } else {
                        $response = array(
                            'status'    => 'error',
                            'info'      => "Error while uploading files. Uploaded files $uploads",
                        );
                    }
                }
            } else {
                $response = array(
                    'status' => 'error',
                    'info'   => 'The specified folder for upload isn\'t writeable.'
                );
            }
            // Return the response
            echo json_encode($response);
            exit();
        }
    }

    public function deleteFile (Request $request) {
        if (!$request->hasAny('p') && empty($_FILES)) {
            $url = route('files.manager');
            return redirect($url.'?p=');
        }
        // get path
        $p = $request->p ? $request->p :  '';
        $fmPath = $this->fmCleanManager($p);
        // clean path
        $del = str_replace('/', '', $this->fmCleanPath($request->del));
        if ($del != '' && $del != '..' && $del != '.') {
            $path = $this->rootPath;
            if ($fmPath != '') {
                $path .= '/' . $fmPath;
            }
            if ($this->deleteFileAndDir($path . '/' . $del)) {
                return back()->with([
                    'message' => 'Successfully deleted',
                    'alert-type' => 'success',
                ]);
            }
            return back()->with([
                'message' => 'Could not deleted',
                'alert-type' => 'danger',
            ]);
        }
        return back()->with([
            'message' => 'Invalid file or folder name',
            'alert-type' => 'danger',
        ]);
    }

    public function newFolder(Request $request) {
        // Create folder
        $request->validate([
            'new' => 'required',
        ]);
        // get path
        $p = $request->hasAny('p') ? $request->p :  '';
        $fmPath = $this->fmCleanManager($p);
        $new = str_replace('/', '', $this->fmCleanPath(strip_tags($request->new)));
        if ($this->isValidFileName($new) && $new != '' && $new != '..' && $new != '.') {
            $path = $this->rootPath;
            $fmPath = $this->fmCleanManager($p);
            if ($fmPath != '') {
                $path .= '/' . $fmPath;
            }
            if ($this->makeDir($path . '/' . $new, false) === true) {
                return back()->with([
                    'message' => 'Successfully created folder',
                    'alert-type' => 'success',
                ]);
            } elseif ($this->makeDir($path . '/' . $new, false) === $path . '/' . $new) {
                return back()->with([
                    'message' => 'Already folder exists with this name',
                    'alert-type' => 'danger',
                ]);
            }
            return back()->with([
                'message' => 'Folder could not created',
                'alert-type' => 'danger',
            ]);
        }
        return back()->with([
            'message' => 'Invalid characters in  folder name',
            'alert-type' => 'danger',
        ]);
    }
    public function fmCleanManager ($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    function fmGetParentPath($path) {
        $path = $this->fmCleanManager($path);
        if ($path != '') {
            $array = explode('/', $path);
            if (count($array) > 1) {
                $array = array_slice($array, 0, -1);
                return implode('/', $array);
            }
            return '';
        }
        return false;
    }

    public function fmIsExcludesItems ($file) {

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $ext = strtolower($ext);
        $excluedItems = array('*.php','*.html','.env');
        if (!in_array($file, $excluedItems) && !in_array("*.$ext", $excluedItems)) {
            return true;
        }
        return false;
    }

    public function fmCleanPath($path, $trim = true)
    {
        $path = $trim ? trim($path) : $path;
        $path = trim($path, '\\/');
        $path = str_replace(array('../', '..\\'), '', $path);
        $path =  $this->getAbsolutePath($path);
        if ($path == '..') {
            $path = '';
        }
        return str_replace('\\', '/', $path);
    }


    private function getFmPath ($request) {
        $p = isset($request->p) ? $request->p : (isset($_POST['p']) ? $_POST['p'] : '');
        return $this->fmCleanManager($p);
    }

    private function isValidFileName($text){
        return (strpbrk($text, '/?%*:|"<>') === FALSE) ? true : false;
    }

    private function  fileRename($old, $new){
        $isFileAllowed = $this->isValidExt($new);
        if (!$isFileAllowed)
            return false;
        return (!file_exists($new) && file_exists($old)) ? rename($old, $new) : null;
    }
    private function isValidExt($filename)
    {
        $allowed = Settings::where('name','allowed_files')->first();
        $allowed = strtolower($allowed->value);
        $allowed = explode(',',$allowed);

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        if (!$ext)
            return true;
        // $d = in_array($ext, $allowed);
        // dd($d);
        $isFileAllowed = $allowed ? in_array($ext, $allowed) : true;

        return $isFileAllowed? true : false;
    }

    private function getAbsolutePath($path)
    {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }

    private function getAllowedExtension () {
        $allowed = Settings::where('name','allowed_files')->first();
        $allowed = strtolower($allowed->value);
        $allowed = explode(',',$allowed);
        return $allowed;
    }

    private function getAllowedExtensionWithDots() {
        $extArr = Settings::where('name','allowed_files')->first();
        if (!strlen($extArr->value)) {
            return "";
        }
        $extArr = strtolower($extArr->value);
        $extArr = explode(',',$extArr);
        array_walk($extArr, function (&$x) {
            $x = ".$x";
        });
        return implode(',', $extArr);
    }

    private function maxUploadSIze () {
        $maxUpload = Settings::where('name','max_upload_size')->first();
        $maxUpload = $maxUpload?$maxUpload->value:300000;
        return $maxUpload;
    }


    public function deleteFileAndDir($path) {
        if (is_link($path)) {
            return unlink($path);
        } elseif (is_dir($path)) {
            $objects = scandir($path);
            $ok = true;
            if (is_array($objects)) {
                foreach ($objects as $file) {
                    if ($file != '.' && $file != '..') {
                        if (!$this->deleteFileAndDir($path . '/' . $file)) {
                            $ok = false;
                        }
                    }
                }
            }
            return ($ok) ? rmdir($path) : false;
        } elseif (is_file($path)) {
            return unlink($path);
        }
        return false;
    }


    private function makeDir($dir, $force){
        if (file_exists($dir)) {
            if (is_dir($dir)) {
                return $dir;
            } elseif (!$force) {
                return false;
            }
            unlink($dir);
        }
        return mkdir($dir, 0777, true);
    }
}
