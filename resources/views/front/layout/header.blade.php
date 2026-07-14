@if (count($errors) > 0)
<div class="alert alert-danger" style="z-index:111111;position:fixed;top:0; right:0;">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="flash-message text-center" style="z-index:111111;position:fixed;top:0;right:0;  ">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if(Session::has('alert-' . $msg))

    <p class="text-center alert alert-{{ $msg }} fontsinc">{{ Session::get('alert-' . $msg) }}
        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
    </p>
    @endif
    @endforeach
</div>


<script>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<!--================   menu bar ================-->

<header class="fixed-top fixedTopMenu text-white">

    <div class="container-fluid px-2 px-md-5 py-2 pb-3">
        <div class="row maxWidhtContainer">
            <div class="col-2 menuContent col-md-4 d-flex">
                <div class="align-self-center  text-center btn btnSm menuBtn" style="line-height: 10px;">
                    <i class="fa fa-bars d-block m-0 pb-1"></i>
                    <span style="font-size: 12px;">Menu</span>
                </div>
            </div>

            <div class="col-5 logoContent col-md-4 align-self-center ">
                <a href="{{url('/')}}"><img src="{{URL::asset('public/front/images/logo.png')}}" width="100px"></a>
            </div>

            <div class="col-5 cartContent col-md-4 d-flex justify-content-end">

                <div class="mx-1 btn btnSm searchBtn align-self-center text-center" style="line-height: 10px;">
                    <i class="fa fa-search d-block m-0 pb-1"></i>
                    <span style="font-size: 12px;">Search</span>
                </div>

                <div class="mx-1 btn btnSm align-self-center " style="line-height: 10px; text-align:left!important">
                    <a href="{{url('cart')}}" class="text-white">
                        <i class="fa fa-shopping-bag d-block m-0 pb-1"><sup class="pl-1 js-cart-count">{{ $cart_item ?? 0 }}</sup></i>
                        <span style="font-size: 12px;">Cart</span>
                    </a>
                </div>

                @if(isset(Auth::user()->id))
                <div class="mx-1 btn btnSm align-self-center text-center" style="line-height: 10px;">
                    <a href="{{url('wishlist')}}" class="text-white">
                        <i class="fa fa-heart-o d-block m-0 pb-1"><sup class="pl-1 js-wishlist-count">{{ $wish_item ?? 0 }}</sup></i>
                        <span style="font-size: 12px;">Wishlist</span>
                    </a>
                </div>
                @else

                <div class="mx-1 btn btnSm align-self-center text-center" data-target="#logSign" data-toggle="modal" style="line-height: 10px;">
                    <i class="fa fa-heart-o d-block m-0 pb-1"><sup class="pl-1 js-wishlist-count">{{ $wish_item ?? 0 }}</sup></i>
                    <span style="font-size: 12px;">Wishlist</span>
                </div>

                @endif

                @if(isset(Auth::user()->id))
                <div class="mx-1 align-self-center  btn btnSm text-center userBtn" style="line-height: 10px;">
                    <i class="fa fa-user-o d-block m-0 pb-1"></i>
                    <span style="font-size: 12px;">
                        <?php 
                              $name = Auth::user()->name;
                              $user_name = explode(" ", $name);
                              echo $user_name[0];
                       ?>
                    </span>
                </div>

                @else
                <div class="mx-1 align-self-center  btn btnSm text-center" data-target="#logSign" data-toggle="modal" style="line-height: 10px;">
                    <i class="fa fa-user-o d-block m-0 pb-1"></i>
                    <span style="font-size: 12px;">User</span>
                </div>
                @endif
            </div>

        </div>
    </div>

</header>

<!--=============================   menu links ================-->

<div class="container-fluid  ">
    <div class="row maxWidhtContainer">
        <div class="col-12 p-0  meunTab fixed-top d-flex">
            <div class="col-12 col-lg-4 p-0 " style="background-color:black; ">
                <div class="position-absolute" style="bottom:10%; left:46%; z-index: 2; ">
                    <i class="fa fa-times py-2 text-white btn closeBtn font-weight-normal bg-danger rounded-circle width:40px; height:40px"></i>
                </div>
                <div class="text-center py-4">
                    <a href="{{url('/')}}"><img src="{{URL::asset('public/front/images/logo.png')}}" width="100px" style="z-index: 1;"></a>
                </div>

                <div class="" style="background-color:black;">
                    <ul class="list-unstyled px-4 mainCat accordion m-0" id="accordion">
                        <li><a href="{{url('/')}}" class="text-white">HOME</a></li>
                        <li><a href="{{url('about')}}" class="text-white">ABOUT US</a></li>
                        <li><a href="{{url('designShirt')}}" class="text-white">MEN`S SHIRT CUSTOMIZATION</a></li>
                        @foreach($categories ?? [] as $category)
                        @if($category->is_active == "ACTIVE")
                        @if(count($category->childs))

                        <li data-toggle="collapse" href="#{{$category->slug}}" aria-expanded="false" aria-controls="menuDown" class="text-white collapseHead">
                            <div class="d-flex justify-content-between"><a class="text-white">{{$category->title}}</a><i class="fa fa-sort-desc align-self-center p-0"></i></div>

                            <?php $childs = $category->childs; ?>
                            <div class="collapse px-2" id="{{$category->slug}}" data-parent="#accordion">
                                @foreach($childs as $child)
                                @if($child->is_active == "ACTIVE")
                                <div><a href="{{url('category',$child->slug)}}" class="text-white d-inline-block py-1 h6 m-0 h-100"><i class="fa fa-caret-right" style="margin-right:-22px;" aria-hidden="true" ></i> {{$child->title}}</a></div>
                                @endif
                                @endforeach
                            </div>

                        </li>
                        @else
                        <li><a href="{{url('category',$category->slug)}}" class="text-white">{{$category->title}}</a></li>
                        @endif

                        @endif
                        @endforeach

                        <li><a href="{{url('blog')}}" class="text-white">BLOGS</a></li>
                        <li><a href="{{url('contact')}}" class="text-white">CONTACT US</a></li>

                    </ul>

                </div>

                <div class="pt-5 pb-3" style=" background-color:black;">
                    @foreach($siteinformation ?? [] as $contact)
                    <ul class="list-unstyled d-flex justify-content-center text-center m-auto">
                        <li><a href="{{$contact->facebook_url}}" target="_blank" class='text-white px-2'><i class="fa fa-facebook "></i></a></li>
                        <li><a href="{{$contact->instagram_url}}" target="_blank" class='text-white px-2'><i class="fa fa-instagram "></i></a></li>
                        <li><a href="{{$contact->pinterest}}" target="_blank" class='text-white px-2'><i class="fa fa-pinterest "></i></a></li>
                        <li><a href="{{$contact->youtube}}" target="_blank" class='text-white px-2'><i class="fa fa-youtube-play "></i></a></li>
                    </ul>
                    @endforeach

                </div>

            </div>
            <div class="col-12 col-lg-8 d-none closeBtn d-lg-block" style=""></div>
        </div>

    </div>
</div>

<!--====================  USER  PANEL =======================-->

<div class="container-fluid  ">
    <div class="row maxWidhtContainer">
        <div class="col-12 p-0 userTab fixed-top d-flex">

            <div class="col-12 col-lg-9 d-none closeBtn2 d-lg-block" style=""></div>

            <div class="col-12 col-lg-3 p-0 " style="background-color:black; ">

                <div class="text-center py-4">
                    <a href="{{url('')}}"><img src="{{URL::asset('public/front/images/logo.png')}}" width="100px" style="z-index: 1;"></a>
                </div>
                
                <div class="position-absolute" style="bottom:10%; left:46%; z-index: 2; ">
                    <i class="fa fa-times py-2 text-white btn closeBtn2 font-weight-normal bg-danger rounded-circle width:40px; height:40px"></i>
                </div>

                <div class="">
                    <ul class="list-unstyled px-4 mainCat">
                        <li><a href="{{url('dashboard')}}" class='text-white'><i class="fa fa-user"></i>MY PROFILE</a></li>
                        <li><a href="{{url('yourOrder')}}" class='text-white'><i class="fa fa-shopping-bag"></i>ORDERS</a></li>
                        <li><a href="{{url('cancleOrder')}}" class='text-white'><i class="fa fa-times"></i>CANCEL ORDER</a></li>
                        <li><a href="{{url('returnOrder')}}" class='text-white'><i class="fa fa-retweet"></i>RETURN ORDERS</a></li>
                        <li><a href="{{url('wishlist')}}" class='text-white'><i class="fa fa-heart"></i>WISHLIST</a></li>

                        <li><a href="{{url('shippingAddress')}}" class='text-white'><i class="fa fa-map-marker"></i>SHIPPING ADDRESS</a></li>
                        <li><a href="{{url('billingAddress')}}" class='text-white'><i class="fa fa-map-marker"></i>BILLING ADDRESS</a></li>

                        <li><a href="{{url('logout')}}" class='text-white'><i class="fa fa-sign-out" aria-hidden="true"></i>LOG OUT</a></li>
                    </ul>

                </div>

            </div>

        </div>

    </div>

</div>

<!--======================  search panel ==================-->

<div class="searchContent m-auto d-flex maxWidhtContainer">
    <div class="form-group col-md-8 offset-md-2 align-self-center searchBox">
        <form action="{{url('searchData')}}" method="get">
            @csrf
            <div class="input-group d-flex">
                <input type="search" class="form-control text-white border-0 bg-transparent" placeholder='Type text here' name="searchData" required>
                <button type="submit" class="aling-self-center pt-1 pl-2 bg-transparent border-0"><i class="fa fa-search fa-2x text-white"></i></button>
            </div>
        </form>
        <div class="text-center my-3 justify-content-center d-flex closeBtn3">
            <i class="fa fa-close fa-2x text-white"></i><span class="h5 pl-2 m-0 align-self-center text-white">Close</span>
        </div>
    </div>
</div>

<!--======================  Alert Example Box ==================-->
<style>
.modal-dialog-update {
   position:fixed;
   top:auto;
   right:auto;
   left:0;
   bottom:0;
}  
</style>
<button id="showAlertBox" type="button" class="btn btn-primary" data-toggle="modal" data-target="#examplePageAlertBox" style="display:none">
    Launch demo modal
</button>
<!-- Modal -->
<div class="modal fade showPageAlertBox text-white rounded-0" id="examplePageAlertBox" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-update rounded-0" role="document">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLongTitle">Do you find what you were looking for ?</h6>
                <button type="button" onclick="dismissShowAlert()" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" onclick="dismissShowAlert()">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <button type="button" onclick="dismissShowAlert()" class="btn-sm btn-secondary " data-dismiss="modal"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Yes</button>
                </div>
                 @if(isset(Auth::user()->id))
                <div class="mt-3">
                    <button type="button" id="alertNoMessage" class="btn-sm btn-danger" data-toggle="modal" data-target="#exampleFormBox" data-dismiss="modal"><i class="fa fa-times-circle-o" aria-hidden="true"></i> No</button>
                </div>
                @else
                <div class="mt-3">
                    <button type="button" id="alertNoMessage" onclick="afterCheckLoginPop()" class="btn-sm btn-danger" data-target="#logSign" data-toggle="modal"><i class="fa fa-times-circle-o" aria-hidden="true"></i> No</button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleFormBox" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-update rounded-0" role="document">
        <div class="modal-content bg-dark text-white rounded-0">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLongTitle">Please let us know what are you looking ?</h6>
                <button onclick="dismissShowAlert()" type="button"  class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" onclick="dismissShowAlert()">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{url('messageSendSave')}}">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="message" class="col-form-label h6">Message </label>
                                <textarea class="form-control rounded-0 h6" name="message" id="message" placeholder="Message" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 m-0 p-0">
                        <button type="submit" class="btn btn-danger"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
