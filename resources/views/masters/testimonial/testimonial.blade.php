@extends('masters.layout.default_layout')
@section('content')
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-flag"></i> Testimonial List</h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <a class="btn btn-primary icon-btn" href="{{route('add_testimonial')}}"><i class="fa fa-plus"></i> Add Testimonial</a>
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
                <div class="table-rep-plugin">
                    <div class="table-responsive" data-pattern="priority-columns">
                        <table id="example" class="table  table-striped table-bordered" cellspacing="0" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th> Testimonial Image </th>
                                    <th> Platform Logo </th>
                                    <th> Name </th>
                                    <th> Heading</th>
                                    <th> Description </th>
                                    <th colspan="1">
                                        <center>Action</center>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1 @endphp
                                @foreach($testimonial_data as $data)
                                @php
                                    $testimonialImage = trim((string) $data->image);
                                    $hasTestimonialImage = z_media_exists($testimonialImage, 'testimonial');
                                    $platformLogo = isset($data->platform_logo) ? trim((string) $data->platform_logo) : '';
                                    $hasPlatformLogo = z_media_exists($platformLogo, 'testimonial-logos');
                                @endphp
                                <tr>
                                    <td>{{$i}}.</td>
                                    <td>
                                        @if($hasTestimonialImage)
                                            <img src="{{ z_media_url($testimonialImage, 'testimonial') }}" class="admin-testimonial-thumb" alt="{{ $data->name }}">
                                        @else
                                            <div class="admin-testimonial-placeholder">
                                                {{ strtoupper(substr(trim($data->name ?: 'Z'), 0, 1)) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Optional platform logo preview; blank when no valid logo exists. --}}
                                        @if($hasPlatformLogo)
                                            <img src="{{ z_media_url($platformLogo, 'testimonial-logos') }}" class="admin-platform-logo-thumb" alt="Platform logo for {{ $data->name }}" onerror="this.style.display='none';">
                                        @endif
                                    </td>
                                    <td>{{$data->name}}</td>
                                    <td>{{$data->heading}}</td>
                                    <td><?php echo $data->description; ?></td>

                                    
                                    <td class="text-center">
                                        <a href="{{route('testimonialUpdate',$data->testimonial_id)}}"><span class="basic_table_icon" style="font-size: 20px;color: green;"><i class="fa fa-pencil" aria-hidden="true"></i></span></a>
                                        <a href="{{route('testimonialDelete',$data->testimonial_id)}}" onClick="return confirm('Are you sure? This item will move to Recycle Bin.');"><span class="basic_table_icon" style="font-size: 20px;color: red;margin-left: 20px;"><i class="fa fa-trash-o" aria-hidden="true"></i></span></a>
                                    </td>
                                </tr>
                                @php $i++ @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@stop
