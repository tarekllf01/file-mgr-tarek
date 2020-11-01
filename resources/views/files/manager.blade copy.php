@inject('service', 'App\helpers\BladeServices');
<?php
header("Content-Type: text/html; charset=utf-8");
header("Expires: Sat, 26 Jul 2030 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
header("Pragma: no-cache");
global $lang, $root_url, $sticky_navbar, $favicon_path;
$isStickyNavBar = 'navbar-normal';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="robots" content="noindex, nofollow">
        <meta name="googlebot" content="noindex">
        <title>File Manger</title>
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.0.3/styles/vs.min.css">
        <link rel="stylesheet" href="{{ asset('/css/file-manager.css')}}">
    </head>
    <body class="navbar-normal">
        <nav class="navbar navbar-expand-lg bg-white mb-4 main-nav ">
            <a class="navbar-brand" href=""> File Manager</a>
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
                            <a title="Upload" class="nav-link" href="?p={{urlencode($path)}}&upload"><i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload</a>
                        </li>
                        <li class="nav-item">
                            <a title="NewItem" class="nav-link" href="#createNewItem" data-toggle="modal" data-target="#createNewItem"><i class="fa fa-plus-square"></i> NewItem</a>
                        </li>
                        <li class="nav-item">
                            <a title="Settings" class="dropdown-item nav-link" href="?p={{urlencode($path)}}&settings=1"><i class="fa fa-cog" aria-hidden="true"></i> Settings</a>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>
        <div id="wrapper" class="container-fluid">

            <!-- main file lists -->
            <form action="" method="post" class="pt-3">
                <input type="hidden" name="p" value="{{$service->fmEnc($path)}}">
                <input type="hidden" name="group" value="1">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm bg-white" id="main-table">
                        <thead class="thead-white">
                            <tr>
                                <th style="width:3%" class="custom-checkbox-header">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="js-select-all-items" onclick="checkbox_toggle()">
                                        <label class="custom-control-label" for="js-select-all-items"></label>
                                    </div>
                                </th>
                                <th>Name</th>
                                <th>Size</th>
                                <th>Modified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <!-- link to parent folder -->
                        @if ($fmPath != '')
                            <tr>
                                <td class="nosort"></td>
                                {{-- <td class="border-0"><a href="?p={{urlencode($parent)}}"><i class="fa fa-chevron-circle-left go-back"></i> ..</a></td> --}}
                                <td class="border-0"><a onclick="window.history.back()"><i class="fa fa-chevron-circle-left go-back"></i> ..</a></td>
                                <td class="border-0"></td>
                                <td class="border-0"></td>
                                <td class="border-0"></td>
                            </tr>
                        @endif
                        <?php
                            $ii = 3399;
                            foreach ($folders as $f) {
                                $isLink = is_link($path . '/' . $f);
                                $img = $isLink ? 'icon-link_folder' : 'fa fa-folder-o';
                                $modifyRaw = filemtime($path . '/' . $f);
                                $modif = date($dateFormat, $modifyRaw);

                                $fileSizeRaw = "";
                                $filesize = "Folder";

                                $perms = substr(decoct(fileperms($path . '/' . $f)), -4);

                                $owner = array('name' => '?');
                                $group = array('name' => '?');

                                ?>
                                <tr>

                                    <td class="custom-checkbox-td">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="{{$ii}}" name="file[]" value="{{$service->fmEnc($f)}}">
                                            <label class="custom-control-label" for="{{$ii}}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="filename">
                                            <a href="?p={{urlencode(trim($fmPath . '/' . $f, '/')) }}">
                                                <i class="{{$img}}"></i> {{$service->fmConvertWin($service->fmEnc($f))}}
                                            </a>{{$isLink ? ' &rarr; <i>' .  ($fmPath . '/' . $f) . '</i>' : ''}}</div>
                                    </td>
                                    <td data-sort="a-{{ str_pad($fileSizeRaw, 18, "0", STR_PAD_LEFT)}}">
                                        {{$filesize}}
                                    </td>
                                    <td data-sort="a-{{ $modifyRaw }}">{{ $modif }}</td>

                                    <td class="inline-actions">
                                        <a title="Delete" href="?p={{urlencode($fmPath)}}&del={{urlencode($f)}}" onclick="return confirm('Delete Folder?')\n \n ( {{urlencode($f)}})');"> <i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                        <a title="Rename" href="#" onclick="rename('{{$service->fmEnc($fmPath)}}', '{{$service->fmEnc(addslashes($f))}}');return false;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                        <a title="CopyTo..." href="?p=&copy={{urlencode(trim($fmPath . '/' . $f, '/'))}}"><i class="fa fa-files-o" aria-hidden="true"></i></a>
                                        <a title="DirectLink" href="{{$service->fmEnc(route('files.manager') . ($fmPath != '' ? '/' . $fmPath : '') . '/' . $f . '/')}}" target="_blank"><i class="fa fa-link" aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                            <?php
                                flush();
                                $ii++;
                            }
                        $ik = 6070;
                        foreach ($files as $f) {
                            $isLink = is_link($path . '/' . $f);
                            $img = $isLink ? 'fa fa-file-text-o' : $service->fmFileIconClass($path . '/' . $f);
                            $modifyRaw = filemtime($path . '/' . $f);
                            $modif = date($dateFormat,$modifyRaw);
                            $fileSizeRaw = $service->fmGetSize($path . '/' . $f);
                            $filesize = $service->fmGetFileSize($fileSizeRaw);
                            $filelink = '?p=' . urlencode($fmPath) . '&view=' . urlencode($f);
                            // $all_files_size += $fileSizeRaw;
                            $perms = substr(decoct(fileperms($path . '/' . $f)), -4);
                            $owner = array('name' => '?');
                            $group = array('name' => '?');

                        ?>
                            <tr>
                                <td class="custom-checkbox-td">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="{{$ik}}" name="file[]" value="{{$service->fmEnc($f)}}">
                                        <label class="custom-control-label" for="{{$ik}}"></label>
                                    </div>
                                </td>
                                <td>
                                    <div class="filename">
                                        <?php
                                        if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'ico', 'svg'))) : ?>
                                            <?php $imagePreview = $service->fmEnc($rootUrl . ($fmPath != '' ? '/' . $fmPath : '') . '/' . $f); ?>
                                            <a href="{{$filelink}}" data-preview-image="{{asset('files/'.$fmPath.'/'.$f)}}" title="{{$f}}">
                                            <?php else : ?>
                                                <a href="{{$filelink}}" title="{{$f}}">
                                                <?php endif; ?>
                                                <i class="{{$img}}"></i> {{$service->fmConvertWin($f)}}
                                                </a>
                                                {{ $isLink ? ' &rarr; <i>' . readlink($fmPath . '/' . $f) . '</i>' : ''}}
                                    </div>
                                </td>
                                <td data-sort="b-{{str_pad($fileSizeRaw, 18, "0", STR_PAD_LEFT)}}">
                                    <span title="{{printf('%s bytes', $fileSizeRaw)}}">
                                        {{$filesize}}
                                    </span></td>
                                <td data-sort="b-{{$modifyRaw}}">{{$modif}}</td>
                                <td class="inline-actions">
                                    {{-- <a title="Preview" href="{{$filelink}}&quickView=1" data-toggle="lightbox" data-gallery="tiny-gallery" data-title="{{$service->fmConvertWin($f)}}" data-max-width="100%" data-width="100%"><i class="fa fa-eye"></i></a> --}}
                                    <a title="Delete" href="?p={{urlencode($fmPath)}}&del={{urlencode($f)}}" onclick="return confirm(' Delete File ? \n \n  {{urlencode($f)}}')"> <i class="fa fa-trash-o"></i></a>
                                    <a title="Rename" href="#" onclick="rename('{{$service->fmEnc($fmPath)}}', '{{$service->fmEnc(addslashes($f))}}');return false;"><i class="fa fa-pencil-square-o"></i></a>
                                    <a title="CopyTo..." href="?p={{urlencode($fmPath)}}&copy={{urlencode(trim($fmPath . '/' . $f, '/'))}}"><i class="fa fa-files-o"></i></a>
                                    <a title="DirectLink" href="{{ $service->fmEnc($rootUrl . ($fmPath != '' ? '/' . $fmPath : '') . '/' . $f)}}" target="_blank"><i class="fa fa-link"></i></a>
                                    <a title="Download" href="?p={{urlencode($fmPath)}}&dl=<?php echo urlencode($f) ?>"><i class="fa fa-download"></i></a>
                                </td>
                            </tr>
                        <?php
                            flush();
                            $ik++;
                        }

                        if (empty($folders) && empty($files)) {
                        ?>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td colspan="4"><em>Folder is empty</em></td>
                                </tr>
                            </tfoot>
                        <?php
                        }
                        ?>
                    </table>
                </div>

            </form>
            <!-- New Item creation -->
            <div class="modal fade" id="createNewItem" tabindex="-1" role="dialog" aria-label="newItemModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content ">
                        <div class="modal-header">
                            <h5 class="modal-title" id="newItemModalLabel"><i class="fa fa-plus-square fa-fw"></i> CreateNew</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p><label for="newfile">ItemType</label></p>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="customRadioInline1" name="newfile" value="file" class="custom-control-input">
                                <label class="custom-control-label" for="customRadioInline1">File</label>
                            </div>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="customRadioInline2" name="newfile" value="folder" class="custom-control-input" checked="">
                                <label class="custom-control-label" for="customRadioInline2">Folder</label>
                            </div>

                            <p class="mt-3"><label for="newfilename">ItemName </label></p>
                            <input type="text" name="newfilename" id="newfilename" value="" class="form-control">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-primary" data-dismiss="modal"><i class="fa fa-times-circle"></i> Cancel</button>
                            <button type="button" class="btn btn-success" onclick="newfolder('{{$fmPath}} ');return false;"><i class="fa fa-check-circle"></i> CreateNow</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content ">
                        <div class="modal-header">
                            <h5 class="modal-title col-10" id="searchModalLabel">
                                <div class="input-group input-group">
                                    <input type="text" class="form-control" placeholder="Search a files" aria-label="Search " aria-describedby="search-addon3" id="advanced-search" autofocus required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="search-addon3"><i class="fa fa-search"></i></span>
                                    </div>
                                </div>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post">
                                <div class="lds-facebook">
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                </div>
                                <ul id="search-wrapper">
                                    <p class="m-2">Search file in folder and subfolders...</p>
                                </ul>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/html" id="js-tpl-modal">
                <div class="modal fade" id="js-ModalCenter-<%this.id%>" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ModalCenterTitle"><%this.title%></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <%this.content%>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-primary" data-dismiss="modal"><i class="fa fa-times-circle"></i> Cancel</button>
                                <%if(this.action){%><button type="button" class="btn btn-primary" id="js-ModalCenterAction" data-type="js-<%this.action%>"><%this.action%></button><%}%>
                    </div>
                </div>
            </div>
        </div>
        </script>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.0.3/highlight.min.js"></script>
    <script>
        hljs.initHighlightingOnLoad();
        var isHighlightingEnabled = true;
    </script>
    <script>
        $(document).on('click', '[data-toggle="lightbox"]', function(event) {
            event.preventDefault();
            var reInitHighlight = function() {
                if (typeof isHighlightingEnabled !== "undefined" && isHighlightingEnabled) {
                    setTimeout(function() {
                        $('.ekko-lightbox-container pre code').each(function(i, e) {
                            hljs.highlightBlock(e)
                        });
                    }, 555);
                }
            };
            $(this).ekkoLightbox({
                alwaysShowClose: true,
                showArrows: true,
                onShown: function() {
                    reInitHighlight();
                },
                onNavigate: function(direction, itemIndex) {
                    reInitHighlight();
                }
            });
        });

        function template(html, options) {
            var re = /<\%([^\%>]+)?\%>/g,
                reExp = /(^( )?(if|for|else|switch|case|break|{|}))(.*)?/g,
                code = 'var r=[];\n',
                cursor = 0,
                match;
            var add = function(line, js) {
                js ? (code += line.match(reExp) ? line + '\n' : 'r.push(' + line + ');\n') : (code += line != '' ? 'r.push("' + line.replace(/"/g, '\\"') + '");\n' : '');
                return add
            }
            while (match = re.exec(html)) {
                add(html.slice(cursor, match.index))(match[1], !0);
                cursor = match.index + match[0].length
            }
            add(html.substr(cursor, html.length - cursor));
            code += 'return r.join("");';
            return new Function(code.replace(/[\r\t\n]/g, '')).apply(options)
        }

        function newfolder(e) {
            var t = document.getElementById("newfilename").value,
                n = document.querySelector('input[name="newfile"]:checked').value;
            null !== t && "" !== t && n && (window.location.hash = "#", window.location.search = "p=" + encodeURIComponent(e) + "&new=" + encodeURIComponent(t) + "&type=" + encodeURIComponent(n))
        }

        function rename(e, t) {
            var n = prompt("New name", t);
            null !== n && "" !== n && n != t && (window.location.href = "{{route('files.rename')}}?p="+ encodeURIComponent(e) + "&ren=" + encodeURIComponent(t) + "&to=" + encodeURIComponent(n))
        }

        function change_checkboxes(e, t) {
            for (var n = e.length - 1; n >= 0; n--) e[n].checked = "boolean" == typeof t ? t : !e[n].checked
        }

        function get_checkboxes() {
            for (var e = document.getElementsByName("file[]"), t = [], n = e.length - 1; n >= 0; n--)(e[n].type = "checkbox") && t.push(e[n]);
            return t
        }

        function select_all() {
            change_checkboxes(get_checkboxes(), !0)
        }

        function unselect_all() {
            change_checkboxes(get_checkboxes(), !1)
        }

        function invert_all() {
            change_checkboxes(get_checkboxes())
        }

        function checkbox_toggle() {
            var e = get_checkboxes();
            e.push(this), change_checkboxes(e)
        }

        function backup(e, t) { //Create file backup with .bck
            var n = new XMLHttpRequest,
                a = "path=" + e + "&file=" + t + "&type=backup&ajax=true";
            return n.open("POST", "", !0), n.setRequestHeader("Content-type", "application/x-www-form-urlencoded"), n.onreadystatechange = function() {
                4 == n.readyState && 200 == n.status && toast(n.responseText)
            }, n.send(a), !1
        }
        // Toast message
        function toast(txt) {
            var x = document.getElementById("snackbar");
            x.innerHTML = txt;
            x.className = "show";
            setTimeout(function() {
                x.className = x.className.replace("show", "");
            }, 3000);
        }
        //Save file
        function edit_save(e, t) {
            var n = "ace" == t ? editor.getSession().getValue() : document.getElementById("normal-editor").value;
            if (n) {
                if (true) {
                    var data = {
                        ajax: true,
                        content: n,
                        type: 'save'
                    };

                    $.ajax({
                        type: "POST",
                        url: window.location,
                        // The key needs to match your method's input parameter (case-sensitive).
                        data: JSON.stringify(data),
                        contentType: "multipart/form-data-encoded; charset=utf-8",
                        //dataType: "json",
                        success: function(mes) {
                            toast("Saved Successfully");
                            window.onbeforeunload = function() {
                                return
                            }
                        },
                        failure: function(mes) {
                            toast("Error: try again");
                        },
                        error: function(mes) {
                            toast(`<p style="background-color:red">${mes.responseText}</p>`);
                        }
                    });

                } else {
                    var a = document.createElement("form");
                    a.setAttribute("method", "POST"), a.setAttribute("action", "");
                    var o = document.createElement("textarea");
                    o.setAttribute("type", "textarea"), o.setAttribute("name", "savedata");
                    var c = document.createTextNode(n);
                    o.appendChild(c), a.appendChild(o), document.body.appendChild(a), a.submit()
                }
            }
        }

        //Save Settings
        function save_settings($this) {
            let form = $($this);
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize() + "&ajax=" + true,
                success: function(data) {
                    if (data) {
                        window.location.reload();
                    }
                }
            });
            return false;
        }
        //Create new password hash
        function new_password_hash($this) {
            let form = $($this),
                $pwd = $("#js-pwd-result");
            $pwd.val('');
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize() + "&ajax=" + true,
                success: function(data) {
                    if (data) {
                        $pwd.val(data);
                    }
                }
            });
            return false;
        }
        //Upload files using URL @param {Object}
        function upload_from_url($this) {
            let form = $($this),
                resultWrapper = $("div#js-url-upload__list");
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize() + "&ajax=" + true,
                beforeSend: function() {
                    form.find("input[name=uploadurl]").attr("disabled", "disabled");
                    form.find("button").hide();
                    form.find(".lds-facebook").addClass('show-me');
                },
                success: function(data) {
                    if (data) {
                        data = JSON.parse(data);
                        if (data.done) {
                            resultWrapper.append('<div class="alert alert-success row">Uploaded Successful: ' + data.done.name + '</div>');
                            form.find("input[name=uploadurl]").val('');
                        } else if (data['fail']) {
                            resultWrapper.append('<div class="alert alert-danger row">Error: ' + data.fail.message + '</div>');
                        }
                        form.find("input[name=uploadurl]").removeAttr("disabled");
                        form.find("button").show();
                        form.find(".lds-facebook").removeClass('show-me');
                    }
                },
                error: function(xhr) {
                    form.find("input[name=uploadurl]").removeAttr("disabled");
                    form.find("button").show();
                    form.find(".lds-facebook").removeClass('show-me');
                    console.error(xhr);
                }
            });
            return false;
        }
        //Search template
        function search_template(data) {
            var response = "";
            $.each(data, function(key, val) {
                response += `<li><a href="?p=${val.path}&view=${val.name}">${val.path}/${val.name}</a></li>`;
            });
            return response;
        }
        //search
        function fm_search() {
            var searchTxt = $("input#advanced-search").val(),
                searchWrapper = $("ul#search-wrapper"),
                path = $("#js-search-modal").attr("href"),
                _html = "",
                $loader = $("div.lds-facebook");
            if (!!searchTxt && searchTxt.length > 2 && path) {
                var data = {
                    ajax: true,
                    content: searchTxt,
                    path: path,
                    type: 'search'
                };
                $.ajax({
                    type: "POST",
                    url: window.location,
                    data: data,
                    beforeSend: function() {
                        searchWrapper.html('');
                        $loader.addClass('show-me');
                    },
                    success: function(data) {
                        $loader.removeClass('show-me');
                        data = JSON.parse(data);
                        if (data && data.length) {
                            _html = search_template(data);
                            searchWrapper.html(_html);
                        } else {
                            searchWrapper.html('<p class="m-2">No result found!<p>');
                        }
                    },
                    error: function(xhr) {
                        $loader.removeClass('show-me');
                        searchWrapper.html('<p class="m-2">ERROR: Try again later!</p>');
                    },
                    failure: function(mes) {
                        $loader.removeClass('show-me');
                        searchWrapper.html('<p class="m-2">ERROR: Try again later!</p>');
                    }
                });
            } else {
                searchWrapper.html("OOPS: minimum 3 characters required!");
            }
        }

        //on mouse hover image preview
        ! function(s) {
            s.previewImage = function(e) {
                var o = s(document),
                    t = ".previewImage",
                    a = s.extend({
                        xOffset: 20,
                        yOffset: -20,
                        fadeIn: "fast",
                        css: {
                            padding: "5px",
                            border: "1px solid #cccccc",
                            "background-color": "#fff"
                        },
                        eventSelector: "[data-preview-image]",
                        dataKey: "previewImage",
                        overlayId: "preview-image-plugin-overlay"
                    }, e);
                return o.off(t), o.on("mouseover" + t, a.eventSelector, function(e) {
                    s("p#" + a.overlayId).remove();
                    var o = s("<p>").attr("id", a.overlayId).css("position", "absolute").css("display", "none").append(s('<img class="c-preview-img">').attr("src", s(this).data(a.dataKey)));
                    a.css && o.css(a.css), s("body").append(o), o.css("top", e.pageY + a.yOffset + "px").css("left", e.pageX + a.xOffset + "px").fadeIn(a.fadeIn)
                }), o.on("mouseout" + t, a.eventSelector, function() {
                    s("#" + a.overlayId).remove()
                }), o.on("mousemove" + t, a.eventSelector, function(e) {
                    s("#" + a.overlayId).css("top", e.pageY + a.yOffset + "px").css("left", e.pageX + a.xOffset + "px")
                }), this
            }, s.previewImage()
        }(jQuery);


        // Dom Ready Event
        $(document).ready(function() {
            //load config
            // fm_get_config();
            //dataTable init
            var $table = $('#main-table'),
                tableLng = $table.find('th').length,
                _targets = (tableLng && tableLng == 7) ? [0, 4, 5, 6] : tableLng == 5 ? [0, 4] : [3],
                mainTable = $('#main-table').DataTable({
                    "paging": false,
                    "info": false,
                    "columnDefs": [{
                        "targets": _targets,
                        "orderable": false
                    }]
                });
            //search
            $('#search-addon').on('keyup', function() {
                mainTable.search(this.value).draw();
            });
            $("input#advanced-search").on('keyup', function(e) {
                if (e.keyCode === 13) {
                    fm_search();
                }
            });
            $('#search-addon3').on('click', function() {
                fm_search();
            });
            //upload nav tabs
            $(".fm-upload-wrapper .card-header-tabs").on("click", 'a', function(e) {
                e.preventDefault();
                let target = $(this).data('target');
                $(".fm-upload-wrapper .card-header-tabs a").removeClass('active');
                $(this).addClass('active');
                $(".fm-upload-wrapper .card-tabs-container").addClass('hidden');
                $(target).removeClass('hidden');
            });
        });
    </script>
    <div id="snackbar"></div>
</body>

</html>
