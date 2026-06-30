<!DOCTYPE html>
<html lang="en">

<head>
    <title>
        @if(isset($page_title))
            {{$page_title}}
        @else
        The Zouple - Master Admin
        @endif
    </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--Css-->
    <link rel="stylesheet" type="text/css" href="{{URL::asset('public/masters/css/main.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('public/masterAdmin/css/zouple-admin-luxury.css')}}?v=20260628-fix2">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
   <link rel="icon" href="{{URL::asset('public/img/dark-logo.png')}}" type="image/gif">
    <!--Js-->
    <script src="{{URL::asset('public/js/password-util.js')}}"></script>
    <style>
        .admin-upload-progress {
            display: none;
            margin: 14px 0;
            padding: 14px;
            border: 1px solid #d8eee9;
            border-radius: 10px;
            background: #f4fffc;
        }

        .admin-upload-progress.is-visible {
            display: block;
        }

        .admin-upload-progress__label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 8px;
            color: #114d45;
            font-weight: 600;
        }

        .admin-upload-progress__track {
            height: 10px;
            overflow: hidden;
            border-radius: 999px;
            background: #dce8e5;
        }

        .admin-upload-progress__bar {
            width: 0;
            height: 100%;
            border-radius: inherit;
            background: #008c7a;
            transition: width .18s ease;
        }

        .admin-upload-progress__hint {
            margin-top: 8px;
            color: #5d6d6a;
            font-size: 13px;
        }
    </style>


</head>

<body class="app sidebar-mini rtl">
    @include('masters.layout.header')
    @yield('content')
    
<!-- Essential javascripts for application to work-->
    <script src="{{URL::asset('public/masters/js/jquery-3.2.1.min.js')}}"></script>
    <script src="{{URL::asset('public/masters/js/popper.min.js')}}"></script>
    <script src="{{URL::asset('public/masters/js/bootstrap.min.js')}}"></script>
    <script src="{{URL::asset('public/masters/js/main.js')}}"></script> 
    <script src="{{URL::asset('public/masterAdmin/js/zouple-admin-luxury.js')}}"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="{{URL::asset('public/masters/js/plugins/pace.min.js')}}"></script>
    <!-- Page specific javascripts-->
    <script type="text/javascript" src="{{URL::asset('public/masters/js/plugins/chart.js')}}"></script>

   <script type="text/javascript" src="{{URL::asset('public/masters/js/plugins/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('public/masters/js/plugins/dataTables.bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('public/masters/js/plugins/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('public/masters/js/plugins/bootstrap-datepicker.min.js')}}"></script>
    
    
     
    
    <!--CkEditores-->
    @if(file_exists(public_path('vendor/unisharp/laravel-ckeditor/ckeditor.js')))
        <script src="{{ asset('vendor/unisharp/laravel-ckeditor/ckeditor.js') }}"></script>
    @endif
    <script>
        if (window.CKEDITOR) {
            ['summary-ckeditor', 'summary-ckeditor1', 'summary-ckeditor2'].forEach(function (editorId) {
                if (document.getElementById(editorId) && !CKEDITOR.instances[editorId]) {
                    CKEDITOR.replace(editorId);
                }
            });
        }
    </script>
    
    <!-- Select2 -->
    <script src="{{URL::asset('public/masters/js/plugins/select2.min.js')}}"></script>
   <script type="text/javascript">
      $('#sl').click(function(){
      	$('#tl').loadingBtn();
      	$('#tb').loadingBtn({ text : "Signing In"});
      });
      
      $('#el').click(function(){
      	$('#tl').loadingBtnComplete();
      	$('#tb').loadingBtnComplete({ html : "Sign In"});
      });
      
      if ($('#demoDate').length && $.fn.datepicker) {
      	$('#demoDate').datepicker({
      		format: "dd/mm/yyyy",
      		autoclose: true,
      		todayHighlight: true
      	});
      }
      
      if ($('#demoSelect').length && $.fn.select2) {
      	$('#demoSelect').select2();
      }

      $(document).on('change', '.js-video-upload-input', function () {
        var input = this;
        var file = input.files && input.files[0] ? input.files[0] : null;
        var maxSizeMb = parseInt(input.getAttribute('data-max-size-mb') || '100', 10);
        var maxSizeBytes = maxSizeMb * 1024 * 1024;
        var allowedTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv', 'video/webm'];
        var allowedExtensions = ['mp4', 'mov', 'avi', 'wmv', 'webm'];
        var errorBox = $(input).closest('form').find('.js-video-upload-error').first();

        if (!file) {
          errorBox.addClass('d-none').text('');
          return;
        }

        var extension = file.name.split('.').pop().toLowerCase();
        var invalidType = allowedTypes.indexOf(file.type) === -1 && allowedExtensions.indexOf(extension) === -1;

        if (file.size > maxSizeBytes || invalidType) {
          input.value = '';
          errorBox.removeClass('d-none').text('Please upload MP4, MOV, AVI, WMV, or WebM up to ' + maxSizeMb + ' MB.');
          return;
        }

        errorBox.addClass('d-none').text('');
      });

      function adminFormHasSelectedFiles(form) {
        var inputs = form.querySelectorAll('input[type="file"]');
        for (var i = 0; i < inputs.length; i++) {
          if (inputs[i].files && inputs[i].files.length > 0) {
            return true;
          }
        }

        return false;
      }

      function adminUploadErrorBox(form) {
        var existing = form.querySelector('.js-admin-upload-error');
        if (existing) {
          return existing;
        }

        var box = document.createElement('div');
        box.className = 'alert alert-danger js-admin-upload-error d-none';

        var firstFormRow = form.querySelector('.row, .form-group');
        if (firstFormRow && firstFormRow.parentNode) {
          firstFormRow.parentNode.insertBefore(box, firstFormRow);
        } else {
          form.insertBefore(box, form.firstChild);
        }

        return box;
      }

      function setAdminUploadError(form, message) {
        var box = adminUploadErrorBox(form);
        box.textContent = message || 'Upload failed. Please check the selected file and try again.';
        box.classList.remove('d-none');
      }

      function clearAdminUploadError(form) {
        var box = form.querySelector('.js-admin-upload-error');
        if (box) {
          box.textContent = '';
          box.classList.add('d-none');
        }
      }

      function adminFileExtension(file) {
        return file && file.name && file.name.indexOf('.') !== -1
          ? file.name.split('.').pop().toLowerCase()
          : '';
      }

      function adminValidateSelectedFiles(form) {
        var inputs = form.querySelectorAll('input[type="file"]');
        var imageExts = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
        var logoExts = ['jpeg', 'jpg', 'png', 'svg', 'webp'];
        var videoExts = ['mp4', 'mov', 'avi', 'wmv', 'webm'];

        for (var i = 0; i < inputs.length; i++) {
          var input = inputs[i];
          if (!input.files || !input.files.length) {
            continue;
          }

          for (var j = 0; j < input.files.length; j++) {
            var file = input.files[j];
            var ext = adminFileExtension(file);
            var name = input.getAttribute('name') || 'file';

            if (name === 'platform_logo') {
              if (logoExts.indexOf(ext) === -1 || file.size > 4 * 1024 * 1024) {
                return 'Platform Logo must be JPG, PNG, SVG, or WebP and 4 MB or smaller.';
              }
              continue;
            }

            if (name === 'video') {
              if (videoExts.indexOf(ext) === -1 || file.size > 100 * 1024 * 1024) {
                return 'Video must be MP4, MOV, AVI, WMV, or WebM and 100 MB or smaller.';
              }
              continue;
            }

            if (imageExts.indexOf(ext) !== -1 && file.size > 4 * 1024 * 1024) {
              return 'Image files must be 4 MB or smaller.';
            }
          }
        }

        return '';
      }

      function adminExtractUploadError(xhr) {
        var fallback = 'The server returned an error. Please check the selected file and try again.';

        try {
          var data = JSON.parse(xhr.responseText || '{}');
          if (data.errors) {
            var messages = [];
            Object.keys(data.errors).forEach(function (key) {
              if (data.errors[key] && data.errors[key].length) {
                messages.push(data.errors[key][0]);
              }
            });

            if (messages.length) {
              return messages.join(' ');
            }
          }

          if (data.message) {
            return data.message;
          }
        } catch (ignore) {}

        var match = (xhr.responseText || '').match(/<li>(.*?)<\/li>/i);
        if (match && match[1]) {
          return match[1].replace(/<[^>]+>/g, '');
        }

        return fallback;
      }

      function adminUploadProgressBox(form) {
        var box = form.querySelector('.admin-upload-progress');
        if (box) {
          return box;
        }

        box = document.createElement('div');
        box.className = 'admin-upload-progress';
        box.innerHTML = '<div class="admin-upload-progress__label"><span class="admin-upload-progress__status">Preparing upload...</span><span class="admin-upload-progress__percent">0%</span></div><div class="admin-upload-progress__track"><div class="admin-upload-progress__bar"></div></div><div class="admin-upload-progress__hint">Keep this page open until the upload finishes.</div>';

        var firstButton = form.querySelector('button[type="submit"], input[type="submit"]');
        if (firstButton && firstButton.parentNode) {
          firstButton.parentNode.insertBefore(box, firstButton);
        } else {
          form.insertBefore(box, form.firstChild);
        }

        return box;
      }

      function setAdminUploadProgress(box, percent, status, hint) {
        percent = Math.max(0, Math.min(100, Math.round(percent || 0)));
        box.classList.add('is-visible');
        box.querySelector('.admin-upload-progress__bar').style.width = percent + '%';
        box.querySelector('.admin-upload-progress__percent').textContent = percent + '%';
        box.querySelector('.admin-upload-progress__status').textContent = status || 'Uploading...';
        if (hint) {
          box.querySelector('.admin-upload-progress__hint').textContent = hint;
        }
      }

      $(document).on('submit', 'form[enctype="multipart/form-data"]', function (event) {
        var form = this;

        if (event.isDefaultPrevented && event.isDefaultPrevented()) {
          return;
        }

        if (form.getAttribute('data-upload-progress') === 'off' || !adminFormHasSelectedFiles(form)) {
          return;
        }

        var validationMessage = adminValidateSelectedFiles(form);
        if (validationMessage) {
          event.preventDefault();
          setAdminUploadError(form, validationMessage);
          return;
        }

        event.preventDefault();
        clearAdminUploadError(form);

        if (window.CKEDITOR) {
          Object.keys(CKEDITOR.instances).forEach(function (key) {
            CKEDITOR.instances[key].updateElement();
          });
        }

        var box = adminUploadProgressBox(form);
        var submitButtons = $(form).find('button[type="submit"], input[type="submit"]');
        var xhr = new XMLHttpRequest();
        var formData = new FormData(form);

        setAdminUploadProgress(box, 0, 'Starting upload...', 'Your browser is sending the selected file to the server.');
        submitButtons.prop('disabled', true);

        xhr.upload.addEventListener('progress', function (progressEvent) {
          if (!progressEvent.lengthComputable) {
            setAdminUploadProgress(box, 5, 'Uploading...', 'Calculating upload progress...');
            return;
          }

          var percent = (progressEvent.loaded / progressEvent.total) * 100;
          if (percent >= 100) {
            setAdminUploadProgress(box, 100, 'Upload received. Processing on Cloudinary...', 'Please wait while the server saves the Cloudinary link.');
          } else {
            setAdminUploadProgress(box, percent, 'Uploading...', Math.round(progressEvent.loaded / 1024 / 1024) + ' MB of ' + Math.round(progressEvent.total / 1024 / 1024) + ' MB uploaded.');
          }
        });

        xhr.addEventListener('load', function () {
          submitButtons.prop('disabled', false);

          if (xhr.status >= 200 && xhr.status < 400) {
            setAdminUploadProgress(box, 100, 'Upload complete.', 'Redirecting...');
            if (xhr.responseURL && xhr.responseURL !== window.location.href) {
              window.location.href = xhr.responseURL;
              return;
            }

            window.location.reload();
            return;
          }

          var errorMessage = adminExtractUploadError(xhr);
          setAdminUploadError(form, errorMessage);
          setAdminUploadProgress(box, 100, 'Upload failed.', errorMessage);
        });

        xhr.addEventListener('error', function () {
          submitButtons.prop('disabled', false);
          setAdminUploadError(form, 'Network error. Please try again.');
          setAdminUploadProgress(box, 100, 'Upload failed.', 'Network error. Please try again.');
        });

        xhr.open((form.method || 'POST').toUpperCase(), form.action || window.location.href, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
      });
    </script>
    
    <script type="text/javascript">
             $(document).ready(function() {
                if ($('#example').length && $.fn.DataTable) {
                    $('#example').DataTable();
                }
            } );

             $(function() {
                if ($.fn.responsiveTable) {
                    $('.table-responsive').responsiveTable({
                        addDisplayAllBtn: 'btn btn-secondary'
                    });
                }
            });
         </script>
    
    <script type="text/javascript">
        var data = {
            labels: ["January", "February", "March", "April", "May"],
            datasets: [{
                    label: "My First dataset",
                    fillColor: "rgba(220,220,220,0.2)",
                    strokeColor: "rgba(220,220,220,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: [65, 59, 80, 81, 56]
                },
                {
                    label: "My Second dataset",
                    fillColor: "rgba(151,187,205,0.2)",
                    strokeColor: "rgba(151,187,205,1)",
                    pointColor: "rgba(151,187,205,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(151,187,205,1)",
                    data: [28, 48, 40, 19, 86]
                }
            ]
        };
        var pdata = [{
                value: 300,
                color: "#46BFBD",
                highlight: "#5AD3D1",
                label: "Complete"
            },
            {
                value: 50,
                color: "#F7464A",
                highlight: "#FF5A5E",
                label: "In-Progress"
            }
        ]

        var lineChartCanvas = $("#lineChartDemo").get(0);
        if (lineChartCanvas && window.Chart) {
            var ctxl = lineChartCanvas.getContext("2d");
            var lineChart = new Chart(ctxl).Line(data);
        }

        var pieChartCanvas = $("#pieChartDemo").get(0);
        if (pieChartCanvas && window.Chart) {
            var ctxp = pieChartCanvas.getContext("2d");
            var pieChart = new Chart(ctxp).Pie(pdata);
        }

    </script>
    
    <!-- Google analytics script-->

</body>

</html>
