<?php

namespace App\Http\Controllers\masterAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Email_template;
use App\Order;
use App\Product;    
use Auth,Redirect,View,File,Config,Image;
use Validator;
use DB;
use Input;
use Mail;
use App\Helper\BasicHelper;
use App\Services\AdminRecycleBinService;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\orderStatusExport;

class OrderController extends Controller
{
    public function order_information_list(Request $request)
    {
        $page_title = "Order List - Zouple";
        $proTitle = [];
        $proImage = [];
        $febricName = [];
        $elementValueName = [];
        
        $data['order_data'] =  AdminRecycleBinService::activeTable('order_system')->orderby('order_id','desc')->join('users','users.id','order_system.user_id')->where('order_system.user_report',Null)->get();
        
        $order_data = AdminRecycleBinService::activeTable('order_system')->where('order_type','!=','DESIGN-SHIRT')->get();
        foreach($order_data as $as)
        {
            $proq = json_decode($as->product_details);
            foreach($proq as $key => $prr)
            {
                 $proTitle[$key] = Product::where('product_id',$key)->value('product_title');
                 $proImage[$key] = Product::where('product_id',$key)->value('product_header_image');
                
            }
        }
        
      return view('masters.order.order_list',compact('page_title','proTitle','proImage','febricName','elementValueName'),$data);
        
    }
    
    
    
    
   /* Export Code Start */
    
    public function orderStatusExportPage(Request $request) 
    {
        
        $orderStatus = "all";
        if(isset($request->orderStatus))   
        {
            $orderStatus = $request->orderStatus;
        }
        return Excel::download(new orderStatusExport($orderStatus), 'orderstatusexport.xlsx');
    }
    
    public function show_order_status_details(Request $request)
    {
       $page_title = "Order List - Zouple";
       
       $order_status = $request->order_status;
        
        $data['order_data'] =  AdminRecycleBinService::activeTable('order_system')->orderby('order_id','desc')->join('users','users.id','order_system.user_id')->where('order_system.order_status', $order_status)->get();
        
        foreach($data['order_data'] as $datass)
        {
            $data['billing_add'] = DB::table('user_information')->where('user_information.user_information_id',$datass->billing_address_id)->get();
            $data['shipping_add'] = DB::table('user_information')->where('user_information.user_information_id',$datass->shipping_address_id)->get();
            
            //Product Details //
            $pro_det = json_decode($datass->product_details);
            foreach($pro_det as $datas)
            {
                $pro_id[] =  $datas[0];
                $pro_qty[] =  $datas[1];
                $pro_opt[] =  $datas[2];
               
                $pro_name[] = DB::table('products')->where('product_id',$datas[0])->value('product_title');
                
                
                foreach($pro_det as $key => $prr)
                {
                     $proTitle[$key] = Product::where('product_id',$key)->value('product_title');
                     $proImage[$key] = Product::where('product_id',$key)->value('product_header_image');
                    
                }
                
                
            }
        }
        if(!$data['order_data']->isEmpty())
        {
            return view('masters.order.order_list',compact('page_title','pro_id','pro_qty','pro_opt','pro_price','pro_name','free_item','proTitle','proImage', 'order_status'),$data);
        }
           
        else
        {
            return view('masters.order.order_list',compact('page_title', 'order_status'),$data);
        }
    }

    public function orderDelete_format(Request $request,$order_id)
    {
        AdminRecycleBinService::softDelete('orders', $order_id);
        $request->session()->flash('alert-success','Order moved to Recycle Bin.');
        return Redirect::route('order_information');
    }
    
    public function orderOldMessagePage(Request $request,$order_id)
    {
        $page_title = "Order Old Message List - Zouple";
        $data['orderOldMsgData'] = DB::table('order_old_msg')->where('order_id','=',$order_id)->get();
        return view('masters.order.order_old_msg',compact('page_title'),$data);
    }
    
    public function orderOldMsgDeleteFormat(Request $request,$order_old_msg_id)
    {
        $order_id = DB::table('order_old_msg')->where('order_old_msg_id','=',$order_old_msg_id)->value('order_id');
        DB::table('order_old_msg')->where('order_old_msg_id','=',$order_old_msg_id)->delete();
        $request->session()->flash('alert-success','Order Old Msg has been deleted Successfully!!');
        return Redirect::route('orderOldMessage',$order_id);
    }
    
    

    public function ordersDeletesFormat(Request $request,$order_id)
    {
        AdminRecycleBinService::softDelete('orders', $order_id);
        $request->session()->flash('alert-success','Order moved to Recycle Bin.');
        return Redirect::route('order_report');
    }

    public function order_status_update_save(Request $request)
    {
        
        /* Order Message Code Start */
        $orderoldmsg['order_id'] = $request->order_id;
        $orderoldmsg['order_status'] = $request->order_status;
        $orderoldmsg['tracking_number'] = $request->tracking_number;
        $orderoldmsg['order_update_date'] = $request->order_update_date;
        $orderoldmsg['tracking_url'] = $request->tracking_url;
        $orderoldmsg['_token'] = $request->_token;
        DB::table('order_old_msg')->insert($orderoldmsg);
        
        /* Order Message Code End */
    
        $subject = "Check Subject .";
        $input = $request->all();
        DB::table('order_system')->where('order_id',$input['order_id'])->update($input);
        $logo_url = "https://zouple.in/public/front/images/logo.png";
        /*Mail Setting*/
        
        $tracking_number = $request->tracking_number;
        $tracking_url = $request->tracking_url;

        $status = $request->status;
        $order_status = $request->order_status;
        $order_date = $request->order_update_date;
        $user_id = DB::table('order_system')->where('order_id',$input['order_id'])->where('status', 1)->value('user_id');
        
        $order_number = DB::table('order_system')->where('order_id',$input['order_id'])->where('status', 1)->value('order_number');
        
        $user_name = DB::table('users')->where('id',$user_id)->value('name');
        $user_email = DB::table('users')->where('id',$user_id)->value('email');
        $date = date('d/M/Y');
        $check_order = DB::table('order_system')->where('order_number',$order_number)->value('order_type');
        $order_dt = DB::table('order_system')->where('order_number',$order_number)->value('order_date');
        $msg="";
        $datas = DB::table('order_system')->where('order_number',$order_number)->value('product_details');
        
        $mail = DB::table('mail_settings')->where('slug','order')->first();
        if ($mail) //checking if table is not empty
        {
            $config = array(
                'driver'     => $mail->driver,
                'host'       => $mail->host,
                'port'       => $mail->port,
                'from'       => array('address' => $mail->from_address, 'name' => $mail->from_name),
                'encryption' => $mail->encryption,
                'username'   => $mail->username,
                'password'   => $mail->password,
                'sendmail'   => '/usr/sbin/sendmail -bs',
                'pretend'    => false,
            );
            Config::set('mail', $config);
        }
        
        
        $invoiceurl = WEBSITE_URL."/printInvoicesss/".$order_number;
        $returnpoliceurl = WEBSITE_URL."/cmsss/return-policy";
        /*$links = "<a href=$returnpoliceurl> Click Here </a>";
        
        $link = "<a href=$invoiceurl> Click Here </a>";*/
        
        if($order_status == "Accepted")
        {
            
            $subject = "Order Confirmation Zouple";
            $msg="<!DOCTYPE html>
            <html lang='en'>

            <head>
                <title>The Zouple</title>
                <meta charset='utf-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1'>
            </head>

            <body>
                
                <section style='width: 60%; min-height: 300px;padding: 15px;margin: 0px auto; c: rgba(255,255,255,.6);  display: block;border-radius: 2px; border:2px solid black'>
                     <div style='text-align:center;background-color:black;padding:8px;'>
                        <img src='{$logo_url}' width='100px'></a>
                     </div>
    
                    <div style='text-align:left;'>
                        <h1 style='text-align: left;  color:#969696!important; margin:0px!important; font-size:30px;padding-top:10px;'>ORDER CONFIRMED</h1>
                        <p style='border-bottom: 3px solid #000000;'> </p>
                        <h3 style='text-align: left;margin:0px!important; color:#969696;'>Hi {$user_name},</h3>
                        <br>
                        <div>
                            <div style='float:left;'>
                                 <h3 style='text-align: left;margin:0px!important; color:#969696;'>Order Number : #{$order_number}</h3>
                            </div>
                            <div style='float:right;'>
                                 <h3 style='text-align: left;margin:0px!important; color:#969696;'>Order Date :  {$order_dt}</h3>
                            </div>
                       </div>
                        <br>";
                       if($check_order != "DESIGN-SHIRT")
                       {
                           
                       $msg=$msg."
                        <table width='100%' border='2px black solid' style='margin-top:15px!important; margin-bottom:15px!important;clear:both;'>
                        <tr>
                            <th style='padding:10px;'>
                            Product Name
                            </th>
                            <th style='padding:10px;'>Quantity
                            </th>
                        </tr>";
                        $pro_det = json_decode($datas);
                        foreach($pro_det as $key => $dt)
                        {
                            $pro_name = DB::table('products')->where('product_id',$key)->value('product_title');
                            $prs = explode('-',$dt);
                        
                        $msg=$msg."
                        <tr>
                            <td style='padding:10px;'>
                                {$pro_name}
                            </td>
                            <td style='padding:10px;'>{$prs[1]}
                            </td>
                        </tr>
                        </table>";
                        }
                       }
                        else
                       {
                         $msg=$msg."<table width='100%' border='2px black solid' style='margin-top:15px!important; margin-bottom:15px!important;clear:both;'>
                            <tr>
                                <th style='padding:10px;'>
                                Product - Customize Shirt Febric
                                </th>
                                <th style='padding:10px;'>";
                                   $pro_det = json_decode($datas);
                                   foreach($pro_det as $key => $dt)
                                   {
                                       $feb = DB::table('febric')->where('febric_id',$dt)->value('name');
                                       break;
                                   }
                                    
                                 $msg=$msg."  
                                 {$feb}
                                </th>
                            </tr>
                            </table>";
                        
                        }
                        $msg=$msg." <br>
                         
                         <div style='font-weight: normal;clear:both; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>Thanks for shopping at The Zouple. Your order is confirmed </div><br>
                         
                         <div style='font-weight: normal;clear:both; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'> Download your <a href='{$invoiceurl}'> INVOICE HERE . </a></div><br>
                          <p style='border-bottom: 3px solid #000000;'> </p>
                         
                         
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>
                             In case of any questions please write to us at order@zouple.in and we will revert to you as soon as we receive your email. <br><br>
                         Thank You,<br>
                         The Zouple
                         </div>
                    </div>
                    
                    
                </section>
            </body>

            </html>
            ";
            
            $request->session()->flash('alert-success','Order Accepted !!');
        }
        elseif($order_status == "Rejected")
        {
    
            $subject = "Order Rejection";
            $msg="<!DOCTYPE html>
            <html lang='en'>

            <head>
                <title>The Zouple</title>
                <meta charset='utf-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1'>
            </head>

            <body>
                
                <section style='width: 60%; min-height: 300px;padding: 15px;margin: 0px auto; c: rgba(255,255,255,.6);  display: block;border-radius: 2px; border:2px solid black'>
                     <div style='text-align:center;background-color:black;padding:8px;'>
                        <img src='{$logo_url}' width='100px'></a>
                     </div>
    
                    <div style='text-align:left;'>
                        <h1 style='text-align: left;  color:#969696!important; margin:0px!important; font-size:30px;padding-top:10px;'>ORDER REJECTION</h1>
                        <p style='border-bottom: 3px solid #000000;'> </p>
                        <h3 style='text-align: left;margin:0px!important; color:#969696;'>Hi {$user_name},</h3>
                        <br>
                        <div>
                            <div style='float:left;'>
                                 <h3 style='text-align: left;margin:0px!important; color:#969696;'>Order Number : #{$order_number}</h3>
                            </div>
                            <div style='float:right;'>
                                 <h3 style='text-align: left;margin:0px!important; color:#969696;'>Order Date :  {$order_dt}</h3>
                            </div>
                       </div>
                        <br>";
                       if($check_order != "DESIGN-SHIRT")
                       {
                           
                       $msg=$msg."
                        <table width='100%' border='2px black solid' style='margin-top:15px!important; margin-bottom:15px!important;clear:both;'>
                        <tr>
                            <th style='padding:10px;'>
                            Product Name
                            </th>
                            <th style='padding:10px;'>Quantity
                            </th>
                        </tr>";
                        $pro_det = json_decode($datas);
                        foreach($pro_det as $key => $dt)
                        {
                            $pro_name = DB::table('products')->where('product_id',$key)->value('product_title');
                            $prs = explode('-',$dt);
                        
                        $msg=$msg."
                        <tr>
                            <td style='padding:10px;'>
                                {$pro_name}
                            </td>
                            <td style='padding:10px;'>{$prs[1]}
                            </td>
                        </tr>
                        </table>";
                        }
                       }
                        else
                       {
                         $msg=$msg."<table width='100%' border='2px black solid' style='margin-top:15px!important; margin-bottom:15px!important;clear:both;'>
                            <tr>
                                <th style='padding:10px;'>
                                Product - Customize Shirt Febric
                                </th>
                                <th style='padding:10px;'>";
                                   $pro_det = json_decode($datas);
                                   foreach($pro_det as $key => $dt)
                                   {
                                       $feb = DB::table('febric')->where('febric_id',$dt)->value('name');
                                       break;
                                   }
                                    
                                 $msg=$msg."  
                                 {$feb}
                                </th>
                            </tr>
                            </table>";
                        
                        }
                        
                        $msg=$msg."<br>
                        
                        <div style='font-weight: normal;clear:both; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'> Download your <a href='{$invoiceurl}'> INVOICE HERE . </a></div><br>
                         
                         <div style='font-weight: normal;clear:both; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>Oops! We are running out of stock on the product you have ordered for. This one for sure is high on demand. Apology for the inconvenience caused to you. </div><br>
                         
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>We will refund your entire money that you have paid for the product. <br> <br> Kindly email below details on order@zouple.in in order to process your refund.</div>
                         
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'><br>
                         Name - <br>
                         Bank Name - <br>
                         Bank account no. - <br>
                         IFSC Code - <br>
                         <br>
                         </div>
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'> Once you share the details it will take 9 - 10 business days for the credit to be reflecting in your account. <br>
                         </div>
                         
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'> <a href='{$returnpoliceurl}'> Please visit our Return Policy. </a><br>
                         </div>
                         
                          <p style='border-bottom: 3px solid #000000;'> </p>
                         
                         
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>
                             In case of any questions please write to us at order@zouple.in and we will revert to you as soon as we receive your email. <br><br>
                         Thank You,<br>
                         The Zouple Team
                         </div>
                    </div>
                    
                    
                </section>
            </body>

            </html>
            ";
        }
        elseif($order_status == "Dispatch")
        {
            $subject = "Order Dispatch Successfully";
            $url = $tracking_number;
            $tracking_url = $tracking_url;
             /*$url ="https://www.fedex.com/apps/fedextrack/?action=track&trackingnumber=".$tracking_number;*/
            
            $msg="<!DOCTYPE html>
            <html lang='en'>

            <head>
                <title>The Zouple</title>
                <meta charset='utf-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1'>

            </head>

            <body>

                <section style='width: 60%; min-height: 300px;padding: 15px;margin: 25px auto; background: rgba(255,255,255,.6);  display: block;border-radius: 2px;'>
                    <div style='text-align:left;'>
                        <h1 style=' text-align: left; color:#969696; font-size:30px; margin:0px;'>PRODUCT SHIPPED</h1>
                        
                        <p style='border-bottom: 3px solid #000000;'> </p>
                         <div style='font-weight: normal;  text-align: left; color:#969696; font-size: 16px;'>Hi {$user_name},</div><br>
                         
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>Your order {$order_number} has been shipped.</div>
                         
                         <div style='font-weight: normal; text-align: left; color:#969696; font-size: 16px;'>Here are your tracking informations - </div>
                         
                         <div style='font-weight: normal; text-align: left; color:#969696; font-size: 16px;'><b>Tracking Number  - {$url} </b></div><br>
                         
                         <a class='link-btn' href='{$tracking_url}' style='padding: 10px 20px;font-size: 18px;line-height: 24px;background: #000000;margin: 45px auto;display: block; width: 150px;text-align: center;color: #fff;text-transform: uppercase;text-decoration: none;'>Track Now</a>
                         
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>You can also login to your The Zouple - My account section to track your order.</div><br>
                         
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>Delivery usually takes 3 to 4 days after it leaves from our office. It may also take up to 24 hours for your tracking number to return any information.</div>
                         
                          <p style='border-bottom: 3px solid #000000;'> </p>
                         
                         
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>
                             In case of any questions please write to us at contact@zouple.in and we will revert to you as soon as we receive your email. 

                            <br><br>
                         Thank You,<br>
                         The Zouple Team 
                         </div>
                    </div>
                    
                    
                </section>
            </body>

            </html>
            ";
        }
        elseif($order_status == "Delivered")
        {
            $subject = "Product Delivered Sucessfully by Zouple";
            
            $msg="<!DOCTYPE html>
            <html lang='en'>

            <head>
                <title>The Zouple</title>
                <meta charset='utf-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1'>

            </head>

            <body>

                <section style='width: 60%; min-height: 300px;padding: 15px;margin: 25px auto; background: rgba(255,255,255,.6);  display: block;border-radius: 2px;'>
                   
                    <div style='text-align:left;'>
                        <h1 style=' text-align: left;font-size:30px; margin:0px; color:#969696;'>PRODUCT DELIVERED SUCCESSFULLY</h1>
                        <h3 style=' text-align: left; margin:0px; color:#969696;'>Your order #{$order_number}</h3>
                        <p style='border-bottom: 3px solid #000000;'> </p>
                         
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>Hi {$user_name},</div><br>
                         
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>Your product has been successful delivered on {$order_date} through courier. </div>
                         
                         
                         
                         <br>
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>
                            In case of any questions please write to us at contact@zouple.in and we will revert to you as soon as we receive your email. 

                        <br><br>
                         Thank You,<br>
                         The Zouple Team 
                         </div>
                    </div>
                    
                    
                </section>
            </body>

            </html>
            ";
        }
            
      /*  echo $msg;
       
       */
        $messageBody = $msg;
        $data['msg']=$messageBody;
        $data['subject']=$subject;
        $data['email']=$user_email;
        Mail::send([],[],  function ($message)  use($data) 
        {
            $message->to($data['email'])->subject($data['subject'])
                ->setBody($data['msg'], 'text/html'); 
        });

        
        
        $request->session()->flash('alert-success','Order Status Updated !!');
        return Redirect::route('order_information');
    }

    public function paymet_status_update_save(Request $request)
    {
        $input = $request->all();
        DB::table('order_system')->where('order_id',$input['order_id'])->update($input);

        $request->session()->flash('alert-success','Payment Status Updated !!');
        return Redirect::route('order_information');
    }

    public function orderShow_format(Request $request,$order_number)
    {
        $data['order_data'] =  AdminRecycleBinService::activeTable('order_system')
            ->join('users','users.id','order_system.user_id')
             ->whereNull('users.deleted_at')
             ->where('order_number',$order_number)
            ->get();

        $order_data = AdminRecycleBinService::activeTable('order_system')->where('order_number', $order_number)->get();

        if(!$order_data->isEmpty())
        {
            foreach($order_data as $as)
            {
                $proq = json_decode($as->product_details);
                foreach($proq as $key => $prr)
                {
                     $proTitle[$key] = Product::where('product_id',$key)->value('product_title');
                     $netAmount[$key] = DB::table('order_system')->where('order_number',$order_number)->value('net_amount');
                }
            }
        }
        
        
        foreach($data['order_data'] as $datass)
        {
            $data['billing_add'] = DB::table('order_address')
                        
                        ->where('order_address_id',$datass->billing_address_id)->get();
            $data['shipping_add'] = DB::table('order_address')
                            
                            ->where('order_address_id',$datass->shipping_address_id)->get();
             
        }
        
        
        $page_title = "Order List Show Details - Zouple";
        
        return view('masters.order.order_show',compact('page_title', 'proTitle', 'netAmount'),$data);
    }
    
    public function order_user_Report(Request $request)
    {
        $page_title = "Order Report - Zouple";
        
        $data['order_data'] =  AdminRecycleBinService::activeTable('order_system')->join('users','users.id','order_system.user_id')
        ->whereNull('users.deleted_at')
        ->where('user_report','!=',"")->get();
        

        
        if(!$data['order_data']->isEmpty())
        {
            return view('masters.order.order_report_list',compact('page_title'),$data);
        }
           
        else
        {
            return view('masters.order.order_report_list',compact('page_title'),$data);
        }
    }

    public function remarkStore(Request $request)
    {
        $input = $request->all();
        DB::table('order_system')->where('order_number',$input['order_number'])->update($input);
        
        $order_number = $input['order_number'];
        $user_name =  DB::table('order_system')
                    ->join('users','users.id','order_system.user_id')
                    ->where('order_system.order_number',$order_number)
                    ->value('users.name');
                    
        $user_email = DB::table('order_system')
                    ->join('users','users.id','order_system.user_id')
                    ->where('order_system.order_number',$order_number)
                    ->value('users.email');
        
        
   
        $email_status = $request->refund_status;
        
        
        $mail = DB::table('mail_settings')->where('slug','order')->first();
            if ($mail) //checking if table is not empty
            {
                $config = array(
                    'driver'     => $mail->driver,
                    'host'       => $mail->host,
                    'port'       => $mail->port,
                    'from'       => array('address' => $mail->from_address, 'name' => $mail->from_name),
                    'encryption' => $mail->encryption,
                    'username'   => $mail->username,
                    'password'   => $mail->password,
                    'sendmail'   => '/usr/sbin/sendmail -bs',
                    'pretend'    => false,
                );
                Config::set('mail', $config);
            }
        
        
            $subject = $request->mail_subject; 
            
            $msg="<!DOCTYPE html>
            <html lang='en'>

            <head>
                <title>The Zouple</title>
                <meta charset='utf-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1'>

            </head>

            <body>

                <section style='width: 60%; min-height: 300px;padding: 15px;margin: 25px auto; background: rgba(255,255,255,.6);  display: block;border-radius: 2px;'>
                   
                    <div style='text-align:left;'>
                        <h1 style=' text-align: left;font-size:30px; margin:0px; color:#969696;'>{$subject}</h1>
                        <h3 style=' text-align: left; margin:0px; color:#969696;'>Your order #{$order_number}</h3>
                        <p style='border-bottom: 3px solid #000000;'> </p>
                         
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>Hi {$user_name},</div><br>
                         
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>{$email_status} </div>
                         <br>
                         
                        <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>This is a acknowledgement mail rearding your request.</div>
                         
                         
                         
                         <br>
                         <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>
                            In case of any questions please write to us at contact@zouple.in and we will revert to you as soon as we receive your email. 

                        <br><br>
                         Thank You,<br>
                            The Zouple
                         </div>
                    </div>
                    
                    
                </section>
            </body>

            </html>
            ";
        
         $messageBody = $msg;
            $data['msg']=$messageBody;
            $data['subject']=$subject;
            $data['email']=$user_email;
            Mail::send([],[],  function ($message)  use($data) 
            {
                $message->to($data['email'])->subject($data['subject'])
                    ->setBody($data['msg'], 'text/html'); 
            });

        
        
        
        $request->session()->flash('alert-success','Remark and Email Status Update Updated !!');
        return Redirect::route('order_report');
    }
}
