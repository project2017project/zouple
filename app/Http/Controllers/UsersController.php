<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Alert;
use Auth,View,Redirect,Response,Hash,Validator,DB;
use App\User;
use App\Product;
use App\Order;
use App\Category;
use App\Helpers\BasicFunction;
use Session;
use Mail;
use Config;
use PDF;
use App\Services\AdminRecycleBinService;

class UsersController extends Controller
{
    private function attachGuestCartToLoggedInUser(Request $request)
    {
        if (!Auth::check()) {
            return;
        }

        DB::table('carts')
            ->where('ip_address', $request->ip())
            ->where(function ($query) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', 0)
                    ->orWhere('user_id', '');
            })
            ->update(['user_id' => Auth::user()->id]);
    }

    private function normalizeAddressType($type, $fallback = 'Shipping')
    {
        $type = trim((string) $type);

        if (stripos($type, 'billing') !== false) {
            return 'Billing';
        }

        if (stripos($type, 'shipping') !== false) {
            return 'Shipping';
        }

        return $fallback;
    }

    private function validateAddressRequest(Request $request)
    {
        $this->validate($request, [
            'address_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'address' => 'required|string|max:500',
            'landmark' => 'nullable|string|max:255',
            'country' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'city_name' => 'required|string|max:100',
            'pin' => 'required|string|max:20',
            'addresstype' => 'nullable|string|max:30',
        ], [
            'mobile.regex' => 'Please enter a valid mobile number.',
        ]);
    }

    private function addressPayload(Request $request, $addressType)
    {
        return [
            'address_name' => trim((string) $request->address_name),
            'addresstype' => $addressType,
            'mobile' => trim((string) $request->mobile),
            'address' => trim((string) $request->address),
            'landmark' => trim((string) $request->landmark),
            'country' => trim((string) $request->country),
            'state' => trim((string) $request->state),
            'city_name' => trim((string) $request->city_name),
            'pin' => trim((string) $request->pin),
        ];
    }

     /* ------------------------------------- Login Code Start -------------------------- */
    
    public function login(Request $request)
    {
     if($request->isMethod('post'))
     {
        $userdata = array(
                'email' 		=> 	$request->get('email'),
                'password' 		=> 	$request->get('password'),
                'user_role' 	=> 	'FRONT',
                
            );
        if(Auth::attempt($userdata))
        {
            if(Auth::user()->is_active == 'ACTIVE')
            {
                $this->attachGuestCartToLoggedInUser($request);
                return Redirect::back();
            }
            else
            {
                Auth::logout();
                $request->session()->flash('alert-danger','Your account has been blocked deactivated by admin.');
                return Redirect::back();
            }
        }
        {
            Auth::logout();
            $request->session()->flash('alert-danger','It seems you have entered an invalid Zouple Now id or password. Please try with a valid id and password.');
            return Redirect::back();
            
        }
    }
        
    }
    
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
    
    
    
    /* ---------------------------- Registration Code Start ---------------------------- */
    
    public function registration(Request $request)
    {   
        $data = $request->all();
        $this->validate($request, [
            'name' => ['required', 'max:255'],
            'email' => ['required','email', 'max:255', 'unique:users'],
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
            'contact' => 'required',
            'password_confirmation' => 'required_with:password|same:password'
        ]);
        $token =  $request->_token;
        $date=date('d-m-y');
        $dates=date('d/m/Y');
        User::insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'contact' => $data['contact'],
            '_token' => $token,
            'user_role' => 'FRONT',
            'email_verified_at' => $date,
            'date' =>$dates,
        ]);
        $user_data['name'] = $data['name'];
        $user_data['email'] = $data['email'];
        
       
        $fullName = $data['name'];
        $email = $data['email'];
        
        $token = User::where('email',$email)->value('_token');
        $user_data['_token'] = $token;
        
        $userdata = array(
                'email' 		=> 	$request->get('email'),
                'password' 		=> 	$request->get('password'),
                'user_role' 	=> 	'FRONT',
                
            );
            
        if(Auth::attempt($userdata))
        {
            if(Auth::user()->is_active == 'ACTIVE')
            {
                $this->attachGuestCartToLoggedInUser($request);
                $request->session()->flash('alert-success','Hurray! Your The Zouple account has been successfully created.');
                return Redirect::back();
            }
        }
      
        
        
        
            
        /*return Redirect::back();*/
    }
    
    /* ---------------------------- Registration Code End ---------------------------- */
    
    
    /* -------------------------- Password Forget Code Start -------------------------- */
    
    public function reset_password(Request $request)
    {
        $email = $request->email;
    
        $mail = DB::table('mail_settings')->where('slug','password')->first();
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
        $email_check = User::where('email',$email)->value('email_verified_at');
        $check_mail = User::where('email',$email)->value('email');
        $token = User::where('email',$email)->value('_token');
        
        $time['link_times'] = date('H:i:s');
        
        if($check_mail==$email)
        {
                DB::table('users')->where('email',$email)->update($time);
                    $fullName = User::where('email',$email)->value('name');
        
                    $url = WEBSITE_URL."/reset_password?token=".$token;
                    $link = "<a href=$url> Click Here </a>";


                    $messageBody = "<!DOCTYPE html>
                            <html lang='en'>

                            <head>
                                <title>The Zouple</title>
                                <meta charset='utf-8'>
                                <meta name='viewport' content='width=device-width, initial-scale=1'>

                            </head>

                            <body>

                                <section style='width: 60%; min-height: 300px;padding: 15px;margin: 25px auto; background: rgba(255,255,255,.6);  display: block;border-radius: 2px;'>

                                    <div style='text-align:center;'>
                                        <h1 style='padding-left: 30px; text-align: center; font-size:30px!important; color:#969696;'>PASSWORD RESET</h1>
                                        <div style=' text-align: center;font-size: 16px; color:#969696; margin-bottom:5px;'>You have recently requested to reset the password of your The Zouple Account. Use the below link to get started.
                                        </div>
                                        <a class='link-btn' href='{$url}' style='padding: 10px 20px;font-size: 18px;line-height: 24px;background: #000000;margin: 30px auto;display: block; width: 150px;text-align: center;color: #fff;text-transform: uppercase;text-decoration: none;'>RESET YOUR PASSWORD</a>
                                        
                                        <div style='font-size: 16px;margin-top:30px; margin-bottom: 5px; color:#969696; text-align: center;'>For security reasons, this link will be valid for only one use and expires in 30 minutes.</div>
                                        
                                            <div style='font-size: 16px;margin-top:30px; margin-bottom: 5px; color:#969696; text-align: center;'>If you did not request for a password reset, you can ignore this email. Only a person with the access to your email can reset your account password.</div>
                                             <div style='font-size: 16px; margin-top:40px; color:#969696; text-align: left;  margin-left:15px;'>
                                                    Thank You,
                                                 </div>
                                                  <div style='font-size: 16px; color:#969696; text-align: left; margin-left:15px;'>
                                                   The Zouple Team
                                                 </div>

                                    </div>


                                </section>
                            </body>

                            </html>
                            ";
                  
                    $subject = "Reset your The Zouple account password";
                    $data['msg']=$messageBody;
                    $data['subject']=$subject;
                    $data['email']=$email;
                    Mail::send([],[],  function ($message)  use($data) 
                    {
                        $message->to($data['email'])->subject($data['subject'])
                            ->setBody($data['msg'], 'text/html'); 
                    });
                    $pagehead = "Forgot Password";
                    $user_data['msg'] = "We have just sent a password reset link on your registered email id.<br> Never share your password with anyone for the security of your account.<br><br>For security reasons, password reset link will be valid for only one use and expires in 30 minutes. If you do not receive your email within five minutes check your spam folder. ";
                    return view('front.notice.after_req',compact('user_data','pagehead'));
            
        }
        else
        {
            $request->session()->flash('alert-danger','This email id is not registered with The Zouple.'); 
            return redirect('/');
        }    
        
    }
    
    public function change_password(Request $request)
    {
        $old_token = $request->token;
        
        $oldtime = \Carbon\Carbon::createFromFormat('H:i:s',User::where('_token',$old_token)->value('link_times'));
        $time = \Carbon\Carbon::createFromFormat('H:i:s',date('H:i:s'));
         $diff_in_minutes = $oldtime->diffInMinutes($time);
        if($diff_in_minutes <= 30)
        {
            $email = $request->email;
            return view('front.notice.change_password',compact('old_token'));
        }
        else
        {
              $request->session()->flash('alert-danger','This link has been expired. To regenerate a new link for password reset please go to password reset option.');
                return redirect('/');   
        }
        
        
       
        
       
    } 
    
    public function update_password_store(Request $request)
    {
        $mail = DB::table('mail_settings')->where('slug','password')->first();
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
       
        $token = $request->token;
        $old_token = $request->old_token;
        $email = User::where('_token',$old_token)->value('email');
        $email_check = User::where('email',$email)->value('email_verified_at');
        $check_mail = User::where('_token',$old_token)->value('email');
        $email = User::where('_token',$old_token)->value('email');
        if($check_mail != '')
        {
            if($email_check == "")
            {
                $user_data['msg'] = "You Email is not Verified ! Please Check Mail";
                 return view('front.notice.after_req',compact('user_data'));
            }    
            else
            {
                $this->validate($request, [
                    'password' => 'required|string|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
                    'password_confirmation' => 'required_with:password|same:password'
                ]);

                $input['password'] = Hash::make($request->password);
                $m=User::where('_token',$old_token)->update($input);
                
                   
                
                    $fullName = User::where('email',$email)->value('name');
                    $url = WEBSITE_URL;
                    $messageBody = "<!DOCTYPE html>
                            <html lang='en'>

                            <head>
                                <title>The Zouple</title>
                                <meta charset='utf-8'>
                                <meta name='viewport' content='width=device-width, initial-scale=1'>

                            </head>

                            <body>

                                <section style='width: 60%; min-height: 300px;padding: 15px;margin: 25px auto; background: rgba(255,255,255,.6);  display: block;border-radius: 2px;'>

                                    <div style='text-align:center;'>
                                        <h1 style='padding-left: 30px; text-align: center; color:#969696; font-size:30px!important;'>PASSWORD RESET SUCCESSFULLY</h1>
                                       
                                        <div style=' text-align: center;font-size: 16px; color:#969696; margin-bottom:5px; padding:0px 10px;'>Your password for The Zouple account has been successfully changed. Kindly use your new password to continue shopping with us.
.
                                        </div>
                                        <a class='link-btn' href='{$url}' style='padding: 10px 20px;font-size: 18px;line-height: 24px;background: #000000;margin: 30px auto;display: block; width: 150px;text-align: center;color: #fff;text-transform: uppercase;text-decoration: none;'>LOGIN</a>
                                            <div style='font-size: 16px;margin-top:30px; margin-bottom: 5px; color:#969696; text-align: center;'>If you did not request for a password reset, you can ignore this email. Only a person with the access to your email can reset your account password.</div>
                                             <div style='font-size: 16px; margin-top:40px; color:#969696; text-align: left; margin-left:15px;'>
                                                    Thank You,
                                                 </div>
                                                  <div style='font-size: 16px; color:#969696; text-align: left; margin-left:15px;'>
                                                   The Zouple Team
                                                 </div>

                                    </div>


                                </section>
                            </body>

                            </html>
                            ";
                
                
                
                    $subject = "Password reset successfully for your The Zouple account";
                    $data['msg']=$messageBody;
                    $data['subject']=$subject;
                    $data['email']=$email;
                    Mail::send([],[],  function ($message)  use($data) 
                    {
                        $message->to($data['email'])->subject($data['subject'])
                            ->setBody($data['msg'], 'text/html'); 
                    });

                    $request->session()->flash('alert-success','Your password has been successfully updated!'); 
                    return redirect('/'); 
                  
            }
        }
        else
        {
            $user_data['msg'] = "You Email is not Sure.";
            return view('front.notice.after_req',compact('user_data','categories','allCategories'));
        }   
        
        
        
    }
    
    /* -------------------------- Password Forget Code End -------------------------- */
    
     /* ------------------------------------- Login Code End -------------------------- */
    
    
    
    /* Shipping Address Code Start */
    
    public function shippingAddress_list(Request $request)
    {

        $user_id = Auth::user()->id;

        $data['profiles_shipping_data'] = AdminRecycleBinService::activeTable('users')->where('id', $user_id)->get();


        $data['shipping_address_list'] = DB::table('user_information')
                ->join('users','users.id','user_information.user_id')
                ->where('user_information.user_id',$user_id)->where('user_information.addresstype','Shipping')->get();

        $page_title = "Shipping Address - The Zouple";
       
       return view('front.user_profile.shippingaddress',compact('page_title'), $data);
    }
    
    public function shipping_address_order_store(Request $request)
     {
            $this->validateAddressRequest($request);

            $user_id = Auth::user()->id;
            $addressType = $this->normalizeAddressType($request->addresstype, 'Shipping');
            $input = $this->addressPayload($request, $addressType);
            $input['user_id'] = $user_id;
            $input['default_address'] = 'YES';

            DB::table('user_information')
                ->where('user_id', $user_id)
                ->where('addresstype', $addressType)
                ->update(['default_address' => 'NO']);

            DB::table('user_information')->insert($input);
            $request->session()->flash('alert-success','Address has been successfully added.');
            return redirect()->back();

    }
    
    public function paymentUpdatesStore(Request $request)
    {
       $this->validateAddressRequest($request);
       $this->validate($request, [
            'user_information_id' => 'required|integer',
       ]);

       $addressType = $this->normalizeAddressType($request->addresstype, 'Shipping');
       $input = $this->addressPayload($request, $addressType);
       $user_information_id = $request->user_information_id;

       DB::table('user_information')
            ->where('user_information_id', $user_information_id)
            ->where('user_id', Auth::user()->id)
            ->update($input); 
       $request->session()->flash('alert-success','Address has been successfully Updated.');     
       return redirect()->back();
    }
    
    public function shippingDeleteFormat(Request $request,$user_information_id)
  {
      $m = DB::table('user_information')
            ->where('user_information_id', '=', $user_information_id)
            ->where('user_id', Auth::user()->id)
            ->delete();
      $request->session()->flash('alert-success','Address has been successfully deleted.');
      return redirect()->back();
  }
    
    
    
    
    /* Shipping Address Code End */
    
    
    /* Billing Address Code Start */
    
    public function billingAddress_list(Request $request)
    {

        $user_id = Auth::user()->id;

        $data['profiles_shipping_data'] = AdminRecycleBinService::activeTable('users')->where('id', $user_id)->get();


        $data['billing_address_list'] = DB::table('user_information')
                ->join('users','users.id','user_information.user_id')
                ->where('user_information.user_id',$user_id)->where('user_information.addresstype','Billing')->get();

        $page_title = "Billing Address - The Zouple";
       
       return view('front.user_profile.billingaddress',compact('page_title'), $data);
    }
    
    public function billing_address_order_store(Request $request)
     {
            $this->validateAddressRequest($request);

            $user_id = Auth::user()->id;
            $addressType = $this->normalizeAddressType($request->addresstype, 'Billing');
            $input = $this->addressPayload($request, $addressType);
            $input['user_id'] = $user_id;
            $input['default_address'] = 'YES';

            DB::table('user_information')
                ->where('user_id', $user_id)
                ->where('addresstype', $addressType)
                ->update(['default_address' => 'NO']);

            DB::table('user_information')->insert($input);
            $request->session()->flash('alert-success','Address has been successfully added.');
            return redirect()->back();

    }
    
    public function billingpaymentUpdatesStore(Request $request)
    {
       $this->validateAddressRequest($request);
       $this->validate($request, [
            'user_information_id' => 'required|integer',
       ]);

       $addressType = $this->normalizeAddressType($request->addresstype, 'Billing');
       $input = $this->addressPayload($request, $addressType);
       $user_information_id = $request->user_information_id;

       DB::table('user_information')
            ->where('user_information_id', $user_information_id)
            ->where('user_id', Auth::user()->id)
            ->update($input); 
       $request->session()->flash('alert-success','Address has been successfully Updated.');     
       return redirect()->back();
    }
    
    public function billingDeleteFormat(Request $request,$user_information_id)
  {
      $m = DB::table('user_information')
            ->where('user_information_id', '=', $user_information_id)
            ->where('user_id', Auth::user()->id)
            ->delete();
      $request->session()->flash('alert-success','Address has been successfully deleted.');
      return redirect()->back();
  }
    
    /* Billing Address Code End */
    
    public function showShippingAddress(Request $request, $id)
    {
        $data = DB::table('user_information')->where('user_information.user_information_id',$id)
                ->get();
        
        foreach($data as $dt)
        {
            $item['address'] = $dt->address;
            $item['address_name'] = $dt->address_name;
            $item['mobile'] = $dt->mobile;
            $item['city_name'] = $dt->city_name;
            $item['state'] = $dt->state;
            $item['country'] = $dt->country;
            $item['landmark'] = $dt->landmark;
            $item['pin'] = $dt->pin;
        }
        
        return $item;
        
    }
    
    
    /* Wish List Code Start */
    
    public function wish_listShow(Request $request)
    {
        
        $page_title = "Wishlist - The Zouple";

        $user_id = Auth::user()->id;

        $data['profiles_wish_data'] = AdminRecycleBinService::activeTable('users')->where('id', $user_id)->get();

        $data['wishs_lists'] = DB::table('wishlists')->join('products', 'products.product_id', '=', 'wishlists.product_id')->join('product_quantity', 'product_quantity.product_quantity_id', '=', 'wishlists.product_qty_id')->where('wishlists.user_id',Auth::user()->id) ->orderBy('products.product_id', 'asc')->get();
       
       return view('front.user_profile.wishlist',compact('page_title'), $data);
    }
    
    
    public function addToCartSaveDataStore(Request $request,$wishlist_id)
      {
          $wish_list = DB::table('wishlists')->where('wishlist_id','=',$wishlist_id)->get();
          
          foreach($wish_list as $wish)
          {
              $data['user_id'] = $wish->user_id;
             
              $data['product_id'] = $wish->product_id;
             
              $data['product_qty_id'] = $wish->product_qty_id;
             
              $data['product_qty'] = $wish->product_qty;
              
              $data['ip_address']= $request->ip();
              
             DB::table('carts')->insert($data);
             
             
          }
          
          DB::table('wishlists')->where('wishlist_id','=',$wishlist_id)->delete();
          $request->session()->flash('alert-success','You have successfully moved your Product to cart.');
          return redirect()->back();
      }
    
    public function wishDeleteFormat(Request $request,$wishlist_id)
          {
              $m = DB::table('wishlists')->where('wishlist_id','=',$wishlist_id)->delete();
              $request->session()->flash('alert-success','Product has been successfully removed from your wishlist.');
              return redirect()->back();
          }
    
    
    /* Wish List Code End */
}
