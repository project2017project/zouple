<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth,Redirect,route,Session,View,Validator,Config,Hash,File,Image;
use DB;
use Mail;
use App\User;
use App\Product;
use App\Services\AdminRecycleBinService;

class ProfileController extends Controller
{
    public function dashboardList(Request $request)
    {
        $page_title = "Dashboard - Zouple";
        
        $user_id = Auth::user()->id;
        
        $data['user_data'] = AdminRecycleBinService::activeTable('users')->where('id', $user_id)->get();
        
        return view('front.user_profile.dashboard',compact('page_title'),$data); 
    }
    
    public function passwordUpdateStore(Request $request)
    {
          $id = Auth::user()->id;
          $old_pass = Auth::user()->password;
          $old_password = $request->oldpassword;

            $this->validate($request , array
             (    
                 'password' => 'required|string|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
                 'password_confirmation' => 'required_with:password|same:password'
             ));  
          if((Hash::check($old_password , $old_pass)))
          {

            $input['password']=Hash::make($request['password']);
             User::where('id','=',$id)->update($input); 

              Auth::logout();
              $request->session()->flash('alert-success','Password has been sucessfully updated. Please login again with new password.'); 
              return redirect('/');
          }
          else
          {

              $request->session()->flash('alert-danger','Old Password does not match !!');
              return Redirect('dashboard');

          }
    }
    
    public function userOrderList(Request $request)
    {
        $user_id = Auth::user()->id;
        
        $data['orderData'] = AdminRecycleBinService::activeTable('order_system')->where('user_report',null)->where('user_id', $user_id)->orderBy('order_id','desc')->paginate(2);
        
        $order_data = AdminRecycleBinService::activeTable('order_system')->where('user_report',null)->where('user_id', $user_id)->where('order_type','!=','DESIGN-SHIRT')->orderBy('order_id','desc')->get();

        $page_title = "Order List - Zouple";

        if(!$order_data->isEmpty())
        {
            foreach($order_data as $as)
            {
                $proq = json_decode($as->product_details);
                foreach($proq as $key => $prr)
                {
                     $proTitle[$key] = Product::where('product_id',$key)->value('product_title');
                     $proImage[$key] = Product::where('product_id',$key)->value('product_header_image');
                    
                }
            }

        return view('front.user_profile.order',compact('page_title','proTitle','proImage'),$data); 
        }
        else
        {
            return view('front.user_profile.order',compact('page_title'),$data); 
        }
        
        
        
    }
    
    public function userCanncleOrderList(Request $request)
    {
        $user_id = Auth::user()->id;
        
        $data['cancleData'] = AdminRecycleBinService::activeTable('order_system')->where('user_report','CANCEL ORDER')->where('user_id', $user_id)->orderBy('order_id','desc')->paginate(2);
        
        $order_data = AdminRecycleBinService::activeTable('order_system')->where('user_report','CANCEL ORDER')->where('user_id', $user_id)->where('order_type','!=','DESIGN-SHIRT')->orderBy('order_id','desc')->get();
        
        $page_title = "Cancel Order List - The Zouple";
        
        if(!$order_data->isEmpty())
        {
            foreach($order_data as $as)
            {
                $proq = json_decode($as->product_details);
                $user_id = $as->user_id;
                foreach($proq as $key => $prr)
                {
                     $proTitle[$key] = Product::where('product_id',$key)->value('product_title');
                     $proImage[$key] = Product::where('product_id',$key)->value('product_header_image');
                    
                }
            }
           
        return view('front.user_profile.cancelorder',compact('page_title','proTitle','proImage'),$data); 
        }
        else
        {
            return view('front.user_profile.cancelorder',compact('page_title'),$data); 
        }
    }
    
    public function userReturnOrderList(Request $request)
    {
        $user_id = Auth::user()->id;
        $data['returnData'] = AdminRecycleBinService::activeTable('order_system')->where('user_report','RETURN ORDER')->where('user_id',$user_id)->orderBy('order_id','desc')->paginate(2);
        $order_data = AdminRecycleBinService::activeTable('order_system')->where('user_report','RETURN ORDER')->where('user_id', $user_id)->where('order_type','!=','DESIGN-SHIRT')->orderBy('order_id','desc')->get();
        $page_title = "Return Order - Zouple";
        if(!$order_data->isEmpty())
        {
            foreach($order_data as $as)
            {
                $proq = json_decode($as->product_details);
                foreach($proq as $key => $prr)
                {
                     $proTitle[$key] = Product::where('product_id',$key)->value('product_title');
                     $proImage[$key] = Product::where('product_id',$key)->value('product_header_image');
                    
                }
            }
            return view('front.user_profile.returnorder',compact('page_title','proTitle','proImage'),$data); 
        }
        else
        {
            return view('front.user_profile.returnorder',compact('page_title'),$data); 
        }
        
       
    }
    
    public function userExchangeOrderList(Request $request)
    {
        $user_id = Auth::user()->id;
        $data['cancleData'] = AdminRecycleBinService::activeTable('order_system')->where('user_report','EXCHANGE ORDER')->where('user_id',Auth::user()->id)->orderBy('order_id','desc')->paginate(2);
        $page_title = "Exchange Order - Zouple";
        $order_data = AdminRecycleBinService::activeTable('order_system')->where('user_report','EXCHANGE ORDER')->where('user_id', $user_id)->where('order_type','!=','DESIGN-SHIRT')->orderBy('order_id','desc')->get();
        if(!$order_data->isEmpty())
        {
            foreach($order_data as $ass)
            {
                $proq = json_decode($ass->product_details);
                foreach($proq as $key => $prr)
                {
                     $proTitle[$key] = Product::where('product_id',$key)->value('product_title');
                     $proImage[$key] = Product::where('product_id',$key)->value('product_header_image');
                    
                }
            }
        return view('front.user_profile.exchangeorder',compact('page_title','proTitle','proImage'),$data); 
        }
        else
        {
            return view('front.user_profile.exchangeorder',compact('page_title'),$data); 
        }
        
    }
    
    
    public function orderUpdateByUserSave(Request $request)
    {
        $description = $request->user_description;
        $order_number = $request->order_number;
       
        $user_email = Auth::user()->email;
        $user_name = Auth::user()->name;
        $logo_url = "https://zouple.in/public/front/images/logo.png";
        $order_date = DB::table('order_system')->where('order_number',$order_number)->value('order_date');
        $ord_date = date('d/M/Y', strtotime($order_date));
        
        $datas = DB::table('order_system')->where('order_number',$order_number)->value('product_details');
        $check_order = DB::table('order_system')->where('order_number',$order_number)->value('order_type');
        
        if($request->user_report=="CANCEL ORDER")
        {
            
             $data['order_data'] =  DB::table('order_system')
                    ->where('order_system.order_number',$order_number)
                    ->get();
        
           
            
            $subject = "Order Cancelled Successfully";
            $msg="<!DOCTYPE html>
                <html lang='en'>

                <head>
                    <title>The Zouple</title>
                    <meta charset='utf-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1'>
                </head>
                <body>
                    <section style='width: 80%; min-height: 300px;padding: 15px;margin: 0px auto; c: rgba(255,255,255,.6);  display: block;border-radius: 2px; border:2px solid black'>
                    <div style='text-align:center;background-color:black;padding:8px;'>
                        <img src='{$logo_url}' width='100px'></a>
                     </div>
                        <div style='text-align:left;'>
                        
                            <h1 style='text-align: left;  color:#969696!important; margin:0px!important; font-size:30px;padding-top:10px;'>ORDER CANCELED SUCCESSFULLY</h1>
                            <p style='border-bottom: 3px solid #000000;'> </p>
                            <h3 style='text-align: left;margin:0px!important; color:#969696;'>Hi {$user_name},</h3>
                            <br>
                            <div>
                                <div style='float:left;'>
                                     <h3 style='text-align: left;margin:0px!important; color:#969696;'>Order Number : #{$order_number}</h3>
                                </div>
                                <div style='float:right;'>
                                     <h3 style='text-align: left;margin:0px!important; color:#969696;'>Order Date :  {$ord_date}</h3>
                                </div>
                           </div>";
                           
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
                        </tr>";
                        }
                        $msg=$msg."</table>";
                       }
                       else
                       {
                           echo "Your shirt Customize";
                       }
                            
                        
                            
                            $msg=$msg."
                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>Oops! We just received an email regarding cancellation request.  </div>
                           
                            <br>
                             
                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>Your order has been successfully cancelled.</div><br>

                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>We’re sorry that this order didn’t work out for you. But we hope we’ll see you again.Please share below mentioned details in order to process your refund.</div><br>

                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>
                             Name - <br>
                             Bank Name - <br>
                             Bank account no. - <br>
                             IFSC Code - <br>

                             </div>
                             <br>
                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'> Please allow 9 - 10 business days for the credit to be reflecting in your account. <br>
                             You can find the details of your cancelled order below<br>
                             </div>

                              <p style='border-bottom: 3px solid #030505;'> </p>


                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>
                                 In case of any questions please write to us at contact@zouple.in and we will revert to you as soon as we receive your email. <br><br>
                             Thank You,<br>
                             The Zouple  
                             </div>
                        </div>


                    </section>
                </body>

                </html>
                ";
            $request->session()->flash('alert-success','Your order is sucessfully Canceled.');
            
        }
        elseif($request->user_report=="RETURN ORDER")
        {
            $subject = "Order Return Request";
                $msg="<!DOCTYPE html>
                <html lang='en'>

                <head>
                    <title>The Zouple</title>
                    <meta charset='utf-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1'>

                </head>
                <body>
               <section style='width: 80%; min-height: 300px;padding: 15px;margin: 0px auto; c: rgba(255,255,255,.6);  display: block;border-radius: 2px; border:2px solid black'>
                    <div style='text-align:center;background-color:black;padding:8px;'>
                        <img src='{$logo_url}' width='100px'></a>
                     </div>
                        <div style='text-align:left;'>
                        
                            <h1 style='text-align: left;  color:#969696!important; margin:0px!important; font-size:30px;padding-top:10px;'>ORDER RETURN SUCCESSFULLY</h1>
                            <p style='border-bottom: 3px solid #000000;'> </p>
                            <h3 style='text-align: left;margin:0px!important; color:#969696;'>Hi {$user_name},</h3>
                            <br>
                            <div>
                                <div style='float:left;'>
                                     <h3 style='text-align: left;margin:0px!important; color:#969696;'>Order Number : #{$order_number}</h3>
                                </div>
                                <div style='float:right;'>
                                     <h3 style='text-align: left;margin:0px!important; color:#969696;'>Order Date :  {$ord_date}</h3>
                                </div>
                           </div>";
                           
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
                        </tr>";
                        }
                        $msg=$msg."</table>";
                       }
                       else
                       {
                           echo "Your shirt Customize";
                       }
                           $msg=$msg." 
                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>Oops! We just received an email regarding a return request raised by you.</div>
                           
                            <br>
                             
                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>Apologies for any inconvenience caused. In order to process your return, we request you to mail below mentioned details to us at order@zouple.in and we will get back to you at the earliest.</div><br>

                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>
                             Order no. -  <br>
                             Product details . -  <br>
                             Date of delivery -  <br>
                             Contact no. & email address -  <br>
                             Reason for return -   <br>

                             </div>
                             <br>
                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'> Kindly attach the photograph of the faulty or damaged good(s) and a copy of packing slip for reference. <br>
                             
                             </div>
                             
                             <br>
                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>Kindly go through our Return Policy for complete details.
                             </div>

                              <p style='border-bottom: 3px solid #030505;'> </p>


                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>
                                 In case of any questions please write to us at contact@zouple.in and we will revert to you as soon as we receive your email. <br><br>
                             Thank You,<br>
                            The Zouple  
                             </div>
                        </div>


                    </section>
                </body>

                </html>
                ";
            $request->session()->flash('alert-success','Your return order request is sucessfully placed.');
        }
        
        elseif($request->user_report=="EXCHANGE ORDER")
        {
            $subject = "Order Exchange Request";
                $msg="<!DOCTYPE html>
                <html lang='en'>

                <head>
                    <title>The Zouple</title>
                    <meta charset='utf-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1'>

                </head>
                <body>

                   <section style='width: 80%; min-height: 300px;padding: 15px;margin: 0px auto; c: rgba(255,255,255,.6);  display: block;border-radius: 2px; border:2px solid black'>
                    <div style='text-align:center;background-color:black;padding:8px;'>
                        <img src='{$logo_url}' width='100px'></a>
                     </div>
                        <div style='text-align:left;'>
                        
                            <h1 style='text-align: left;  color:#969696!important; margin:0px!important; font-size:30px;padding-top:10px;'>ORDER EXCHANGE SUCCESSFULLY</h1>
                            <p style='border-bottom: 3px solid #000000;'> </p>
                            <h3 style='text-align: left;margin:0px!important; color:#969696;'>Hi {$user_name},</h3>
                            <br>
                            <div>
                                <div style='float:left;'>
                                     <h3 style='text-align: left;margin:0px!important; color:#969696;'>Order Number : #{$order_number}</h3>
                                </div>
                                <div style='float:right;'>
                                     <h3 style='text-align: left;margin:0px!important; color:#969696;'>Order Date :  {$ord_date}</h3>
                                </div>
                           </div>";
                           
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
                        </tr>";
                        }
                        $msg=$msg."</table>";
                       }
                       else
                       {
                           echo "Your shirt Customize";
                       }
                           $msg=$msg." 
                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>Oops! We just received an email regarding a exchange request raised by you </div>
                           
                            <br>
                             
                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>Apologies for any inconvenience caused. In order to process your return, we request you to mail below mentioned details to us at order@zouple.in and we will get back to you at the earliest.</div><br>

                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>
                             Order no. -  <br>
                             Product details . -  <br>
                             Date of delivery -  <br>
                             Contact no. & email address -  <br>
                             Reason for return -   <br>

                             </div>
                             <br>
                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'> Kindly attach the photograph of the faulty or damaged good(s) and a copy of packing slip for reference. <br>
                             
                             </div>
                             
                             <br>
                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>Kindly go through our Return Policy for complete details.
                             </div>

                              <p style='border-bottom: 3px solid #030505;'> </p>


                             <div style='font-weight: normal; margin-bottom:5px; text-align: left; color:#969696; font-size: 16px;'>
                                 In case of any questions please write to us at contact@zouple.in and we will revert to you as soon as we receive your email. <br><br>
                             Thank You,<br>
                             The Zouple 
                             </div>
                        </div>


                    </section>
                </body>

                </html>
                ";
            
             $request->session()->flash('alert-success','Your exchange order request is sucessfully placed.');
            }
        
       /* echo $msg;
        */
        
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
        
        
         $messageBody = $msg;
        $data['msg']=$messageBody;
        $data['subject']=$subject;
        $data['email']=$user_email;
         Mail::send([],[],  function ($message)  use($data) 
        {
            $message->to($data['email'])->subject($data['subject'])
                ->setBody($data['msg'], 'text/html'); 
        });
        
      $input = $request->all();
        $input['cust_order_update_date'] = date('y/m/d');
        DB::table('order_system')->where('order_number',$input['order_number'])->update($input);
        
        return redirect()->back();
    }
    
    public function showcustomerInvoice(Request $request,$order_number)
    {
         $data['order_data'] =  AdminRecycleBinService::activeTable('order_system')
            ->join('users','users.id','order_system.user_id')
             ->whereNull('users.deleted_at')
             ->where('order_number',$order_number)
            ->get();
        
        $order_data = AdminRecycleBinService::activeTable('order_system')->where('order_number',$order_number)->where('order_type','!=','DESIGN-SHIRT')->get();
        foreach($order_data as $as)
        {
            $proq = json_decode($as->product_details);
            foreach($proq as $key => $prr)
            {
                 $proTitle[$key] = Product::where('product_id',$key)->value('product_title');
                 $proImage[$key] = Product::where('product_id',$key)->value('product_header_image');
                
            }
        }
        foreach($data['order_data'] as $datass)
        {
            $data['billing_add'] = DB::table('order_address')->where('order_address_id',$datass->billing_address_id)->get();
            $data['shipping_add'] = DB::table('order_address')->where('order_address_id',$datass->shipping_address_id)->get();
        }
        
        return view('front.payment.invoice',compact('proTitle','proImage'),$data);
    }
    
    
    
    
    public function customerMailInvoice(Request $request,$order_number)
    {
         $data['order_data'] =  AdminRecycleBinService::activeTable('order_system')
            ->join('users','users.id','order_system.user_id')
             ->whereNull('users.deleted_at')
             ->where('order_number',$order_number)
            ->get();
        
        $order_data = AdminRecycleBinService::activeTable('order_system')->where('order_number',$order_number)->where('order_type','!=','DESIGN-SHIRT')->get();
        foreach($order_data as $as)
        {
            $proq = json_decode($as->product_details);
            foreach($proq as $key => $prr)
            {
                 $proTitle[$key] = Product::where('product_id',$key)->value('product_title');
                 $proImage[$key] = Product::where('product_id',$key)->value('product_header_image');
                
            }
        }
        foreach($data['order_data'] as $datass)
        {
            $data['billing_add'] = DB::table('order_address')->where('order_address_id',$datass->billing_address_id)->get();
            $data['shipping_add'] = DB::table('order_address')->where('order_address_id',$datass->shipping_address_id)->get();
        }
        
        return view('front.payment.invoice',compact('proTitle','proImage'),$data);
    }
    
    
    
    
}
