@extends('masters.layout.default_layout')
@section('content')
<style>
    .flash_hide {
        display: none;
    }

    .flash_show {
        display: flex;
    }

</style>
<script type="text/javascript" src="{{URL::asset('public/js/213jquery.min.js')}}"></script>

<script src="{{URL::asset('public/js/jquery.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $("form").submit(function(e) {
            var messageLength = CKEDITOR.instances['summary-ckeditor'].getData().replace(/<[^>]*>/gi, '').length;
            if (!messageLength) {
                alert('Please Fill Product Specification');
                e.preventDefault();
            }
        });
    })
    $(document).ready(function() {
        $("form").submit(function(e) {
            var messageLength = CKEDITOR.instances['summary-ckeditor1'].getData().replace(/<[^>]*>/gi, '').length;
            if (!messageLength) {
                alert('Please Fill Product Description');
                e.preventDefault();
            }
        });
    })
    $(document).ready(function() {
        $("form").submit(function(e) {
            var messageLength = CKEDITOR.instances['summary-ckeditor2'].getData().replace(/<[^>]*>/gi, '').length;
            if (!messageLength) {
                alert('Please Fill Product Addition Information');
                e.preventDefault();
            }
        });
    })

</script>

<script>
    function maxLengthCheck(object) {
        if (object.value.length > object.maxLength)
            object.value = object.value.slice(0, object.maxLength)
    }

    function isNumeric(evt) {
        var theEvent = evt || window.event;
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode(key);
        var regex = /[0-9]|\./;
        if (!regex.test(key)) {
            theEvent.returnValue = false;
            if (theEvent.preventDefault) theEvent.preventDefault();
        }
    }

    function changeFlashSale(str) {
        if (str == "YES") {
            $('#flash_sale').addClass('flash_show');
            $('#flash_sale').removeClass('flash_hide');
        } else {
            $('#flash_sale').addClass('flash_hide');
            $('#flash_sale').removeClass('flash_show');
        }

    }

</script>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-tree"></i> Add Product</h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <a class="btn btn-primary icon-btn" href="{{route('product_list')}}"><i class="fa fa-eye"></i> Product List</a>
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
                <form action="{{route('product_save')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="control-label"> Choose Categories <span class="text-danger"> <b> * </b> </span></label>
                            <div class="form-group py-3 px-3" style="border:2px solid #CED4DA; height:370px; overflow:auto;border-radius:5px;">
                                @foreach($categories as $category)
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" value="{{$category->category_id}}" name="category[]">{{$category->title}}
                                    </label>
                                </div>
                                <?php $childs = $category->childs; ?>
                                @if(count($category->childs))
                                <ul type="none">
                                    @foreach($childs as $child)
                                    @if($child->is_active == "ACTIVE")
                                    <li>
                                        <div class="form-check pt-1">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="checkbox" value="{{$child->category_id}}" name="category[]">{{$child->title}}
                                            </label>
                                        </div>
                                        <?php $childs = $child->childs; ?>
                                        <ul type="none">
                                            @if(count($child->childs))
                                            @foreach($childs as $child)
                                            @if($child->is_active == "ACTIVE")
                                            <li>
                                                <div class="form-check pt-1">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="checkbox" value="{{$child->category_id}}" name="category[]">{{$child->title}}
                                                    </label>
                                                </div>

                                            </li>
                                            @endif
                                            @endforeach
                                            @endif
                                        </ul>
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="col-sm-6">

                            <div class="form-group">
                                <label class="control-label"> Product Vendors <span class="text-danger"> <b> * </b> </span><span class="text-danger"><b></b></span></label>
                                <select class="form-control" required name="vendor_id">
                                    <option value=""> -- Select Field -- </option>
                                    @foreach($vendors_data as $vendors)
                                    <option value="{{$vendors->vendor_id}}">{{$vendors->vendor_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="control-label"> Product SKU <span class="text-danger"> <b> * </b> </span></label>
                                <input type="text" name="product_sku" id="product_sku" class="form-control" required placeholder="Product SKU">
                            </div>
                            <div class="form-group">
                                <label class="control-label"> Product Title <span class="text-danger"> <b> * </b> </span></label>
                                <input type="text" name="product_title" id="Product" class="form-control" required placeholder="Product Title">
                            </div> 
                            <div class="form-group">
                                <label class="control-label"> Product HSN <span class="text-danger"> <b> </b> </span></label>
                                <input type="text" name="product_hsn" id="Product" class="form-control" required placeholder="Product HSN">
                            </div>
                            <div class="form-group">
                                <label class="control-label"> Product GST <span class="text-danger"> <b> * </b> </span></label>
                                <input type="number" name="product_gst" class="form-control" required placeholder="Product GST">
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-repsonsive">
                                <span id="error"></span>
                                <table class="table table-bordered" id="item_table">
                                    <tr class="bg-light">
                                        <th>Attribut Name</th>
                                        <th>Attribut Values</th>
                                        <th class="text-center"><button type="button" name="add" class="btn btn-success  add">+</button></th>
                                    </tr>
                                </table>
                            </div>


                        </div>
                    </div>
                    <hr>
                    
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label"> Product Header Image<span class="text-danger"><b> (184*245 Pixel *)</b></span></label>
                                <input type="file" name="product_header_image" id="pincode" class="form-control" required accept="image/png,image/gif,image/jpeg,image/webp">
                                <small class="text-muted">Upload JPG, PNG, GIF, or WebP up to 120 MB.</small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label"> Product Images <span class="text-danger"><b> (Multiple Iamge - 720*900 Pixel *)</b></span></label>
                                <input type="file" name="product_images[]" class="form-control" required accept="image/png,image/gif,image/jpeg,image/webp" multiple>
                                <small class="text-muted">Each image can be up to 120 MB.</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label"> Is New Product <span class="text-danger"> <b> * </b> </span></label>
                                <div class="row">
                                    <div class="col-sm-4">

                                        <label>
                                            <input required type="radio" name="new_arrivals" value="YES" class="required"><span class="label-text"> Yes</span>
                                        </label>

                                    </div>
                                    <div class="col-sm-4">

                                        <label>
                                            <input type="radio" name="new_arrivals" value="NO"><span class="label-text"> No</span>
                                        </label>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label"> Is Featured Product <span class="text-danger"> <b> * </b> </span></label>
                                <div class="row">
                                    <div class="col-sm-4">

                                        <label>
                                            <input required type="radio" name="featured_product" value="YES" class="required"><span class="label-text"> Yes</span>
                                        </label>

                                    </div>
                                    <div class="col-sm-4">

                                        <label>
                                            <input type="radio" name="featured_product" value="NO"><span class="label-text"> No</span>
                                        </label>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label"> In Stock <span class="text-danger"> <b> * </b> </span></label>
                                <div class="row">
                                    <div class="col-sm-4">

                                        <label>
                                            <input type="radio" name="in_stock" value="INSTOCK" required><span class="label-text"> In Stock</span>
                                        </label>
                                    </div>
                                    <div class="col-sm-4">

                                        <label>
                                            <input type="radio" name="in_stock" value="OUTSTOCK"><span class="label-text"> Out Stock</span>
                                        </label>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label"> Is Active <span class="text-danger"> <b> * </b> </span></label>
                                <div class="row">
                                    <div class="col-sm-4">

                                        <label>
                                            <input type="radio" name="is_active" value="ACTIVE" required><span class="label-text"> Active</span>
                                        </label>
                                    </div>

                                    <div class="col-sm-4">

                                        <label>
                                            <input type="radio" name="is_active" value="INACTIVE"><span class="label-text"> Inactive</span>
                                        </label>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    
                    
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label"> Meta Title <span class="text-danger"> <b> * </b> </span></label>
                                <input type="text" name="meta_title" id="Product" class="form-control" required placeholder="Meta Title">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label"> Meta Keyword <span class="text-danger"> <b> * </b> </span></label>
                                <input type="text" name="meta_keyword" id="Product" class="form-control" required placeholder="Meta Keyword">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"> Meta Description <span class="text-danger"> <b> * </b> </span></label>
                                <textarea type="text" name="meta_description" id="Product" class="form-control" required placeholder="Meta Description"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"> Amazon Product Link <span class="text-info"><b>(Optional - Shop on Amazon button will show if filled)</b></span></label>
                                <input type="url" name="amazon_link" id="amazon_link" class="form-control" placeholder="https://www.amazon.in/dp/...">  
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"> Product Specification <span class="text-danger"> <b> * </b> </span></label>
                                <textarea id="summary-ckeditor" name="product_specification" class="form-control" required></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"> Product Description <span class="text-danger"> <b> * </b> </span></label>
                                <textarea id="summary-ckeditor1" name="product_description" class="form-control" required></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"> Product Addition Information <span class="text-danger"> <b> * </b> </span></label>
                                <textarea id="summary-ckeditor2" name="product_addition_information" class="form-control" required></textarea>
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


<script>
    $(document).ready(function() {

        $(document).on('click', '.add', function() {
            var html = '';
            html += '<tr>';
            html += '<td><select name="attribute_name[]" id="attribute_id[]" class="form-control attribute_id" onchange = "oncheckvalue(this.value)" required><option value="">Select Field</option>@foreach($attribute_data as $attribute)<option value="{{$attribute->name}}">{{$attribute->name}}</option>@endforeach</select></td>';
            
            html += '<td>';
            html += '<div class="mb-1"><small class="text-muted">Value 1 (required)</small><input type="text" name="attribute_val1[]" class="form-control mb-1 attribute_value" placeholder="e.g. Small" required /></div>';
            html += '<div class="mb-1"><small class="text-muted">Value 2</small><input type="text" name="attribute_val2[]" class="form-control mb-1" placeholder="e.g. Medium" /></div>';
            html += '<div class="mb-1"><small class="text-muted">Value 3</small><input type="text" name="attribute_val3[]" class="form-control mb-1" placeholder="e.g. Large" /></div>';
            html += '<div class="mb-1"><small class="text-muted">Value 4</small><input type="text" name="attribute_val4[]" class="form-control mb-1" placeholder="e.g. XL" /></div>';
            html += '<div><small class="text-muted">Value 5</small><input type="text" name="attribute_val5[]" class="form-control" placeholder="e.g. XXL" /></div>';
            html += '</td>';
        
            html += '<td class="text-center"><button type="button" name="remove" class="btn btn-danger remove">-</button></td></tr>';
            $('#item_table').append(html);
        });

        $(document).on('click', '.remove', function() {
            $(this).closest('tr').remove();
        });

        $('#insert_form').on('submit', function(event) {
            event.preventDefault();
            var error = '';
            $('.attribute_id').each(function() {
                var count = 1;
                if ($(this).val() == '') {
                    error += "<p>Enter Item Name at " + count + " Row</p>";
                    return false;
                }
                count = count + 1;
            });

            $('.attribute_value').each(function() {
                var count = 1;
                if ($(this).val() == '') {
                    error += "<p>Enter Item Quantity at " + count + " Row</p>";
                    return false;
                }
                count = count + 1;
            });

            
        });

    });

</script>






@stop
