
@extends('masters.layout.default_layout')
@section('content')
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class="fa fa-futbol-o"></i> Site Information List</h1>
            </div>
            <!-- <ul class="app-breadcrumb breadcrumb">
                <a class="btn btn-primary icon-btn" href="{{route('site_information')}}"><i class="fa fa-eye"></i>  Site Inofrmation listing</a>
            </ul> -->
        </div>
        <div class="row bg-white py-3">
            <div class="col-md-12">
                @if (isset($errors) && count($errors) > 0)
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(Session::has('alert-' . $msg))

                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    </p>
                    @endif
                    @endforeach
                </div>
                <div class="card-box">
                    <form action="{{route('site_information_update')}}" method="post">
                      @csrf
                      @foreach($siteinfor_data as $site)
                        <div class="row col-sm-12">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Site Profile <span class="text-danger"></span></label>
                                    <input class="form-control" type="text" name="site_profile" value="{{$site->site_profile}}" autofocus required placeholder="Site Profile">
                                     <input type="hidden" name="siteinfo_id" value="{{$site->siteinfo_id}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Phone Number <span class="text-danger"></span></label>
                                    <input class="form-control" value="{{$site->phone_number}}"  type="text" name="phone_number" placeholder="Phone Number">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> WhatsApp Number for Bulk Enquiry </label>
                                    <input class="form-control" value="{{$site->whatsapp_number ?? ''}}" type="text" name="whatsapp_number" placeholder="Example: 916375134498">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Address </label>
                                    <input class="form-control" value="{{$site->address}}"  type="text" name="address"  required placeholder="Address">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="Country" >Country<span class="text-danger">*</span></label>
                                    
                                    <input type="text" name="country" class="form-control" value="{{$site->country}}">
                                    
                                 </div>
                             </div>
                             <div class="col-sm-6">
                                 <div class="form-group">
                                    <label for="state" >State<span class="text-danger">*</span></label>
                                    <input type="text" name="state" class="form-control" value="{{$site->state}}">
                                 </div>
                             </div>

                             <div class="col-sm-6">
                                 <div class="form-group">
                                    <label for="city" >City<span class="text-danger">*</span></label>
                                    <input type="text" name="city" class="form-control" value="{{$site->city}}">
                                 </div>
                             </div>


                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Meta Email </label>
                                    <input class="form-control" value="{{$site->meta_email}}"  type="email" name="meta_email"  required placeholder="Meta Email">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Meta Title </label>
                                    <input class="form-control" value="{{$site->meta_title}}"  type="text" name="meta_title"  required placeholder="Meta Title">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Meta Keyword </label>
                                    <input class="form-control" value="{{$site->meta_keyword}}"  type="text" name="meta_keyword"  required placeholder="Meta Keyword">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Email Signatute </label>
                                    <input class="form-control" value="{{$site->email_signature}}"  type="text" name="email_signature"  required placeholder="Meta Signatute">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Facebook Url </label>
                                    <input class="form-control" value="{{$site->facebook_url}}"  type="text" name="facebook_url"  required placeholder="Facebook Url">
                                </div>
                            </div>
                            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Twitter Url </label>
                                    <input class="form-control" value="{{$site->twitter_url}}"  type="text" name="twitter_url"  required placeholder="Twitter Url">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Pinterest Url</label>
                                    <input class="form-control" value="{{$site->pinterest}}"  type="text" name="pinterest"  required placeholder="Pinterest Url">
                                </div>
                            </div>
                            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">youtube Url</label>
                                    <input class="form-control" value="{{$site->youtube}}"  type="text" name="youtube"  required placeholder="Youtube Url">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label"> Instagram Url </label>
                                    <input class="form-control" value="{{$site->instagram_url}}"  type="text" name="instagram_url"  required placeholder="Instagram Url">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Site Rupee Shipping Charage (Per KG)</label>
                                     <input class="form-control" value="{{$site->rupee_shipping_charge}}"  type="number" name="rupee_shipping_charge"  step="0.01" required placeholder="Rupee Shipping Charge">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Site Dollar Shipping Charage (Per KG)</label>
                                     <input class="form-control" value="{{$site->dollar_shipping_charge}}"  type="number" name="dollar_shipping_charge"  step="0.01" required placeholder="Dollar Shipping Charge">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Site Euro Shipping Charage (Per KG)</label>
                                     <input class="form-control" value="{{$site->euro_shipping_charge}}"  type="number" name="euro_shipping_charge"  step="0.01" required placeholder="Euro Shipping Charge">
                                </div>
                            </div>


                            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Minimum KG For Shippinng Free (Per KG)</label>
                                     <input class="form-control" value="{{$site->minimum_charge}}"  type="number" name="minimum_charge"  step="0.01" required placeholder="Minimum KG For Shippinng Free (Per KG)">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Recycle Bin Auto Cleanup Days</label>
                                     <select class="form-control" name="recycle_cleanup_days">
                                        @foreach([30,60,90] as $days)
                                            <option value="{{ $days }}" {{ ($site->recycle_cleanup_days ?? 90) == $days ? 'selected' : '' }}>{{ $days }} days</option>
                                        @endforeach
                                     </select>
                                </div>
                            </div>
                 

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label"> Meta Description</label>
                                    <textarea id="summary-ckeditor" name="meta_description" class="form-control" required>{{$site->meta_description}}</textarea>
                                </div>
                            </div>
                            
                            <div class="col-sm-8">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Submit</button>&nbsp;
                            </div>
                        </div>
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </main>
 
   @stop
