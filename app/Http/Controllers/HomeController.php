<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Slider;
use App\Category;
use App\Offerbanner;
use Auth,Redirect,View,File,Config,Image;
use Validator;
use DB;
use App\Product;

use App\ProductQty;
use Session;
use Mail;
use App\Helper\BasicHelper;
use Illuminate\Support\Facades\Schema;
use App\Services\AdminRecycleBinService;


class HomeController extends Controller
{
   
   public function __construct()
    {
        // Constructor is intentionally left clean.
        // Currency detection is handled per-request in middleware.
    }
    public function index(Request $request)
    {
        try {
       
       $is_flash = "INACTIVE";
        $amt = 0;
        
        $count_down = date('M d, Y 00:00:00');
        
       $data['cate_data'] = Schema::hasTable('categorys')
           ? AdminRecycleBinService::activeTable('categorys')->where('is_show',"SHOW")->get()
           : collect([]);
        
       $siteInfo = Schema::hasTable('siteinfos') ? DB::table('siteinfos')->where('siteinfo_id', 1)->first() : null;
       $whatsappNumber = $siteInfo && !empty($siteInfo->whatsapp_number) ? $siteInfo->whatsapp_number : ($siteInfo->phone_number ?? '');
       $data['bulk_whatsapp_link'] = 'https://wa.me/' . preg_replace('/\D+/', '', $whatsappNumber) . '?text=' . rawurlencode('Hello Zouple, I want to enquire about bulk order.');
       $data['blog_data'] = collect([]);
        
       $data['main_video'] = Schema::hasTable('video') ? AdminRecycleBinService::activeTable('video')->get() : collect([]);
        
       

       $data['slider_data'] = Schema::hasTable('sliders') ? AdminRecycleBinService::activeTable('sliders')->where('is_active', 'ACTIVE')->get() : collect([]);
        
       $data['banner_data'] = Schema::hasTable('offerbanners') ? AdminRecycleBinService::activeTable('offerbanners')->orderby('offerbanners_id', 'asc')->get() : collect([]);
        
      $data['featured_products'] = $this->homepageProductQuery()
            ->where('products.featured_product','YES')
            ->orderBy('products.product_id', 'ASC')->take(12)->get();
       
        
        $data['new_arrivals'] = $this->homepageProductQuery()
            ->where('products.new_arrivals','YES')
            ->orderBy('products.product_id', 'ASC')
            ->take(12)->get();
        
       /*return  $data['new_arrivals'] ;*/
        
        $data['view_flash_data'] = collect([]);
        if (Schema::hasTable('flash_sale') && Schema::hasTable('products')) {
            $flashQuery = DB::table('flash_sale')
                ->join('products', 'products.product_id', '=', 'flash_sale.product_id')
                ->where('flash_sale.flash_active', 'ACTIVE');
            AdminRecycleBinService::withoutDeleted($flashQuery, 'products');
            $data['view_flash_data'] = $flashQuery->get();
        }
        
        foreach($data['view_flash_data'] as $dt)
        {
            $start_date = $dt->start_date;
            $start_time = $dt->start_time;
            $end_time = $dt->end_time;
            $end_date = $dt->end_date;
            $is_flash = $dt->flash_active;
            $productId = $dt->product_id;
            
            $count_down = date('M d, Y 00:00:00');
            $last = $end_date." ".$end_time;
            $currdate = date('Y-m-d');
            $currTime = date('H:i:s');


            $data['flashSalesData'] = $this->homepageProductQuery()
                ->where('products.product_id',$productId)
                ->get();
           
            if($currdate >= $end_date && $currTime > $end_time)
            {
               $is_flash = "INACTIVE"; 
            }
            $count_down = $last;

            $currencySession = Session::get('currency');
         /*   $ips = $request->ip();
             return $ips; */              
             if($currencySession == "rupee_price")
             {
                 $p_dt = json_decode($dt->product_prize);
                 
             }
             elseif($currencySession == "dollar_price")
             {
                 $p_dt = json_decode($dt->dollar_prize);
             } 
             elseif($currencySession == "euro_price")
             {
                 $p_dt = json_decode($dt->euro_prize);
             }
             else
             {
                $p_dt = json_decode($dt->dollar_prize);
             }
            if (is_array($p_dt) || is_object($p_dt)) {
                foreach($p_dt as $dassa)
                {
                    $pros = explode(',',$dassa);
                    break;
                }
            }
            $amt = isset($pros[1]) ? $pros[1] : 0;
        }
        
        
        
        /*$price = DB::table('flash_sale')->orderby('flash_banner_id', 1)->value('start_date'); */
        
        
      
        $data['flash_sales_data'] = Schema::hasTable('flash_banner') ? DB::table('flash_banner')->where('flash_banner_id', 1)->orderby('flash_banner_id', 'asc')->get() : collect([]);
        
        $data['customer_data'] = Schema::hasTable('customer_shirt') ? DB::table('customer_shirt')->where('customer_shirt_id', 1)->get() : collect([]);
        $data['testimonials'] = Schema::hasTable('testimonial') ? AdminRecycleBinService::activeTable('testimonial')->orderBy('testimonial_id', 'desc')->take(12)->get() : collect([]);
        
        
            return view('front.index',compact('is_flash','count_down','amt'), $data);
        } catch (\Exception $e) {
            // Database unavailable or error — return safe defaults for all view variables
            $data['main_video']       = collect([]);
            $data['slider_data']      = collect([]);
            $data['cate_data']        = collect([]);
            $data['blog_data']        = collect([]);
            $data['banner_data']      = collect([]);
            $data['featured_products']= collect([]);
            $data['new_arrivals']     = collect([]);
            $data['view_flash_data']  = collect([]);
            $data['flashSalesData']   = collect([]);
            $data['flash_sales_data'] = collect([]);
            $data['customer_data']    = collect([]);
            $data['testimonials']     = collect([]);
            $is_flash = "INACTIVE";
            $count_down = date('M d, Y 00:00:00');
            $amt = 0;
            return view('front.index', compact('is_flash','count_down','amt'), $data);
        }
    }

    private function homepageProductQuery()
    {
        if (!Schema::hasTable('products')) {
            throw new \RuntimeException('Products table is missing.');
        }

        $query = DB::table('products');
        AdminRecycleBinService::withoutDeleted($query, 'products');

        if (
            Schema::hasTable('product_quantity')
            && Schema::hasColumn('product_quantity', 'product_quantity_id')
            && Schema::hasColumn('product_quantity', 'product_id')
        ) {
            $query->leftJoin('product_quantity as homepage_product_quantity', function ($join) {
                $join->on(
                    'homepage_product_quantity.product_quantity_id',
                    '=',
                    DB::raw('(SELECT MIN(pq.product_quantity_id) FROM product_quantity pq WHERE pq.product_id = products.product_id)')
                );
            });
        }

        return $query
            ->select($this->homepageProductColumns())
            ->where('products.is_active', 'ACTIVE');
    }

    private function homepageProductColumns()
    {
        $columns = ['products.*'];
        $quantityColumns = [
            'product_quantity_id',
            'product_quantity',
            'rupee_price',
            'dollar_price',
            'euro_price',
            'product_discount',
            'rupee_net_amount',
            'dollar_net_amount',
            'euro_net_amount',
            'rupee_net_with_gst',
            'dollar_net_with_gst',
            'euro_net_with_gst',
        ];

        foreach ($quantityColumns as $column) {
            if (
                Schema::hasTable('product_quantity')
                && Schema::hasColumn('product_quantity', 'product_quantity_id')
                && Schema::hasColumn('product_quantity', 'product_id')
                && Schema::hasColumn('product_quantity', $column)
            ) {
                $columns[] = 'homepage_product_quantity.' . $column . ' as ' . $column;
            } else {
                $columns[] = DB::raw('NULL as ' . $column);
            }
        }

        return $columns;
    }
    
    
    public function newsub_store(Request $request)
    {
        $input = $request->all();
        $input['date'] = date("d/m/Y");
        $email =  $request->email;
        $check = DB::table('subscribe')->where('email',$email)->get();
        if(!$check->isEmpty())
        {
            $request->session()->flash('alert-danger','Sorry ! You are already subscribed.');
           
        }
        else
        {
            $check = DB::table('subscribe')->insert($input);
            $request->session()->flash('alert-success','Thank you for subscribed for latest News.');
            
        }
        return redirect()->back();
        
    }
    
    public function ipAddressFounder(Request $request)
    {
        echo "Mahi";
        $m = $this->getIp();
        echo $clientIP = \Request::getClientIp(true);;
    }
    
     public function cmspages(Request $request, $slug)
    {
       
        $cmsQuery = AdminRecycleBinService::activeTable('cms')->where('slug',$slug);
        $data['cms_data'] = $cmsQuery->get();

            $cmsMeta = AdminRecycleBinService::activeTable('cms')->where('slug',$slug)->first();
            $meta_description = $cmsMeta ? $cmsMeta->meta_description : '';
            $meta_keyword = $cmsMeta ? $cmsMeta->meta_keywords : '';
            $page_title = $cmsMeta ? $cmsMeta->meta_title : 'Zouple';
       
       
        return view('front.cms.cmspage',compact('page_title', 'meta_description', 'meta_keyword'),$data);
    }
    
    public function cmspagesss(Request $request, $slug)
    {
       
        $cmsQuery = AdminRecycleBinService::activeTable('cms')->where('slug',$slug);
        $data['cms_data'] = $cmsQuery->get();
        $cmsMeta = AdminRecycleBinService::activeTable('cms')->where('slug',$slug)->first();
        $meta_description = $cmsMeta ? $cmsMeta->meta_description : '';
        $meta_keyword = $cmsMeta ? $cmsMeta->meta_keywords : '';
        $page_title = $cmsMeta ? $cmsMeta->meta_title : 'Zouple';
        
        return view('front.cms.cmspagess',compact('page_title', 'meta_description', 'meta_keyword'),$data);
    }
    
    
     public function getIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
    }
    
    
    public function show_search_data(Request $request)
    {
        
       
        $search_string = $request->searchData;
       
        
        $slug = Product::where('product_title','LIKE','%'.$search_string.'%')->where('is_active','ACTIVE')->get();
        
        
        foreach($slug as $dt)
        {
            $pro_id[] = $dt->product_id;
        }
        
        /*echo "<pre>";
        print_r($pro_id);
        echo "</pre>";
        
        
        return $request;*/
 
        
        if(isset($pro_id))
        {
         
            $data['products'] = Product::leftJoin('product_quantity', function ($join) {
                $join->on('product_quantity.product_quantity_id', '=', DB::raw('(SELECT product_quantity_id FROM product_quantity WHERE product_quantity.product_id = products.product_id LIMIT 1)'));})
                ->whereIn('products.product_id',$pro_id)
                ->get();
            
            
            $page_title = "Searched Product List";
            $page_head = "Searched Product List";

          return view('front.product.productSearch',compact('page_title', 'page_head'), $data);
        }
        else
        {
            $page_title = "Not Record Found";
            $page_head = "Not Record Found";
            return view('front.product.productSearch',compact('page_title','page_head'));
        }
        
    }
    
    
    
    /* Change Currency Code Start */
    
    public function changeCurrencyPage(Request $request, $currency)
    {
        Session::put('currency', \App\Helper\CurrencyHelper::normalizeSessionCurrency($currency));
        $data['val'] = "true";
        return $data;
    }

    public function messageSendStore(Request $request)
    {
        $input = $request->all();
        
        Session::put('showAlert', "No");
        Session::put('showAlertContent', "No");
        $input['name']= Auth::user()->name;
        $input['email']= Auth::user()->email;
        $input['contact']= Auth::user()->contact;
        $input['date'] = date('d/m/Y');
        DB::table('send_message')->insert($input);
        $request->session()->flash('alert-success','Thank you for answerning this Poll. Your feedback is highly apprectiated!');
        return redirect()->back();
        
    }
}
