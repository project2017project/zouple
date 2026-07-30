<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth,Redirect,View,File,Config,Image;
use Validator;
use DB;
use Session;
use Mail;
use App\Services\AdminRecycleBinService;

class ContactController extends Controller
{
    public function contactList(Request $request)
    {
       $page_title = "Contact - zouple.in";
        $data['site_data'] = DB::table('siteinfos')->orderby('siteinfo_id', 'asc')->get();
        return view('front.contact.contact',compact('page_title'), $data); 
    }
    
    public function contactStore(Request $request)
    {
  
      $input = $request->all();
	    $input['date'] = date("Y/m/d");
      DB::table('contact')->insert($input);
      $request->session()->flash('alert-success','Thank you for contacting Zouple. We will revert you as soon as we receive your email..');
      return redirect('contact');
    } 



    /* About Code Start */

    public function aboutPage(Request $request)
    {
       $page_title = "About us - zouple.in";
        $data['about_data'] = AdminRecycleBinService::activeTable('about')->orderby('about_id', 'asc')->get();
        return view('front.about.about',compact('page_title'), $data); 
    }
    
    /* getNotificationStore */
    
    public function getNotificationStore(Request $request)
    {
        $input = $request->all();   
        $input['date_time'] = date('y-m-d');
          DB::table('getnotification')->insert($input);
          $request->session()->flash('alert-success','Thankyou, We recive your query. We will send mail when your requirment full fill.');
          return Redirect::back();
    }



}
