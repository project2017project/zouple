<?php

namespace App\Helpers;

use Html;
use File;
use App\Siteinfo;
use DB;

/*define('WEBSITE_URL', 'http://earthlyplush.com/new_earthly');*/
define('WEBSITE_URL', 'http://zouple.in/');
define('DS', DIRECTORY_SEPARATOR);
define('PAGETITLE', 'The Zouple');

/*define('PRODUCT_IMG_URL', 'http://earthlyplush.com/test_earthly/public/upload/product/');
define('PUBLIC_IMG_URL', 'http://earthlyplush.com/test_earthly/public/upload/');*/


function get_pro_name($id)
{
    $pro_name = DB::table('products')->where('product_id',$id)->value('prodcut_title');
    return $pro_name; 
}


?>
