@php
    try {
        $recycleCount = \App\Services\AdminRecycleBinService::count();
    } catch (\Exception $e) {
        $recycleCount = 0;
    }
    $currentRoute = \Route::currentRouteName();
    $isActive = function ($routes) use ($currentRoute) {
        return in_array($currentRoute, (array) $routes) ? 'active' : '';
    };
    $isExpanded = function ($routes) use ($currentRoute) {
        return in_array($currentRoute, (array) $routes) ? 'is-expanded' : '';
    };
    $adminUser = Auth::guard('admin')->user();
@endphp
<header class="app-header">
    <a class="app-header__logo" href="{{ route('admin.dashboard') }}">Zouple</a>
    <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
    <ul class="app-nav">
        <li class="app-search">
            <span class="app-search__input">{{ $adminUser->name ?? 'Admin' }}</span>
        </li>
        <li>
            <a class="app-nav__item" href="{{ route('admin.logout') }}"><i class="fa fa-sign-out fa-lg"></i></a>
        </li>
    </ul>
</header>

<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="app-sidebar__user">
        <img class="app-sidebar__user-avatar" src="{{ URL::asset('public/img/dark-sm-logo.png') }}" width="48" height="48" alt="Zouple">
        <div>
            <p class="app-sidebar__user-name">{{ $adminUser->name ?? 'Zouple Admin' }}</p>
            <p class="app-sidebar__user-designation">Administrator</p>
        </div>
    </div>
    <ul class="app-menu">
        <li>
            <a class="app-menu__item {{ $isActive('admin.dashboard') }}" href="{{ route('admin.dashboard') }}">
                <i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Dashboard</span>
            </a>
        </li>

        <li class="treeview {{ $isExpanded(['product_list','add_product','productUpdate','filterProduct','viewFlashProduct','review_information','reviewInformationUpdate']) }}">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fa fa-shopping-bag"></i><span class="app-menu__label">Products</span><i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a class="treeview-item {{ $isActive(['product_list','productUpdate']) }}" href="{{ route('product_list') }}"><i class="icon fa fa-circle-o"></i> Product List</a></li>
                <li><a class="treeview-item {{ $isActive('add_product') }}" href="{{ route('add_product') }}"><i class="icon fa fa-circle-o"></i> Add Product</a></li>
                <li><a class="treeview-item {{ $isActive('filterProduct') }}" href="{{ route('filterProduct') }}"><i class="icon fa fa-circle-o"></i> Product Filter</a></li>
                <li><a class="treeview-item {{ $isActive('viewFlashProduct') }}" href="{{ route('viewFlashProduct') }}"><i class="icon fa fa-circle-o"></i> Flash Sale</a></li>
                <li><a class="treeview-item {{ $isActive(['review_information','reviewInformationUpdate']) }}" href="{{ route('review_information') }}"><i class="icon fa fa-circle-o"></i> Reviews</a></li>
            </ul>
        </li>

        <li class="treeview {{ $isExpanded(['category_list','category_edit','sub_category','sub_category_edit','attribute','attributeUpdate']) }}">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fa fa-sitemap"></i><span class="app-menu__label">Catalogue</span><i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a class="treeview-item {{ $isActive(['category_list','category_edit','sub_category','sub_category_edit']) }}" href="{{ route('category_list') }}"><i class="icon fa fa-circle-o"></i> Categories</a></li>
                <li><a class="treeview-item {{ $isActive(['attribute','attributeUpdate']) }}" href="{{ route('attribute') }}"><i class="icon fa fa-circle-o"></i> Attributes</a></li>
            </ul>
        </li>

        <li class="treeview {{ $isExpanded(['order_information','order_report']) }}">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fa fa-shopping-cart"></i><span class="app-menu__label">Orders</span><i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a class="treeview-item {{ $isActive('order_information') }}" href="{{ route('order_information') }}"><i class="icon fa fa-circle-o"></i> Order List</a></li>
                <li><a class="treeview-item {{ $isActive('order_report') }}" href="{{ route('order_report') }}"><i class="icon fa fa-circle-o"></i> Order Report</a></li>
            </ul>
        </li>

        <li class="treeview {{ $isExpanded(['userlist','newsubscribers','contact_information','mess_replay','getNotification']) }}">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fa fa-users"></i><span class="app-menu__label">Customers & Forms</span><i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a class="treeview-item {{ $isActive('userlist') }}" href="{{ route('userlist') }}"><i class="icon fa fa-circle-o"></i> Customers</a></li>
                <li><a class="treeview-item {{ $isActive('newsubscribers') }}" href="{{ route('newsubscribers') }}"><i class="icon fa fa-circle-o"></i> Subscribers</a></li>
                <li><a class="treeview-item {{ $isActive('contact_information') }}" href="{{ route('contact_information') }}"><i class="icon fa fa-circle-o"></i> Contact Enquiries</a></li>
                <li><a class="treeview-item {{ $isActive('mess_replay') }}" href="{{ route('mess_replay') }}"><i class="icon fa fa-circle-o"></i> Enquiry Replies</a></li>
                <li><a class="treeview-item {{ $isActive('getNotification') }}" href="{{ route('getNotification') }}"><i class="icon fa fa-circle-o"></i> Notifications</a></li>
            </ul>
        </li>

        <li class="treeview {{ $isExpanded(['slider_mange','sliderUpdate','banner','bannerUpdate','flashBanner','flashBannerUpdate','offer','offerbanner_edit','loginBanner','loginBannerUpdate','mainVideo','mainVideoUpdate','subVideo','subVideoUpdate']) }}">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fa fa-picture-o"></i><span class="app-menu__label">Media & Banners</span><i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a class="treeview-item {{ $isActive(['slider_mange','sliderUpdate']) }}" href="{{ route('slider_mange') }}"><i class="icon fa fa-circle-o"></i> Home Sliders</a></li>
                <li><a class="treeview-item {{ $isActive(['banner','bannerUpdate']) }}" href="{{ route('banner') }}"><i class="icon fa fa-circle-o"></i> Banners</a></li>
                <li><a class="treeview-item {{ $isActive(['flashBanner','flashBannerUpdate']) }}" href="{{ route('flashBanner') }}"><i class="icon fa fa-circle-o"></i> Flash Banner</a></li>
                <li><a class="treeview-item {{ $isActive(['offer','offerbanner_edit']) }}" href="{{ route('offer') }}"><i class="icon fa fa-circle-o"></i> Offer Banners</a></li>
                <li><a class="treeview-item {{ $isActive(['loginBanner','loginBannerUpdate']) }}" href="{{ route('loginBanner') }}"><i class="icon fa fa-circle-o"></i> Login Banners</a></li>
                <li><a class="treeview-item {{ $isActive(['mainVideo','mainVideoUpdate']) }}" href="{{ route('mainVideo') }}"><i class="icon fa fa-circle-o"></i> Main Video</a></li>
                <li><a class="treeview-item {{ $isActive(['subVideo','subVideoUpdate']) }}" href="{{ route('subVideo') }}"><i class="icon fa fa-circle-o"></i> Sub Video</a></li>
            </ul>
        </li>

        <li class="treeview {{ $isExpanded(['about','cms_page','blog_page','testimonial','testimonialUpdate','vendor','currency']) }}">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fa fa-file-text"></i><span class="app-menu__label">Content</span><i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a class="treeview-item {{ $isActive('about') }}" href="{{ route('about') }}"><i class="icon fa fa-circle-o"></i> About Us</a></li>
                <li><a class="treeview-item {{ $isActive('cms_page') }}" href="{{ route('cms_page') }}"><i class="icon fa fa-circle-o"></i> CMS Pages</a></li>
                <li><a class="treeview-item {{ $isActive('blog_page') }}" href="{{ route('blog_page') }}"><i class="icon fa fa-circle-o"></i> Blogs</a></li>
                <li><a class="treeview-item {{ $isActive(['testimonial','testimonialUpdate']) }}" href="{{ route('testimonial') }}"><i class="icon fa fa-circle-o"></i> Testimonials</a></li>
                <li><a class="treeview-item {{ $isActive('vendor') }}" href="{{ route('vendor') }}"><i class="icon fa fa-circle-o"></i> Vendors</a></li>
                <li><a class="treeview-item {{ $isActive('currency') }}" href="{{ route('currency') }}"><i class="icon fa fa-circle-o"></i> Currency</a></li>
            </ul>
        </li>

        <li class="treeview {{ $isExpanded(['productCoupon','customerCoupon','pricesCoupon']) }}">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fa fa-gift"></i><span class="app-menu__label">Coupons</span><i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a class="treeview-item {{ $isActive('productCoupon') }}" href="{{ route('productCoupon') }}"><i class="icon fa fa-circle-o"></i> Product Coupon</a></li>
                <li><a class="treeview-item {{ $isActive('customerCoupon') }}" href="{{ route('customerCoupon') }}"><i class="icon fa fa-circle-o"></i> Customer Coupon</a></li>
                <li><a class="treeview-item {{ $isActive('pricesCoupon') }}" href="{{ route('pricesCoupon') }}"><i class="icon fa fa-circle-o"></i> Price Coupon</a></li>
            </ul>
        </li>

        <li class="treeview {{ $isExpanded(['shirtCategory','shirtAttribut','attributValue','shirtSize','customerShirt']) }}">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fa fa-shield"></i><span class="app-menu__label">Custom Item</span><i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a class="treeview-item {{ $isActive('shirtCategory') }}" href="{{ route('shirtCategory') }}"><i class="icon fa fa-circle-o"></i> material</a></li>
                <li><a class="treeview-item {{ $isActive('shirtAttribut') }}" href="{{ route('shirtAttribut') }}"><i class="icon fa fa-circle-o"></i> Pre Design</a></li>
                <li><a class="treeview-item {{ $isActive('attributValue') }}" href="{{ route('attributValue') }}"><i class="icon fa fa-circle-o"></i>Size</a></li>
                <li><a class="treeview-item {{ $isActive('shirtSize') }}" href="{{ route('shirtSize') }}"><i class="icon fa fa-circle-o"></i>Text</a></li>
                <li><a class="treeview-item {{ $isActive('customerShirt') }}" href="{{ route('customerShirt') }}"><i class="icon fa fa-circle-o"></i> Home Customize Item</a></li>
            </ul>
        </li>

        <li class="treeview {{ $isExpanded(['site_information','siteinformationUpdate','mail_page','payment_settings','pincode','import_pincode','change_admin_panel','recycleCleanup']) }}">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fa fa-cog"></i><span class="app-menu__label">Settings</span><i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a class="treeview-item {{ $isActive('site_information') }}" href="{{ route('site_information') }}"><i class="icon fa fa-circle-o"></i> Site Information</a></li>
                <li><a class="treeview-item {{ $isActive('siteinformationUpdate') }}" href="{{ route('siteinformationUpdate') }}"><i class="icon fa fa-circle-o"></i> Bulk WhatsApp</a></li>
                <li><a class="treeview-item {{ $isActive('mail_page') }}" href="{{ route('mail_page') }}"><i class="icon fa fa-circle-o"></i> Mail Settings</a></li>
                <li><a class="treeview-item {{ $isActive('payment_settings') }}" href="{{ route('payment_settings') }}"><i class="icon fa fa-circle-o"></i> Payment Gateway</a></li>
                <li><a class="treeview-item {{ $isActive('pincode') }}" href="{{ route('pincode') }}"><i class="icon fa fa-circle-o"></i> Pincodes</a></li>
                <li><a class="treeview-item {{ $isActive('import_pincode') }}" href="{{ route('import_pincode') }}"><i class="icon fa fa-circle-o"></i> Import Pincodes</a></li>
                <li><a class="treeview-item {{ $isActive('change_admin_panel') }}" href="{{ route('change_admin_panel') }}"><i class="icon fa fa-circle-o"></i> Admin Passwords</a></li>
                <li><a class="treeview-item {{ $isActive('recycleCleanup') }}" href="{{ route('recycleCleanup') }}"><i class="icon fa fa-circle-o"></i> Cleanup Recycle Bin</a></li>
            </ul>
        </li>

        <li>
            <a class="app-menu__item {{ $isActive('recycleBin') }}" href="{{ route('recycleBin') }}">
                <i class="app-menu__icon fa fa-recycle"></i>
                <span class="app-menu__label">Recycle Bin</span>
                @if($recycleCount > 0)
                    <span class="badge badge-danger ml-auto">{{ $recycleCount }}</span>
                @endif
            </a>
        </li>
    </ul>
</aside>
