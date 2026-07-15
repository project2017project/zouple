
@extends('masters.layout.default_layout')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script type="text/javascript">
    $('document').ready(function() {
        $('input[type="checkbox"]').click(function() {
            if ($(this).prop("checked") == true) {
                var st = concat_string(this.value);

            } else if ($(this).prop("checked") == false) {
                var st = remove_string(this.value);

            }
        });

    });


    var val = "";

    function concat_string(str) {
        var kno = str;
        val = val.concat(kno);
    }

    function remove_string(str) {
        val = val.replace(str, "");
    }

    function exportSelctedData()
    {
        var product_ids = val;
        var url = "exportSelctedProduct?pro_ids="+product_ids;
        document.location.href=url;
    }



</script>
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class="fa fa-tree"></i> Product List</h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <a class="btn btn-primary icon-btn" href="{{route('viewFlashProduct')}}"><i class="fa fa-eye"></i> View Flash Porduct </a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                
                <a class="btn btn-primary icon-btn" href="{{route('add_product')}}"><i class="fa fa-plus"></i> Add Product</a>
                &nbsp; &nbsp;&nbsp; &nbsp;
                <a class="btn btn-primary icon-btn" href="{{route('export')}}"><i class="fa fa-download"></i> Export All</a>

                 &nbsp; &nbsp;&nbsp; &nbsp;
                <a class="btn btn-primary icon-btn" onclick="exportSelctedData()" href="#"><i class="fa fa-download"></i> Export Selected</a>

                &nbsp; &nbsp;&nbsp; &nbsp;
                <a class="btn btn-primary icon-btn" href="{{route('filterProduct')}}"><i class="fa fa-filter"></i> Filter Product</a>

                <!-- &nbsp; &nbsp;&nbsp; &nbsp;
                <button class="btn btn-primary icon-btn" type="submit"> <i class="fa fa-download"></i> Selected Export</button> -->
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
                                        <th></th>
                                        <th>S.No</th>
                                        <th>Category</th>
                                        <th> SKU</th>
                                        <!-- <th> Slug </th>
                                        <th> Title </th> 
                                        <th> GST </th>-->
                                        <th> Image </th>
                                        
                                        <th> Price</th>
                                        <!--<th>In Stock </th>-->
                                        <th>Status </th>


                                        <th colspan="1">
                                            <center>Action</center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1 @endphp
                                    @foreach($product_data as $data)
                                    <?php $pro_id = $data->product_id;?>
                                    <tr>
                                        <td><input type="checkbox" name="product_id[]" value="{{$data->product_id}},"></td>
                                        <td>{{$i}}.</td>
                                        <td>
                                        @php
                                            $catArray = json_decode($data->category, true);
                                            $catArray = is_array($catArray) ? $catArray : [];
                                        @endphp
                                        @forelse($catArray as $kj)
                                            {{ $cateName[$kj] ?? 'Category missing' }}<br>
                                        @empty
                                            <span class="text-muted">No category</span>
                                        @endforelse
                                           
                                        </td>
                                        <td><a href="{{route('productshowdetail',$pro_id)}}">{{$data->product_sku}}</a></td>
                                      
                                        <!-- <td><a href="{{route('productshowdetail',$pro_id)}}">{{$data->slug}}</a></td>
                                        <td><a href="{{route('productshowdetail',$data->product_id)}}">{{$data->product_title}}</a></td>
                                        <td>{{$data->product_gst}}</td> -->
                                        
                                        @php
                                            $productHeaderImage = trim((string) $data->product_header_image);
                                            $hasProductHeaderImage = z_media_exists($productHeaderImage, 'product');
                                        @endphp
                                        <td>
                                            @if($hasProductHeaderImage)
                                                <img src="{{ z_media_url($productHeaderImage, 'product') }}" class="admin-product-thumb" alt="{{ $data->product_title }}">
                                            @else
                                                <div class="admin-product-placeholder">{{ strtoupper(substr(trim($data->product_title ?: 'P'), 0, 1)) }}</div>
                                            @endif
                                        </td>
                                        
                                        <td class="text-center">
                                            <b>INR : {{$data->rupee_net_with_gst}}<br>
                                            Doller : {{$data->dollar_net_with_gst}}<br>
                                            Euro : {{$data->euro_net_with_gst}}<br>
                                           </b>  
                                        </td>
                                        <!--<td class="text-center">
                                            <b>{{$data->in_stock}}</b>
                                        <br>
                                            <form class="mt-4" action="{{route('product_isstock_update')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{$data->product_id}}">
                                                <select class="form-control" required name="in_stock">
                                                    <option value="">--Select--</option>
                                                    <option value="INSTOCK">In Stock</option>
                                                    <option value="OUTSTOCK">Out Stock</option>
                                                </select>
                                                <input type="submit" value="Update" class="form-control mt-2 btn-info">
                                            </form> 
                                        </td>-->
                                        <td class="text-center">
                                            <b>{{$data->is_active}}</b>
                                            <br>
                                            <form class="mt-4" action="{{route('product_inactive_update')}}">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{$data->product_id}}" >
                                                <select class="form-control" required name="is_active">
                                                    <option value="">--Select--</option>
                                                    <option value="ACTIVE">Active</option>
                                                    <option value="INACTIVE">In Active</option>
                                                </select>
                                                <input type="submit" value="Update" class="form-control mt-2 btn-info">
                                            </form> 
                                        </td>
                                       
                                        <td class="text-center">
                                            <a href="{{route('productUpdate',$data->product_id)}}"><span class="basic_table_icon" style="font-size: 20px;color: green;"><i class="fa fa-pencil" aria-hidden="true"></i></span></a>
                                            <a href="{{route('productDelete',$data->product_id)}}" onClick="return confirm('Are you sure? This item will move to Recycle Bin.');"><span class="basic_table_icon" style="font-size: 20px;color: red;margin-left: 20px;"><i class="fa fa-trash-o" aria-hidden="true"></i></span></a>
                                            
                                            <a class="btn btn-primary icon-btn" href="{{route('product_quantity_update',$data->product_id)}}"><i class="fa fa-plus"></i> Update Quantity</a> 
                                            
                                            <a class="btn btn-primary icon-btn mt-2" href="{{route('flaceSales',$data->product_id)}}"><i class="fa fa-plus"></i> Flash Sales</a>
                                            
                                            <!--<a class="btn btn-primary icon-btn" href=""><i class="fa fa-plus"></i> Flash Sales</a>-->
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
