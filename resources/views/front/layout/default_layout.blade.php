<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @php
        $seoTitle = isset($page_title) && trim($page_title) !== '' ? trim($page_title) : 'Zouple | Premium Signage, Name Plates and Custom Signs';
        $seoDescription = isset($meta_description) && trim($meta_description) !== '' ? trim($meta_description) : 'Shop Zouple stainless steel signage, acrylic signs, name plates, flat number plates, office signs, washroom signs, CCTV signs, beware of dog signs, and custom address signage.';
        $seoKeywords = isset($meta_keyword) && trim($meta_keyword) !== '' ? trim($meta_keyword) : 'Zouple, signage, name plates, stainless steel signs, acrylic signs, custom signs, office signs, flat number plates, CCTV signs, beware of dog signs';
        $seoUrl = url()->current();
        $seoImage = URL::asset('public/img/dark-logo.png');
        $robotsContent = request()->is('masterAdmin*') ? 'noindex,nofollow' : 'index,follow,max-image-preview:large';
        $organizationSchema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Zouple',
            'url' => url('/'),
            'logo' => $seoImage,
        );
        $websiteSchema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'Zouple',
            'url' => url('/'),
            'potentialAction' => array(
                '@type' => 'SearchAction',
                'target' => url('searchData') . '?search={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ),
        );
        $schemaProduct = null;
        if (isset($products_show) && is_object($products_show) && method_exists($products_show, 'first') && $products_show->count() > 0) {
            $schemaProductData = $products_show->first();
            $schemaProductPrice = isset($schemaProductData->rupee_net_with_gst) && (float) $schemaProductData->rupee_net_with_gst > 0
                ? (float) $schemaProductData->rupee_net_with_gst
                : (isset($schemaProductData->rupee_price) ? (float) $schemaProductData->rupee_price : 0);
            $schemaProductImage = isset($schemaProductData->product_image) ? z_optimized_media_url($schemaProductData->product_image, 'products') : $seoImage;
            $schemaProduct = array(
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => isset($schemaProductData->product_title) ? $schemaProductData->product_title : $seoTitle,
                'image' => $schemaProductImage,
                'description' => $seoDescription,
                'sku' => isset($schemaProductData->sku) ? $schemaProductData->sku : '',
                'brand' => array('@type' => 'Brand', 'name' => 'Zouple'),
                'offers' => array(
                    '@type' => 'Offer',
                    'priceCurrency' => 'INR',
                    'price' => $schemaProductPrice,
                    'availability' => (isset($schemaProductData->in_stock) && $schemaProductData->in_stock === 'IN_STOCK') ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    'url' => $seoUrl,
                ),
            );
        }
    @endphp
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ $seoKeywords }}">
    <meta name="robots" content="{{ $robotsContent }}">
    <meta name="author" content="PROFTCODE IT INDUSTRIES">
    <link rel="canonical" href="{{ $seoUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:site_name" content="Zouple">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">


    <title>{{ $seoTitle }}</title>

    <!--====================   custom css ===================-->

    <link href="{{URL::asset('public/front/css/index.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{URL::asset('public/front/css/owl.carousel.css')}}">
    <link rel="stylesheet" href="{{URL::asset('public/front/css/owl.theme.css')}}">
    <link rel="icon" href="{{URL::asset('public/img/dark-logo.png')}}" type="image/gif" sizes="16x16">


    <!--=======================  cdn files ====================-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Jura:700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">

    <link rel="stylesheet" href="{{URL::asset('public/front/css/magiczoomplus.css')}}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
    <link href="{{URL::asset('public/front/css/zouple-luxury.css')}}?v=20260703-testimonial-logo1" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css?family=PT+Sans&display=swap" rel="stylesheet">
    <!--===============================================================================================-->
    <script src="{{URL::asset('public/front/js/magiczoom.js')}}"></script>

    <!-- For Seo -->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-149857508-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-149857508-1');

    </script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-149857508-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-149857508-1', {
            'optimize_id': 'GTM-TLSPCXD'
        });

    </script>
    <!-- Google Tag Manager -->
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-MZW8H54');

    </script>
    <!-- End Google Tag Manager -->
    <!-- End Google Tag Manager -->

    <!-- End For SEO -->
    <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @if($schemaProduct)
    <script type="application/ld+json">{!! json_encode($schemaProduct, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif

    <style>
        /* Center the loader */
        #loader {
            position: absolute;
            left: 50%;
            top: 50%;
            z-index: 1;

            margin: -125px 0 0 -125px;
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid black;
            width: 250px;
            height: 250px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Add animation to "page content" */
        .animate-bottom {
            position: relative;
            -webkit-animation-name: animatebottom;
            -webkit-animation-duration: 1s;
            animation-name: animatebottom;
            animation-duration: 1s
        }

        @-webkit-keyframes animatebottom {
            from {
                bottom: -100px;
                opacity: 0
            }

            to {
                bottom: 0px;
                opacity: 1
            }
        }

        @keyframes animatebottom {
            from {
                bottom: -100px;
                opacity: 0
            }

            to {
                bottom: 0;
                opacity: 1
            }
        }

        #loaderImage {
            left: 50%;
            top: 50%;
            z-index: 1;
            width: 200px;
            height: 200px;
            margin: -120px 0 0 -100px;
            position: absolute !important;
        }



        #myDiv {
            display: none;
        }

    </style>

</head>

<body onload="myFunction()" style="margin:0;">



    <!--  Time Popup Code Start -->

    

    <!--  Time Popup Code End  -->


    <!-- FOR SEO -->
    <!-- Google Tag Manager (noscript) -->
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MZW8H54" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <!-- End Google Tag Manager (noscript) -->


    <div id="loaderdiv" class="py-3 text-center align-self-center">
        <div id="loaderImage">
            <img src="{{URL::asset('public/img/dark-sm-logo.png')}}" width="200px" class="mt-5">
        </div>
        <div id="loader">

        </div>
    </div>
    <div style="display:none;" id="myDiv" class="animate-bottom">
        @include('front.layout.header')
        @yield('content')



        <!------------------------------- User Logi Code Start ------------------------------->

        <script>
            function chanageImage() {
                $('#imageStore').html(@foreach(($signupbann ?? []) as $sign)
                    "<img src='{{ z_media_url($sign->image, 'loginbanner') }}' width='100%' height='100%'>"
                    @endforeach)
            }

            function changeImage2() {
                $('#imageStore').html(@foreach(($loginban ?? []) as $login)
                    "<img src='{{ z_media_url($login->image, 'loginbanner') }}' width='100%' height='100%'>"
                    @endforeach)
            }

        </script>

        <!--========================== log in modal =====================-->

        <div class="modal fade loginn mt-4" id="logSign" role="dialog">
            <div class="modal-dialog m-auto ">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-body row  py-0">
                        <div class="col-md-6 d-none d-md-block align-self-center p-0" id="imageStore">
                            @foreach(($loginban ?? []) as $sign) <img src="{{ z_media_url($sign->image, 'loginbanner') }}" width="100%" height="100%"> @endforeach

                        </div>
                        <div class="col-md-6 login_form">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <ul class="nav nav-tabs py-3">
                                <li class="px-2 active signIn"><a data-toggle="tab" href="#home" class="text-dark" onclick="changeImage2()">LOGIN</a></li>
                                <li class="px-2 signUP"><a data-toggle="tab" href="#sign_m1" class="text-dark" onclick="chanageImage()">SIGNUP</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="home" class="tab-pane fade in active">
                                    <form action="{{url('userlogin')}}" class="log_fm" method="post">
                                        @csrf
                                        <div class="form-group pt-2 col-md-12">
                                            <label for="login_email">Email:</label>
                                            <input type="email" class="form-control" id="login_email" placeholder="Enter email" name="email" required>
                                        </div>
                                        <div class="form-group pt-2 m-0 col-md-12">
                                            <label for="login_pwd">Password:</label>
                                            <input type="password" class="form-control password-field" id="login_pwd" placeholder="Enter password" name="password" required>

                                        </div>
                                        <div class="h6 m-0 text-right p-2 text-secondary forgatPass">Forget Password?</div>
                                        <div class="form-group col-md-12">
                                            <button type="submit" class="cta border-0">
                                                <span>Log in </span>
                                                <svg width="13px" height="10px" viewBox="0 0 13 10">
                                                    <path d="M1,5 L11,5"></path>
                                                    <polyline points="8 1 12 5 8 9"></polyline>
                                                </svg>
                                            </button>




                                            <p class="trm_condi pt-3">By creating this account, you agree to our <a href="{{url('cms/terms-of-use')}}">Terms & Conditions </a>&<a href="{{url('/cms/private-policy')}}"> Privacy Policy</a> .</p>
                                        </div>
                                    </form>
                                    <form action="{{url('forgotpassword')}}" method="post" class="forgot">
                                        @csrf
                                        <div class="form-group pt-2 col-md-12">
                                            <label for="forgot_email">Email:</label>
                                            <input type="email" class="form-control" id="forgot_email" placeholder="Enter email" name="email" required>

                                        </div>
                                        <div class="h6 m-0 text-right p-2 text-secondary logIN">Log in</div>
                                        <div class="form-group col-md-12">
                                            <button type="submit" class="cta border-0">
                                                <span>Submit</span>
                                                <svg width="13px" height="10px" viewBox="0 0 13 10">
                                                    <path d="M1,5 L11,5"></path>
                                                    <polyline points="8 1 12 5 8 9"></polyline>
                                                </svg>
                                            </button>
                                        </div>
                                    </form>

                                </div>
                                <div id="sign_m1" class="tab-pane fade px-3">
                                    <form action="{{url('registration')}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group pt-2 col-12 col-sm-12  col-md-12">
                                                <label for="name">Name:</label>
                                                <input type="text" class="form-control" id="name" placeholder="Enter Full Name" name="name" required>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="form-group pt-2 col-md-12">
                                                <label for="signup_email">Email:</label>
                                                <input type="email" class="form-control" id="signup_email" placeholder="Enter Email" name="email" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group pt-2 col-12 col-sm-6  col-md-6">
                                                <label for="signup_pwd">Password:</label>
                                                <input type="password" class="form-control password-field-full" id="signup_pwd" data-password-hint="signup_pwd_hint" placeholder="Enter Password" name="password" required>
                                            </div>
                                            <div class="form-group pt-2 col-12  col-sm-6 col-md-6">
                                                <label for="signup_pwd_confirm">Confirm Password:</label>
                                                <input type="password" class="form-control password-field" id="signup_pwd_confirm" placeholder="Enter Confirm password" name="password_confirmation" required>
                                            </div>
                                            <div class="col-12">
                                                <p class="password-static-hint invalid mb-2" id="signup_pwd_hint">Your password must be more than 8 characters long. It should contain atleast 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character</p>
                                            </div>
                                        </div>



                                        <div class="row">
                                            <div class="form-group pt-2 col-md-12">
                                                <label for="signup_contact">Contact Number:</label>
                                                <input type="number" class="form-control" id="signup_contact" placeholder="Enter Contact Number" name="contact" required>
                                                <p class="trm_condi pt-3">By creating this account, you agree to our <a href="{{url('cms/terms-of-use')}}">Terms & Conditions </a>&<a href="{{url('/cms/private-policy')}}"> Privacy Policy</a> .</p>
                                            </div>
                                        </div>
                                        <div class="form-group pt-1 col-md-12 p-0">
                                            <button type="submit" class="cta border-0">
                                                <span>Signup</span>
                                                <svg width="13px" height="10px" viewBox="0 0 13 10">
                                                    <path d="M1,5 L11,5"></path>
                                                    <polyline points="8 1 12 5 8 9"></polyline>
                                                </svg>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!------------------------------- User Logi Code End ------------------------------->

        <!-- Waiting Loader -->
        <div id="wait" class="text-center">
            <img src="{{URL::asset('public/img/loader.gif')}}" width="25%">
        </div>


        <footer class="zouple-luxury-footer" itemscope itemtype="https://schema.org/Organization">

            <div class="container-fluid zouple-footer-brand">
                <div class="row maxWidhtContainer align-items-center py-4">
                    <div class="col-md-4 py-2">
                        <a href="{{url('/')}}" aria-label="Zouple home" itemprop="url">
                            <img src="{{URL::asset('public/img/dark-logo.png')}}" alt="Zouple premium signage" width="140" itemprop="logo">
                        </a>
                    </div>
                    <div class="col-md-8 py-2">
                        <h2 class="m-0" itemprop="name">Zouple</h2>
                        <p class="zouple-footer-tagline m-0">Precision-Crafted Premium Signage for Modern Homes, Offices & Commercial Spaces.</p>
                    </div>
                </div>
            </div>

            <div class="zouple-footer-newsletter">
                <div class="container-fluid">
                    <div class="row maxWidhtContainer">
                        <div class="col-md-6 py-4">
                            <div class="h5 m-0 text-white subscribe">SUBSCRIBE FOR NEWSLETTER</div>
                        </div>

                        <div class="col-md-6 subscribeForm py-3 align-self-center">
                            <form method="post" action="{{url('newsubscribe_save')}}">
                                @csrf
                                <div class="input-group subscribe">
                                    <input type="email" name="email" required class="form-control text-white border-right-0" style="border-top-right-radius: 0px!important; border-bottom-right-radius: 0px!important;">
                                    <input type="submit">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <div class="container-fluid text-white zouple-footer-links">
                <div class="row py-2 maxWidhtContainer">
                    <div class="col-sm-6 col-md-3 col-6 py-3 quickLinks">
                        <ul class="list-unstyled">
                            <h5 class="m-0 pb-2  footer_head" >My Account</h5>
                            @if(isset(Auth::user()->id))
                            <li class="pl-3 pt-2"><a href="{{url('yourOrder')}}" class="text-white">My Order</a></li>
                            <li class="pl-3 pt-2"><a href="{{url('returnOrder')}}" class="text-white">Return Order</a></li>
                            <li class="pl-3 pt-2"><a href="{{url('dashboard')}}" class="text-white">Change Password</a></li>
                            <li class="pl-3 pt-2"><a href="{{url('wishlist')}}" class="text-white">My Wishlist</a></li>
                            @else

                            <li class="pl-3 pt-2"><a href="" data-target="#logSign" data-toggle="modal" class="text-white">My Order</a></li>
                            <li class="pl-3 pt-2"><a href="" data-target="#logSign" data-toggle="modal" class="text-white">Return Order</a></li>
                            <li class="pl-3 pt-2"><a href="" data-target="#logSign" data-toggle="modal" class="text-white">Change Password</a></li>
                            <li class="pl-3 pt-2"><a href="" data-target="#logSign" data-toggle="modal" class="text-white">My Wishlist</a></li>

                            @endif


                        </ul>
                    </div>
                    <div class="col-sm-6 col-md-3 col-6 py-3 quickLinks">
                        <ul class="list-unstyled">
                            <h5 class="m-0 pb-2  footer_head">Quick Links</h5>
                            <li class="pl-3 pt-2"><a href="{{url('/')}}" class="text-white">Home</a></li>
                            <li class="pl-3 pt-2"><a href="{{url('about')}}" class="text-white">About Us</a></li>
                            <li class="pl-3 pt-2"><a href="{{url('blog')}}" class="text-white">Blogs</a></li>
                            <li class="pl-3 pt-2"><a href="{{url('contact')}}" class="text-white">Contact Us</a></li>

                        </ul>
                    </div>

                    <div class="col-sm-6 col-md-3 col-6 py-3 quickLinks">
                        <ul class="list-unstyled">
                            <h5 class="m-0 pb-2 footer_head">Policies</h5>
                            <li class="pl-3 pt-2"><a href="{{url('cms','private-policy')}}" class="text-white">Privacy & Policy</a></li>
                            <li class="pl-3 pt-2"><a href="{{url('cms','terms-of-use')}}" class="text-white">Terms & conditions</a></li>
                            <li class="pl-3 pt-2"><a href="{{url('cms','delivery-shipping')}}" class="text-white">Shipping Policy</a></li>
                            <li class="pl-3 pt-2"><a href="{{url('cms','return-policy')}}" class="text-white">Return Policy</a></li>
                            <li class="pl-3 pt-2"><a href="{{url('cms','disclaimer')}}" class="text-white">Disclaimer</a></li>


                        </ul>
                    </div>
                    <div class="col-sm-6 col-md-3 py-3 col-6 quickLinks">
                        <ul class="list-unstyled">
                            <h5 class="m-0 pb-2 footer_head">Follow Us</h5>
                            @foreach(($siteinformation ?? []) as $contact)
                            <li class=" pl-3 pt-2">
                                <a href="{{$contact->facebook_url}}" target="_blank" class="text-white d-flex" aria-label="Zouple on Facebook">
                                    <div style="width: 25px;"><i class="fa fa-facebook"></i></div>
                                    <div>Facebook</div>
                                </a>

                            </li>
                            <!--<li class=" pl-3 pt-2">
                            <a href="{{$contact->twitter_url}}" target="_blank" class="text-white d-flex">
                                <div style="width: 25px;"><i class="fa fa-twitter"></i></div>
                                <div>Twitter</div>
                            </a>

                        </li>-->
                            <li class=" pl-3 pt-2">
                                <a href="{{$contact->instagram_url}}" target="_blank" class="text-white d-flex" aria-label="Zouple on Instagram">
                                    <div style="width: 25px;"><i class="fa fa-instagram"></i></div>
                                    <div>Instagram</div>
                                </a>
                            </li>
                            <li class=" pl-3 pt-2">
                                <a href="{{$contact->pinterest}}" target="_blank" class="text-white d-flex" aria-label="Zouple on Pinterest">
                                    <div style="width: 25px;"><i class="fa fa-pinterest"></i></div>
                                    <div>Pinterest</div>
                                </a>
                            </li>

                            <li class=" pl-3 pt-2">
                                <a href="{{$contact->youtube}}" target="_blank" class="text-white d-flex" aria-label="Zouple on YouTube">
                                    <div style="width: 25px;"><i class="fa fa-youtube-play"></i></div>
                                    <div>Youtube</div>
                                </a>
                            </li>

                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="container-fluid zouple-footer-trust">
                <div class="row maxWidhtContainer">
                    <div class="paymentThro col-md-6 py-3">
                        <div class="h6 m-0 text-dark">WE DELIVER THROUGH</div>
                        <div><img src="{{URL::asset('public/front/images/social1.png')}}"></div>
                    </div>

                    <div class="paymentThro col-md-6 paymentMadEasy py-3">
                        <div class="h6 m-0 text-dark">PAYMENT MADE EASY THROUGH</div>
                        <div><img src="{{URL::asset('public/front/images/social2.png')}}" width="70%"></div>
                    </div>
                </div>
            </div>

            <div class="container-fluid zouple-footer-copy">
                <div class="row maxWidhtContainer">
                    <div class="col-12 text-center py-2 text-white">
                        <i class="fa fa-copyright"></i> 2019 - 2026 zouple.in. All rights reserved

                    </div>
                </div>
            </div>

        </footer>

    </div>

</body>

<!--======================  custom css =====================-->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
<script src="{{URL::asset('public/front/js/owl.carousel.min.js')}}"></script>
<script src="{{URL::asset('public/front/js/custom-owl.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="{{URL::asset('public/front/js/index.js')}}?v=20260703-mobile-visible1"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script src="{{URL::asset('public/front/js/zouple-luxury.js')}}?v=20260703-mobile-visible1"></script>


<script type="text/javascript" src="{{URL::asset('public/front/vendor/sweetalert/sweetalert.min.js')}}"></script>
<script type="text/javascript">
    function addProductFav(product_id) {
        $.ajax({
            url: 'product_add_fav',
            type: "GET",
            data: "product_id=" + product_id,
            success: function(data) {
                swal("Product Added!", 'Product successfully added in your Wishlist.', "success");

                setTimeout(reloadPage, 1500);

            }
        });

    }

    function removeProductFav(product_id) {
        $.ajax({
            url: 'product_remove_fav',
            type: "GET",
            data: "product_id=" + product_id,
            success: function(data) {
                swal("Product Remove!", 'Product successfully removed in your Wishlist.', "success");
                setTimeout(reloadPage, 1500);
            }
        });

    }

    function reloadPage() {
        location.reload();
    }

</script>
<script src="{{URL::asset('public/js/password-util.js')}}"></script>
<script>
    $(document).ready(function() {
        if (typeof PasswordUtil !== 'undefined') {
            PasswordUtil.initAll();
            $('#logSign').on('shown.bs.modal', function() {
                PasswordUtil.initAll();
            });
        }
    });
</script>
<script>
    var myVar;
    var m

    function myFunction() {
        myVar = setTimeout(showPage, 0);
    }

    function showPage() {
        document.getElementById("loaderdiv").style.display = "none";
        document.getElementById("myDiv").style.display = "block";
    }

    (function () {
        function revealSite() {
            var loader = document.getElementById("loaderdiv");
            var page = document.getElementById("myDiv");

            if (loader) {
                loader.style.display = "none";
            }

            if (page) {
                page.style.display = "block";
            }
        }

        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", function () {
                setTimeout(revealSite, 800);
            });
        } else {
            setTimeout(revealSite, 800);
        }

        setTimeout(revealSite, 3500);
    })();

    function showPageAlertBox()
    {
       document.getElementById("showAlertBox").click();
    }
    
    function dismissShowAlert()
    {
        <?php
        session(['showAlert' => 'No']);
        session(['showAlertContent' => 'No']);
        ?>
        alert("are you sure");
    }
    function afterCheckLoginPop()
    {
        <?php 
        session(['showAlert' => 'Yes']);
        session(['showAlertContent' => 'Yes']);
        ?>
    }
    function showAlertContent()
    {
        document.getElementById("alertNoMessage").click();
    }
</script>

</html>
