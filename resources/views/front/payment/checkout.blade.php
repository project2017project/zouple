 @extends('front.layout.default_layout')
@section('content')
<style>
    .displaynon {
        display: none;
    }

    .displayblock {
        display: block;
    }

    .payment-methods {
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 14px;
        background: #fff;
        margin-top: 16px;
    }
    .payment-method-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 8px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        margin: 0;
    }
    .payment-method-option:last-child {
        border-bottom: 0;
    }
    .payment-method-option input {
        margin: 0;
    }
    .payment-method-copy {
        display: flex;
        flex-direction: column;
        line-height: 1.25;
    }
    .payment-method-copy small {
        color: #777;
    }

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>

<script>
    $('document').ready(function() {
        //btnContinue
        $('input[type="checkbox"]').click(function() {
            if ($(this).prop("checked") == true) {
                var str = $('#shippingAddress').val();
                if (str > 0) {

                } else {
                    swal("Sorry!", 'Please first add Shipping Adress.', "warning");
                    $("#checksame").prop("checked", false);
                }
                $.ajax({
                    url: 'showShipping/' + str,
                    type: "GET",
                     beforeSend: function() { $('#wait').show(); },
                     complete: function() { $('#wait').hide(); },
                    success: function(data) {
                        $('#saddress').html(data['address']);
                        $('#saddress_name').html(data['address_name']);
                        $('#scity').html(data['city_name']);
                        $('#sstate').html(data['state']);
                        $('#scountry').html(data['country']);
                        $('#spin').html(data['pin']);
                        $('#smobile').html(data['mobile']);
                        $('#sfaddress').html(data['address']);
                        $('#sfaddress_name').html(data['address_name']);
                        $('#sfcity').html(data['city_name']);
                        $('#sfstate').html(data['state']);
                        $('#sfcountry').html(data['country']);
                        $('#sfpin').html(data['pin']);
                        $('#sfmobile').html(data['mobile']);

                        $('#btnContinue').html("<button type='button' class='cta border-0' onclick='nextPrev(1)'><span style='z-index: 9;'>Continue</span><svg width='13px' height='10px' viewBox='0 0 13 10'><path d='M1,5 L11,5'></path><polyline points='8 1 12 5 8 9'></polyline></svg></button>");

                        $('#sameaddresspopup').addClass('displayblock');
                        $('#sameaddresspopup').removeClass('displaynon');
                        $('#sameaddresspopup1').addClass('displayblock');
                        $('#sameaddresspopup1').removeClass('displaynon');


                    }
                });

            } else if ($(this).prop("checked") == false) {
                var str = $('#billingAddress').val();
                var sstr = $('#shippingAddress').val();

                $('#billingAddress').val(str);

                if (str > 0) {
                    $('#btnContinue').html("<button type='button' class='cta border-0' onclick='nextPrev(1)'><span style='z-index: 9;'>Continue</span><svg width='13px' height='10px' viewBox='0 0 13 10'><path d='M1,5 L11,5'></path><polyline points='8 1 12 5 8 9'></polyline></svg></button>");

                    if (str == sstr) {
                        $('#sameaddresspopup').addClass('displayblock');
                        $('#sameaddresspopup').removeClass('displaynon');
                        $('#sameaddresspopup1').addClass('displayblock');
                        $('#sameaddresspopup1').removeClass('displaynon');
                    } else {
                        $('#sameaddresspopup').addClass('displaynon');
                        $('#sameaddresspopup').removeClass('displayblock');
                        $('#sameaddresspopup1').addClass('displaynon');
                        $('#sameaddresspopup1').removeClass('displayblock');
                    }


                } else {
                    $('#btnContinue').html("");
                    $('#sameaddresspopup').addClass('displaynon');
                    $('#sameaddresspopup').removeClass('displayblock');
                    $('#sameaddresspopup1').addClass('displaynon');
                    $('#sameaddresspopup1').removeClass('displayblock');
                }


                $.ajax({
                    url: 'showShipping/' + str,
                    type: "GET",
                     beforeSend: function() { $('#wait').show(); },
                     complete: function() { $('#wait').hide(); },
                    success: function(data) {
                        $('#saddress').html(data['address']);
                        $('#saddress_name').html(data['address_name']);
                        $('#scity').html(data['city_name']);
                        $('#sstate').html(data['state']);
                        $('#scountry').html(data['country']);
                        $('#spin').html(data['pin']);
                        $('#smobile').html(data['mobile']);
                        $('#sfaddress').html(data['address']);
                        $('#sfaddress_name').html(data['address_name']);
                        $('#sfcity').html(data['city_name']);
                        $('#sfstate').html(data['state']);
                        $('#sfcountry').html(data['country']);
                        $('#sfpin').html(data['pin']);
                        $('#sfmobile').html(data['mobile']);


                    }
                });
            }
        });

    });

</script>

<script>
    function showaddressError()
    {
         swal("Oops!", 'Please add shipping and billing address.', "warning");
    }


    function changeaddresstype(str) {

        $('#addresstypes').val(str);
        $('#checkAdd').html(str);

        if (str == "Billing") {
            $('#add_pincode_data').html("<input type='number' name='pin' class='form-control' placeholder='Pincode'>");
            $('#amsg').html('');

        } else {
            $('#add_pincode_data').html("<select class='form-control' name='pin' id='apin_id' required><option value=''> -- Select Field --</option></select>");
            $('#amsg').html('Note - Kindly mention the correct address with pincode so that your product would we delivard on time.');
        }


    }
    
    
    function changeshipping(str)
    {
        $('#shippingAddress').val(str);
        $.ajax({
                url: 'showShipping/'+ str,
                type: "GET",
                 beforeSend: function() { $('#wait').show(); },
                 complete: function() { $('#wait').hide(); },
                success: function(data) {
                    $('#address').html(data['address']);
                    $('#address_name').html(data['address_name']);
                    $('#city').html(data['city_name']);
                    $('#state').html(data['state_name']);
                    $('#country').html(data['country_name']);
                    $('#pin').html(data['pin']);
                    $('#shippin').val(data['pin']);
                    $('#mobile').html(data['mobile']);
                    $('#faddress').html(data['address']);
                    $('#faddress_name').html(data['address_name']);
                    $('#fcity').html(data['city_name']);
                    $('#fstate').html(data['state_name']);
                    $('#fcountry').html(data['country_name']);
                    $('#fpin').html(data['pin']);
                    $('#fmobile').html(data['mobile']);
                }
            });
    }
    function changeBilling(str)
    {
        $('#billingAddress').val(str);
        $.ajax({
                url: 'showShipping/'+ str,
                type: "GET",
                 beforeSend: function() { $('#wait').show(); },
                 complete: function() { $('#wait').hide(); },
                success: function(data) {
                    $('#saddress').html(data['address']);
                    $('#saddress_name').html(data['address_name']);
                    $('#scity').html(data['city_name']);
                    $('#sstate').html(data['state_name']);
                    $('#scountry').html(data['country_name']);
                    $('#spin').html(data['pin']);
                    $('#smobile').html(data['mobile']); 
                    $('#sfaddress').html(data['address']);
                    $('#sfaddress_name').html(data['address_name']);
                    $('#sfcity').html(data['city_name']);
                    $('#sfstate').html(data['state_name']);
                    $('#sfcountry').html(data['country_name']);
                    $('#sfpin').html(data['pin']);
                    $('#sfmobile').html(data['mobile']);
                }
            });
    }
    
    
    function edit_payment(address_name, mobile, address, landmark, country, state, city_name, pin, user_information_id, address_type) {

        $('#eaddress_name').val(address_name);
        $('#emobile').val(mobile);
        $('#eaddress').val(address);
        $('#elandmark').val(landmark);

        $('#epin').val(pin);
        $('#euser_information_id').val(user_information_id);
        $('#eaddresstypes').val(address_type);
        $('#ecity_name').val(city_name);
        $('#epin_id').val(pin);
        $('#estate').val(state);
        $('#ecountry').val(country);
        $("#btn_paumentbtn").attr('value', 'Update');

        $('#paymentfor_form').attr('action', 'paymentUpdate');
        if (address_type == "Shipping") {
            $('#emsg').html('Note - Kindly mention correct address with pincode to ensure your product reaches the correct address and on time.');
        } else {
            $('#emsg').html('');
        }


    }
    
    
    
    function checkPincode(pincode)
    {
        var addresstypes = $('#addresstypes').val();
        if(pincode != "")
        {
            if(addresstypes == "Shipping")
            {
                $.ajax({
                url: 'check_pincodeStatus',
                type: "GET",
                 beforeSend: function() { $('#wait').show(); },
                 complete: function() { $('#wait').hide(); },
                data: "pincode=" +pincode,
                success: function(data) 
                {
                    if(data == 1) 
                    {
                       $('#changeButton').html("<button type='submit' id='addsubmitbtn' class='cta border-0 ml-auto'><span>Save</span><svg width='13px' height='10px' viewBox='0 0 13 10'><path d='M1,5 L11,5'></path><polyline points='8 1 12 5 8 9'></polyline></svg></button>");   
                    } 
                    else 
                    {
                        $('#changeButton').html("<button type='button' onclick='showPincodeError()' class='cta border-0 ml-auto'><span>Save</span><svg width='13px' height='10px' viewBox='0 0 13 10'><path d='M1,5 L11,5'></path><polyline points='8 1 12 5 8 9'></polyline></svg></button>");
                    }


                    }
                });
            }
            else
            {
                $('#addressSaveForm').attr('action', '{{url('address_save')}}');
                $('#addSubmit').prop('type','submit');   
            }
        }
        else
           {
               
           }
    }
    
    function echeckPincode(pincode)
    {
       /* alert(pincode);*/
        var addresstypes = $('#eaddresstypes').val();
        if(pincode != "")
        {
            if(addresstypes == "Shipping")
            {
                $.ajax({
                url: 'check_pincodeStatus',
                type: "GET",
                 beforeSend: function() { $('#wait').show(); },
                 complete: function() { $('#wait').hide(); },
                data: "pincode=" +pincode,
                success: function(data) 
                {
                    if(data == 1) 
                    {
                       $('#echangeButton').html("<button type='submit' id='addsubmitbtn' class='cta border-0 ml-auto'><span>Update</span><svg width='13px' height='10px' viewBox='0 0 13 10'><path d='M1,5 L11,5'></path><polyline points='8 1 12 5 8 9'></polyline></svg></button>");   
                    } 
                    else 
                    {
                        $('#echangeButton').html("<button type='button' onclick='showPincodeError()' class='cta border-0 ml-auto'><span>Update</span><svg width='13px' height='10px' viewBox='0 0 13 10'><path d='M1,5 L11,5'></path><polyline points='8 1 12 5 8 9'></polyline></svg></button>");
                    }


                    }
                });
            }
            else
            {
                $('#addressSaveForm').attr('action', '{{url('address_save')}}');
                $('#addSubmit').prop('type','submit');   
            }
        }
        else
           {
               
           }
    }
    
    
    function showPincodeError()
    {
        swal("Sorry!", 'We don\'t delivers here but we will start shortly and we will let you know.', "warning");
    }
    
    function couponcodeApply()
    {
        var total_amt = $('#net_amount').val();
        var coupon = $('#coupon').val();
         $.ajax({
                url: 'checkoutCoupenApply',
                type: "GET",
                 beforeSend: function() { $('#wait').show(); },
                 complete: function() { $('#wait').hide(); },
                data: "total_amt=" +total_amt+"&coupon="+coupon,
                success: function(data) 
                {
                    $('#net_amount').val(data['total_net_amount']);
                    $('#total_discount').val(data['total_discount']);
                    $('#discount').val(data['discount']);
                    $('#product_gst').val(data['total_gst']);
                    $('#total_shipping').val(data['total_shipping']);
                    $('#total_final_amount').val(data['total_final_amount']);
                    $('#coupon_id').val(data['coupon_id']);
                    $('#coupenApply').val(data['coupenApply']);
                    $('#coupon_type').val(data['coupon_type']);
                    $('#coupen_text').html(data['text']);
                    $('#total_pro_net').html(data['total_pro_net']);
                    
                    if(data['total_discount']>0)
                    {
                        $('#discountShow').html("<div class='border-bottom px-3 py-2'>Coupon Discount&nbsp;(<span>"+data['discount']+"%</span>)</div><div class='border-bottom px-3 py-2 '><i class='fa fa-inr pr-2'></i><span>"+data['total_discount']+"</span></div>");
                    }
                    else
                    {
                        $('#discountShow').html(" ");
                    }
                    $('#sgst').html(data['total_gst']/2);
                    $('#cgst').html(data['total_gst']/2);
                    $('#total_amount').html(data['total_final_amount']);
                    
                
                    
                }
         });
    }

     function currency(sel) {
        var currency= sel.value;
        $.ajax({
            url: 'changeCurrency/'+ currency,
            type: "GET",
            beforeSend: function() {$('#wait').show();},
            complete: function() {$('#wait').hide();},
            success: function(data) {
               location.reload();
            }
        });
    }
    

</script>





<!--===============  section =====================-->

<div class="zouple-commerce-flow zouple-checkout-flow">
<section class="pb-5">
    <!-- Banner Code Start -->

@include('front.layout.banner')

<!-- Banner Code End -->

    <!--======================   breadcumbs =======================-->
    <div class="container-fluid  mb-3 " style="border-bottom:6px solid black;border-top:6px solid black; background-color:gray;">
        <div class="row px-5 maxWidhtContainer">
            <div class=" col-12 align-self-center col-sm-12 col-md-12  h6 m-0  text-white">
            <nav aria-label="breadcrumb m-0 ">
                <ol class="breadcrumb m-0 bg-transparent">
                    <li class="breadcrumb-item"><a href="{{url('/')}}" class=" h6 m-0 text-white">Home</a></li>
                    <li class="breadcrumb-item text-white">Payment</li>
                </ol>
            </nav>
        </div>
        </div>


    </div>


    <!--==============  content  ===========-->




    <div class="container zouple-checkout-page">

        <div class="d-flex align-self-center justify-content-between stepsPayment text-right my-2 py-2 zouple-checkout-steps">
            <div class="d-flex align-self-center">
                <div class="color0-hov text-dark align-self-center " id="prevBtn" onclick="nextPrev(-1)"><i class="fa fa-angle-double-left px-2" aria-hidden="true"></i> Previous</div>
            </div>
            <div class="justify-content-end stepsHead row">

                <div class="step d-flex active">
                    <div class="stepnu border pt-1 t-middle align-self-center text-center h6 m-0">1</div>
                    <div class="h6 align-self-center m-0 stepTitle">Select Address</div>
                </div>

                <div class="step d-flex">
                    <div class="stepnu border pt-1 t-middle align-self-center text-center h6 m-0">2</div>
                    <div class="h6  align-self-center m-0 stepTitle">Review Cart</div>
                </div>
            </div>
        </div>
    </div>
    <hr>

    <div class="container pb-5 zouple-checkout-shell">
        <div class="row shadow py-4 border-top zouple-checkout-panel">

            <form id="regForm" action="{{url('paymentNow')}}" class="w-100" method="post">
                @csrf
                <div class="tab">

                    <div class="container cartHeading zouple-address-step">
                        <div class="row w-100">
                            <div class="col-12 col-sm-12 col-md-6 ">
                               <!-- <div class="h5 m-0 py-3 text-center m-auto">
                                    Shipping Address
                                </div>-->
                                
                                    <div class="zouple-section-kicker">
                                      <b>Please enter shipping / billing address.</b>
                                    </div>
                                    
                                
                                <div onclick="changeaddresstype('Shipping')" class="h6 text-left mt-1 text-danger btnFormHover zouple-add-address" data-toggle="modal" data-target="#shipping">
                                    + Add Shipping Address
                                </div>
                                @foreach($buynow_shipping_list as $data)
                                <div class="border px-4 my-3 zouple-address-card">
                                    <div class="text-center  py-2">
                                        <input type="hidden" name="shippingAddress" value="{{$data->user_information_id}}" id="shippingAddress">
                                        <input type="hidden" name="shippin" value="{{$data->pin}}" id="shippin">
                                        <label for="Add_1">
                                            <div class="h5 m-0 pl-3" id="address_name">{{$data->address_name}}</div>
                                        </label>
                                    </div>

                                    <div class="text-left">

                                        <div class="" id="address">
                                            {{$data->address}}
                                        </div>
                                        <div id="city">{{$data->city_name}}</div>
                                        <div><span id="state">{{$data->state}}</span>, <span id="pin">{{$data->pin}}</span></div>
                                        <div id="country">{{$data->country}}</div>
                                        <div class="d-flex">
                                            <div class="h6 pr-2">Mobile:</div>
                                            <div>+91 <span id="mobile">{{$data->mobile}}</span></div>
                                        </div>
                                    </div>
                                    <div onclick="echangeaddresstype('Shipping Address')" class="text-right h6 btnFormHover zouple-address-edit" data-toggle="modal" data-target="#Editing">

                                        <a href="#" onclick="edit_payment('{{$data->address_name}}',{{$data->mobile}},'{{$data->address}}','{{$data->landmark}}','{{$data->country}}','{{$data->state}}','{{$data->city_name}}',{{$data->pin}},{{$data->user_information_id}},'{{$data->addresstype}}')">Edit</a>


                                    </div>
                                    <div class="h6 zouple-select-label">Or choose from below Addresses</div>
                                    <div class="form-group">
                                        <select class="form-control" onchange="changeshipping(this.value)">
                                            <option value="">---- Choose Other ----</option>
                                            @foreach($showShipping as $dt)
                                            <option value="{{$dt->user_information_id}}">{{$dt->address}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                @endforeach
                            </div>

                            <div class="col-12 col-sm-12 col-md-6">
                                <!--<div class="h5 m-0 py-3 text-center m-auto">
                                    Billing Address
                                </div>-->
                                 <div class="row justify-content-end">
                                    <div class="text-right h6 col-12 col-sm-12 btnFormHover zouple-same-address">
                                        <input type="checkbox" id="checksame" name="checksame"><label for="checksame">&nbsp; My billing address is same as shipping address</label>
                                    </div>
                                       </div>
                                <div class="row justify-content-end">
                                    
                                    <div onclick="changeaddresstype('Billing')" class="h6 col-12 col-sm-5 btnFormHover text-right text-danger zouple-add-address" data-toggle="modal" data-target="#shipping">
                                        + Add Billing Address
                                    </div>
                                </div>
                                
                               
                                    
                             
                                
                                <div class="border px-4 mb-3 mt-2 displaynon zouple-address-card zouple-same-address-note" id="sameaddresspopup">
                                    <div class="text-center py-2">
                                        You have selected shipping and billing address as same.
                                    </div>
                                </div>
                                @foreach($buynow_billing_list as $data)
                                <div class="border px-4 mb-3 mt-2 zouple-address-card">
                                    <div class="text-center py-2">
                                        <label for="BillAdd_1" class="align-seld-center ">
                                            <div class="h5 m-0 pl-3" id="saddress_name">{{$data->address_name}}</div>
                                            <input type="hidden" name="billingAddress" value="{{$data->user_information_id}}" id="billingAddress">
                                        </label>
                                    </div>
                                    <div class="text-left">
                                        <div class="" id="saddress">
                                            {{$data->address}}
                                        </div>
                                        <div id="scity">{{$data->city_name}}</div>
                                        <div><span id="sstate">{{$data->state}}</span>, <span id="spin">{{$data->pin}}</span></div>
                                        <div id="scountry">{{$data->country}}</div>
                                        <div class="d-flex">
                                            <div class="h6 pr-2">Mobile:</div>
                                            <div>+91 <span id="smobile">{{$data->mobile}}</span></div>
                                        </div>
                                    </div>
                                    <div onclick="echangeaddresstype('Billing Address')" class="text-right h6 btnFormHover zouple-address-edit" data-toggle="modal" data-target="#Editing">

                                        <a href="#" onclick="edit_payment('{{$data->address_name}}',{{$data->mobile}},'{{$data->address}}','{{$data->landmark}}','{{$data->country}}','{{$data->state}}','{{$data->city_name}}',{{$data->pin}},{{$data->user_information_id}},'{{$data->addresstype}}')">Edit</a>

                                    </div>
                                    <div class="h6 zouple-select-label">Or choose from below Addresses</div>
                                    <div class="form-group">
                                        <select class="form-control" name="droBillingAdress" onchange="changeBilling(this.value)">
                                            <option value="">---- Choose Other ----</option>
                                            @foreach($showBilling as $dt)
                                            <option value="{{$dt->user_information_id}}">{{$dt->address}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endforeach
                            </div>


                        </div>
                        <div class="px-4 zouple-step-action" id="btnContinue">
                            @if(!$buynow_shipping_list->isEmpty() && !$buynow_billing_list->isEmpty())
                            <button type="button" class="cta border-0" onclick="nextPrev(1)">
                                <span style="z-index: 9;">Continue</span>
                                <svg width="13px" height="10px" viewBox="0 0 13 10">
                                    <path d="M1,5 L11,5"></path>
                                    <polyline points="8 1 12 5 8 9"></polyline>
                                </svg>
                            </button>
                            @else
                            <button type="button" class="cta border-0" onclick="showaddressError()">
                                <span style="z-index: 9;">Continue</span>
                                <svg width="13px" height="10px" viewBox="0 0 13 10">
                                    <path d="M1,5 L11,5"></path>
                                    <polyline points="8 1 12 5 8 9"></polyline>
                                </svg>
                            </button>
                            @endif
                        </div>


                    </div>
                </div>
                <div class="tab">
                    <div class="container zouple-review-step">
                        <div class="row">
                            <div class="col-lg-9">

                                <div class="row py-1 zouple-cart-head">
                                    <div class="col-8 ">Items</div>
                                    <div class="col-2 d-none d-sm-block  text-right">Unit Price</div>
                                    <div class="col-2 d-none d-sm-block  text-right">Net Amount</div>
                                </div>
                                <?php $ttl_net_amt = 0;?>
                                @foreach($cart_data as $data)
                                @php
                                    $cartAttributes = $proAttributes[$data->product_id] ?? collect();
                                    $cartSelectedAttributes = json_decode($data->attributes_value, true);
                                    $cartSelectedAttributes = is_array($cartSelectedAttributes) ? $cartSelectedAttributes : [];
                                @endphp
                                <div class="row py-1 my-2 border zouple-cart-item zouple-checkout-item">
                                    <div class="col-4 col-sm-2 p-0">
                                        <img src="{{ z_media_url($data->product_header_image, 'product') }}" width="100%" class="zouple-cart-image">
                                    </div>
                                    <div class="col-8 col-sm-6 py-2 zouple-cart-copy">
                                        <div class="h6 font-weight-normal m-0 zouple-cart-title">{{$data->product_title}}
                                        <br>
                                        <small class="text-danger zouple-cart-note">Prices are inclusive all taxes.<!--of {{$data->product_gst}}% GST--></small>
                                        </div>
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

                                        <div class="col-12 col-sm-8 py-2  px-0 d-flex align-self-center priceCart2">

                                            <div class="flex-w bo5 of-hidden zouple-qty-pill">
                                                Quantity : <b>{{$data->product_qty}}</b>
                                            </div>
                                        </div>

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
                                        <div> <i class="{{$iicon}} pr-2"></i><span>{{$netAmount}}</span></div>
                                        
                                    </div>
                                    <div class="col-6 col-sm-2 untPrcSm py-2 text-right zouple-cart-price">
                                        <div class="d-block d-sm-none zouple-mobile-label">Net Amount</div>
                                        <div class=" font-weight-bold"><i class="{{$iicon}} pr-2"></i><span>{{$subTotal}}</span></div>
                                    </div>
                                    <?php  $ttl_net_amt = $netAmount * $data->product_qty + $ttl_net_amt;
                                    ?>

                                </div>
                                @endforeach



                                <div class="row zouple-address-preview-grid">
                                    <div class="col-12 col-sm-12 col-md-6">
                                        <div class="border p-3 zouple-address-preview">
                                            <div class="h6 text-center zouple-preview-title">Shipping Address</div>
                                            @foreach($buynow_shipping_list as $data)
                                            <div class="h6" id="faddress_name">{{$data->address_name}}</div>
                                            <div id="faddress">
                                                {{$data->address}}
                                            </div>
                                            <div id="fcity">
                                                {{$data->city_name}}
                                            </div>
                                            <div>
                                                <span id="fstate">{{$data->state}}</span>, <span id="fpin">{{$data->pin}}</span>
                                            </div>
                                            <div id="fcountry">
                                                {{$data->country}}
                                            </div>
                                            <div class="d-flex">
                                                <div class="h6 m-0">Mobile No.</div>
                                                <div><span>+91</span><span id="fmobile">{{$data->mobile}}</span></div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6">
                                        <div class="border p-3 zouple-address-preview">
                                            <div class="h6 text-center zouple-preview-title">Billing Address</div>
                                            <div class="border px-4 mb-3 mt-2 displaynon zouple-same-address-note" id="sameaddresspopup1">
                                                <div class="text-center py-2">
                                                    You have selected shipping and billing address as same
                                                </div>
                                            </div>
                                            @foreach($buynow_billing_list as $data)
                                            <div class="h6" id="sfaddress_name">{{$data->address_name}}</div>
                                            <div id="sfaddress">
                                                {{$data->address}}
                                            </div>
                                            <div id="sfcity">
                                                {{$data->city_name}}
                                            </div>
                                            <div>
                                                <span id="sfstate">{{$data->state}}</span>, <span id="fpin">{{$data->pin}}</span>
                                            </div>
                                            <div id="sfcountry">
                                                {{$data->country}}
                                            </div>
                                            <div class="d-flex">
                                                <div class="h6 m-0">Mobile No.</div>
                                                <div><span>+91</span><span id="sfmobile">{{$data->mobile}}</span></div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>





                            </div>

                            <?php
                                $total_net_amount = 0;
                                $total_final_amount = 0;
                                $total_discount=0;
                                $total_shipping = 0;
                                $total_shipping_gst=0;
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
                                $gst=$data->product_gst;
                                if($currencySession == "rupee_price")
                                 {
                                     $iicon = "fa fa-inr";
                                     $net_amount = round($data->rupee_net_with_gst);
                                 }
                                 elseif($currencySession == "dollar_price")
                                 {
                                     $iicon = "fa fa-usd";
                                     $net_amount = round($data->dollar_net_with_gst);
                                 } 
                                 elseif($currencySession == "euro_price")
                                 {
                                     $iicon = "fa fa-eur";
                                     $net_amount = round($data->euro_net_with_gst);
                                 }
                                 else
                                 {
                                    $iicon = "fa fa-usd";
                                    $net_amount = round($data->dollar_net_with_gst);
                                 }
                                $net_amount = $netAmount * $data->product_qty;
                               
                                $weight = $data->product_weight;
                                if($minKg<$weight)
                                {
                                    $shipp = $rupeeShipCh * $weight * $data->product_qty;
                                }
                                else
                                {
                                    $shipp = 0;
                                }
                                $pro_gst = $net_amount * $data->product_gst / 100;
                                $total_pro_gst = $total_pro_gst+$pro_gst;

                                $shpping_gst = $shipp * $gst /100;
                                

                                $total_net_amount = round($total_net_amount+$net_amount);
                                
                                $total_shipping = $total_shipping+$shipp;
                                

                            }
                            $total_pro_net = round($total_net_amount + $total_pro_gst);

                            $total_final_amount = round($ttl_net_amt + $total_shipping);
                            ?>
                            
                            
                           

                            


                            <div class="col-lg-3">

                                <div class="border zouple-cart-summary zouple-checkout-summary">
                                    @if($discountCouponFetchDatass == "")
                                    <div class="p-2 border zouple-coupon-box">
                                        <div class="h6 m-0 pb-2 ">Coupon</div>
                                        <div class="input-group  d-flex border-bottom " style="border-color:black!important;">
                                           <input  placeholder="Coupon Code" type="text" class="form-control border-0  shadow-none deliveryPlace m-0 font-weight-normal  h-100"  name="coupon_code" id="coupon">
                                            <span class="btn input-group-addon text-right checkDel rounded-0 bg-transparent border-0" style="color:black;font-weight:bold;" onclick="couponcodeApply()">Apply</span>
                                        </div>
                                        <div id="coupen_text">
                                        
                                        </div>
                                    </div>
                                    @endif
                                    <div class="text-center py-4 zouple-summary-kicker">
                                        Item(s): <span class="font-weight-bold">{{$cart_item}}</span>
                                    </div>

                                    <div class="px-3 py-2 border-bottom d-flex justify-content-between zouple-summary-line">
                                        <div class="">Sub - Total</div>
                                        <div><i class="{{$iicon}} pr-2"></i><span>{{$ttl_net_amt}}</span></div>
                                    </div>
                                    
                                    
                                    <!--<div style="font-size:13px;" class="px-3 py-2  border-bottom d-flex justify-content-between">
                                        <div class="">SGST&nbsp;</div>
                                        <div><i class="fa fa-inr pr-2"></i><span id="sgst">{{round($total_pro_gst/2,2)}}</span></div>
                                    </div>
                                    <div style="font-size:13px;" class="px-3 py-2  border-bottom d-flex justify-content-between">
                                        <div class="">CGST&nbsp;</div>
                                        <div><i class="fa fa-inr pr-2"></i><span id="cgst">{{round($total_pro_gst/2,2)}}</span></div>
                                    </div>-->
                                   <!-- <div style="font-size:13px;" class="h6 px-3 py-3 border-bottom d-flex justify-content-between text-right">
                                        <div class=""><b>Net Amount  <br>(Round off)</b></div>
                                        <div><i class="fa fa-inr py-2 pr-2"></i><span id="total_pro_net">{{$total_pro_net}} 
                                            
                                        </span></div>
                                    </div>-->

                                    <?php
                                        if($discountCouponFetchData)
                                        {
                                              $couponDiscount = $discountCouponFetchData;
                                              $discountAmount = round($couponDiscount*$ttl_net_amt/100);

                                              $totalDiscountAmount = $ttl_net_amt - $discountAmount;

                                              $total_final_amount = $total_shipping + $totalDiscountAmount;
                                        }      
                                    ?>
                                    
                                    
                                    <input type="hidden" name="net_amount" value="{{$ttl_net_amt}}" id="net_amount">
                                    <input type="hidden" name="total_discount" value="{{$total_discount}}" id="total_discount">
                                    <input type="hidden" name="discount" value="{{$total_discount}}" id="discount">
                                    <input type="hidden" name="product_gst" value="{{$total_pro_gst}}" id="product_gst">
                                    <input type="hidden" name="shiping_charge" value="{{$total_shipping}}" id="shiping_charge">
                                    <input type="hidden" name="total_amount" value="{{$total_final_amount}}" id="total_final_amount">
                                    
                                    <input type="hidden" name="coupenApply" id="coupenApply">
                                    <input type="hidden" name="coupon_type" id="coupon_type">
                                    <input type="hidden" name="coupon_id" id="coupon_id">
                            
                                    @if(isset($discountAmount))
                                    <input type="hidden" name="total_discount" value="{{$discountAmount}}" id="total_discount">
                                    <input type="hidden" name="discount" value="{{$discountCouponFetchData}}" id="discount">
                                    <div class="px-3 py-2 border-bottom d-flex justify-content-between zouple-summary-line">
                                        <div class="">Discount({{$discountCouponFetchData}}%)</div>
                                        <div><i class="{{$iicon}} pr-2"></i><span>{{$discountAmount}}</span></div>
                                    </div>
                                    @endif
                                    <div class="d-flex justify-content-between zouple-summary-line" id="discountShow">
                                    
                                    </div>
                                    <div class="px-3 py-2 border-bottom d-flex justify-content-between zouple-summary-line">
                                        <div class="">Shipping Charge</div>
                                        <div><i class="{{$iicon}} pr-2"></i><span>{{$total_shipping}}</span></div>
                                    </div>

                                    <div class="h6 px-3 py-3 d-flex justify-content-between zouple-summary-total">
                                        <div class="">TOTAL PAYABLE</div>
                                        <div><i class="{{$iicon}} pr-2"></i><span id="total_amount">{{$total_final_amount}}</span></div>
                                    </div>
                                </div>
                                <div class="payment-methods zouple-payment-methods">
                                    <div class="h6 mb-2">Payment Method</div>
                                    <label class="payment-method-option">
                                        <input type="radio" name="payment_method" value="razorpay" {{ ($currencySession ?? '') == 'rupee_price' ? 'checked' : '' }}>
                                        <span class="payment-method-copy">
                                            <strong>UPI / Card / Netbanking</strong>
                                            <small>Secure online payment through Paytm gateway.</small>
                                        </span>
                                    </label>
                                    
                                    <label class="payment-method-option">
                                        <input type="radio" name="payment_method" value="paypal" {{ ($currencySession ?? '') != 'rupee_price' ? 'checked' : '' }}>
                                        <span class="payment-method-copy">
                                            <strong>Card / PayPal</strong>
                                            <small>Recommended for international payments.</small>
                                        </span>
                                    </label>
                                    <label class="payment-method-option">
                                        <input type="radio" name="payment_method" value="cod">
                                        <span class="payment-method-copy">
                                            <strong>Cash on Delivery</strong>
                                            <small>Pay when your order arrives, where available.</small>
                                        </span>
                                    </label>
                                </div>
                                <div class="my-4 zouple-payment-action">
                                    <!-- Button -->
                                    <a href="#" class="text-white">
                                        <button onclick="nextPrev(1)" type="submit" class="cta border-0" onclick="nextPrev(1)">
                                            <span>Proceed to Payment</span>
                                            <svg width="13px" height="10px" viewBox="0 0 13 10">
                                                <path d="M1,5 L11,5"></path>
                                                <polyline points="8 1 12 5 8 9"></polyline>
                                            </svg>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>



                </div>




            </form>


        </div>

    </div>







    <!--//////////////   add address modal /////////////////////-->
    <div class="modal fade" id="shipping" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add <span id="checkAdd">Shipping </span> Address</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="addressSaveForm" name="addressSaveForm" action="{{url('address_save')}}">
                    @csrf
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="name">Name<span class="text-danger">*</span></label>
                            <input type="text" placeholder="Enter your Name" class="form-control" name="address_name" required>

                            <input type="hidden" name="addresstype" id="addresstypes">
                        </div>
                        <div class="form-group">
                            <label for="Phnumber">Mobile Number<span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <input type="number" id="Phnumber" class="form-control" placeholder="Enter Mobile Number" name="mobile" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="Address">Address<span class="text-danger">*</span></label>
                            <input type="text" placeholder="Enter your Address" class="form-control" id="Adress" name="address" required>
                        </div>
                        <div class="form-group">
                            <label for="City">LandMark</label>
                            <input type="text" placeholder="Enter your nearest Landmark" class="form-control" id="landmark" name="landmark">
                        </div>

                        <div class="form-group">
                            <label for="country">Country<span class="text-danger">*</span></label>
                            <input type="text" placeholder="Country" class="form-control" name="country" required>
                        </div>


                        <div class="form-group">
                            <label for="state">State<span class="text-danger">*</span></label>
                            <input type="text" placeholder="State" class="form-control" name="state" required>
                        </div>

                        <div class="form-group">
                            <label for="city">City / Town<span class="text-danger">*</span></label>

                            <input type="text" name="city_name" id="acity_name" class="form-control" placeholder="City / Town" required>

                        </div>


                        <div class="form-group">
                            <label for="pin">Pin Code<span class="text-danger">*</span></label>

                            <input type="number" name="pin" id="apin_id" class="form-control" placeholder="Pincode" required>

                        </div>
                        <div id="amsg">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <div id="changeButton">
                            <button type="submit" id="addsubmitbtn" class="cta border-0 ml-auto">
                                <span>Save</span>
                                <svg width="13px" height="10px" viewBox="0 0 13 10">
                                    <path d="M1,5 L11,5"></path>
                                    <polyline points="8 1 12 5 8 9"></polyline>
                                </svg>
                            </button>
                        </div>
                        <!-- <div id="changeButton">
                            <input onclick="" type="Submit" class="btn  prcPay rounded-0 text-white" id="addsubmitbtn" style="background-color:#8cc542;" value="Save">
                        </div>-->
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!--////////////////////////   edit address modal //////////////-->

    <div class="modal fade" id="Editing" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update <span id="echeckAdd">Shipping </span> Address</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{url('addressUpdates')}}" method="post" id="paymentfor_form">
                    @csrf
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="name">Name*</label>
                            <input type="text" placeholder="Enter your Name" class="form-control" name="address_name" required id="eaddress_name">

                            <input type="hidden" name="user_information_id" id="euser_information_id">
                            <input type="hidden" name="addresstype" id="eaddresstypes">
                        </div>




                        <div class="form-group">
                            <label for="Phnumber">Mobile Number</label>
                            <div class="input-group mb-3">
                                <input type="number" class="form-control" placeholder="Enter Mobile Number" name="mobile" required id="emobile">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="Address">Address<span class="text-danger">*</span></label>
                            <input type="text" placeholder="Enter your Address" class="form-control" name="address" required id="eaddress">
                        </div>
                        <div class="form-group">
                            <label for="City">LandMark</label>
                            <input type="text" placeholder="Enter your nearest Landmark" class="form-control" id="elandmark" name="landmark">
                        </div>

                        <div class="form-group">
                            <label for="country">Country<span class="text-danger">*</span></label>
                            <input type="text" placeholder="Country" class="form-control" name="country" id="ecountry" required>
                        </div>


                        <div class="form-group">
                            <label for="state">State<span class="text-danger">*</span></label>
                            <input type="text" placeholder="State" class="form-control" name="state" required id="estate">
                        </div>

                        <div class="form-group">
                            <label for="city">City / Town<span class="text-danger">*</span></label>

                            <input type="text" name="city_name" id="ecity_name" class="form-control" placeholder="City / Town">

                        </div>

                        <div class="form-group">
                            <label for="pin">Pin Code<span class="text-danger">*</span></label>

                            <input type="number" name="pin" id="epin" class="form-control" placeholder="Pincode" required>


                        </div>
                        <div id="emsg">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <div id="echangeButton">
                            <button type="submit" class="cta border-0 ml-auto">
                                <span>Update</span>
                                <svg width="13px" height="10px" viewBox="0 0 13 10">
                                    <path d="M1,5 L11,5"></path>
                                    <polyline points="8 1 12 5 8 9"></polyline>
                                </svg>
                            </button>
                        </div>
                        <!--<div id="echangeButton">
                            <input type="Submit" class="btn  prcPay rounded-0 text-white" id="editsubmitbtn" style="background-color:#8cc542;" value="Save">
                        </div>-->
                    </div>
                </form>
            </div>
        </div>
    </div>




</section>
</div>







<!--===================  end section  ====================-->


@stop
