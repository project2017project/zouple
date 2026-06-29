<?php

namespace App\Http\Controllers\masterAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Accessories;
use Auth,Redirect,View,File,Config,Image;
use Validator;
use DB;
use Schema;
use Input;
use App\Helper\BasicHelper;
use App\Helper\CurrencyHelper;
use App\Product;
use App\ProductGalleryImage;
use App\Category;
use App\Imports\productImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\AdminMediaService;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

use App\Attribute;
use App\Services\AdminRecycleBinService;


use App\Exports\UsersExport;
use App\Imports\UsersImport;

class ProductController extends Controller
{
   /* Prodcut */
    
    public function product_create(Request $request)
    {
        $data['cate_data'] = AdminRecycleBinService::activeTable('categorys')->where('parent_id',0)->where('is_active','ACTIVE')->get();
       
        $categories = Category::where('parent_id', '=', 0)->where('is_active','ACTIVE')->get();
        $data['attribute_data'] = DB::table('attribute')->orderby('attribute_id', 'asc')->get();

        $data['vendors_data'] = AdminRecycleBinService::activeTable('vendors')->orderby('vendor_id', 'asc')->get();

       
       $page_title = "Add Product - Zouple";
       return view('masters.product.add_product',compact('page_title','categories'),$data);
    }
        
    
    
    public function product_store(Request $request)
    {
        $this->recoverProductUploads($request);

        $this->validate($request, [
            'category' => 'required|array',
            'vendor_id' => 'required',
            'product_sku' => 'required|max:255|unique:products,product_sku',
            'product_title' => 'required|max:255|unique:products,product_title',
            'product_gst' => 'required|numeric|min:0',
            'product_header_image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:4096',
            'product_images' => 'required|array|min:1',
            'product_images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:4096',
            'amazon_link' => 'nullable|url|max:500',
        ], $this->productImageValidationMessages());

        $input = $request->all();
        /*$this->validate($request , array
        (    
            'product_title' => 'required|max:255|unique:products',
            'product_sku' => 'required|max:255|unique:products',
        )); */
        $storedImages = [];
        try {
            $headerUpload = $this->storeProductImage($this->uploadedFile($request, 'product_header_image'));
            $input['product_header_image'] = $headerUpload['path'];
            if (Schema::hasColumn('products', 'product_header_image_public_id')) {
                $input['product_header_image_public_id'] = $headerUpload['public_id'];
            }
            $storedImages[] = $headerUpload;

            $product_image = [];
            foreach($this->uploadedFiles($request, 'product_images') as $image)
            {
                if (!$image || !$image->isValid()) {
                    continue;
                }

                $upload = $this->storeProductGalleryImage($image);
                $storedImages[] = $upload;
                $product_image[] = $upload;
            }
        } catch (\Exception $e) {
            \Log::error('Product image upload failed while creating product.', [
                'message' => $e->getMessage(),
                'product_title' => $request->input('product_title'),
                'has_header_file' => $request->hasFile('product_header_image'),
                'gallery_count' => is_array($request->file('product_images')) ? count($request->file('product_images')) : 0,
            ]);
            $this->deleteProductImages($storedImages);
            $request->session()->flash('alert-danger','Product images could not be uploaded: '.$e->getMessage());
            return Redirect::back()->withInput();
        }
        
        $cmsObj = new Product();
        $input['slug'] = BasicHelper::getproductSlug($cmsObj, $request->product_title);
        $input['product_images'] = json_encode($this->galleryImagePathList($product_image));
        
        $input['category'] = json_encode($request->category);
      
        
        unset($input['attribute_name']);
        unset($input['attribute_value']);
        
        try {
            DB::beginTransaction();

            $productInput = $this->filterTableInput('products', $input);
            Product::insert($productInput);
            
            $pro_id = DB::getPdo()->lastInsertId();
            $this->syncProductGalleryImages($pro_id, $product_image);
            
            $meta_tag['meta_title'] = $request->meta_title;
            $meta_tag['meta_keyword'] = $request->meta_keyword;
            $meta_tag['meta_description'] = $request->meta_description;
            $meta_tag['meta_page'] = $request->product_title;
            $meta_tag['meta_slug'] = $input['slug'];
            DB::table('meta_tags')->insert($this->filterTableInput('meta_tags', $meta_tag));
            
            $product_que['product_id'] = $pro_id;
            
            $attribute_name = $request->attribute_name ?? [];
            $att_val1 = $request->attribute_val1 ?? [];
            $att_val2 = $request->attribute_val2 ?? [];
            $att_val3 = $request->attribute_val3 ?? [];
            $att_val4 = $request->attribute_val4 ?? [];
            $att_val5 = $request->attribute_val5 ?? [];

            $count = count($attribute_name);

            for($i=0;$i<$count;$i++)
            {
                $att_values = array_filter([
                    $att_val1[$i] ?? '',
                    $att_val2[$i] ?? '',
                    $att_val3[$i] ?? '',
                    $att_val4[$i] ?? '',
                    $att_val5[$i] ?? '',
                ]);
                $att_values = array_values($att_values);
                if(empty($att_values)) continue;
                $product_que['product_id'] = $pro_id;
                $product_que['attribute_value'] = json_encode($att_values);
                $product_que['attribute_name'] = $attribute_name[$i];
                DB::table('product_attributes')->insert($this->filterTableInput('product_attributes', $product_que));
            }
            $pro_ats =  DB::table('product_attributes')->where('product_id',$pro_id)->get();
            $atts = [];
            foreach($pro_ats as $dt)
            {
                $at_name = $dt->attribute_name;
                $at_values = $dt->attribute_value;
                $a_vals = json_decode($at_values);
                foreach($a_vals as $dt)
                {
                    $at_s[] = $at_name.":".$dt;
                }
                $atts[] = $at_s;
                unset($at_s);
            }
            
            $arrSize = $this->get_combinations($atts);
            
            foreach($arrSize as $dat)
            {
                $inputs['product_id'] = $pro_id;
                $inputs['attributes_value'] = json_encode($dat);
                $inputs['product_quantity'] = 0;
                $inputs['rupee_price'] = 0;
                $inputs['dollar_price'] = 0;
                $inputs['euro_price'] = 0;
                $inputs['product_discount'] = 0;
                $inputs['rupee_net_amount'] = 0;
                $inputs['product_weight'] = 0;
                
                DB::table('product_quantity')->insert($inputs);
                    
            }

            DB::commit();
        } catch (\Exception $e) {
            \Log::error('Product create transaction failed.', [
                'message' => $e->getMessage(),
                'product_title' => $request->input('product_title'),
            ]);
            DB::rollBack();
            $this->deleteProductImages($storedImages);
            $request->session()->flash('alert-danger','Product could not be saved: '.$e->getMessage());
            return Redirect::back()->withInput();
        }
        
        
  /*      
        echo "<pre>";
        print_r($arrSize);
        echo "</pre>";*/
        
        // Product Quantity Table // 
        
        
       
        
        $request->session()->flash('alert-success','Product has been sucessfully added.');
        return Redirect::route('product_list');
         
    }
    
    // For Product Arrtibutes Combination Function  // 
    public function get_combinations($arrays) 
    {
        $result = array();
        $arrays = array_values($arrays);
        $sizeIn = sizeof($arrays);
        $size = $sizeIn > 0 ? 1 : 0;
        foreach ($arrays as $array)
            $size = $size * sizeof($array);
        for ($i = 0; $i < $size; $i ++)
        {
            $result[$i] = array();
            for ($j = 0; $j < $sizeIn; $j ++)
                array_push($result[$i], current($arrays[$j]));
            for ($j = ($sizeIn -1); $j >= 0; $j --)
            {
                if (next($arrays[$j]))
                    break;
                elseif (isset ($arrays[$j]))
                    reset($arrays[$j]);
            }
        }
        return $result;
    }
    
    
    public function product_listing(Request $request)
    {
        if (!Schema::hasTable('products')) {
            $data['product_data'] = collect([]);
            $request->session()->flash('alert-warning', 'Product table is missing. Please run database migrations on Hostinger.');
        } else {
            $data['product_data'] = $this->productListQuery()
                ->orderBy('products.product_id', 'desc')
                ->get();
        }
        
        
        
         $page_title = "Product List - Zouple";
         $cateName = [];
         $categories = Category::select('category_id', 'title')->get();
         foreach ($categories as $category) {
             $cateName[$category->category_id] = $category->title;
         }
         
        /* return $data;*/
         return view('masters.product.product_list',compact('page_title', 'cateName'),$data);
    }

    public function productshowdetail(Request $request, $product_id)
    {
        $data['product_show_data'] = AdminRecycleBinService::activeTable('products')
            ->where('product_id',$product_id)
            ->get();
        
        $vender_id = AdminRecycleBinService::activeTable('products')
            ->where('product_id',$product_id)
            ->value('vendor_id');
        
        $vendorname = AdminRecycleBinService::activeTable('vendors')
            ->where('vendor_id',$vender_id)
            ->value('vendor_name');
        
        
        
        


        /*$data['product_show_data'] = DB::table('products')
            ->join('vendors', 'vendors.vendor_id', '=', 'products.vendor_id')
            ->select('products.*','vendors.vendor_name')
            ->orderBy('products.product_id', 'DESC')
            ->get();*/


         $page_title = "Product Show - Zouple";
        $data['product_gallery_images'] = [];
        foreach ($data['product_show_data'] as $productShow) {
            $data['product_gallery_images'][$productShow->product_id] = $this->getProductGalleryImages($productShow->product_id, $productShow->product_images);
        }
        return view('masters.product.product_show',compact('page_title' , 'vendorname'),$data);    
    }

    public function productUpdate(Request $request, $product_id)
    {
        $data['product_update_data'] = AdminRecycleBinService::activeTable('products')
             ->where('products.product_id',$product_id)
            ->orderBy('products.product_id', 'DESC')
            ->get();
        $data['product_gallery_images'] = [];
        foreach ($data['product_update_data'] as $productUpdate) {
            $data['product_gallery_images'][$productUpdate->product_id] = $this->getProductGalleryImages($productUpdate->product_id, $productUpdate->product_images);
        }



        $data['product_attributes_data'] = DB::table('product_attributes')
             ->where('product_id',$product_id)
            ->orderBy('product_id', 'DESC')
            ->get();


        $data['attribute_data'] = DB::table('attribute')->orderby('attribute_id', 'asc')->get();

        $data['vendors_data'] = AdminRecycleBinService::activeTable('vendors')->orderby('vendor_id', 'asc')->get();
        
        $data['cate_data'] = AdminRecycleBinService::activeTable('categorys')->where('parent_id',0)->where('is_active','ACTIVE')->get();
       
        
        $categories = Category::where('parent_id', '=', 0)->where('is_active','ACTIVE')->get();

        $data['attribute_data'] = DB::table('attribute')->orderby('attribute_id', 'asc')->get();
        
        /*$data['free_ass'] = Accessories::all();*/


         $page_title = "Product Update - Zouple";
         return view('masters.product.product_edit',compact('page_title','categories'),$data);
    }

    public function product_update_save(Request $request)
    {
        $this->recoverProductUploads($request);

        $this->validate($request, [
            'product_id' => 'required',
            'category' => 'required|array',
            'vendor_id' => 'required',
            'product_sku' => 'required|max:255|unique:products,product_sku,' . $request->product_id . ',product_id',
            'product_title' => 'required|max:255',
            'product_gst' => 'required|numeric|min:0',
            'product_header_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:4096',
            'product_images' => 'nullable|array',
            'product_images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:4096',
            'remove_gallery_images' => 'nullable|array',
            'remove_gallery_images.*' => 'string|max:500',
            'replace_gallery_images' => 'nullable|in:1',
            'amazon_link' => 'nullable|url|max:500',
        ], $this->productImageValidationMessages());

        $input = $request->all();
        $product_id = $request->product_id;
        $newUploadedImages = [];
        $oldImagesToDelete = [];
        $currentProductImages = DB::table('products')->where('product_id', $product_id)->value('product_images');
        $currentGalleryImages = $this->getProductGalleryImages($product_id, $currentProductImages);
        $removeGalleryImages = [];
        foreach ((array) $request->input('remove_gallery_images', []) as $removeImage) {
            $removeGalleryImages[] = $this->normalizeProductImageReference($removeImage);
        }
        $replaceGallery = $request->input('replace_gallery_images') === '1';

        try {
        $headerFile = $this->uploadedFile($request, 'product_header_image');
        if($headerFile && $headerFile->isValid())
        {
            $product_header_image = $request->product_header_image_old;
            $headerUpload = $this->storeProductImage($headerFile);
            $input['product_header_image']= $headerUpload['path'];
            if (Schema::hasColumn('products', 'product_header_image_public_id')) {
                $input['product_header_image_public_id'] = $headerUpload['public_id'];
            }
            $newUploadedImages[] = $headerUpload;
            if (!empty($product_header_image)) {
                $oldImagesToDelete[] = $product_header_image;
            }
        } 

        $galleryFiles = $this->uploadedFiles($request, 'product_images');
        if(count($galleryFiles) > 0 || !empty($removeGalleryImages) || $replaceGallery)
        {
            $product_image = [];
            foreach ($currentGalleryImages as $image) {
                $image = $this->normalizeProductImageReference($image);
                if ($image === '') {
                    continue;
                }

                if ($replaceGallery || in_array($image, $removeGalleryImages)) {
                    $oldImagesToDelete[] = $image;
                    continue;
                }

                $product_image[] = $image;
            }

            if(count($galleryFiles) > 0)
            {
            foreach($galleryFiles as $image)
            {
                if (!$image || !$image->isValid()) {
                    continue;
                }

                $upload = $this->storeProductGalleryImage($image);
                $newUploadedImages[] = $upload;
                $product_image[] = $upload;
            }
            }

            $input['product_images'] = json_encode($this->galleryImagePathList($product_image));
        }
        } catch (\Exception $e) {
            \Log::error('Product image upload failed while updating product.', [
                'message' => $e->getMessage(),
                'product_id' => $product_id,
                'has_header_file' => $request->hasFile('product_header_image'),
                'gallery_count' => is_array($request->file('product_images')) ? count($request->file('product_images')) : 0,
            ]);
            $this->deleteProductImages($newUploadedImages);
            $request->session()->flash('alert-danger','Product images could not be uploaded: '.$e->getMessage());
            return Redirect::back()->withInput();
        }
        
        $cmsObj = new Product();
        $input['slug'] = BasicHelper::getproductSlug($cmsObj, $request->product_title);

        $input['category'] = json_encode($request->category);
        
        
        /*DB::table('product_quantity')->where('product_id','=',$product_id)->delete();*/
        
        try {
            DB::beginTransaction();

            DB::table('product_attributes')->where('product_id',$product_id)->delete();
            
            unset($input['product_images_old']);
            unset($input['product_header_image_old']);
            unset($input['remove_gallery_images']);
            unset($input['replace_gallery_images']);
            if (!Schema::hasColumn('products', 'product_header_image_public_id')) {
                unset($input['product_header_image_public_id']);
            }
            
            unset($input['attribute_name']);
            unset($input['attribute_value']);
            unset($input['attribute_val1']);
            unset($input['attribute_val2']);
            unset($input['attribute_val3']);
            unset($input['attribute_val4']);
            unset($input['attribute_val5']);

            
            $productInput = $this->filterTableInput('products', $input);
            Product::where('product_id',$product_id)->update($productInput);
            if (isset($product_image)) {
                $this->syncProductGalleryImages($product_id, $product_image);
            }
            
            
            $product_que['product_id'] = $product_id;
            
            $attribute_name = $request->attribute_name ?? [];
            $att_val1 = $request->attribute_val1 ?? [];
            $att_val2 = $request->attribute_val2 ?? [];
            $att_val3 = $request->attribute_val3 ?? [];
            $att_val4 = $request->attribute_val4 ?? [];
            $att_val5 = $request->attribute_val5 ?? [];

            $count = count($attribute_name);

            for($i=0;$i<$count;$i++)
            {
                $att_values = array_filter([
                    $att_val1[$i] ?? '',
                    $att_val2[$i] ?? '',
                    $att_val3[$i] ?? '',
                    $att_val4[$i] ?? '',
                    $att_val5[$i] ?? '',
                ]);
                $att_values = array_values($att_values);
                if(empty($att_values)) continue;
                $product_que['product_id'] = $product_id;
                $product_que['attribute_value'] = json_encode($att_values);
                $product_que['attribute_name'] = $attribute_name[$i];
                DB::table('product_attributes')->insert($this->filterTableInput('product_attributes', $product_que));
            }
            
            
            $pro_ats =  DB::table('product_attributes')->where('product_id',$product_id)->get();
            $atts = [];
            foreach($pro_ats as $dt)
            {
                $at_name = $dt->attribute_name;
                $at_values = $dt->attribute_value;
                $a_vals = json_decode($at_values);
                foreach($a_vals as $dt)
                {
                    $at_s[] = $at_name.":".$dt;
                }
                $atts[] = $at_s;
                unset($at_s);
            }
            
            $arrSize = $this->get_combinations($atts);
            
            $newList = [];
            foreach($arrSize as $dat)
            {
                $inputs['product_id'] = $product_id;
                $inputs['attributes_value'] = json_encode($dat);
                $inputs['product_quantity'] = 0;
                $inputs['rupee_price'] = 0;
                $inputs['dollar_price'] = 0;
                $inputs['euro_price'] = 0;
                $inputs['product_discount'] = 0;
                $inputs['rupee_net_amount'] = 0;
                $inputs['product_weight'] = 0;
                
                $check_data = DB::table('product_quantity')->where('product_id',$product_id)->where('attributes_value',json_encode($dat))->value('product_quantity_id');
                
                $newList[] = json_encode($dat);
                
                
                if($check_data > 0)
                {
                    /*echo "Allready";*/
                }
                else
                {
                    DB::table('product_quantity')->insert($inputs);
                }   
                
                //DB::table('product_quantity')->insert($inputs);
                 
                //DB::table('product_attributes')->where('product_id',$product_id)->delete();  
            }

            $check_new_data = DB::table('product_quantity')->where('product_id',$product_id)->get();
            foreach($check_new_data as $nData)
            {
                $pro_q_id = $nData->product_quantity_id;
                $pro_att = $nData->attributes_value;
                if(in_array($pro_att, $newList)) 
                {
                    
                }
                else
                {
                    DB::table('product_quantity')->where('product_quantity_id',$pro_q_id)->delete();  
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            \Log::error('Product update transaction failed.', [
                'message' => $e->getMessage(),
                'product_id' => $product_id,
            ]);
            DB::rollBack();
            $this->deleteProductImages($newUploadedImages);
            $request->session()->flash('alert-danger','Product could not be updated: '.$e->getMessage());
            return Redirect::back()->withInput();
        }

        $this->deleteProductImages($oldImagesToDelete);
        
        $request->session()->flash('alert-success','Product has been successfully Updated !!');
        return Redirect::route('product_list');
         
    }

    public function productDelete(Request $request , $product_id)
    {
      AdminRecycleBinService::softDelete('products', $product_id);
      $request->session()->flash('alert-success','Product moved to Recycle Bin successfully.');
      return Redirect::route('product_list');   
    }
    
    public function productQuantityUpdate(Request $request,$product_id)
    {   
         $page_title = "Product Quantity Update - Zouple";
         $data['products'] = AdminRecycleBinService::activeTable('products')->where('product_id',$product_id)->get();
         $data['pro_attributs'] = DB::table('product_quantity')->where('product_id',$product_id)->get();
         return view('masters.product.product_quantity_update',compact('page_title'),$data);
    }
    
    public function updateProductGSTStore(Request $request)
    {   
        
         $input=$request->all(); 
        $product_id = $request->product_id;    
        DB::table('products')->where('product_id',$product_id)->update($input); 
        $request->session()->flash('alert-success','Proudct GST Data Updated Successfully!!');     
        return redirect()->back(); 
    }
    
    
    
    public function flaceSalesPage(Request $request,$product_id)
    {   
         $page_title = "Product Flace Sale  - Zouple";
         $data['productss'] = AdminRecycleBinService::activeTable('products')->where('product_id',$product_id)->get();
         $data['flash_data'] = DB::table('flash_sale')->where('flash_sale_id',1)->get();
         $data['pro_attributss'] = DB::table('product_quantity')->where('product_id',$product_id)->get();
         
         return view('masters.product.product_flash_sale',compact('page_title'),$data);
    }
    
    public function flashProductStore(Request $request)
    {
        /*$input = $request->all();*/
        $product_id = $request->product_id;
        $product_quantity_id = $request->product_quantity_id;
        $price = $request->price;
        $dollar = $request->dollar;
        $euro = $request->euro;
        $attributes_value = $request->attributes_value;
        $product_quantity_id = $request->product_quantity_id;
        $count = sizeof($attributes_value);
        for($i=0;$i<$count;$i++)
        {
             $pro_details[$product_quantity_id[$i]] = $attributes_value[$i].",".$price[$i];
             $doll_details[$product_quantity_id[$i]] = $attributes_value[$i].",".$dollar[$i];
             $eu_details[$product_quantity_id[$i]] = $attributes_value[$i].",".$euro[$i];
        }
       $product_details[$i] = json_encode($pro_details);
       $dollar_details[$i] = json_encode($doll_details);
       $euro_details[$i] = json_encode($eu_details);
        
        
        $input['product_prize'] = $product_details[$i];
        $input['dollar_prize'] = $dollar_details[$i];
        $input['euro_prize'] = $euro_details[$i];
        $input['product_id'] = $product_id;
        $input['start_date'] = $request->start_date;
        $input['start_time'] = $request->start_time;
        $input['end_time'] = $request->end_time;
        $input['end_date'] = $request->end_date;
        $input['flash_active'] = $request->flash_active;
        $input['_token'] = $request->_token;
         DB::table('flash_sale')->where('flash_sale_id', 1)->update($input);
        $request->session()->flash('alert-success','Flash Product has been Updated Successfully!!');
        
        
        return Redirect::route('product_list');  
    }
    
    
    public function viewFlashPage(Request $request)
    {   
        $data['view_flash_data'] = DB::table('flash_sale')
            ->join('products', 'products.product_id', '=', 'flash_sale.product_id')
            ->where('flash_sale.flash_active', 'ACTIVE')
            
            ->get();
        $page_title = "Flash product List - Zouple";
        return view('masters.product.view_flash',compact('page_title'),$data);
    }
    
    
    
    
    public function updateProductQuantityStore(Request $request)
    {
        $this->validate($request, [
            'product_quantity_id' => 'required|array|min:1',
            'product_quantity_id.*' => 'required|integer',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|numeric|min:0',
            'price' => 'required|array|min:1',
            'price.*' => 'required|numeric|min:0',
            'discount' => 'required|array|min:1',
            'discount.*' => 'required|numeric|min:0|max:100',
            'weight' => 'required|array|min:1',
            'weight.*' => 'required|numeric|min:0',
        ]);

        $qty = (array) $request->qty;
        $product_quantity_id = (array) $request->product_quantity_id;
        $discount = (array) $request->discount;
        $weight = (array) $request->weight;
        $price = (array) $request->price;
        $pro_id = DB::table('product_quantity')->where('product_quantity_id', $product_quantity_id[0])->value('product_id');
        if (!$pro_id) {
            $request->session()->flash('alert-danger','Product quantity rows were not found. Please open the product again and retry.');
            return Redirect::back();
        }
        $gst = DB::table('products')->where('product_id',$pro_id)->value('product_gst');
        
        $count = count($product_quantity_id);
        for($i=0; $i<$count; $i++)
        {
            if (!isset($qty[$i], $price[$i], $discount[$i], $weight[$i])) {
                continue;
            }

            $update = CurrencyHelper::pricesFromRupee($price[$i], $discount[$i], $gst ?: 0);
            $update['product_quantity'] = $qty[$i];
            $update['product_discount'] = $discount[$i];
            $update['product_weight'] = $weight[$i];

            DB::table('product_quantity')->where('product_quantity_id',$product_quantity_id[$i])->update($update);
        }
        $request->session()->flash('alert-success','Product quantity and prices have been successfully updated.');
        return Redirect::route('product_list');
        
    }
    
    
    
    /////////////////////earthly
    
    public function product_quantity_update_save(Request $request)
    {
        $input = $request->all();
        
        DB::table('products')->where('product_id',$input['product_id'])->update($input);
        $request->session()->flash('alert-success','Product Quantity has been sucessfully updated.
');
        return Redirect::route('product_list');
    }
    
    public function product_inactive_update(Request $request)
    {
        $input = $request->all();
        
        $check = DB::table('products')->where('product_id',$input['product_id'])->update($input);
        
        /*echo $check;*/
        $request->session()->flash('alert-success','Product Status has been sucessfully updated.
');
        return Redirect::route('product_list');
    }
    
    public function product_price_update_save(Request $request)
    {
        $input = $request->all();
        $price = $request->product_price;
        $product_discount = $request->product_discount;
        $net_amount = $price - ($price*$product_discount/100);
        $input['net_amount'] = $net_amount;
        DB::table('products')->where('product_id',$input['product_id'])->update($input);
        $request->session()->flash('alert-success','Product has been sucessfully updated.
');
        return Redirect::route('product_list');
    }

    private function productImageValidationMessages()
    {
        return [
            'product_header_image.required' => 'Please upload a product header image.',
            'product_header_image.image' => 'The product header file must be a valid image.',
            'product_header_image.mimes' => 'Please upload JPG, PNG, GIF, or WebP image only for the product header.',
            'product_header_image.max' => 'The product header image must be 4 MB or smaller.',
            'product_images.required' => 'Please upload at least one product gallery image.',
            'product_images.array' => 'Please upload product gallery images correctly.',
            'product_images.min' => 'Please upload at least one product gallery image.',
            'product_images.*.image' => 'Every product gallery file must be a valid image.',
            'product_images.*.mimes' => 'Product gallery images must be JPG, PNG, GIF, or WebP only.',
            'product_images.*.max' => 'Each product gallery image must be 4 MB or smaller.',
        ];
    }

    private function filterTableInput($table, array $input)
    {
        if (!Schema::hasTable($table)) {
            return $input;
        }

        $columns = array_flip(Schema::getColumnListing($table));

        return array_intersect_key($input, $columns);
    }

    private function makeProductImageName($filename)
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $safeName = trim(preg_replace('/[^A-Za-z0-9_-]+/', '-', $name), '-');

        return time() . '_' . mt_rand(100000, 999999) . '_' . ($safeName ?: 'product') . '.' . $extension;
    }

    private function recoverProductUploads(Request $request)
    {
        $this->recoverRawFile($request, 'product_header_image');
        $this->recoverRawFileArray($request, 'product_images');
    }

    private function recoverRawFile(Request $request, $field)
    {
        if ($request->files->get($field) instanceof SymfonyUploadedFile) {
            return;
        }

        if (!isset($_FILES[$field]) || !is_array($_FILES[$field])) {
            return;
        }

        $file = $this->uploadedFileFromRaw($_FILES[$field]);
        if ($file) {
            $request->files->set($field, $file);
        }
    }

    private function recoverRawFileArray(Request $request, $field)
    {
        $existing = $request->files->get($field);
        if (is_array($existing) && count($existing) > 0) {
            return;
        }

        if (!isset($_FILES[$field]) || !is_array($_FILES[$field]) || !isset($_FILES[$field]['name']) || !is_array($_FILES[$field]['name'])) {
            return;
        }

        $files = [];
        foreach ($_FILES[$field]['name'] as $index => $name) {
            $raw = [
                'name' => $name,
                'type' => isset($_FILES[$field]['type'][$index]) ? $_FILES[$field]['type'][$index] : null,
                'tmp_name' => isset($_FILES[$field]['tmp_name'][$index]) ? $_FILES[$field]['tmp_name'][$index] : null,
                'error' => isset($_FILES[$field]['error'][$index]) ? $_FILES[$field]['error'][$index] : UPLOAD_ERR_NO_FILE,
                'size' => isset($_FILES[$field]['size'][$index]) ? $_FILES[$field]['size'][$index] : 0,
            ];

            $file = $this->uploadedFileFromRaw($raw);
            if ($file) {
                $files[] = $file;
            }
        }

        if (!empty($files)) {
            $request->files->set($field, $files);
        }
    }

    private function uploadedFileFromRaw(array $raw)
    {
        $error = isset($raw['error']) ? (int) $raw['error'] : UPLOAD_ERR_NO_FILE;
        $tmpName = isset($raw['tmp_name']) ? $raw['tmp_name'] : null;
        $originalName = isset($raw['name']) ? $raw['name'] : 'upload';
        $mimeType = isset($raw['type']) ? $raw['type'] : null;

        if ($error === UPLOAD_ERR_OK && $tmpName && is_file($tmpName)) {
            return new SymfonyUploadedFile($tmpName, $originalName, $mimeType, $error, true);
        }

        return null;
    }

    private function uploadedFile(Request $request, $field)
    {
        $file = $request->files->get($field);
        return $file instanceof SymfonyUploadedFile ? $file : null;
    }

    private function uploadedFiles(Request $request, $field)
    {
        $files = $request->files->get($field);
        $files = is_array($files) ? $files : [];

        return array_values(array_filter($files, function ($file) {
            return $file instanceof SymfonyUploadedFile;
        }));
    }

    private function storeProductImage($file)
    {
        return app(AdminMediaService::class)->uploadImage($file, 'products/header', 'product');
    }

    private function storeProductGalleryImage($file)
    {
        $upload = app(AdminMediaService::class)->uploadImage($file, 'products/gallery', 'product/gallery');
        if (empty($upload['cloudinary'])) {
            $upload['path'] = 'gallery/' . basename($upload['path']);
        }

        return $upload;
    }

    private function getProductGalleryImages($productId, $legacyJson = null)
    {
        $images = [];

        if ($productId && Schema::hasTable('product_gallery_images')) {
            $rows = ProductGalleryImage::where('product_id', $productId)
                ->orderBy('sort_order', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($rows as $row) {
                $images[] = isset($row->image_url) && $row->image_url ? $row->image_url : $row->image;
            }
        }

        if (empty($images) && $legacyJson) {
            $legacyImages = json_decode($legacyJson, true);
            $images = is_array($legacyImages) ? $legacyImages : [];
        }

        $cleanImages = [];
        foreach ($images as $image) {
            $image = $this->normalizeProductImageReference($image);
            if ($image !== '') {
                $cleanImages[] = $image;
            }
        }

        return array_values(array_unique($cleanImages));
    }

    private function syncProductGalleryImages($productId, array $images)
    {
        $cleanImages = [];
        $records = [];
        foreach ($images as $image) {
            $publicId = null;
            if (is_array($image)) {
                $publicId = isset($image['public_id']) ? $image['public_id'] : null;
                $image = isset($image['path']) ? $image['path'] : '';
            }

            $image = $this->normalizeProductImageReference($image);
            if ($image === '') {
                continue;
            }

            if ($publicId === null && Schema::hasTable('product_gallery_images')) {
                $oldRow = ProductGalleryImage::where('product_id', $productId)
                    ->where(function ($query) use ($image) {
                        $query->where('image', $image);
                        if (Schema::hasColumn('product_gallery_images', 'image_url')) {
                            $query->orWhere('image_url', $image);
                        }
                    })
                    ->first();
                $publicId = $oldRow && isset($oldRow->public_id) ? $oldRow->public_id : null;
            }

            $cleanImages[] = $image;
            $records[] = [
                'image' => $image,
                'public_id' => $publicId,
            ];
        }

        $cleanImages = array_values(array_unique($cleanImages));

        if (Schema::hasTable('product_gallery_images')) {
            ProductGalleryImage::where('product_id', $productId)->delete();
            foreach ($records as $sortOrder => $record) {
                if (!in_array($record['image'], $cleanImages, true)) {
                    continue;
                }

                $row = [
                    'product_id' => $productId,
                    'image' => $record['image'],
                    'sort_order' => $sortOrder,
                ];

                if (Schema::hasColumn('product_gallery_images', 'image_url')) {
                    $row['image_url'] = $record['image'];
                }
                if (Schema::hasColumn('product_gallery_images', 'public_id')) {
                    $row['public_id'] = $record['public_id'];
                }

                ProductGalleryImage::create($row);
            }
        }

        DB::table('products')->where('product_id', $productId)->update([
            'product_images' => json_encode($cleanImages),
        ]);
    }

    private function normalizeProductImageReference($filename)
    {
        $filename = trim(str_replace('\\', '/', (string) $filename));
        if ($filename === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $filename)) {
            return $filename;
        }

        if (strpos($filename, 'gallery/') === 0) {
            return 'gallery/' . basename($filename);
        }

        return basename($filename);
    }

    private function deleteProductImages($filenames)
    {
        foreach ((array) $filenames as $filename) {
            $publicId = null;
            if (is_array($filename)) {
                $publicId = isset($filename['public_id']) ? $filename['public_id'] : null;
                $filename = isset($filename['path']) ? $filename['path'] : '';
            }

            $filename = $this->normalizeProductImageReference($filename);
            if ($filename === '') {
                continue;
            }

            if ($publicId === null && Schema::hasTable('product_gallery_images')) {
                $row = ProductGalleryImage::where('image', $filename);
                if (Schema::hasColumn('product_gallery_images', 'image_url')) {
                    $row->orWhere('image_url', $filename);
                }
                $row = $row->first();
                $publicId = $row && isset($row->public_id) ? $row->public_id : null;
            }

            app(AdminMediaService::class)->deleteMedia($filename, 'product', $publicId, 'image');
        }
    }

    private function galleryImagePathList(array $images)
    {
        $paths = [];
        foreach ($images as $image) {
            if (is_array($image)) {
                $image = isset($image['path']) ? $image['path'] : '';
            }
            $image = $this->normalizeProductImageReference($image);
            if ($image !== '') {
                $paths[] = $image;
            }
        }

        return array_values(array_unique($paths));
    }


    
    
    /* ------------------------------------ Product Quentity Code End ------------------------------------ */
    
    
    /* Review Code Start */
    
    public function review_information_list(Request $request)
    {
        $data['review_data'] = AdminRecycleBinService::activeTable('review')
            ->join('products', 'products.product_id', '=', 'review.product_id')
            ->whereNull('products.deleted_at')
            ->select('review.*', 'products.product_title')
            ->orderBy('review.review_id', 'DESC')
            ->get(); 
         $page_title = "Review List - Zouple";
         return view('masters.product.review_list',compact('page_title'),$data);
    }
    
    public function addReviewInformationPage (Request $request)
    {
        $data['productsData'] = Product::all();
         $page_title = "Add Review Page - Zouple";
         return view('masters.product.addReview',compact('page_title'),$data);
    }
    
    public function reviewInformationStore(Request $request)
    {
        $input = $request->all();
        $product_image = [];
       
          if($request->file('user_profile')!='')
            {
                $upload = app(AdminMediaService::class)->uploadImage($request->file('user_profile'), 'reviews', 'review');
                $input['user_profile']= $upload['path'];
                if (Schema::hasColumn('review', 'user_profile_public_id')) {
                    $input['user_profile_public_id'] = $upload['public_id'];
                }
            } 
        if($request->file('review_product_image')!='')
            {
                $publicIds = [];
                foreach($request->file('review_product_image') as $image)
                {
                    $upload = app(AdminMediaService::class)->uploadImage($image, 'reviews', 'review');
                    $product_image[] = $upload['path'];
                    $publicIds[] = $upload['public_id'];
                }
                if (Schema::hasColumn('review', 'review_product_image_public_ids')) {
                    $input['review_product_image_public_ids'] = json_encode($publicIds);
                }
            }
        
        $input['review_product_image'] = json_encode($product_image);

        DB::table('review')->insert($input);
        $request->session()->flash('alert-success','Review has been sucessfully added.');
        return Redirect::route('addReviewInformation');   
    }
    
    
    public function reviewInformationUpdatePage(Request $request, $review_id)
    {
        $data['reviewDatass'] = AdminRecycleBinService::activeTable('review')->where('review_id', $review_id)->get(); 
         $page_title = "Review List - Zouple";
        $data['productsData'] = Product::all();
         return view('masters.product.editReviewList',compact('page_title'),$data);
    }
    
    public function reviewInformationEditStore(Request $request)
    {
        $input = $request->all();
        $review_id = $request->review_id;
        if($request->file('user_profile')!='')
        {
            $user_profile = DB::table('review')->where('review_id','=',$review_id)->value('user_profile');
            $publicId = Schema::hasColumn('review', 'user_profile_public_id')
                ? DB::table('review')->where('review_id', $review_id)->value('user_profile_public_id')
                : null;
            app(AdminMediaService::class)->deleteMedia($user_profile, 'review', $publicId, 'image');
            $upload = app(AdminMediaService::class)->uploadImage($request->file('user_profile'), 'reviews', 'review');
            $input['user_profile']= $upload['path'];
            if (Schema::hasColumn('review', 'user_profile_public_id')) {
                $input['user_profile_public_id'] = $upload['public_id'];
            }
        } 
        
        if($request->file('review_product_image')!='')
        { 
          $product_images = DB::table('review')->where('review_id','=',$review_id)->value('review_product_image');
          if($product_images != "")
          {
            $backimages=json_decode($product_images);
            $backPublicIds = Schema::hasColumn('review', 'review_product_image_public_ids')
                ? json_decode(DB::table('review')->where('review_id', $review_id)->value('review_product_image_public_ids'), true)
                : [];
            $backPublicIds = is_array($backPublicIds) ? $backPublicIds : [];
            $idx = 0;
              foreach($backimages as $image)
              {
                app(AdminMediaService::class)->deleteMedia($image, 'review', isset($backPublicIds[$idx]) ? $backPublicIds[$idx] : null, 'image');
                $idx++;
              }
          }
          
            $publicIds = [];
            foreach($request->file('review_product_image') as $image)
            {
                $upload = app(AdminMediaService::class)->uploadImage($image, 'reviews', 'review');
                $product_image[] = $upload['path'];
                $publicIds[] = $upload['public_id'];
            }
            $input['review_product_image'] = json_encode($product_image);
            if (Schema::hasColumn('review', 'review_product_image_public_ids')) {
                $input['review_product_image_public_ids'] = json_encode($publicIds);
            }
        }
        
        
        DB::table('review')->where('review_id','=',$review_id)->update($input);
        
        $request->session()->flash('alert-success','Review has been successfully Updated !!');
        return Redirect::route('review_information');
         
    }
    

    public function review_status_update_save(Request $request)
    {
        $input = $request->all();
        DB::table('review')->where('review_id',$input['review_id'])->update($input);
        $request->session()->flash('alert-success','Review Updated !!');
        return Redirect::route('review_information');
    }

    public function reviewDelete(Request $request , $review_id)
    {
      AdminRecycleBinService::softDelete('reviews', $review_id);
      $request->session()->flash('alert-success','Review moved to Recycle Bin successfully.');
      return Redirect::route('review_information');   
    }
    
    
    /* Review Code End */





  /* Export Code */

    public function export() 
    {
        return Excel::download(new UsersExport, 'Products.xlsx');
    }

    public function filterProductPage(Request $request)
    {
        $data['cate_data'] = AdminRecycleBinService::activeTable('categorys')->where('parent_id',0)->where('is_active','ACTIVE')->get();
       
        $categories = Category::where('parent_id', '=', 0)->where('is_active','ACTIVE')->get();
        $data['attribute_data'] = DB::table('attribute')->orderby('attribute_id', 'asc')->get();

        $data['vendors_data'] = AdminRecycleBinService::activeTable('vendors')->orderby('vendor_id', 'asc')->get();

       
       $page_title = "Filter Product - Zouple";
       return view('masters.product.filter_product',compact('page_title','categories'),$data);
    }

    public function filterProductStore(Request $request)
    {
        $page_title = "Filter Product - Zouple";
       $category = (array) $request->input('category', []);

       if (empty($category)) {
           $request->session()->flash('alert-warning','Please select at least one category to filter products.');
           return Redirect::back();
       }

       if (!Schema::hasTable('products')) {
           $request->session()->flash('alert-warning','Product table is missing. Please run database migrations on Hostinger.');
           return Redirect::back();
       }

       $data['product_data'] = $this->productListQuery()
           ->where(function ($query) use ($category) {
               foreach ($category as $cat) {
                   $query->orWhere('products.category', 'LIKE', '%"'.$cat.'"%');
               }
           })
           ->orderBy('products.product_id', 'desc')
           ->get();

       $cateName = [];
       $categories = Category::select('category_id', 'title')->get();
       foreach ($categories as $catData) {
           $cateName[$catData->category_id] = $catData->title;
       }

       return view('masters.product.product_list',compact('page_title', 'cateName'),$data);

    }

    private function productListColumns()
    {
        return $this->productListSelectColumns('product_list_quantity');
    }

    private function productListQuery()
    {
        $query = DB::table('products');
        AdminRecycleBinService::withoutDeleted($query, 'products');

        if (Schema::hasTable('product_quantity')) {
            $query->leftJoin('product_quantity as product_list_quantity', function ($join) {
                $join->on(
                    'product_list_quantity.product_quantity_id',
                    '=',
                    DB::raw('(SELECT MIN(pq.product_quantity_id) FROM product_quantity pq WHERE pq.product_id = products.product_id)')
                );
            });

            return $query->select($this->productListSelectColumns('product_list_quantity'));
        }

        return $query->select($this->productListSelectColumns(null));
    }

    private function productListSelectColumns($quantityAlias = null)
    {
        $columns = [
            'products.*',
        ];

        $quantityColumns = [
            'product_quantity_id' => 'list_product_quantity_id',
            'product_quantity' => 'list_product_quantity',
            'rupee_price' => 'rupee_price',
            'dollar_price' => 'dollar_price',
            'euro_price' => 'euro_price',
            'product_discount' => 'product_discount',
            'rupee_net_amount' => 'rupee_net_amount',
            'rupee_net_with_gst' => 'rupee_net_with_gst',
            'dollar_net_with_gst' => 'dollar_net_with_gst',
            'euro_net_with_gst' => 'euro_net_with_gst',
        ];

        foreach ($quantityColumns as $column => $alias) {
            if ($quantityAlias && Schema::hasColumn('product_quantity', $column)) {
                $columns[] = $quantityAlias . '.' . $column . ' as ' . $alias;
            } else {
                $columns[] = DB::raw('NULL as ' . $alias);
            }
        }

        return $columns;
    }

    public function exportSelctedProductPage(Request $request)
    {
        $pros_id = $request->pro_ids;
       
         return Excel::download(new UsersExport, 'products.xlsx');
    }
    
}
