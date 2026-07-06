@extends('masters.layout.default_layout')
@section('content')
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class="fa fa-flag"></i>  Add Testimonial</h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <a class="btn btn-primary icon-btn" href="{{route('testimonial')}}"><i class="fa fa-eye"></i> Testimonial List</a>
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
                    <form action="{{route('testimonial_save')}}" method="post" enctype="multipart/form-data">
                      @csrf
                        <div class="row col-sm-12">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Name<span class="text-danger"> <b> * </b></span></label>
                                    <input class="form-control" type="text" name="name" required placeholder="Name">
                                </div>
                            </div> 

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Heading<span class="text-danger"> <b> * </b></span></label>
                                    <input class="form-control" type="text" name="heading" required placeholder="Heading">
                                </div>
                            </div> 

                            

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Testimonial Image <span class="text-danger"> <b> * </b></span> <!-- <span class="text-danger">(Image Dimensions - 1920*700 Pixel *)</span> --></label>
                                    <input class="form-control" type="file" name="image" accept="image/png,image/gif,image/jpeg,image/webp,image/bmp" autofocus required>
                                </div>
                            </div>

                            {{-- Platform logo upload: optional source logo shown beside frontend stars. --}}
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Platform Logo <small class="text-muted">(optional)</small></label>
                                    <input class="form-control" type="file" name="platform_logo" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                                    <small class="text-muted">Upload JPG, PNG, SVG, or WebP up to 50 MB.</small>
                                </div>
                            </div>
                            
                            
                            
                            

                            

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label"> Description<span class="text-danger"> <b> * </b> </label>
                                    <textarea class="form-control"  name="description" id="summary-ckeditor" required placeholder="Description"></textarea>
                                </div>
                            </div>


                            <div class="col-sm-12">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Save</button>&nbsp;
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <!-- Essential javascripts for application to work-->



@stop
