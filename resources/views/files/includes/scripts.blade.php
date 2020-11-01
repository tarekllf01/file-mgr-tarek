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
        null !== t && "" !== t && n && (window.location.href = "{{route('files.newFolder')}}?p=" + encodeURIComponent(e) + "&new=" + encodeURIComponent(t) + "&type=" + encodeURIComponent(n))
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
