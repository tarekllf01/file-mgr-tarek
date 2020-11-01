@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('css/settings.css')}} ">
@endsection
@section('content')
    <div class="container-fluid mt-5">
        <div class="row">
            @if (session('message'))
                <div class="m-3 row w-100 alert alert-{{session('alert-type')}}">
                    {{session('message')}}
                </div>
            @endif
        </div>
        <div class="row">
            <div class="col-lg-4 pb-5">
                <!-- Account Sidebar-->
                <div class="author-card pb-3">
                    <div class="author-card-cover" style="background-image: url(https://demo.createx.studio/createx-html/img/widgets/author/cover.jpg);"></div>
                    <div class="author-card-profile">

                        <div class="author-card-details">

                            <h3 class="mt-3">File Manger Settings</h3>
                        </div>
                    </div>
                </div>
                <div class="wizard">
                    <nav class="list-group list-group-flush">

                        <a class="list-group-item active" href="#"><i class="fe-icon-user text-muted"></i>Configurations & Settings </a>

                    </nav>
                </div>
            </div>
            <!-- Profile Settings-->
            <div class="col-lg-8 pb-5">
                <form class="row" action="{{route('settings.save')}}" method="post">
                    @csrf
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="account-fn">Maximum Uplaod Size (Eampty value means no limit)</label>
                            <input class="form-control" name="maximum_allowed_size" type="text" id="account-fn" value="{{$maxUpload}}" >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="account-ln">Allwed extension (Comma seperated)</label>
                            <input class="form-control" name="extensions" type="text" id="account-ln" value="{{$allowedFiles}}" required >
                        </div>
                    </div>
                    <button class="form-control m-5 btn btn-info" type="submit">Update </button>
                </form>

                <div class="container mt-5">
                    <div class="alert alert-warning">For file manager version 1 : configuration settings at app/config/file-manager</div>
                    <br>
                    <h5 class="text-center">OR</h5> <br>
                    <div class="alert alert-warning">In ENV file add these keys</div>
                    <h6>
                        <p>
                            <h6>maxUploadFiles=300000 </h6>
                            <h6>allowFileTypes="png,jpg,gif,mp3,mp4,PNG" </h6>
                        </p>
                    </h6>

                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')

@endsection
