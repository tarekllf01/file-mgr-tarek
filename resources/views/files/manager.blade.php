@inject('service', 'App\helpers\BladeServices');
@php
    header("Content-Type: text/html; charset=utf-8");
    header("Expires: Sat, 26 Jul 2030 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    global $root_url;
@endphp
@extends('layouts.master')

@section('contents')
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
                                <a title="Delete" href="{{route('files.delete')}}?p={{urlencode($fmPath)}}&del={{urlencode($f)}}" onclick="return confirm('Delete Folder?')\n \n ( {{urlencode($f)}})');"> <i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                <a title="Rename" href="#" onclick="rename('{{$service->fmEnc($fmPath)}}', '{{$service->fmEnc(addslashes($f))}}');return false;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                {{-- <a title="CopyTo..." href="?p=&copy={{urlencode(trim($fmPath . '/' . $f, '/'))}}"><i class="fa fa-files-o" aria-hidden="true"></i></a>
                                <a title="DirectLink" href="{{$service->fmEnc(route('files.manager') . ($fmPath != '' ? '/' . $fmPath : '') . '/' . $f . '/')}}" target="_blank"><i class="fa fa-link" aria-hidden="true"></i></a> --}}
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
                            <a title="Delete" href="{{route('files.delete')}}?p={{urlencode($fmPath)}}&del={{urlencode($f)}}" onclick="return confirm(' Delete File ? \n \n  {{urlencode($f)}}')"> <i class="fa fa-trash-o"></i></a>
                            <a title="Rename" href="#" onclick="rename('{{$service->fmEnc($fmPath)}}', '{{$service->fmEnc(addslashes($f))}}');return false;"><i class="fa fa-pencil-square-o"></i></a>
                            {{-- <a title="CopyTo..." href="?p={{urlencode($fmPath)}}&copy={{urlencode(trim($fmPath . '/' . $f, '/'))}}"><i class="fa fa-files-o"></i></a> --}}
                            {{-- <a title="DirectLink" href="{{ $service->fmEnc($rootUrl . ($fmPath != '' ? '/' . $fmPath : '') . '/' . $f)}}" target="_blank"><i class="fa fa-link"></i></a> --}}
                            <a title="Download" href="{{asset('files/'.$fmPath.'/'.$f)}}"><i class="fa fa-download"></i></a>
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
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="customRadioInline2" name="newfile" value="folder" class="custom-control-input" checked="">
                        <label class="custom-control-label" for="customRadioInline2">Folder</label>
                    </div>

                    <p class="mt-3"><label for="newfilename">Folder Name </label></p>
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
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">
                            <i class="fa fa-times-circle"></i> Cancel
                        </button>
                        <%if(this.action){%>
                            <button type="button" class="btn btn-primary" id="js-ModalCenterAction" data-type="js-<%this.action%>"><%this.action%>
                            </button>
                        <%}%>
                </div>
            </div>
        </div>
    </script>
@endsection
