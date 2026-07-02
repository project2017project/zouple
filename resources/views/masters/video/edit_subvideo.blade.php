@extends('masters.layout.default_layout')
@section('content')
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-video-camera"></i>
                Edit Sub Video
            </h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <a class="btn btn-primary icon-btn" href="{{route('subVideo')}}"><i class="fa fa-eye"></i> Sub Video List</a>
        </ul>
    </div>
    <div class="row bg-white py-3">
        <div class="col-md-12">
            @if (isset($errors) && count($errors) > 0)
            <div class="alert alert-danger">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="flash-message">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('alert-' . $msg))

                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                </p>
                @endif
                @endforeach
            </div>
            <div class="card-box">
                <form action="{{route('subvideoUpdateSave')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="alert alert-danger d-none js-video-upload-error"></div>
                    @foreach($videos_data as $data)
                    <div class="row col-sm-12">
                        <div class="col-sm-6">
                            <div class="form-group">
                                @if(!empty($data->video))
                                    <video width="320" height="240" controls>
                                        <source src="{{ z_media_url($data->video, 'video') }}" type="video/mp4">
                                    </video>
                                @else
                                    <div class="alert alert-info">No sub video uploaded.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label"> Sub Video </label>
                                <input class="form-control js-video-upload-input" type="file" name="video" accept="video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/webm" data-max-size-mb="100">
                                <small class="form-text text-muted">Upload MP4, MOV, AVI, WMV, or WebM up to 100 MB.</small>
                            </div>
                            @if(!empty($data->video))
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="remove_video" value="1"> Remove current video from frontend
                                </label>
                            </div>
                            @endif
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label"> Title <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="title" value="{{$data->title}}" required>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"> Description <span class="text-danger">*</span></label>
                                <textarea id="summary-ckeditor" name="description" class="form-control" required><?php echo $data->description; ?></textarea>
                            </div>
                        </div>



                        <div class="col-sm-12">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update</button>&nbsp;&nbsp;&nbsp;
                        </div>
                    </div>
                    @endforeach
                </form>
            </div>
        </div>
    </div>
</main>
<!-- Essential javascripts for application to work-->



@stop
