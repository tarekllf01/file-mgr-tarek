<?php
namespace App\Traits;

use App\Models\Settings;

trait FileManagerTraits {

    public function getMaxFileSize () {
        //  max Upload file size
        $maxUploadSize = Settings::where('name','max_upload_size')->first();
        $maxUploadSize = $maxUploadSize?$maxUploadSize->value:null;
        return $maxUploadSize;
    }

    public function getAllowedFiles () {
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
