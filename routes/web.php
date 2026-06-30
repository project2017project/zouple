<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes(['verify' => true]);
Route::get('robots.txt', 'SeoController@robots');
Route::get('sitemap.xml', 'SeoController@sitemap');
Route::get('getdata','HomeController@ipAddressFounder');
Route::any('printInvoicesss/{order_number}','ProfileController@customerMailInvoice');

Route::any('cmsss/{slug}','HomeController@cmspagesss');  
/* ============================== Front Admin Code Start ============================== */

Route::group([ 'middleware' => ['shirt_mid']], function () {
        Route::any('printInvoice/{order_number}','ProfileController@showcustomerInvoice');
    });
Route::group([ 'middleware' => ['guest_front','web_mid']], function () {
    

/* ------------------------------------- Design Shirt Code Start ------------------------------------- */    
Route::group([ 'middleware' => ['shirt_mid']], function () {
    Route::any('designShirt','DesignShirtController@designShirtList');
    Route::any('elementsList','DesignShirtController@elementsListShow');
    Route::any('nextElementList','DesignShirtController@nextElementListShow');
    Route::any('selectedElementChange/{dt}','DesignShirtController@selectedElementChangeShow');
    Route::any('seeYourShirt','DesignShirtController@seeYourShirtShow');
    Route::any('selectedFebricChange/{dt}','DesignShirtController@selectedFebricChangeShow');
    Route::any('seeYourFebric','DesignShirtController@seeYourFebricShow');
    Route::any('preElementList','DesignShirtController@preElementListShow');
    Route::any('querySetSession','DesignShirtController@querySetSessionStore');
});
/* ------------------------------------- Design Shirt Code End ------------------------------------- */
    
Route::get('/','HomeController@index');  
    
Route::post('messageSendSave', 'HomeController@messageSendStore');   
    
Route::get('changeCurrency/{currency}','HomeController@changeCurrencyPage');    
    

Route::any('searchData','HomeController@show_search_data');

Route::post('newsubscribe_save','HomeController@newsub_store');

Route::get('blog','BlogController@blogPage');
Route::get('blogShow/{slug}','BlogController@blogShowPage');
    
    
    
    
/* ---------------------------- Fornt End Login Panel Code Start ----------------------- */
    
    Route::any('userlogin','UsersController@login');
    Route::any('registration','UsersController@registration');
    /*Route::any('verify_email','UsersController@verify_email_registration');
    Route::any('resend_verify/{token}','UsersController@resend_verify');*/
    Route::any('logout','UsersController@logout');
    
    Route::any('forgotpassword','UsersController@reset_password');
    Route::any('reset_password','UsersController@change_password');
    Route::post('update_pass','UsersController@update_password_store');
    
/* ---------------------------- Fornt End Login Panel Code End ----------------------- */


/* Product Code Start */

    Route::any('product/{slug}','ProductController@productShowDetails'); 
    
    Route::any('flashproduct/{slug}','ProductController@flashproductDetails'); 
    

    Route::get('check_pincodeStatus','ProductController@pincodeStatusCheck');

     Route::any('checkProductStock','ProductController@enquiryProductStock');
    
    /* Review Code Start */
    
    Route::get('reviewDetail/{product_id}','ProductController@reviewDetailShow');
    
    /* Review Code End */


/* Product Code End */

/* ----------------------------- Filter Page Code Start ----------------------------- */

    Route::any('category/{slug}','ProductController@categories_list');
    Route::any('filter_product','ProductController@product_filter_list');
    Route::any('product/{slug}','ProductController@productShowDetails');  
    Route::any('addJSCartloginProduct','ProductController@addJSCartloginProductStore');  
    Route::any('addJSCartProduct','ProductController@addJSCartProductStore');  
    Route::any('checkProductStock','ProductController@enquiryProductStock');  
    Route::any('checkProductFilterPrice','ProductController@productFilterPrice');
    
    Route::any('checkflashsalesFilterPrice','ProductController@checkflashsalesFilterPrice');


/* ----------------------------- Filter Page Code End ----------------------------- */ 
    
    
/* ------------------------------ Product Filter Page Code Start ----------------------*/
    
    Route::any('featureProduct','ProductController@featureProductCategoriesList');
    Route::any('newArrivals','ProductController@newArrivalsProductCategoriesList');
    
/* ------------------------------ Product Filter Page Code End ----------------------*/
    
/* ----------------------------- Contact Code Start ----------------------------- */
    
    Route::any('contact','ContactController@contactList');
    Route::post('contactSave','ContactController@contactStore');
    
/* ----------------------------- Contact Code End ----------------------------- */
    

    /* ------------------------------------- cms Code End ------------------------------------- */
    
    
     Route::any('cms/{slug}','HomeController@cmspages');  
    
    /* Cart Code Start */
    
    
    Route::any('getAttributesList/{id}','CartController@getAttributesData');
   
    /* Cart Code End */
    
    
    /*Shoping cart Details*/
    Route::any('cart','CartController@cart_list');
     Route::any('update_cartSystem','CartController@updateCartSystem');
    
    Route::any('remove_cart_product/{id}','CartController@product_remove_cart');
    
    Route::any('shopping_clear','CartController@shopping_cart_clear');
    
    Route::any('updateCartSystem','CartController@updateCartSystemStore');
    
    
    /* Wish List Code Start */
    
    Route::any('wishDelete/{wishlist_id}','UsersController@wishDeleteFormat');
    

    /* About Code Start */

    Route::any('about','ContactController@aboutPage');
    
    /* Get NOTIFie */
     Route::any('getNotificationSave','ContactController@getNotificationStore');
     
    
});

Route::any('paytm-callback','CheckoutController@paytmcallback');


Route::group([ 'middleware' => ['auth_front','web_mid']], function () 
{
    
    /* Design Check Out Code Start */
     
    Route::group([ 'middleware' => ['shirt_mid']], function () {
        Route::any('goDesignCheckout','DesignShirtController@goDesignCheckoutList'); 
        Route::any('shirtCheckout','CheckoutController@shirtCheckoutStore'); 
        Route::any('clearDesign','DesignShirtController@clearDesignFormat');
    });
    
    /* Design Check Out Code End */
    
    
    
    
    
    Route::post('review_saveds','ProductController@productReviewStore');

    /* Wishlist Code Start */

   /* Route::get('product_add_fav','ProductController@product_add_Wishlist');
    Route::get('product_remove_fav','ProductController@product_remove_Wishlist');*/
    
    Route::get('product_add_fav','ProductController@product_add_Wishlist');
    Route::get('product_remove_fav','ProductController@product_remove_Wishlist');
    Route::any('wishlist','UsersController@wish_listShow'); 
    
    Route::any('addToCartSaveData/{wishlist_id}','UsersController@addToCartSaveDataStore');
    
    Route::any('wishDelete/{wishlist_id}','UsersController@wishDeleteFormat');
    
    Route::any('wishDeletes/{wishlist_id}','UsersController@wishDeletesFormat');

    /* Wishlist Code End */
    
    /* Profile Code Start */
    
    Route::get('dashboard','ProfileController@dashboardList');
    
    Route::post('passwordUpdate','ProfileController@passwordUpdateStore');
    
    /* Profile Code End */
    
    
    /* Buy Now Complete System */
    Route::any('buyNowProduct','CartController@purchaseNow');
    Route::any('buyNowFlashProduct','CartController@FlashProductPurchashNow');
    Route::any('showBuyNow','CartController@purchaseNow');
    Route::any('buyNowCoupenApply','CartController@buyNowSingleCoupenApply');
    Route::any('checkoutCoupenApply','CartController@checkoutSystemCoupenApply');
  /*  Route::any('buyNowCheckout','CheckoutController@buyNowCheckoutPayment');
    */
    
    /// Cart System 

    
  /*-------------- Shirt Controller for payment*/
    Route::any('shoppingNow','CheckoutController@shirtCheckoutStore');
      
    
    
    
    
    /*Route::any('addtocartForm','CartController@addToCartFormStore');*/ 
    
     //Address Show In Payment //
    Route::any('showShipping/{id}','UsersController@showShippingAddress');
    
    /* Cart Code End */
    
    
    
    /* Check Out Code Start */
    
    Route::any('go_checkout','CheckoutController@checkout_details');
    
    Route::any('paymentNow','CheckoutController@goToPaymentSystem');
    
    Route::any('buyNow','CheckoutController@buyNowSystem');
    
    Route::any('paypalStatus','CheckoutController@getPaypalPaymentStatus');
    
    Route::any('flashBuyNow','CheckoutController@flashBuyNowSystem');





    Route::get('processPaypal','CheckoutController@processPaypal')->name('process.paypal');
    Route::get('canclePaypal','CheckoutController@canclePaypal')->name('cancle.paypal');
    
    
    
    
    /* Check Out Code End */
    
    /* Design Check Out Code Start */
     
    Route::group([ 'middleware' => ['shirt_mid']], function () {

        Route::any('goDesignCheckout','DesignShirtController@goDesignCheckoutList'); 
        Route::any('shirtCheckout','CheckoutController@shirtCheckoutStore'); 
        Route::any('clearDesign','DesignShirtController@clearDesignFormat');
    });
    
    /* Design Check Out Code End */
    
    
    /* Shipping Address Code Start */
    
    Route::any('shippingAddress','UsersController@shippingAddress_list');
    
    Route::post('address_save','UsersController@shipping_address_order_store');
    
     Route::post('addressUpdates','UsersController@paymentUpdatesStore');
    
    Route::post('addressUpdates','UsersController@paymentUpdatesStore');
    
     Route::post('paymentUpdate','UsersController@paymentUpdatesStore');
    
    Route::any('shippingDelete/{user_information_id}','UsersController@shippingDeleteFormat');
    
    /* Shipping Address Code End */
    
    /* Billing Address Code Start */
    
    Route::any('billingAddress','UsersController@billingAddress_list');
    
    Route::post('billingaddress_save','UsersController@billing_address_order_store');
    
    Route::post('billingaddressUpdates','UsersController@billingpaymentUpdatesStore');
    
    Route::post('billingaddressUpdates','UsersController@billingpaymentUpdatesStore');
    
    Route::post('billingpaymentUpdate','UsersController@billingpaymentUpdatesStore');
    
    Route::any('billingDelete/{user_information_id}','UsersController@billingDeleteFormat');
    
    /* Billing Address Code End */
    
    /* Order and Invoice System */

    Route::group([ 'middleware' => ['shirt_mid']], function () {
        Route::any('yourOrder','ProfileController@userOrderList');
        
        
        
        Route::any('returnOrder','ProfileController@userReturnOrderList');
        Route::any('exchangeOrder','ProfileController@userExchangeOrderList');
        Route::any('cancleOrderByUserSave','ProfileController@orderUpdateByUserSave'); 
        Route::any('cancleOrder','ProfileController@userCanncleOrderList');
        Route::any('printInvoice/{order_number}','ProfileController@showcustomerInvoice');
    });

    
    /* Review Code Start */
    
    /* Review Code End */
    
    
    
    
    /* Add to Cart Code Start */ 
    
    
     Route::any('addJSWishListloginProduct','ProductController@addJSWishListloginProductStore'); 
    
    /* Add to Cart Code End  */

     
});

/* ============================== Front Admin Code End ============================== */



/* ============================== Master Admin Code Start ============================== */

Route::group(array('prefix' => 'masterAdmin','namespace'=>'masterAdmin'), function(){
   Route::group([ 'middleware' => ['guest_admin']], function () {
        Route::any('', ['as' => 'admin.root.clean', 'uses' => 'MasterAdmin@login']);
        Route::any('/', ['as' => 'admin.root', 'uses' => 'MasterAdmin@login']);
        Route::any('/login', ['as' => 'admin.login', 'uses' => 'MasterAdmin@login']);
  });
    
    Route::group([ 'middleware' => ['auth_admin','web_mid']], function () {
        Route::any('/dashboard',['as' => 'admin.dashboard', 'uses' =>'DashboardController@showDashboard']);
        Route::any('logout', ['as' => 'admin.logout', 'uses' =>'MasterAdmin@logout']);
        
        /* ------------------------------------ Slider Code Start ------------------------------ */
        
        Route::any('add_slider', ['as' => 'add_slider', 'uses' =>'SliderController@add_slide']);
        Route::get('slider_mange', ['as' => 'slider_mange', 'uses' =>'SliderController@slider_list']);
        Route::post('slider_save', ['as' => 'slider_save', 'uses' =>'SliderController@slider_saves']);
        Route::any('slider_status', ['as' => 'slider_status', 'uses' =>'SliderController@slider_status_changed']);
        Route::any('sliderDelete/{id}', ['as' => 'sliderDelete', 'uses' =>'SliderController@sliderDelete']);
        Route::any('sliderUpdate/{id}', ['as' => 'sliderUpdate', 'uses' =>'SliderController@slideredit']);
        Route::post('slider_update_save', ['as' => 'slider_update_save', 'uses' =>'SliderController@slider_edit_save']);

        Route::post('slider_status_update', ['as' => 'slider_status_update', 'uses' =>'SliderController@slider_status_update_save']);
        
        
        /* ------------------------------------ Slider Code End ------------------------------- */
        
        /* ---------------------- Advertisement Banner Code Start ------------------------------*/
        
        Route::get('offer', ['as' => 'offer', 'uses' =>'OfferController@offer_list']);
        Route::get('add_offerbanner', ['as' => 'add_offerbanner', 'uses' =>'OfferController@add_offerbanner']);
        Route::post('offerbanner_save', ['as' => 'offerbanner_save', 'uses' =>'OfferController@offerbanner_save']);
      
        Route::get('offerbanner_edit/{offerbanners_id}', ['as' => 'offerbanner_edit', 'uses'                       =>'OfferController@offerbanner_edit']);
        Route::post('offerbanner_update_save', ['as' => 'offerbanner_update_save', 'uses' =>'OfferController@offerbanner_update_save']);

        Route::any('offerbannerDelete/{offerbanners_id}', ['as' => 'offerbannerDelete', 'uses' =>'OfferController@offerbannerDelete']);
        
        
        /* -------------------------- Advertisement Banner Code End ----------------------------- */
        
        
        /* ------------------------ Attributes Code Starts --------------------------------- */
        
        Route::get('attribute', ['as' => 'attribute', 'uses' =>'AttributeController@attributeList']);
        Route::get('addAttribute', ['as' => 'addAttribute', 'uses' =>'AttributeController@addAttributePage']);
        Route::post('attributeSave', ['as' => 'attributeSave', 'uses' =>'AttributeController@attributeStore']);
        Route::get('attributeUpdate/{attribute_id}', ['as' => 'attributeUpdate', 'uses' =>'AttributeController@attributeUpdatePage']);
        Route::post('attributeEditSave', ['as' => 'attributeEditSave', 'uses' =>'AttributeController@attributeEditStore']);
        Route::get('attributeDelete/{attribute_id}', ['as' => 'attributeDelete', 'uses' =>'AttributeController@attributeDeleteformat']);

        
        /* ------------------------ Attribute Code End ------------------------------------- */
        
        /* ----------------------- Category Code Start ------------------------------------  */
        
        /* Category Level 1*/
        
        Route::get('category_list', ['as' => 'category_list', 'uses' =>'CategoryController@category_show']);
        Route::any('add_category', ['as' => 'add_category', 'uses' =>'CategoryController@add_category']);
        Route::post('category_save', ['as' => 'category_save', 'uses' =>'CategoryController@category_store']);
        Route::any('category_edit/{id}', ['as' => 'category_edit', 'uses' =>'CategoryController@category_update']);
        Route::post('category_update', ['as' => 'category_update', 'uses' =>'CategoryController@category_update_store']);
         Route::any('categoryDelete/{id}', ['as' => 'categoryDelete', 'uses' =>'CategoryController@categoryDestory']);

         Route::post('category_status_update', ['as' => 'category_status_update', 'uses' =>'CategoryController@category_status_update']);
        
        Route::post('category_show_update', ['as' => 'category_show_update', 'uses' =>'CategoryController@category_show_update_store']);
        
         /*Category Level 2*/
        Route::get('sub_category/{id}', ['as' => 'sub_category', 'uses' =>'CategoryController@sub_category_show']);
        Route::any('add_sub_category/{id}', ['as' => 'add_sub_category', 'uses' =>'CategoryController@add_sub_category']);
         Route::post('sub_category_save', ['as' => 'sub_category_save', 'uses' =>'CategoryController@sub_category_store']);
        Route::any('sub_category_edit/{id}', ['as' => 'sub_category_edit', 'uses' =>'CategoryController@sub_category_update']);
        Route::post('sub_category_update_save', ['as' => 'sub_category_update_save', 'uses' =>'CategoryController@sub_category_update_store']);

        Route::post('sub_category_status_update', ['as' => 'sub_category_status_update', 'uses' =>'CategoryController@sub_category_status_update']);

        
        /* ----------------------- Category Code End  --------------------------------------- */
        
        /* ----------------------- Product Code Start ------------------------------------- */
        
        Route::any('product_list', ['as' => 'product_list', 'uses' =>'ProductController@product_listing']);

        Route::any('filterProduct', ['as' => 'filterProduct', 'uses' =>'ProductController@filterProductPage']);

        Route::any('exportSelctedProduct', ['as' => 'exportSelctedProduct', 'uses' =>'ProductController@exportSelctedProductPage']);

        Route::any('filterProductSave', ['as' => 'filterProductSave', 'uses' =>'ProductController@filterProductStore']);

        Route::get('export', ['as' => 'export', 'uses' =>'ProductController@export']);
        
         Route::get('add_product', ['as' => 'add_product', 'uses' =>'ProductController@product_create']);
        
        Route::post('product_save', ['as' => 'product_save', 'uses' =>'ProductController@product_store']);
        
        Route::get('productUpdate/{product_id}', ['as' => 'productUpdate', 'uses' =>'ProductController@productUpdate']);
        
         Route::post('product_update_save', ['as' => 'product_update_save', 'uses' =>'ProductController@product_update_save']);
        
        Route::get('productDelete/{product_id}', ['as' => 'productDelete', 'uses' =>'ProductController@productDelete']);
        
        Route::get('productshowdetail/{product_id}', ['as' => 'productshowdetail', 'uses' =>'ProductController@productshowdetail']);
        
        Route::get('product_quantity_update/{product_id}', ['as' => 'product_quantity_update', 'uses' =>'ProductController@productQuantityUpdate']);
        
        Route::post('updateProductGSTSave', ['as' => 'updateProductGSTSave', 'uses' =>'ProductController@updateProductGSTStore']);
        
        Route::get('flaceSales/{product_id}', ['as' => 'flaceSales', 'uses' =>'ProductController@flaceSalesPage']);
        
        Route::post('flashProductSave', ['as' => 'flashProductSave', 'uses' =>'ProductController@flashProductStore']);
        
        Route::get('viewFlashProduct', ['as' => 'viewFlashProduct', 'uses' =>'ProductController@viewFlashPage']);
        
        
        
        /*Route::get('flashSales/{product_id}', ['as' => 'flashSales', 'uses' =>'ProductController@flashSalesUpdate']);*/
        
         Route::any('updateProductQuantity', ['as' => 'updateProductQuantity', 'uses' =>'ProductController@updateProductQuantityStore']);
        
        
        
        
        Route::any('product_price_update', ['as' => 'product_price_update', 'uses' =>'ProductController@product_price_update_save']);
        
        Route::any('product_isstock_update', ['as' => 'product_isstock_update', 'uses' =>'ProductController@product_price_update_save']);
        
        Route::any('product_inactive_update', ['as' => 'product_inactive_update', 'uses' =>'ProductController@product_inactive_update']);

        
        /* ----------------------- Product Code End --------------------------------------- */
        
        
        /* --------------------- Order Code Start ------------------------------------------- */
        Route::group([ 'middleware' => ['shirt_mid']], function () {
            Route::get('order_information', ['as' => 'order_information', 'uses' =>'OrderController@order_information_list']);

            Route::post('show_order_status', ['as' => 'show_order_status', 'uses' =>'OrderController@show_order_status_details']);
            
            Route::post('orderStatusExport', ['as' => 'orderStatusExport', 'uses' =>'OrderController@orderStatusExportPage']);

            Route::get('order_report', ['as' => 'order_report', 'uses' =>'OrderController@order_user_Report']);

            Route::post('remarkSave', ['as' => 'remarkSave', 'uses' =>'OrderController@remarkStore']);

            Route::any('order_status_update', ['as' => 'order_status_update', 'uses' =>'OrderController@order_status_update_save']);

            Route::any('paymet_status_update', ['as' => 'paymet_status_update', 'uses' =>'OrderController@paymet_status_update_save']);

            Route::get('orderDelete/{order_id}', ['as' => 'orderDelete', 'uses' =>'OrderController@orderDelete_format']);
            
            Route::get('orderOldMessage/{order_id}', ['as' => 'orderOldMessage', 'uses' =>'OrderController@orderOldMessagePage']);
            
            Route::get('orderOldMsgDelete/{order_old_msg_id}', ['as' => 'orderOldMsgDelete', 'uses' =>'OrderController@orderOldMsgDeleteFormat']);

            Route::get('orderShow/{order_number}', ['as' => 'orderShow', 'uses' =>'OrderController@orderShow_format']);

            Route::get('ordersDeletes/{order_id}', ['as' => 'ordersDeletes', 'uses' =>'OrderController@ordersDeletesFormat']);
        });
        /* --------------------- Order Code End ---------------------------------------------- */
        
        /* ---------------------------------- Testimonial Code Start ------------------------- */
        
        Route::get('testimonial', ['as' => 'testimonial', 'uses' =>'TestimonialController@testimonialList']);

        Route::get('add_testimonial', ['as' => 'add_testimonial', 'uses' =>'TestimonialController@add_testimonialPage']);

         Route::post('testimonial_save', ['as' => 'testimonial_save', 'uses' =>'TestimonialController@testimonialStore']);

         Route::get('testimonialUpdate/{testimonial_id}', ['as' => 'testimonialUpdate', 'uses' =>'TestimonialController@testimonialUpdatePage']);

         Route::post('testimonialEdit_save', ['as' => 'testimonialEdit_save', 'uses' =>'TestimonialController@testimonialEditUpdate']);

         Route::get('testimonialDelete/{testimonial_id}', ['as' => 'testimonialDelete', 'uses' =>'TestimonialController@testimonialDeleteFormat']);
        
        
        /* ---------------------------------- Testimonial Code End ----------------------------- */
        
        /* -------------------------- CMS Page Code Start -------------------------------------- */
        
        Route::get('cms_page', ['as' => 'cms_page', 'uses' =>'CMSController@cms_list']);
        Route::get('add_cms', ['as' => 'add_cms', 'uses' =>'CMSController@add_cms_page']);
        Route::post('cms_save', ['as' => 'cms_save', 'uses' =>'CMSController@cms_store']);
        
        Route::any('cms_edit/{id}', ['as' => 'cms_edit', 'uses' =>'CMSController@cms_update']);
        Route::post('cms_update_save', ['as' => 'cms_update_save', 'uses' =>'CMSController@cms_update_store']);
        Route::any('cmsDelete/{id}', ['as' => 'cmsDelete', 'uses' =>'CMSController@cmsdestory']);
        
        Route::any('cmsSeeMore/{cms_id}', ['as' => 'cmsSeeMore', 'uses' =>'CMSController@cmsSeeMorePage']);
        
        /* -------------------------- CMS Page Code End ----------------------------------------- */
        
        /*------------------------ Vendors Code start------------- */
        
        Route::get('vendor', ['as' => 'vendor', 'uses' =>'VendorController@vendorList']);
        Route::get('addVendor', ['as' => 'addVendor', 'uses' =>'VendorController@addVendorPage']);
        Route::post('vendorSave', ['as' => 'vendorSave', 'uses' =>'VendorController@vendorStore']);
        Route::get('vendorUpdate/{vendor_id}', ['as' => 'vendorUpdate', 'uses' =>'VendorController@vendorUpdatePage']);
        Route::post('vendorEditSave', ['as' => 'vendorEditSave', 'uses' =>'VendorController@vendorEditStore']);
        Route::get('vendorDelete/{vendor_id}', ['as' => 'vendorDelete', 'uses' =>'VendorController@vendorDeleteformat']);
        
        
        /*---------------------------vendors Code end-----------------*/
        
        /* ------------------------ Blog Code Start ---------------------------------------------*/
        
        Route::get('blog_page', ['as' => 'blog_page', 'uses' =>'BlogController@blog_list']);

         Route::get('add_blog', ['as' => 'add_blog', 'uses' =>'BlogController@add_blog_page']);

         Route::post('blog_save', ['as' => 'blog_save', 'uses' =>'BlogController@blog_store']);

         Route::get('blogUpdate/{blog_id}', ['as' => 'blogUpdate', 'uses' =>'BlogController@blogUpdatePage']);

         Route::get('comment/{blog_id}', ['as' => 'comment', 'uses' =>'BlogController@commentPage']);

         Route::post('comment_status_update', ['as' => 'comment_status_update', 'uses' =>'BlogController@comment_status_update_save']);

         Route::get('blogDelete/{blog_id}', ['as' => 'blogDelete', 'uses' =>'BlogController@blogDeleteFormat']);

         Route::post('blog_edit_save', ['as' => 'blog_edit_save', 'uses' =>'BlogController@blog_edit_Update']);
         
         Route::get('blogSeeMore/{blog_id}', ['as' => 'blogSeeMore', 'uses' =>'BlogController@blogSeeMorePage']);

        
        
        /* ------------------------ Blog Code End ----------------------------------------------*/
        
        /* ------------------------ User Code Start -------------------------------------------- */
        
        /* ------------------------- User List Code Start --------------------------------------- */
        
        Route::get('userlist', ['as' => 'userlist', 'uses' =>'UserController@user_list']);
        
        Route::any('user_update', ['as' => 'user_update', 'uses' =>'UserController@user_update_save']);
        
        Route::any('user_delete/{id}', ['as' => 'user_delete', 'uses' =>'UserController@user_data_delete']);
        
        Route::any('userAddress/{id}', ['as' => 'userAddress', 'uses' =>'UserController@userAddressPage']);
        
        /* -------------------------- User List Code Start ------------------------------------- */
        
        /* ------------------------ New Subscribe Code Start ------------------------------------ */
        
         Route::get('newsubscribers', ['as' => 'newsubscribers', 'uses' =>'UserController@newsubscribers_list']);
        Route::any('newsubscribersDelete/{id}', ['as' => 'newsubscribersDelete', 'uses' =>'UserController@newsubscribersdestory']); 
        
        /* ------------------------ New Subscribe Code End -------------------------------------- */
        
        /* ----------------------- User Code End ------------------------------------------------ */
        
        /* ----------------------- Contact Information Code Start (enquiry) --------------------- */
        
        Route::get('contact_information', ['as' => 'contact_information', 'uses' =>'UserController@contact_information_list']);

        Route::get('contactDelete/{contact_id}', ['as' => 'contactDelete', 'uses' =>'UserController@contactDelete_format']);

        Route::get('contactReplay/{contact_id}', ['as' => 'contactReplay', 'uses' =>'UserController@contactReplayPage']);

        Route::get('contactSeeMore/{contact_id}', ['as' => 'contactSeeMore', 'uses' =>'UserController@contactSeeMorePage']);

        Route::post('reloay_save', ['as' => 'reloay_save', 'uses' =>'UserController@reloay_store']);

        Route::get('mess_replay', ['as' => 'mess_replay', 'uses' =>'UserController@mess_replay_list']);

        Route::get('messageDelete/{replay_id}', ['as' => 'messageDelete', 'uses' =>'UserController@messageDelete_format']);

        /* Messgae Code Start */

        Route::get('messageSend', ['as' => 'messageSend', 'uses' =>'UserController@messageSendPage']);

        Route::get('messageSendDelete/{send_message_id}', ['as' => 'messageSendDelete', 'uses' =>'UserController@messageSendDeleteFormat']);

        /* Message Code End */
        
        /* ---------------------- Contact Information Code End (enquiry) ------------------------ */
        
        /* --------------------- Setting Code Start -------------------------------------------- */
        
        /* ------------------- Site Porfile Code Start ------------------------------------------ */
        
        Route::get('site_information', ['as' => 'site_information', 'uses' =>'SettingController@site_information_list']);

        Route::get('siteinformationUpdate', ['as' => 'siteinformationUpdate', 'uses' =>'SettingController@siteinformationUpdate_format']);

        Route::get('country_manges/{id}','SettingController@country_manges');

       Route::get('state_manges/{id}','SettingController@state_manges');

        Route::post('site_information_update', ['as' => 'site_information_update', 'uses' =>'SettingController@site_information_save_update_format']);
        
        /* -------------------------------- Site Profile Code End --------------------------- */
        
        /* ------------------------------ Mail Setting Code Start ---------------------------- */
        
        Route::get('mail_page', ['as' => 'mail_page', 'uses' =>'SettingController@mail_list']); 

        Route::get('mail_setting/{id}', ['as' => 'mail_setting', 'uses' =>'SettingController@mail_update']);
         
        Route::post('mail_setting_update', ['as' => 'mail_setting_update', 'uses' =>'SettingController@mail_update_save']);    
        Route::get('payment_settings', ['as' => 'payment_settings', 'uses' =>'SettingController@payment_settings']);
        
        /* ------------------------------ Mail Setting Code End -------------------------------- */
        
        /* ---------------------------- Pin Code Start ------------------------------------------ */
        
        Route::get('pincode', ['as' => 'pincode', 'uses' =>'SettingController@pincode_list']);
         Route::get('add_pincode', ['as' => 'add_pincode', 'uses' =>'SettingController@add_pincode']);
         Route::get('import_pincode', ['as' => 'import_pincode', 'uses' =>'SettingController@import_pincode']);
         Route::post('import_pincode_store', ['as' => 'import_pincode_store', 'uses' =>'SettingController@import_pincodeStore']);
        Route::post('pincode_save', ['as' => 'pincode_save', 'uses' =>'SettingController@pincode_store']);

         Route::get('pinUpdate/{pincode_id}', ['as' => 'pinUpdate', 'uses' =>'SettingController@pinUpdate']);

         Route::post('pincode_update_save', ['as' => 'pincode_update_save', 'uses' =>'SettingController@pincode_update_save']);

         Route::any('pinDelete/{pincode_id}', ['as' => 'pinDelete', 'uses' =>'SettingController@pinDelete']);
        
        /* ---------------------------- Pin Code End -------------------------------------------- */
        
        /* ---------------------------- Password Update Code Start ---------------------------- */
        
        Route::any('change_admin_panel', ['as' => 'change_admin_panel', 'uses' =>'SettingController@change_admin_panel_list']);

        Route::any('passwordUpdate/{id}', ['as' => 'passwordUpdate', 'uses' =>'SettingController@chanegpasswordUpdate']);

        Route::any('change_password_update', ['as' => 'change_password_update', 'uses' =>'SettingController@change_password_update_save']);
        
        
        /* ---------------------------- Password Updae Code End -------------------------------- */
        
        /* --------------------Setting Code End ------------------------------------------------ */
        
        
        /* ------------------------------------- Main Video Code Start --------------------------- */
        
         Route::get('mainVideo', ['as' => 'mainVideo', 'uses' =>'VideoController@mainVideoList']);
         Route::get('mainVideoUpdate', ['as' => 'mainVideoUpdate', 'uses' =>'VideoController@mainVideoUpdatePage']);
         Route::get('mainvideoUpdateSave', ['as' => 'mainvideoUpdateSaveRedirect', 'uses' =>'VideoController@mainvideoUpdateSaveRedirect']);
         Route::post('mainvideoUpdateSave', ['as' => 'mainvideoUpdateSave', 'uses' =>'VideoController@mainvideoUpdateStore']);
        
        /* ------------------------------------ Main Video Code End ------------------------------ */
        
        /* ------------------------------------- Sub Video Code Start --------------------------- */
        
         Route::get('subVideo', ['as' => 'subVideo', 'uses' =>'VideoController@subVideoList']);
        
        Route::get('subVideoUpdate', ['as' => 'subVideoUpdate', 'uses' =>'VideoController@subVideoUpdatePage']);
        
        Route::get('subvideoUpdateSave', ['as' => 'subvideoUpdateSaveRedirect', 'uses' =>'VideoController@subvideoUpdateSaveRedirect']);
        Route::post('subvideoUpdateSave', ['as' => 'subvideoUpdateSave', 'uses' =>'VideoController@subvideoUpdateStore']);
        
        /* ------------------------------------ Sub Video Code End ------------------------------ */
        
        
         /* ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- Shirt Design Code Start ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
        
        
        /* ------------------------------------ Shirt Design Code Start ------------------------------ */
        
         Route::get('shirtCategory', ['as' => 'shirtCategory', 'uses' =>'DesignController@shirtCategoryList']);
        
         Route::get('addShirtDesign', ['as' => 'addShirtDesign', 'uses' =>'DesignController@addShirtDesignPage']);
        
         Route::post('shirtCategorySave', ['as' => 'shirtCategorySave', 'uses' =>'DesignController@shirtCategoryStore']);
        
         Route::get('shirtCategoryUpdate/{febric_id}', ['as' => 'shirtCategoryUpdate', 'uses' =>'DesignController@shirtCategoryUpdatePage']);
        
         Route::post('shirtCategoryEditSave', ['as' => 'shirtCategoryEditSave', 'uses' =>'DesignController@shirtCategoryEditStore']);
        
         Route::get('shirtCategoryDelete/{febric_id}', ['as' => 'shirtCategoryDelete', 'uses' =>'DesignController@shirtCategoryDeleteFormat']);
        
        
            
        /* ------------------------------------ Shirt Design Code End ------------------------------ */
        
        /* ----------------------------------- Shirt Attribut Code Start -----------------------------*/
        
        Route::get('shirtAttribut', ['as' => 'shirtAttribut', 'uses' =>'DesignController@shirtAttributList']);
        
        Route::get('addShirtAttribut', ['as' => 'addShirtAttribut', 'uses' =>'DesignController@addShirtAttributPage']);
        
        Route::post('shirtAttributSave', ['as' => 'shirtAttributSave', 'uses' =>'DesignController@shirtAttributStore']);
        
        Route::get('shirtAttributUpdate/{element_id}', ['as' => 'shirtAttributUpdate', 'uses' =>'DesignController@shirtAttributUpdatePage']);
        
        Route::post('shirtAttributEditSave', ['as' => 'shirtAttributEditSave', 'uses' =>'DesignController@shirtAttributEditStore']);
        
        
        Route::get('shirtAttributDelete/{element_id}', ['as' => 'shirtAttributDelete', 'uses' =>'DesignController@shirtAttributDeleteFormat']);
        
        
        /* ----------------------------------- Shirt Attribut Code End -----------------------------*/
        
        
        /* -----------------------------------  Attribut Value Code End -----------------------------*/
        
        Route::get('attributValue', ['as' => 'attributValue', 'uses' =>'DesignController@attributValueList']);
        
        Route::get('addAttributValue', ['as' => 'addAttributValue', 'uses' =>'DesignController@addAttributValuePage']);
        
        Route::post('attributValueSave', ['as' => 'attributValueSave', 'uses' =>'DesignController@attributValueStore']);
        
        Route::get('attributValueUpdate/{element_value_id}', ['as' => 'attributValueUpdate', 'uses' =>'DesignController@attributValueUpdatePage']);
        
        Route::post('attributValueEditSave', ['as' => 'attributValueEditSave', 'uses' =>'DesignController@attributValueEditStore']);
        
        Route::get('attributValueDelete/{element_value_id}', ['as' => 'attributValueDelete', 'uses' =>'DesignController@attributValueDeleteFormat']);
        
        /* -----------------------------------  Attribut Value Code End -----------------------------*/
        
        Route::get('shirtSize', ['as' => 'shirtSize', 'uses' =>'DesignController@shirtSizeList']);
        
        Route::get('addShirtSize', ['as' => 'addShirtSize', 'uses' =>'DesignController@addShirtSizePage']);
        
        Route::post('shirtSizeSave', ['as' => 'shirtSizeSave', 'uses' =>'DesignController@shirtSizeStore']);
        
        Route::get('shirtSizeUpdate/{shirt_size_id}', ['as' => 'shirtSizeUpdate', 'uses' =>'DesignController@shirtSizeUpdatePage']);
        
        Route::post('shirtSizeEditSave', ['as' => 'shirtSizeEditSave', 'uses' =>'DesignController@shirtSizeEditStore']);
        
        Route::get('shirtSizeDelete/{shirt_size_id}', ['as' => 'shirtSizeDelete', 'uses' =>'DesignController@shirtSizeDeleteFormat']);
        
        
        /* ------------------------- Shirt Size Design Code Start ------------------------- */
        
        
        
        /* ------------------------- Shirt Size Design Code End ------------------------- */
        
        
        
        /* ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- Shirt Design Code End ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
        
        
        
        /* ----------------------------------- Coupen Code Start ------------------------------- */
        
        Route::get('productCoupon', ['as' => 'productCoupon', 'uses' =>'CouponController@productCouponList']);
        
        Route::post('productCouponStatusAllUpdate', ['as' => 'productCouponStatusAllUpdate', 'uses' =>'CouponController@productCouponStatusAllUpdateStore']);
        
        Route::get('addProductCoupon', ['as' => 'addProductCoupon', 'uses' =>'CouponController@addProductCouponpage']);
        
        Route::post('productCouponSave', ['as' => 'productCouponSave', 'uses' =>'CouponController@productCouponStore']);
        
        Route::get('productCouponUpdate/{product_coupon_id}', ['as' => 'productCouponUpdate', 'uses' =>'CouponController@productCouponUpdatepage']);
        
        Route::post('productCouponEditSave', ['as' => 'productCouponEditSave', 'uses' =>'CouponController@productCouponEditStore']);
        
        Route::get('productCouponDelete/{product_coupon_id}', ['as' => 'productCouponDelete', 'uses' =>'CouponController@productCouponDeleteFormat']);
        
        Route::any('productCouponStatusUpdate', ['as' => 'productCouponStatusUpdate', 'uses' =>'CouponController@productCouponStatusUpdateSave']);
        
        /* ----------------------------------- Coupen Code End ------------------------------- */
        
        
        /* -------------------------------- Customer Coupon Code Start ---------------------------*/
        
        Route::get('customerCoupon', ['as' => 'customerCoupon', 'uses' =>'CouponController@customerCouponList']);
        
        Route::post('customerCouponStatusAllUpdate', ['as' => 'customerCouponStatusAllUpdate', 'uses' =>'CouponController@customerCouponStatusAllUpdateStore']);
        
        Route::get('addCustomerCoupon', ['as' => 'addCustomerCoupon', 'uses' =>'CouponController@addCustomerCouponPage']);
        
        Route::post('customerCouponSave', ['as' => 'customerCouponSave', 'uses' =>'CouponController@customerCouponStore']);
        
        Route::get('customerCouponUpdate/{customer_coupon_id}', ['as' => 'customerCouponUpdate', 'uses' =>'CouponController@customerCouponUpdatePage']);
        
        Route::post('customerCouponEditSave', ['as' => 'customerCouponEditSave', 'uses' =>'CouponController@customerCouponEditStore']);
        
        Route::get('customerCouponDelete/{customer_coupon_id}', ['as' => 'customerCouponDelete', 'uses' =>'CouponController@customerCouponDeleteFormat']);
        
        Route::any('customerCouponStatusUpdate', ['as' => 'customerCouponStatusUpdate', 'uses' =>'CouponController@customerCouponStatusUpdateSave']);
        
        
        /* -------------------------------- Customer Coupon Code Emd ---------------------------*/
        
        /* -------------------------------- Price Coupon Code Start ---------------------------*/
        
        Route::get('pricesCoupon', ['as' => 'pricesCoupon', 'uses' =>'CouponController@pricesCouponList']);
        
        Route::post('priceCouponStatusAllUpdate', ['as' => 'priceCouponStatusAllUpdate', 'uses' =>'CouponController@priceCouponStatusAllUpdateStore']);
        
        Route::get('addpricesCoupon', ['as' => 'addpricesCoupon', 'uses' =>'CouponController@addpricesCouponPage']);
        
        Route::post('pricesCouponSave', ['as' => 'pricesCouponSave', 'uses' =>'CouponController@pricesCouponStore']);
        
        Route::get('pricesCouponUpdate/{price_coupon_id}', ['as' => 'pricesCouponUpdate', 'uses' =>'CouponController@pricesCouponUpdatePage']);
        
        Route::post('pricesCouponEditSave', ['as' => 'pricesCouponEditSave', 'uses' =>'CouponController@pricesCouponEditStore']);
        
        Route::get('pricesCouponDelete/{price_coupon_id}', ['as' => 'pricesCouponDelete', 'uses' =>'CouponController@pricesCouponDeleteFormat']);
        
        Route::any('pricesCouponStatusUpdate', ['as' => 'pricesCouponStatusUpdate', 'uses' =>'CouponController@pricesCouponStatusUpdateSave']);
        
        
        /* -------------------------------- Price Coupon Code Emd ---------------------------*/
        
        
        
        
        
        /* ------------------------ New Subscribe Code Start ------------------------------------ */
        
         Route::get('newsubscribers', ['as' => 'newsubscribers', 'uses' =>'UserController@newsubscribers_list']);
        Route::any('newsubscribersDelete/{subscribe_id}', ['as' => 'newsubscribersDelete', 'uses' =>'UserController@newsubscribersdestory']); 
        
        /* ------------------------ New Subscribe Code End -------------------------------------- */
        
        
        /* Review Code Start */
        
        Route::any('review_information', ['as' => 'review_information', 'uses' =>'ProductController@review_information_list']);
        
        Route::any('addReviewInformation', ['as' => 'addReviewInformation', 'uses' =>'ProductController@addReviewInformationPage']);
        
        Route::any('reviewInformationSave', ['as' => 'reviewInformationSave', 'uses' =>'ProductController@reviewInformationStore']);
        
        Route::any('reviewInformationUpdate/{review_id}', ['as' => 'reviewInformationUpdate', 'uses' =>'ProductController@reviewInformationUpdatePage']);
        
        Route::any('reviewInformationEditSave', ['as' => 'reviewInformationEditSave', 'uses' =>'ProductController@reviewInformationEditStore']);

        Route::any('reviewDelete/{review_id}', ['as' => 'reviewDelete', 'uses' =>'ProductController@reviewDelete']);

        Route::post('review_status_update', ['as' => 'review_status_update', 'uses' =>'ProductController@review_status_update_save']);

        
        /* Review Code End */
        
        
        
        /* Banner Code Start */
        
        Route::any('banner', ['as' => 'banner', 'uses' =>'BannerController@bannerPage']);
        Route::any('bannerUpdate/{banner_id}', ['as' => 'bannerUpdate', 'uses' =>'BannerController@bannerUpdate']);
        Route::any('bannerEditSave', ['as' => 'bannerEditSave', 'uses' =>'BannerController@bannerEditStore']);
        
        /* Banner Code End */
        
        /* Flash Banner Code Start */
        
        Route::any('flashBanner', ['as' => 'flashBanner', 'uses' =>'BannerController@flashBannerList']);
        Route::any('flashBannerUpdate/{flash_banner_id}', ['as' => 'flashBannerUpdate', 'uses' =>'BannerController@flashBannerUpdate']);
        Route::post('flashBannerEditSave', ['as' => 'flashBannerEditSave', 'uses' =>'BannerController@flashBannerEditStore']);
        
        /* Flash Banner Code End */


        /* Abouts Code Start */

        Route::any('about', ['as' => 'about', 'uses' =>'AboutController@aboutPage']);
        Route::any('addAbout', ['as' => 'addAbout', 'uses' =>'AboutController@addAboutPage']);
        Route::any('aboutSave', ['as' => 'aboutSave', 'uses' =>'AboutController@aboutStore']);
        Route::any('aboutUpdate/{about_id}', ['as' => 'aboutUpdate', 'uses' =>'AboutController@aboutUpdatePage']);
        Route::any('aboutEditSave', ['as' => 'aboutEditSave', 'uses' =>'AboutController@aboutEditStore']);
        Route::any('aboutDelete/{about_id}', ['as' => 'aboutDelete', 'uses' =>'AboutController@aboutDeleteFormat']);

        /* Abouts Code End */
        
        
        /* Logi Banner Code Start */
        
        Route::any('loginBanner', ['as' => 'loginBanner', 'uses' =>'BannerController@loginBannerList']);
        Route::any('loginBannerUpdate/{login_banner_id}', ['as' => 'loginBannerUpdate', 'uses' =>'BannerController@loginBannerUpdatePage']);
        Route::post('loginBannerEditSave', ['as' => 'loginBannerEditSave', 'uses' =>'BannerController@loginBannerEditStore']);
        
        /* Logi Banner Code End */
        
        
        
        /* Get Notification Code Start */
        
        Route::any('getNotification', ['as' => 'getNotification', 'uses' =>'BannerController@getNotificationList']);
        
        Route::any('getNotificationDelete/{notifi_id}', ['as' => 'getNotificationDelete', 'uses' =>'BannerController@getNotificationDeleteFormat']);
        
        /* Get NOticication Code End */
        
        /*Customer Shirt Code Start */
        
        Route::any('customerShirt', ['as' => 'customerShirt', 'uses' =>'BannerController@customerShirtList']);
        
        Route::any('customerShirtUpdate/{customer_shirt_id}', ['as' => 'customerShirtUpdate', 'uses' =>'BannerController@customerShirtUpdatePage']);
        
        Route::post('customerShirtEditSave', ['as' => 'customerShirtEditSave', 'uses' =>'BannerController@customerShirtEditStore']);
    
        
        /*Customer Shirt Code End */
        
        /* Currency Code Start */

        Route::any('currency', ['as' => 'currency', 'uses' =>'CurrencyController@currencyPage']);

        Route::any('addCurrency', ['as' => 'addCurrency', 'uses' =>'CurrencyController@addCurrencyPage']);

        Route::post('currencySave', ['as' => 'currencySave', 'uses' =>'CurrencyController@currencyStore']);

        Route::any('currencyUpdate/{currency_id}', ['as' => 'currencyUpdate', 'uses' =>'CurrencyController@currencyUpdatePage']);

        Route::post('currencyEditSave', ['as' => 'currencyEditSave', 'uses' =>'CurrencyController@currencyEditStore']);

        Route::any('currencyDelete/{currency_id}', ['as' => 'currencyDelete', 'uses' =>'CurrencyController@currencyDeleteFormat']);


        /* Currency Code End */
        
        /* ================= Recycle Bin Routes ================= */
        Route::get('recycleBin', ['as' => 'recycleBin', 'uses' => 'RecycleBinController@index']);
        Route::get('restoreItem/{type}/{id}', ['as' => 'restoreItem', 'uses' => 'RecycleBinController@restore']);
        Route::get('permanentDelete/{type}/{id}', ['as' => 'permanentDelete', 'uses' => 'RecycleBinController@permanentDelete']);
        Route::post('recycleBulk', ['as' => 'recycleBulk', 'uses' => 'RecycleBinController@bulk']);
        Route::get('recycleCleanup', ['as' => 'recycleCleanupPage', 'uses' => 'RecycleBinController@cleanupPage']);
        Route::post('recycleCleanup', ['as' => 'recycleCleanup', 'uses' => 'RecycleBinController@cleanup']);
        Route::get('videoDelete/{video_id}', ['as' => 'videoDelete', 'uses' => 'VideoController@deleteVideo']);
        /* ================= Recycle Bin Routes ================= */
        
    });
});
/* ============================== Master Admin Code End =============================== */
