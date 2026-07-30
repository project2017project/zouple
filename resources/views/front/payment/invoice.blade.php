<html>
<head>
    <title>The Zouple Invoice</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--//////////////  bootstrap ///////////-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <!-- ///  font awesome //////-->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!--//////////   external css //////////////-->
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/magiczoomplus.css">
    <!--/////////////////   font /////////-->
    <!--<link href="https://fonts.googleapis.com/css?family=Lobster&display=swap" rel="stylesheet">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link rel="icon" href="{{URL::asset('public/img/dark-logo.png')}}" type="image/gif" sizes="16x16">
    
</head>
<body>
    <section>
            <!--//////////////////////////   content ///////////////////////-->
            <section class="border border-dark  my-3">
                <div class="container py-2 px-5">
                    <div class="row">
                        <div class="col-12 h1 text-center pt-4">
                            <u>INVOICE</u>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6 align-self-center text-center">
                            <img src="{{URL::asset('public/img/dark-logo.png')}}" width="200px" alt="logo">
                        </div>
                        <div class="col-md-6 align-self-center">
                            <div class="h4">The Zouple</div>
                            <div class="h5">E/TF/4 Block E, Mukandpura, Bhankrora </div>
                            <div class="h5">Rajasthan, Jaipur ( 303006 )</div>
                            <div class="h5">GST No. : 08BTQPR767MIZH</div>
                        </div>
                    
                    </div>
                     <div class="row py-4">
                         <div class="col-md-6 align-self-center border py-2">
                            <div class="h4">Ship to</div>
                            @foreach($shipping_add as $data)
                                <div class="h5">{{$data->address_name}}</div>
                                <div class="h5">{{$data->address}}</div>
                                <div class="h5">{{$data->landmark}}</div>
                                <div class="h5"> {{$data->city_name}}</div>
                                <div class="h5">{{$data->state}}, {{$data->pin}}</div>
                                
                            @endforeach
                        </div>
                        <div class="col-md-6 align-self-center border py-2">
                            <div class="h4">Billed to</div>
                             @foreach($billing_add as $data)
                                <div class="h5">{{$data->address_name}}</div>
                                <div class="h5">{{$data->address}}</div>
                                <div class="h5">{{$data->landmark}}</div>
                                <div class="h5"> {{$data->city_name}}</div>
                                <div class="h5">{{$data->state}}, {{$data->pin}}</div>
                                
                            @endforeach
                        </div>
                        
                    
                    </div>
                    @foreach($order_data as $data)
                    <div class="row py-4">
                        <div class="col-md-6 align-self-center ">
                            <div class="h3">Invoice</div>
                            <div class="h5" style="color:#000000;"><span>0{{$data->order_id}}</span> / <span>{{date('Y')}}</span></div>
                            
                             <div class="h6">Order Number: <span>{{$data->order_number}}</span></div>
                            <div class="h6">Order Date: <span>{{date('d/M/Y',strtotime($data->order_date))}}</span></div>
                        </div>
                        <div class="col-md-5 align-self-center py-3 px-3 text-white" style="background-color:#000000;">
                            <div class="h3">
                                <i class="fa fa-inr pr-4"></i><span> @foreach($order_data as $data) {{$data->total_amount}}  @endforeach</span>/-
                            </div>
                            <div class="text-justify">
                                Thank you for your purchase. If you have any queries about your purchase or invoice, please feel free to contact us at your convenience. We will reply to you as soon as we get your message.
                            </div>
                        </div>
                    
                    </div>
                    @endforeach
                    <?php  $j=1; ?>
                    @foreach($order_data as $data)
                    <?php
                        $proDetails = json_decode($data->product_details);

                    ?>
                    @if($data->order_type == "DESIGN-SHIRT")
                    <div class="row py-1 my-2 border-top border-bottom">
                        @foreach($proDetails as $key => $dt)
                        <div class="col-3 col-sm-1 p-0">
                            <div class="text-center border"><b>{{$key}}</b></div>
                            <div class="text-center border">
                                @if($key == "febric" || $key == "FEBRIC" || $key == "Febric")
                                <img src="{{ z_media_url($febricImage[$dt], 'shirt') }}" width="100%">
                                @else
                                <img src="{{ z_media_url($elementValueImage[$dt], 'shirt') }}" width="100%">
                                @endif
                            </div>
                            <div class="text-center border">
                                @if($key == "febric" || $key == "FEBRIC" || $key == "Febric")
                                {{$febricName[$dt]}}
                                @else
                                {{$elementValueName[$dt]}}
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    
                    <div class="row py-2">
                        <div class="col-12">
                            <table class="table table-striped table-responsive-sm invoice table-borderd">
                                    <thead>
                                        <tr>
                                            <th style="width:80px;">Sr. No.</th>
                                            <th>Product Name</th>
                                            <th>Cost (<i class="fa fa-inr px-1"></i>)</th>
                                            <th  class="text-center">Qty</th>
                                            
                                            <th class="text-right">Total (<i class="fa fa-inr px-1"></i>)</th>
                                        </tr>
                                
                                    </thead>
                                <tbody>
                                     <?php
                                   
                                        $product_details = json_decode($data->product_details);

                                        ?>
                                        @foreach($product_details as $key => $pros)
                                        <?php 
                                            $pro_dets = explode('-',$pros);
                                        $sub_total = $pro_dets[2] * $pro_dets[1]
                                        ?>
                                    <tr>
                                        <td style="width:80px;" class="text-center">{{$j}}.</td>
                                        <td>{{$proTitle[$key]}} </td>
                                        <td><span>{{$pro_dets[2]}}</span>/-</td>
                                        <td  class="text-center">{{$pro_dets[1]}}</td>
                                        
                                        <td class="text-right"><span>{{$sub_total}}</span>/-</td>
                                    </tr>
                                         <?php $j++;?>
                                      @endforeach
                                </tbody>
                            
                            </table>
                            <small class="text-danger">Prices are inclusive all taxes.</small>
                        </div>
                        
                    </div>
                    
                   
                    @endif
                   
                    @endforeach
                   <h6>{{$data->query_Text}}</h6>
                    <div class="row">
                        
                         <div class="col-md-6">
                         </div>
                        
                        @foreach($order_data as $data)
                       
                        <div class="col-md-6  p-4 ">
                            @if($data->order_type == "DESIGN-SHIRT")
                             <div class="d-flex justify-content-between h6">
                                <div class="">Net Amount</div>
                                <div class=""><i class="fa fa-inr pr-2"></i><span>{{$data->total_amount}}</span>/-</div>
                            </div>
                            
                            <hr style="width:100%; margin: 8px 0px; border:1px solid #000000;">
                            
                            <hr>
                            <div class="d-flex justify-content-between h5">
                                <div class="">Total<small> ( With gst and shipping )</small></div>
                                <div class=""><i class="fa fa-inr pr-2"></i><span>{{$data->total_amount}}</span>/-</div>
                            </div>
                            
                        @else
                        <div class="d-flex justify-content-between h6">
                                <div class="">Net Amount</div>
                                <div class=""><i class="fa fa-inr pr-2"></i><span>{{$data->net_amount}}</span>/-</div>
                            </div>
                            @if($data->discount>0)
                            <div class="d-flex justify-content-between h6">
                                <div class="">Coupon Discount ({{$data->coupon_code}} with {{$data->coupon_discount}}%):</div>
                                <div class=""><i class="fa fa-inr pr-2"></i><span>{{$data->discount}}</span>/-</div>
                            </div>
                            @endif
                            <!--<div class="d-flex justify-content-between h6">
                                <div class="">GST</div>
                                <div class=""><i class="fa fa-inr pr-2"></i><span>{{$data->product_gst}}</span>/-</div>
                            </div>-->
                            <div class="d-flex justify-content-between h6">
                                <div class="">Shipping</div>
                                <div class=""><i class="fa fa-inr pr-2"></i><span>{{$data->shipping}}</span>/-</div>
                            </div>  
                            
                            <hr style="width:100%; margin: 8px 0px; border:1px solid #000000;">
                            
                            <div class="d-flex justify-content-between h5">
                                <div class="">Total (<small>with Round</small>)</div>
                                <div class=""><i class="fa fa-inr pr-2"></i><span>{{$data->total_amount}}</span>/-</div>
                            </div>
                            
                        @endif
                           
                            
                           
                            
                            
                           
                        </div>  
                        @endforeach
                    </div>
                    <hr style="border:2px solid #000000;">
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <div>
                               <span class="h6"> Email:-</span> contact@zouple.in

                         </div>  
                        </div>
                        <div class="col-md-6 text-center">
                            <div class="">
                                <span class="h6">Website:-</span> zouple.in
                            </div>
                        </div>
                    </div>
                    
                    <div class="row justify-content-center mt-4">
                        <div class="col-sm-4 text-center">
                        
                        <button onclick="myFunction()" class="btn text-white" style="background-color:#000000">Print this page</button>
                        
                        <script>
                        function myFunction() {
                          window.print();
                        }
                        </script>                        
                        </div>
                    </div>
                </div>
            </section>
    </section>   
</body>
    <!--/////////////////////  script src ////////////////////-->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script  src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!--//////////////   external js ////////////////-->
</html>
        
        