@extends('front.layout.default_layout')
@section('content')
@if($check_code>0)
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
@if($cart_item > 0 && $status == "YES")
<script>
    //CouponCode CouponDiscount CouponAmount
    $(document).ready(function() {
        swal("Congratulation!", '{{$dailog_text}}.', "success");
    });

</script>
@endif
@endif



<script>
    function updateProductFilter(i) {
        var pro_qty = normalizeCartQty(i);
        var cart_id = $('#cart_id' + i).val();

        var product_id = $('#product_id' + i).val();
        $.ajax({
            url: 'getAttributesList/' + product_id,
            type: 'GET',
            beforeSend: function() {
                $('#wait').show();
            },
            complete: function() {
                $('#wait').hide();
            },
            dataType: 'JSON',
            success: function(data) {
                var filter = [];
                $.each(data, function(id, value) {
                    var m = $('#' + value + i).val();
                    filter.push(m);

                });

                //second array

                $.ajax({
                    url: 'updateCartSystem',
                    type: "GET",
                    beforeSend: function() {
                        $('#wait').show();
                    },
                    complete: function() {
                        $('#wait').hide();
                    },
                    data: "filter=" + filter + "&pro_qty=" + pro_qty + "&product_id=" + product_id + "&cart_id=" + cart_id,
                    success: function(data) {
                        reloadPage();
                    }
                });


            }

        });


    }

    function normalizeCartQty(i, options) {
        options = options || {};
        var qtyInput = $('#qty' + i);
        var rawValue = $.trim(qtyInput.val());

        if (rawValue === '') {
            if (options.allowBlank) {
                return null;
            }
            qtyInput.val(1);
            return 1;
        }

        var qty = parseInt(rawValue, 10);
        if (isNaN(qty) || qty < 1) {
            qty = 1;
        }

        qtyInput.val(qty);
        return qty;
    }

    function updateProductQty(i, str, forceNormalize) {
        var pro_qty = normalizeCartQty(i, {allowBlank: str == "sam" && !forceNormalize});
        if (pro_qty === null) {
            return;
        }

        var cart_id = $('#cart_id' + i).val();
        if (str == "min") {
            pro_qty = pro_qty - 1;
        } else if (str == "max") {
            pro_qty = pro_qty + 1;
        }

        if (pro_qty < 1) {
            pro_qty = 1;
        }

        $('#qty' + i).val(pro_qty);

        var product_id = $('#product_id' + i).val();
        $.ajax({
            url: 'getAttributesList/' + product_id,
            type: 'GET',
            beforeSend: function() {
                $('#wait').show();
            },
            complete: function() {
                $('#wait').hide();
            },
            dataType: 'JSON',
            success: function(data) {
                var filter = [];
                $.each(data, function(id, value) {
                    var m = $('#' + value + i).val();
                    filter.push(m);

                });

                //second array

                $.ajax({
                    url: 'updateCartSystem',
                    type: "GET",
                    data: "filter=" + filter + "&pro_qty=" + pro_qty + "&product_id=" + product_id + "&cart_id=" + cart_id,
                    success: function(data) {
                        reloadPage();
                    }
                });


            }

        });


    }

    function removecartItem(id) {
        $.ajax({
            url: 'remove_cart_product/' + id,
            type: "GET",
            beforeSend: function() {
                $('#wait').show();
            },
            complete: function() {
                $('#wait').hide();
            },
            success: function(data) {
                swal("Product Remove!", 'Product successfully remove in your Cart.', "success");
                setTimeout(reloadPage, 1500);
            }

        });
    }

    function reloadPage() {
        location.reload();
    }

    function showStockMessage() {
        swal("Oops!", 'Please remove outstock product in your cart.', "warning");
    }

    function clearCart() {
        $.ajax({
            url: 'shopping_clear',
            type: "GET",
            beforeSend: function() {
                $('#wait').show();
            },
            complete: function() {
                $('#wait').hide();
            },
            success: function(data) {
                swal("Shopping Cart Clear!", 'Cart successfully empty.', "success");
                setTimeout(reloadPage, 1500);
            }

        });
    }

    function currency(sel) {
        var currency = sel.value;
        $.ajax({
            url: 'changeCurrency/' + currency,
            type: "GET",
            beforeSend: function() {
                $('#wait').show();
            },
            complete: function() {
                $('#wait').hide();
            },
            success: function(data) {
                location.reload();
            }
        });
    }

</script>

<div class="zouple-commerce-flow zouple-cart-flow">

<!-- Banner Code Start -->

@include('front.layout.banner')

<!-- Banner Code End -->

<!--======================   breadcumbs =======================-->
<div class="container-fluid  mb-3 " style="border-bottom:6px solid black;border-top:6px solid black; background-color:gray;">
    <div class="row px-0 px-sm-5 maxWidhtContainer">
        <div class=" col-12 align-self-center col-sm-12 col-md-12  h6 m-0  text-white">
            <nav aria-label="breadcrumb m-0 ">
                <ol class="breadcrumb m-0 bg-transparent">
                    <li class="breadcrumb-item"><a href="{{url('/')}}" class=" h6 m-0 text-white">Home</a></li>
                    <li class="breadcrumb-item text-white">Cart</li>
                </ol>
            </nav>
        </div>
    </div>


</div>




<!--=========  contnet =========-->

<div class="container zouple-cart-page">
    @if(!$cart_data->isEmpty())
    <div class="row zouple-cart-layout">
        <div class="col-lg-9 pt-4">

            <div class="row py-1 zouple-cart-head">
                <div class="col-8 ">Items</div>
                <div class="col-2 d-none d-sm-block  text-right">Unit Price</div>
                <div class="col-2 d-none d-sm-block  text-right">Net Amount</div>
            </div>

            <?php $i=0; $stock = 0; $ttl_net_amt=0;?>
            @foreach($cart_data as $data)
            @php
                $cartAttributes = $proAttributes[$data->product_id] ?? collect();
                $cartSelectedAttributes = json_decode($data->attributes_value, true);
                $cartSelectedAttributes = is_array($cartSelectedAttributes) ? $cartSelectedAttributes : [];
            @endphp

            <div class="row py-1 my-2 border zouple-cart-item">
                <div class="col-4 col-sm-2 p-0">
                    <img src="{{ z_media_url($data->product_header_image, 'product') }}" width="100%" class="zouple-cart-image">
                </div>
                <div class="col-8 col-sm-6 py-2 zouple-cart-copy">
                    <div class="h6 font-weight-normal m-0 zouple-cart-title">{{$data->product_title}}
                        <br>
                        <small class="text-danger zouple-cart-note">Prices are inclusive all Taxes
                            <!--of {{$data->product_gst}}% GST--></small>
                    </div>
                    <input type="hidden" id="product_id{{$i}}" value="{{$data->product_id}}">
                    <input type="hidden" id="cart_id{{$i}}" value="{{$data->cart_id}}">

                    <div class="row zouple-cart-attributes">
                        @foreach($cartAttributes as $attValue)
                        @if($attValue->attribute_name != "Self")
                        <div class=" py-2 col-12 col-sm-6  col-md-6 text-danger">
                            <?php
                            foreach($cartSelectedAttributes as $dt)
                            {
                                echo $dt;
                            }
                            ?>
                        </div>
                        @endif
                        @endforeach
                    </div>

                    <div class="col-12 col-sm-8 py-2 px-0 d-flex align-self-center priceCart2">

                        <div class="flex-w bo5 of-hidden zouple-qty-pill">
                            Quantity : <b>{{$data->product_qty}}</b>
                        </div>
                        <div class="col-12 col-sm-4 py-2  priceCart2">
                            <div class="flex-w bo5 of-hidden zouple-qty-control">
                                <button type="button" class="zouple-qty-down disabled" style="background-color:black;" onclick="changeQuantity('min')" disabled="">
                                    <i class="fs-12 fa fa-minus text-white" aria-hidden="true"></i>
                                </button>

                                <input class=" w-25 text-center num-product" type="number" name="qty" value="1" min="1" max="1" id="qty" onkeyup="changeQuantity('equal')" onblur="changeQuantity('equal', true)" disabled="">

                                <button type="button" class="zouple-qty-up disabled" style="background-color:black;" onclick="changeQuantity('max')" disabled="">
                                    <i class="fs-12 fa fa-plus text-white" aria-hidden="true"></i>
                                </button>
                            </div>

                        </div>

                    </div>
                    <!--<div class="py-2 d-flex justify-content-between" style="font-size:13px;">
                        <div>Availability:<span style="padding-left:10px; font-weight: bold;">{{$data->product_quantity}}</span></div>
                        <div style="font-weight: bold; padding-right: 10px;">
                            @if($data->product_qty > $data->product_quantity)
                            OUTSTOCK
                            <?php $stock++; ?>
                            @else
                            INSTOCK
                            @endif
                        </div>
                    </div>-->

                    <!-- <div class=" row " style="font-size:10px; !important">
                        @foreach($cartAttributes as $attValue)
                        @if($attValue->attribute_name != "Self")
                        <div class=" py-2 col-12 col-sm-5  col-md-4">
                            <div class="rs2-select2 rs3-select2 bo4 of-hidden fullsize">
                                <select class="form-control" name="{{$attValue->attribute_name}}" id="{{$attValue->attribute_name}}{{$i}}" onchange="updateProductFilter({{$i}})">
                                    <?php
                                    $pro_values = json_decode($attValue->attribute_value);
                                    
                                    ?>

                                    @foreach($pro_values as $val)
                                    <option value="{{$attValue->attribute_name}}:{{$val}}" <?php
                                            $pro_Att = $attValue->attribute_name.":".$val;
                                            foreach($cartSelectedAttributes as $dt)
                                            {
                                                if($dt == $pro_Att)
                                                {
                                                    echo "selected";
                                                }
                                            }
                                            ?>>{{$attValue->attribute_name}}:{{$val}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @else
                            <input type="hidden" id="{{$attValue->attribute_name}}{{$i}}" value="{{$attValue->attribute_name}}:Self">
                        @endif
                        

                        @endforeach
                    </div>-->

                    <!--<div class="col-12 col-sm-8 py-2  px-0 d-flex align-self-center priceCart2">

                        <div class="flex-w bo5 of-hidden">
                            <button type="button" class="zouple-qty-down" style="background-color:black;" onclick="updateProductQty({{$i}},'min')">
                                <i class="fs-12 fa fa-minus text-white" aria-hidden="true" aria-hidden="true"></i>
                            </button>

                            <input class=" w-25 text-center num-product" type="number" name="num-product1" value="{{$data->product_qty}}" id="qty{{$i}}" min="1" onkeyup="updateProductQty({{$i}},'sam')" onblur="updateProductQty({{$i}},'sam', true)">

                            <button type="button" class="zouple-qty-up" style="background-color:black;" onclick="updateProductQty({{$i}},'max')">
                                <i class="fs-12 fa fa-plus text-white" aria-hidden="true"></i>
                            </button>
                        </div>

                    </div>-->
                </div>

                <?php 
                    $currencySession = Session::get('currency');
                    
                     if($currencySession == "rupee_price")
                     {
                         $iicon = "fa fa-inr";
                         $netAmount = round($data->rupee_net_with_gst);
                     }
                     elseif($currencySession == "dollar_price")
                     {
                         $iicon = "fa fa-usd";
                         $netAmount = round($data->dollar_net_with_gst);
                     } 
                     elseif($currencySession == "euro_price")
                     {
                         $iicon = "fa fa-eur";
                         $netAmount = round($data->euro_net_with_gst);
                     }
                     else
                     {
                        $iicon = "fa fa-usd";
                        $netAmount = round($data->dollar_net_with_gst);
                     }

                     $subTotal = $data->product_qty * $netAmount;
                    ?>

                <div class="col-6 col-sm-2 untPrcSm py-2 text-right zouple-cart-price">
                    <div class="d-block d-sm-none zouple-mobile-label">Unit Price</div>
                    <div> <i class="{{$iicon}} pr-2"></i><span id="price{{$i}}">{{$netAmount}}</span></div>

                </div>
                <div class="col-6 col-sm-2 untPrcSm py-2 text-right zouple-cart-price">
                    <div class="d-block d-sm-none zouple-mobile-label">Sub- Total</div>
                    <div class=" font-weight-bold"><i class="{{$iicon}} pr-2"></i><span id="subtotal{{$i}}">{{$subTotal}}</span></div>
                </div>
                <div class="col-12 col-sm-12 p-0   remWishbtn">
                    <div class="border-top py-1">
                        <div class="btn py-0 px-4 mx-3 text-danger zouple-remove-btn" onclick="removecartItem({{$data->cart_id}})">Remove</div>
                    </div>
                </div>

            </div>


            <?php

            $i++;
            $ttl_net_amt = $data->product_qty * $netAmount + $ttl_net_amt;
            ?>
            @endforeach


            <div class="row justify-content-end border zouple-cart-total-row">

                <div class="col-6 col-sm-4  untPrcSm py-2 text-right">

                    <div><b>Total Net Amount</b></div>

                </div>
                <div class="col-6 col-sm-2 untPrcSm  py-2 text-right ">

                    <div class=" font-weight-bold"><i class="{{$iicon}} pr-2"></i>{{$ttl_net_amt}}</span></div>
                </div>

            </div>

            <div class="row my-2 zouple-cart-actions">
                <div class="col-12 col-sm-6 my-3">
                    <button type="submit" class="cta border-0" onclick="clearCart()">
                        <span>Clear Cart</span>
                        <svg width="13px" height="10px" viewBox="0 0 13 10">
                            <path d="M1,5 L11,5"></path>
                            <polyline points="8 1 12 5 8 9"></polyline>
                        </svg>
                    </button>

                </div>

                <div class="col-12 col-sm-6 my-3">
                    <a href="{{url('category')}}/{{$slug}}">
                        <button type="submit" class="cta border-0">
                            <span>Continue Shopping</span>
                            <svg width="13px" height="10px" viewBox="0 0 13 10">
                                <path d="M1,5 L11,5"></path>
                                <polyline points="8 1 12 5 8 9"></polyline>
                            </svg>
                        </button>
                    </a>

                </div>

            </div>



        </div>

        <?php
            $total_net_amount = 0;
            $total_final_amount = 0;
            $total_discount = 0;
            $total_shipping = 0;
            $total_pro_gst = 0;
            $minKg = MINIMUMKG;
             $currencySession = Session::get('currency');
                            
             if($currencySession == "rupee_price")
             {
                 $rupeeShipCh = RUPEESHIPPINCHARGE;
                 
             }
             elseif($currencySession == "dollar_price")
             {
                 $rupeeShipCh = DOLLARSHIPPINCHARGE;
             } 
             elseif($currencySession == "euro_price")
             {
                 $rupeeShipCh = EUROSHIPPINCHARGE;
             }
             else
             {
                $rupeeShipCh = DOLLARSHIPPINCHARGE;
             }
            
        foreach($cart_data as $data)
        {
            $net_amount = $data->rupee_net_amount * $data->product_qty;
            
            $weight =  $data->product_weight;
            if($weight > $minKg)  
            {
                $Ship = $rupeeShipCh * $weight * $data->product_qty;
            }
            else
            {
                $Ship = 0;
            }
            
            $total_shipping = $Ship + $total_shipping; 
            
            
           
            $pro_gst = $net_amount * $data->product_gst / 100;
            
            $total_pro_gst = $total_pro_gst+$pro_gst;
            
            $total_net_amount = round($total_net_amount+$net_amount);
          
            
        }
        
        $total_pro_net = floor($total_net_amount + $total_pro_gst);
        
        $total_final_amount = round($ttl_net_amt  + $total_shipping);
        ?>

        <div class="col-lg-3">

            <div class="border zouple-cart-summary">
                <div class="text-center py-4 zouple-summary-kicker">
                    Item(s): <span class="font-weight-bold">{{$cart_item}}</span>
                </div>


                <div class="px-3 py-2 border-bottom d-flex justify-content-between zouple-summary-line">
                    <div class="">Sub Total</div>
                    <div><i class="{{$iicon}} pr-2"></i><span>{{$ttl_net_amt}}</span></div>
                </div>
                <?php
                    if(isset($discountCouponFetchData))
                    {
                          $couponDiscount = $discountCouponFetchData;
                          $discountAmount = round($couponDiscount*$ttl_net_amt/100);

                          $totalDiscountAmount = $ttl_net_amt - $discountAmount;

                          $total_final_amount = $total_shipping + $totalDiscountAmount;
                    }      
                ?>
                
                @if(isset($discountAmount))
                <div class="px-3 py-2 border-bottom d-flex justify-content-between zouple-summary-line">
                    <div class="">Discount({{$discountCouponFetchData}}%)</div>
                    <div><i class="{{$iicon}} pr-2"></i><span>{{$discountAmount}}</span></div>
                </div>
                @endif


                <!--<div style="font-size:13px;" class="px-3 py-2  border-bottom d-flex justify-content-between">
                    <div class="">GST&nbsp;</div>
                    <div><i class="fa fa-inr pr-2"></i><span>{{round($total_pro_gst,2)}}</span></div>
                </div>
                
                <div style="font-size:13px;" class="h6 px-3 py-3 border-bottom d-flex justify-content-between text-right">
                    <div class=""><b>Net Amount  <br>(Round off)</b></div>
                    <div><i class="fa fa-inr py-2 pr-2"></i><span>{{$total_pro_net}} 
                        
                    </span></div>
                </div>-->

                <div class="px-3 py-2 border-bottom d-flex justify-content-between zouple-summary-line">
                    <div class="">Shipping Charge</div>
                    <div><i class="{{$iicon}} pr-2"></i><span>{{$total_shipping}}</span></div>
                </div>

                <div class="h6 px-3 py-3 d-flex justify-content-between zouple-summary-total">
                    <div class="">TOTAL PAYABLE</div>
                    <div><i class="{{$iicon}} pr-2"></i><span>{{$total_final_amount}}</span></div>
                </div>
            </div>
            <div class="my-4 zouple-checkout-cta">
                @if(isset(Auth::user()->id))
                @if($stock > 0)
                <!-- Button -->
                <a href="#" class="text-white">
                    <button type="submit" class="cta border-0" onclick="showStockMessage()">
                        <span>Process to Checkout</span>
                        <svg width="13px" height="10px" viewBox="0 0 13 10">
                            <path d="M1,5 L11,5"></path>
                            <polyline points="8 1 12 5 8 9"></polyline>
                        </svg>
                    </button>
                </a>
                @else
                    <a href="{{url('go_checkout')}}" class="text-white">
                        <button type="submit" class="cta border-0">
                            <span>Process to Checkout</span>
                            <svg width="13px" height="10px" viewBox="0 0 13 10">
                                <path d="M1,5 L11,5"></path>
                                <polyline points="8 1 12 5 8 9"></polyline>
                            </svg>
                        </button>
                    </a>

                @endif
                @else

                <a data-target="#logSign" data-toggle="modal" class="text-white">
                    <button class="cta border-0">
                        <span>Process to Checkout</span>
                        <svg width="13px" height="10px" viewBox="0 0 13 10">
                            <path d="M1,5 L11,5"></path>
                            <polyline points="8 1 12 5 8 9"></polyline>
                        </svg>
                    </button>
                </a>

                @endif
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-sm-12 h4 pt-4 pb-5 text-center my-5 h6 text-danger zouple-empty-cart">
            <i class="fa fa-shopping-bag d-block mb-3"></i>
            Oops !! Currently there is no item in your cart.
        </div>
    </div>
    @endif

</div>
</section>
</div>







<!--===================  end section  ====================-->


@stop
