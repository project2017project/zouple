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
                    @foreach($testimonial_datas as $data)
                    @php
                        $testimonialImage = trim((string) $data->image);
                        $hasTestimonialImage = z_media_exists($testimonialImage, 'testimonial');
                        $platformLogo = isset($data->platform_logo) ? trim((string) $data->platform_logo) : '';
                    @endphp
                    <form action="{{route('testimonialEdit_save')}}" method="post" enctype="multipart/form-data">
                      @csrf
                      <input type="hidden" name="testimonial_id" value="{{$data->testimonial_id}}">
                        <div class="row col-sm-12">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Name<span class="text-danger"> <b> * </b></span></label>
                                    <input class="form-control" type="text" name="name" required placeholder="Name" value="{{$data->name}}">
                                </div>
                            </div> 

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Heading<span class="text-danger"> <b> * </b></span></label>
                                    <input class="form-control" type="text" name="heading" required placeholder="Heading" value="{{$data->heading}}">
                                </div>
                            </div> 

                            

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">
                                        Testimonial Image
                                        @if(!$hasTestimonialImage)
                                            <span class="text-danger"> <b> * </b></span>
                                        @else
                                            <small class="text-muted">(optional)</small>
                                        @endif
                                    </label>
                                    <input class="form-control" type="file" name="image" accept="image/png,image/gif,image/jpeg,image/webp,image/bmp" @if(!$hasTestimonialImage) required @endif>
                                </div>
                            </div>

                            <div class="col-sm-6 pb-4">
                                @if($hasTestimonialImage)
                                    <img src="{{ z_media_url($testimonialImage, 'testimonial') }}" class="admin-testimonial-thumb" alt="{{ $data->name }}">
                                @else
                                    <div class="admin-testimonial-placeholder">
                                        {{ strtoupper(substr(trim($data->name ?: 'Z'), 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            {{-- Platform logo edit: replace or remove optional source logo. --}}
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Platform Logo <small class="text-muted">(optional)</small></label>
                                    <input class="form-control" type="file" name="platform_logo" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                                    <small class="text-muted">Upload JPG, PNG, SVG, or WebP up to 50 MB.</small>
                                </div>
                            </div>

                            <div class="col-sm-6 pb-4">
                                @if($platformLogo !== '')
                                    <img src="{{ z_media_url($platformLogo, 'testimonial-logos') }}" class="admin-platform-logo-thumb" alt="Platform logo for {{ $data->name }}" onerror="this.style.display='none';">
                                    <label class="d-block mt-2 mb-0">
                                        <input type="checkbox" name="remove_platform_logo" value="1">
                                        <span class="text-danger">Remove platform logo</span>
                                    </label>
                                @else
                                    <small class="text-muted">No platform logo uploaded.</small>
                                @endif
                            </div>
                            
                            
                            
                            

                            

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label"> Description<span class="text-danger"> <b> * </b> </label>
                                    <textarea class="form-control"  name="description" id="summary-ckeditor" required placeholder="Description"><?php echo $data->description; ?></textarea>
                                </div>
                            </div>


                            <div class="col-sm-12">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Save</button>&nbsp;
                            </div>
                        </div>
                    </form>

                    @endforeach
                </div>
            </div>
        </div>
    </main>
    <!-- Essential javascripts for application to work-->



@stop
