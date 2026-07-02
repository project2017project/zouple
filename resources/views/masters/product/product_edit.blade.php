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

   

</script>

<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-tree"></i> Update Product</h1>
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
                <form action="{{route('product_update_save')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    @foreach($product_update_data as $product_update)

                    <input type="hidden" name="product_id" value="{{$product_update->product_id}}">
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="control-label"> Choose Categories <span class="text-danger"> <b> * </b> </span></label>
                            <div class="form-group py-3 px-3" style="border:2px solid #CED4DA; height:370px; overflow:auto;border-radius:5px;">
                                <?php 
                                $categorys = json_decode($product_update->category);
                                ?>
                                @foreach($categories as $category)
                                <?php
                                    $id = $category->category_id;
                                    if(in_array($id,$categorys))
                                    {
                                        $check1 = "checked";   
                                    }
                                    else
                                    {
                                        $check1 = "";   
                                    }
                                    ?>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" value="{{$category->category_id}}" name="category[]" {{$check1}}>{{$category->title}}
                                    </label>
                                </div>
                                <?php $childs = $category->childs; ?>
                                @if(count($category->childs))
                                <ul type="none">
                                    @foreach($childs as $child)
                                    <?php
                                    $id = $child->category_id;
                                    if(in_array($id,$categorys))
                                    {
                                        $check2 = "checked";   
                                    }
                                    else
                                    {
                                        $check2 = "";   
                                    }
                                    ?>
                                    @if($child->is_active == "ACTIVE")
                                    <li>
                                        <div class="form-check pt-1">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="checkbox" value="{{$child->category_id}}" name="category[]" {{$check2}}>{{$child->title}}
                                            </label>
                                        </div>
                                        <?php $childs = $child->childs; ?>
                                        <ul type="none">
                                            @if(count($child->childs))
                                            @foreach($childs as $child)
                                            <?php
                                            $id = $child->category_id;
                                            if(in_array($id,$categorys))
                                            {
                                                $check3 = "checked";   
                                            }
                                            else
                                            {
                                                $check3 = "";   
                                            }
                                            ?>
                                            @if($child->is_active == "ACTIVE")
                                            <li>
                                                <div class="form-check pt-1">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="checkbox" value="{{$child->category_id}}" name="category[]" {{$check3}}>{{$child->title}}
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
                                    <option value="{{$vendors->vendor_id}}" {{$vendors->vendor_id == $product_update->vendor_id ? 'selected' : ''}}> {{$vendors->vendor_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="control-label"> Product SKU <span class="text-danger"> <b> * </b> </span></label>
                                <input type="text" name="product_sku" id="product_sku" class="form-control" required placeholder="Product SKU" value="{{$product_update->product_sku}}">
                            </div>
                            <div class="form-group">
                                <label class="control-label"> Product Title <span class="text-danger"> <b> * </b> </span></label>
                                <input type="text" name="product_title" id="Product" class="form-control" required placeholder="Product Title" value="{{$product_update->product_title}}">
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label"> Product HSN <span class="text-danger"> <b> * </b> </span></label>
                                <input type="number" name="product_hsn" id="pincode" class="form-control" required placeholder="Product Weight" value="{{$product_update->product_hsn}}">
                            </div>
                            <div class="form-group">
                                <label class="control-label"> Product GST <span class="text-danger"> <b> * </b> </span></label>
                                <input type="number" name="product_gst" class="form-control" required placeholder="Product GST" value="{{$product_update->product_gst}}">
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
                                        <th>Attributes Name</th>
                                        <th>Attributes Values</th>
                                        <th class="text-center"><button type="button" name="add" class="btn btn-success  add">+</button></th>
                                    </tr>

                                    @foreach($product_attributes_data as $attribute)
                                    
                                    <?php
                                    $attri = json_decode($attribute->attribute_value);
                                    ?>
                                    <tr>
                                        <td>
                                            <select name="attribute_name[]" id="attribute_id[]" class="form-control attribute_id" required >
                                                <option value="{{$attribute->attribute_name}}">
                                                    Select Field
                                                </option>
                                                @foreach($attribute_data as $attributes)
                                                <option value="{{$attributes->name}}" {{$attributes->name == $attribute->attribute_name ? 'selected' : ''}}>
                                                    {{$attributes->name}}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>


                                        <?php
                                        $values = json_decode($attribute->attribute_value);
                                        $vals_arr = is_array($values) ? array_values($values) : [];
                                        ?>
                                        <td>
                                            <div class="mb-1"><small class="text-muted">Value 1 (required)</small><input type="text" name="attribute_val1[]" class="form-control mb-1 attribute_value" placeholder="e.g. Small" required value="{{$vals_arr[0] ?? ''}}" /></div>
                                            <div class="mb-1"><small class="text-muted">Value 2</small><input type="text" name="attribute_val2[]" class="form-control mb-1" placeholder="e.g. Medium" value="{{$vals_arr[1] ?? ''}}" /></div>
                                            <div class="mb-1"><small class="text-muted">Value 3</small><input type="text" name="attribute_val3[]" class="form-control mb-1" placeholder="e.g. Large" value="{{$vals_arr[2] ?? ''}}" /></div>
                                            <div class="mb-1"><small class="text-muted">Value 4</small><input type="text" name="attribute_val4[]" class="form-control mb-1" placeholder="e.g. XL" value="{{$vals_arr[3] ?? ''}}" /></div>
                                            <div><small class="text-muted">Value 5</small><input type="text" name="attribute_val5[]" class="form-control" placeholder="e.g. XXL" value="{{$vals_arr[4] ?? ''}}" /></div>
                                        </td>
                                        
                                        <td class="text-center"><button type="button" name="remove" class="btn btn-danger remove">-</button></td>
                                       
                                    </tr>
                                    @endforeach
                                </table>
                            </div>


                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label"> Product Header Image<span class="text-danger"><b> (184*245 Pixel *)</b></span></label>
                                <input type="file" name="product_header_image" id="product_header_image_input" class="form-control product-header-input" accept="image/png,image/gif,image/jpeg,image/webp">
                                <small class="text-muted d-block mt-1">Upload JPG, PNG, GIF, or WebP up to 120 MB.</small>

                                <input type="hidden" name="product_header_image_old" value="{{$product_update->product_header_image}}">
                                @php
                                    $productHeaderImage = trim((string) $product_update->product_header_image);
                                    $hasProductHeaderImage = z_media_exists($productHeaderImage, 'product');
                                @endphp
                                @if($hasProductHeaderImage)
                                    <img src="{{ z_media_url($productHeaderImage, 'product') }}" class="admin-product-thumb" alt="{{ $product_update->product_title }}">
                                @else
                                    <div class="admin-product-placeholder">{{ strtoupper(substr(trim($product_update->product_title ?: 'P'), 0, 1)) }}</div>
                                @endif
                                <div class="admin-selected-preview-block" id="product_header_preview_block" style="display:none;">
                                    <small class="text-muted d-block mt-2">Selected new header image, not saved yet:</small>
                                    <div class="admin-product-gallery-grid" id="product_header_preview"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label"> Product Images <span class="text-danger"><b> (Multiple Iamge - 400*400 Pixel *)</b></span></label>
                                <input type="file" name="product_images[]" id="product_gallery_input" class="form-control product-gallery-input" accept="image/png,image/gif,image/jpeg,image/webp" multiple>
                                <small class="text-muted d-block mt-1">Each gallery image can be up to 120 MB.</small>

                                <input type="hidden" name="product_images_old" value="{{$product_update->product_images}}">
                                <label class="admin-product-replace-gallery mt-2">
                                    <input type="checkbox" name="replace_gallery_images" value="1">
                                    Replace all old gallery images with newly uploaded images
                                </label>
                            </div>
                            <?php
                                $imgs = $product_gallery_images[$product_update->product_id] ?? json_decode($product_update->product_images, true);
                                $imgs = is_array($imgs) ? $imgs : [];
                            ?>
                                 <small class="text-muted d-block mb-2">Saved gallery images:</small>
                                 <div class="admin-product-gallery-grid" id="saved_product_gallery">
                                 @forelse($imgs as $val)
                                    @php
                                        $galleryImage = trim((string) $val);
                                        $hasGalleryImage = z_media_exists($galleryImage, 'product');
                                    @endphp
                                    <label class="admin-product-image-option">
                                    @if($hasGalleryImage)
                                        <img src="{{ z_media_url($galleryImage, 'product') }}" class="admin-product-thumb" alt="{{ $product_update->product_title }}">
                                    @else
                                        <div class="admin-product-placeholder">{{ strtoupper(substr(trim($product_update->product_title ?: 'P'), 0, 1)) }}</div>
                                    @endif
                                        @if($galleryImage !== '')
                                            <span class="admin-product-remove-check">
                                                <input type="checkbox" name="remove_gallery_images[]" value="{{ $galleryImage }}">
                                                Remove
                                            </span>
                                        @endif
                                    </label>
                                 @empty
                                    <div class="admin-product-empty-state">No gallery images saved yet. Choose files above and click Update.</div>
                                 @endforelse
                                 </div>
                                 <div class="admin-selected-preview-block" id="product_gallery_preview_block" style="display:none;">
                                    <small class="text-muted d-block mt-3 mb-2">Selected new gallery images, not saved yet:</small>
                                    <div class="admin-product-gallery-grid" id="product_gallery_preview"></div>
                                 </div>
                                 <small class="text-muted d-block mt-2">Tick Remove for selected old images, or use Replace All when uploading a fresh gallery.</small>
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
                                            <input required type="radio" name="new_arrivals" value="YES" class="required" {{$product_update->new_arrivals == "YES" ? 'checked' : ''}}><span class="label-text"> Yes</span>
                                        </label>

                                    </div>
                                    <div class="col-sm-4">

                                        <label>
                                            <input type="radio" name="new_arrivals" value="NO" {{$product_update->new_arrivals == "NO" ? 'checked' : ''}}><span class="label-text"> No</span>
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
                                            <input required type="radio" name="featured_product" value="YES" class="required" {{$product_update->featured_product == "YES" ? 'checked' : ''}}><span class="label-text"> Yes</span>
                                        </label>

                                    </div>
                                    <div class="col-sm-4">

                                        <label>
                                            <input type="radio" name="featured_product" value="NO" {{$product_update->featured_product == "NO" ? 'checked' : ''}}><span class="label-text"> No</span>
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
                                            <input type="radio" name="in_stock" value="INSTOCK" required {{$product_update->in_stock == "INSTOCK" ? 'checked' : ''}}><span class="label-text"> In Stock</span>
                                        </label>
                                    </div>
                                    <div class="col-sm-4">

                                        <label>
                                            <input type="radio" name="in_stock" value="OUTSTOCK" {{$product_update->in_stock == "OUTSTOCK" ? 'checked' : ''}}><span class="label-text"> Out Stock</span>
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
                                            <input type="radio" name="is_active" value="ACTIVE" required {{$product_update->is_active == "ACTIVE" ? 'checked' : ''}}><span class="label-text"> Active</span>
                                        </label>
                                    </div>

                                    <div class="col-sm-4">

                                        <label>
                                            <input type="radio" name="is_active" value="INACTIVE" {{$product_update->is_active == "INACTIVE" ? 'checked' : ''}}><span class="label-text"> Inactive</span>
                                        </label>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!--<div class="row ">
                        <div class="form-group col-sm-6">
                            <label class="control-label"> Is Flash Sale <span class="text-danger"> <b> * </b> </span></label>
                            <select class="form-control" onchange="changeFlashSale(this.value)" name="is_flash_sale" required>
                                <option value="">-- Select -- </option>
                                <option value="YES">Yes</option>
                                <option value="NO">No</option>
                            </select>
                        </div>
                    </div>-->
                    <div class="row flash_hide" id="flash_sale">





                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label"> Meta Title <span class="text-danger"> <b> * </b> </span></label>
                                <input type="text" name="meta_title" id="Product" class="form-control" required placeholder="Meta Title" value="{{$product_update->meta_title}}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label"> Meta Keyword <span class="text-danger"> <b> * </b> </span></label>
                                <input type="text" name="meta_keyword" id="Product" class="form-control" required placeholder="Meta Keyword" value="{{$product_update->meta_keyword}}">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"> Meta Description <span class="text-danger"> <b> * </b> </span></label>
                                <textarea type="text" name="meta_description" id="Product" class="form-control" required placeholder="Meta Description"><?php echo $product_update->meta_description; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"> Amazon Product Link <span class="text-info"><b>(Optional - Shop on Amazon button will show if filled)</b></span></label>
                                <input type="url" name="amazon_link" id="amazon_link" class="form-control" placeholder="https://www.amazon.in/dp/..." value="{{$product_update->amazon_link ?? ''}}">
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"> Product Specification <span class="text-danger"> <b> * </b> </span></label>
                                <textarea id="summary-ckeditor" name="product_specification" class="form-control" required>{{$product_update->product_specification}}</textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"> Product Description <span class="text-danger"> <b> * </b> </span></label>
                                <textarea id="summary-ckeditor1" name="product_description" class="form-control" required>{{$product_update->product_description}}</textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label"> Product Addition Information <span class="text-danger"> <b> * </b> </span></label>
                                <textarea id="summary-ckeditor2" name="product_addition_information" class="form-control" required>{{$product_update->product_addition_information}}</textarea>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update</button>&nbsp;
                        </div>
                    </div>
                    @endforeach
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

        function renderSelectedImages(input, previewSelector, blockSelector) {
            var files = input.files || [];
            var $preview = $(previewSelector);
            var $block = $(blockSelector);

            $preview.empty();
            if (!files.length) {
                $block.hide();
                return;
            }

            Array.prototype.forEach.call(files, function(file) {
                if (!file.type || !file.type.match(/^image\//)) {
                    return;
                }

                var reader = new FileReader();
                reader.onload = function(event) {
                    var $item = $('<div class="admin-product-image-option admin-product-selected-option"></div>');
                    $item.append('<img src="' + event.target.result + '" class="admin-product-thumb" alt="Selected product image">');
                    $item.append('<span class="admin-product-selected-name"></span>');
                    $item.find('.admin-product-selected-name').text(file.name);
                    $preview.append($item);
                };
                reader.readAsDataURL(file);
            });

            $block.show();
        }

        $('#product_header_image_input').on('change', function() {
            renderSelectedImages(this, '#product_header_preview', '#product_header_preview_block');
        });

        $('#product_gallery_input').on('change', function() {
            renderSelectedImages(this, '#product_gallery_preview', '#product_gallery_preview_block');
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




<script type="text/javascript">
    /* cate_level1 */


    function change_sub_category(id) {
        if (id) {
            $.ajax({
                url: '../sub_cate_pro/' + id,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#sub_cate').empty();
                    $('#sub_cate').append('<option value="0">-- Please Select --</option>');
                    $.each(data, function(key, value) {
                        $('#sub_cate').append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });
        } else {
            $('#sub_cate').empty();
        }
    }

    function change_sub_sub_category(id) {
        if (id) {
            $.ajax({
                url: '../sub_sub_cate_pro/' + id,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#sub_sub_cate').empty();
                    $('#sub_sub_cate').append('<option value="0">-- Please Select --</option>');
                    $.each(data, function(key, value) {
                        $('#sub_sub_cate').append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });
        } else {
            $('#sub_sub_cate').empty();
        }
    }

    function change_sub_sub_sub_category(id) {
        if (id) {
            $.ajax({
                url: '../sub_sub_sub_cate_pro/' + id,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#sub_sub_sub_cate').empty();
                    $('#sub_sub_sub_cate').append('<option value="0">-- Please Select --</option>');
                    $.each(data, function(key, value) {
                        $('#sub_sub_sub_cate').append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });
        } else {
            $('#sub_sub_sub_cate').empty();
        }
    }

</script>



@stop
