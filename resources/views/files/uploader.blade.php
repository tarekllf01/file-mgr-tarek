@inject('service', 'App\helpers\BladeServices');
@php
    header("Content-Type: text/html; charset=utf-8");
    header("Expires: Sat, 26 Jul 2030 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    global $root_url;
@endphp
@extends('layouts.master')

@section('extra_heads')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet">
@endsection

@section('contents')
    <div class="path">
        <div class="card mb-2 fm-upload-wrapper ">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#fileUploader" data-target="#fileUploader"><i class="fa fa-arrow-circle-o-up"></i> UploadingFiles</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="#urlUploader" class="js-url-upload" data-target="#urlUploader"><i class="fa fa-link"></i> Upload from URL</a>
                    </li> --}}
                </ul>
            </div>
            <div class="card-body">
                <p class="card-text">
                    <a href="{{route('files.manager')}}?p={{$fmPath}}" class="float-right"><i class="fa fa-chevron-circle-left go-back"></i> Back</a>
                    DestinationFolder:  {{$service->fmEnc($service->fmConvertWin($rootPath . '/' . $fmPath))}}
                </p>

                <form action="{{route('files.uploaderAjax')}}?p={{$service->fmEnc($fmPath)}}" class="dropzone card-tabs-container" id="fileUploader" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="p" value="{{$service->fmEnc($fmPath)}}">
                    <input type="hidden" name="fullpath" id="fullpath" value="{{$service->fmEnc($fmPath)}}">
                    <div class="fallback">
                        <input name="file" type="file" multiple />
                    </div>
                </form>

                <div class="upload-url-wrapper card-tabs-container hidden" id="urlUploader">
                    <form id="js-form-url-upload" class="form-inline" onsubmit="return upload_from_url(this);" method="POST" action="">
                        <input type="hidden" name="type" value="upload" aria-label="hidden" aria-hidden="true">
                        <input type="url" placeholder="URL" name="uploadurl" required class="form-control" style="width: 80%">
                        <button type="submit" class="btn btn-primary ml-3">Upload</button>
                        <div class="lds-facebook">
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </form>
                    <div id="js-url-upload__list" class="col-9 mt-3"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
    <script>
        Dropzone.options.fileUploader = {
            timeout: 120000,
            maxFilesize: {{$maxUpload}},
            acceptedFiles: "{{$extString}}",
            init: function() {
                this.on("sending", function(file, xhr, formData) {
                    let _path = (file.fullPath) ? file.fullPath : file.name;
                    document.getElementById("fullpath").value = _path;
                    xhr.ontimeout = (function() {
                        toast('Error: Server Timeout');
                    });
                }).on("success", function(res) {
                    let _response = JSON.parse(res.xhr.response);
                    console.log(_response);
                    if (_response.status == "error") {
                        toast(_response.info);
                    }
                }).on("error", function(file, response) {
                    toast(response);
                });
            }
        }
    </script>
@endsection


@section('extra_scripts')

@endsection
