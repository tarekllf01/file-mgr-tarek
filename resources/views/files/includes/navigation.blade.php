<nav class="navbar navbar-expand-lg bg-white mb-4 main-nav ">
    <a class="navbar-brand" href="{{route('files.manager')}} "> File Manager</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">

        @php
            $root_url = "<a href='?p='><i class='fa fa-home' aria-hidden='true' title='" . $rootPath . "'></i></a>";
            $sep = '<i class="bread-crumb"> / </i>';
            if ($fmPath != '') {
                $exploded = explode('/', $fmPath);
                $count = count($exploded);
                $array = array();
                $urlStack = '';
                for ($i = 0; $i < $count; $i++) {
                    $urlStack = trim($urlStack . '/' . $exploded[$i], '/');
                    $parent_enc = urlencode($urlStack);
                    $array[] = "<a href='?p=$parent_enc'>". $service->fmEnc($service->fmConvertWin($exploded[$i])) ." </a>";
                }
                $root_url .= $sep . implode($sep, $array);
            }
            echo '<div class="col-xs-6 col-sm-5">' . $root_url . '</div>';
        @endphp

        <div class="col-xs-6 col-sm-7 text-right">
            <ul class="navbar-nav mr-auto float-right ">
                <li class="nav-item mr-2">
                    <div class="input-group input-group-sm mr-1" style="margin-top:4px;">
                        <input type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="search-addon2" id="search-addon">
                        <div class="input-group-append">
                            <span class="input-group-text" id="search-addon2"><i class="fa fa-search"></i></span>
                        </div>
                        <div class="input-group-append btn-group">
                            <span class="input-group-text dropdown-toggle" id="search-addon2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></span>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ $path ? $path : '.'}}" id="js-search-modal" data-toggle="modal" data-target="#searchModal">Advanced Search</a>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a title="Upload" class="nav-link" href="{{route('home')}} "><i class="fa fa-cloud-files" aria-hidden="true"></i> V1</a>
                </li>
                <li class="nav-item">
                    <a title="Upload" class="nav-link" href="{{route('files.manager')}} "><i class="fa fa-files" aria-hidden="true"></i> V2</a>
                </li>
                <li class="nav-item">
                    <a title="Upload" class="nav-link" href="{{route('files.uploader')}}?p={{urlencode($fmPath)}}&upload"><i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload</a>
                </li>
                {{-- <li class="nav-item">
                    <a title="NewItem" class="nav-link" href="#createNewItem" data-toggle="modal" data-target="#createNewItem"><i class="fa fa-plus-square"></i> NewItem</a>
                </li> --}}
                <li class="nav-item">
                    <a title="Settings" class="dropdown-item nav-link" href="{{route('settings.main')}} "><i class="fa fa-cog" aria-hidden="true"></i> Settings</a>
                </li>

            </ul>
        </div>
    </div>
</nav>
