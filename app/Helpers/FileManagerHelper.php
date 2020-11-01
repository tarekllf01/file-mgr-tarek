<?php
namespace App\Helpers;

use Symfony\Component\Console\Helper\Helper;
use App\Models\Settings;
class FileManagerHelper  {

    public static function getMaxFileSize () {
        //  max Upload file size
        $maxUploadSize = Settings::where('name','max_upload_size')->first();
        $maxUploadSize = $maxUploadSize?$maxUploadSize->value:null;
        return $maxUploadSize;
    }

    public static function getAllowedFiles ($value) {
        //  allowed files
        $allowedFiles = Settings::where('name','allowed_files')->first();
        $allowedFiles = strtolower($allowedFiles->value);
        $upperString = strtoupper($allowedFiles);
        $allowedFiles = explode(',',$allowedFiles);
        $upperString = explode(',',$upperString);
        foreach ($upperString as $key) {
            if(strlen($key) > 0 )
                array_push($allowedFiles,$key);
        }
        return $allowedFiles;
    }

}
