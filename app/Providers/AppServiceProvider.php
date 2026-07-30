<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $mediaHelpers = app_path('Support/media.php');
        if (file_exists($mediaHelpers)) {
            require_once $mediaHelpers;
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Define constants from siteinfos table
        try {
            $check_data = DB::table('siteinfos')->where('siteinfo_id', 1)->get();
            foreach ($check_data as $data) {
                if (!defined('PHONE_NUMBNER')) define('PHONE_NUMBNER', $data->phone_number);
                if (!defined('WHATSAPP_NUMBER')) define('WHATSAPP_NUMBER', isset($data->whatsapp_number) && $data->whatsapp_number ? $data->whatsapp_number : $data->phone_number);
                if (!defined('FB_LINK')) define('FB_LINK', $data->facebook_url);
                if (!defined('LINKEDIN_LINK')) define('LINKEDIN_LINK', $data->linkedin_url);
                if (!defined('INSTA_LINK')) define('INSTA_LINK', $data->instagram_url);
                if (!defined('META_DESCIPTION')) define('META_DESCIPTION', $data->meta_description);
                if (!defined('META_KEYWORD')) define('META_KEYWORD', $data->meta_keyword);
                if (!defined('META_TITLE')) define('META_TITLE', $data->meta_title);
                if (!defined('META_EMAIL')) define('META_EMAIL', $data->meta_email);
                if (!defined('ADDRESS')) define('ADDRESS', $data->address);
                if (!defined('SITEPROFILE')) define('SITEPROFILE', $data->site_profile);
                if (!defined('RUPEESHIPPINCHARGE')) define('RUPEESHIPPINCHARGE', $data->rupee_shipping_charge);
                if (!defined('DOLLARSHIPPINCHARGE')) define('DOLLARSHIPPINCHARGE', $data->dollar_shipping_charge);
                if (!defined('EUROSHIPPINCHARGE')) define('EUROSHIPPINCHARGE', $data->euro_shipping_charge);
                if (!defined('MINIMUMKG')) define('MINIMUMKG', $data->minimum_charge);
                if (!defined('RECYCLE_CLEANUP_DAYS')) define('RECYCLE_CLEANUP_DAYS', isset($data->recycle_cleanup_days) ? $data->recycle_cleanup_days : 90);
                if (!defined('WEBSITE_URL')) define('WEBSITE_URL', "https://zouple.in");
            }
        } catch (\Exception $e) {
            // Database not available yet, define defaults
        }
        
        if (!defined('PAGETITLE')) define('PAGETITLE', "The Zouple");

        // Register HeaderComposer for header view
        View::composer(
            'front.layout.header',
            \App\Http\View\Composers\HeaderComposer::class
        );
    }
}
