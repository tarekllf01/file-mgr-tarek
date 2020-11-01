<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Alexusmai\LaravelFileManager\Services\ConfigService\ConfigRepository;
use Illuminate\Support\Env;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // return env('maxUploadFiles');
        // return config('file-manager.maxUploadFileSize');
        // return config('file-manager.allowFileTypes');
        return view('home');
    }


    public function settings () {
        // $data = Settings::where('name','allowed_files')->first();
        // $data = strtolower($data);
        // $upperString = strtoupper($data);
        // $data = explode(',',$data);
        // $upperString = explode(',',$upperString);
        // foreach ($upperString as $key) {
        //     array_push($data,$key);
        // }

        // $this->changeEnv(['maxUploadFiles'=> 30000]);
        // return env('maxUploadFiles');

        // return $data;
        // config::set('file-manager.allowFileTypes', $data);

        // return $this->setEnvironmentValue("maxUploadFiles","10000");

        // return config('file-manager.maxUploadFileSize');
        // return config('file-manager.allowFileTypes');

        // apache_setenv('maxUploadFiles', 1000);
        // Env::enablePutenv();
        // putenv('maxUploadFiles=1000');
        // $_ENV['maxUploadFiles'] = 4000;
        // parent::setUp();
        $maxUpload = Settings::where('name','max_upload_size')->first();
        $data['maxUpload'] = $maxUpload?$maxUpload->value:300000;
        $allowedFiles = Settings::where('name','allowed_files')->first();
        $data['allowedFiles'] = $allowedFiles?$allowedFiles->value:'png,jpg,gif,zip,txt,jpeg,mp3,mp4';
        return view('settings.main',$data);
    }

    public function saveSettings (Request $request) {
        $request->validate([
            'maximum_allowed_size' => 'nullable|integer',
            'extensions' => 'required|string',
        ]);

        $fileSize = Settings::where('name','max_upload_size')
                            ->update(['value'=>$request->maximum_allowed_size]);
        $extensions = Settings::where('name','allowed_files')
                                ->update(['value'=>$request->extensions]);
        if ($fileSize && $extensions) {
            return back()->with([
                'alert-type' => 'success',
                'message' => 'Updated Settings',
                ]);
        } else {
            return back()->with([
                'alert-type' => 'danger',
                'message' => 'Could not update settings',
                ]);
        }

    }

    public function allowedTypes () {

    }

    public function setEnvironmentValue($envKey, $envValue){
        // return $envKey;
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        return $oldValue = strtok($str, "{$envKey}=");

        $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}\n", $str);

        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
    }


    protected function changeEnv($data = array()){
        if(count($data) > 0){

            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');

            // Split string on every " " and write into array
            $env = preg_split('/\s+/', $env);;

            // Loop through given data
            foreach((array)$data as $key => $value){

                // Loop through .env-data
                foreach($env as $env_key => $env_value){

                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if($entry[0] == $key){
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }

            // Turn the array back to an String
            $env = implode("\n", $env);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);

            return true;
        } else {
            return false;
        }
    }

}
