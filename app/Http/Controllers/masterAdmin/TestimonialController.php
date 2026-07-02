<?php

namespace App\Http\Controllers\masterAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth,Redirect,View,File,Config,Image;
use Validator;
use DB;
use Schema;
use Log;
use App\Services\AdminRecycleBinService;
use App\Services\AdminMediaService;
use App\Services\CloudinaryService;

class TestimonialController extends Controller
{
    const TESTIMONIAL_IMAGE_MAX_KB = 10240;
    const TESTIMONIAL_IMAGE_MAX_MB = 10;

    public function testimonialList(Request $request)
    {
        $users['testimonial_data'] = DB::table('testimonial')->whereNull('deleted_at')->orderBy('testimonial_id', 'asc')->get();
        $page_title = "Testimonial List - Zouple";
       return view('masters.testimonial.testimonial',compact('page_title'),$users);
    }

    public function add_testimonialPage(Request $request)
    {
        
        $page_title = " Add Testimonial - Zouple";
       return view('masters.testimonial.add_testimonial',compact('page_title'));
    }

  public function testimonialStore(Request $request)
   {
      $this->validate($request, [
          'name' => 'required|string|max:255',
          'heading' => 'required|string|max:500',
          'description' => 'required|string|max:10000',
          'image' => 'required|file|mimes:jpeg,jpg,png,gif,webp,bmp|max:' . self::TESTIMONIAL_IMAGE_MAX_KB,
          'platform_logo' => 'nullable|file|mimes:jpeg,jpg,png,svg,webp|max:' . self::TESTIMONIAL_IMAGE_MAX_KB,
      ], [
          'image.required' => 'Please upload a testimonial image.',
          'image.file' => 'The testimonial upload must be a valid file.',
          'image.mimes' => 'Please upload JPG, PNG, GIF, WebP, or BMP image only.',
          'image.max' => 'The testimonial image must be ' . self::TESTIMONIAL_IMAGE_MAX_MB . ' MB or smaller.',
          'platform_logo.mimes' => 'Please upload a JPG, PNG, SVG, or WebP platform logo only.',
          'platform_logo.max' => 'The platform logo must be ' . self::TESTIMONIAL_IMAGE_MAX_MB . ' MB or smaller.',
      ]);

      $input = $this->safeInput($request);
      $input['_token'] = $request->input('_token') ?: md5(uniqid('', true));
       
        /*$this->validate($request , array
         (    
             'image' => 'required|dimensions:min_width=1920,min_height=700',
         ));*/ 
      if($request->hasFile('image') && $request->file('image')->isValid())
      {
          try {
              $input['image'] = $this->storeTestimonialImage($request->file('image'));
          } catch (\Exception $e) {
              Log::error('Testimonial image upload failed.', ['message' => $e->getMessage()]);
              $request->session()->flash('alert-danger','The image could not be saved. Please check the file and try again.');
              return Redirect::back()->withInput();
          }
      }

      if (empty($input['image'])) {
          $request->session()->flash('alert-danger','The image was not saved. Please choose a valid image and try again.');
          return Redirect::back()->withInput();
      }

      // Platform logo is optional and independent from the customer image.
      if($request->hasFile('platform_logo') && $request->file('platform_logo')->isValid())
      {
          try {
              $input['platform_logo'] = $this->storePlatformLogo($request->file('platform_logo'));
          } catch (\Exception $e) {
              Log::error('Testimonial platform logo upload failed.', ['message' => $e->getMessage()]);
              $this->deleteTestimonialImage($input['image']);
              $request->session()->flash('alert-danger','The platform logo could not be saved. Please upload JPG, PNG, SVG, or WebP up to ' . self::TESTIMONIAL_IMAGE_MAX_MB . ' MB.');
              return Redirect::back()->withInput();
          }
      }

      if (!Schema::hasColumn('testimonial', 'platform_logo') && !empty($input['platform_logo'])) {
          $this->deleteTestimonialImage($input['image']);
          $this->deletePlatformLogo($input['platform_logo']);
          $request->session()->flash('alert-danger','Platform logo database column is missing. Run migrations on Hostinger, then try again.');
          return Redirect::back()->withInput();
      }

      $input = $this->onlyExistingColumns('testimonial', $input);

      try {
          DB::table('testimonial')->insert($input);
      } catch (\Exception $e) {
          $this->deleteTestimonialImage($input['image']);
          if (!empty($input['platform_logo'])) {
              $this->deletePlatformLogo($input['platform_logo']);
          }
          Log::error('Testimonial save failed.', ['message' => $e->getMessage(), 'input_keys' => array_keys($input)]);
          $request->session()->flash('alert-danger','The testimonial could not be saved. Please try again.');
          return Redirect::back()->withInput();
      }

      $request->session()->flash('alert-success','Testimonial has been successfully added.');
      return Redirect::route('testimonial');   
   }

   public function testimonialUpdatePage(Request $request,$testimonial_id)
  {
      $users['testimonial_datas'] = AdminRecycleBinService::activeTable('testimonial')->where('testimonial_id',$testimonial_id)->get();
      $page_title = "Edit Testimonial - Zouple";
      return view('masters.testimonial.edit_testimonial',compact('page_title'),$users);
  }

  public function testimonialEditUpdate(Request $request)
    {
      $testimonial_id = $request->testimonial_id;
      $current = DB::table('testimonial')->where('testimonial_id','=',$testimonial_id)->first();
      $currentImage = $current ? $current->image : null;
      $currentPlatformLogo = $current && isset($current->platform_logo) ? $current->platform_logo : null;
      $currentImagePath = public_path('upload/testimonial/').$currentImage;
      $needsImage = empty($currentImage) || (!preg_match('#^https?://#i', $currentImage) && !File::exists($currentImagePath));

      $this->validate($request, [
          'testimonial_id' => 'required|integer',
          'name' => 'required|string|max:255',
          'heading' => 'required|string|max:500',
          'description' => 'required|string|max:10000',
          'image' => ($needsImage ? 'required' : 'nullable') . '|file|mimes:jpeg,jpg,png,gif,webp,bmp|max:' . self::TESTIMONIAL_IMAGE_MAX_KB,
          'platform_logo' => 'nullable|file|mimes:jpeg,jpg,png,svg,webp|max:' . self::TESTIMONIAL_IMAGE_MAX_KB,
          'remove_platform_logo' => 'nullable|in:1',
      ], [
          'image.required' => 'Please upload a testimonial image because this testimonial does not have a saved image.',
          'image.file' => 'The testimonial upload must be a valid file.',
          'image.mimes' => 'Please upload JPG, PNG, GIF, WebP, or BMP image only.',
          'image.max' => 'The testimonial image must be ' . self::TESTIMONIAL_IMAGE_MAX_MB . ' MB or smaller.',
          'platform_logo.mimes' => 'Please upload a JPG, PNG, SVG, or WebP platform logo only.',
          'platform_logo.max' => 'The platform logo must be ' . self::TESTIMONIAL_IMAGE_MAX_MB . ' MB or smaller.',
      ]);

      $input = $this->safeInput($request);
    /*$this->validate($request , array
     (    
         'image' => 'required|dimensions:min_width=1920,min_height=700',
     )); */
      $oldImageToDelete = null;
      if($request->hasFile('image') && $request->file('image')->isValid())
      {
          $data=DB::table('testimonial')->where('testimonial_id','=',$testimonial_id)->value('image');
          try {
              $input['image'] = $this->storeTestimonialImage($request->file('image'));
          } catch (\Exception $e) {
              Log::error('Testimonial image update upload failed.', ['testimonial_id' => $testimonial_id, 'message' => $e->getMessage()]);
              $request->session()->flash('alert-danger','The image could not be saved. Please check the file and try again.');
              return Redirect::back()->withInput();
          }

          if($data && $data !== $input['image']) {
              $oldImageToDelete = $data;
          }
      } 
      
      else
      {
          unset($input['image']);
      }

      $oldLogoToDelete = null;
      if($request->hasFile('platform_logo') && $request->file('platform_logo')->isValid())
      {
          try {
              $input['platform_logo'] = $this->storePlatformLogo($request->file('platform_logo'));
          } catch (\Exception $e) {
              Log::error('Testimonial platform logo update upload failed.', ['testimonial_id' => $testimonial_id, 'message' => $e->getMessage()]);
              if (!empty($input['image'])) {
                  $this->deleteTestimonialImage($input['image']);
              }
              $request->session()->flash('alert-danger','The platform logo could not be saved. Please upload JPG, PNG, SVG, or WebP up to ' . self::TESTIMONIAL_IMAGE_MAX_MB . ' MB.');
              return Redirect::back()->withInput();
          }

          if (!Schema::hasColumn('testimonial', 'platform_logo')) {
              if (!empty($input['image'])) {
                  $this->deleteTestimonialImage($input['image']);
              }
              $this->deletePlatformLogo($input['platform_logo']);
              $request->session()->flash('alert-danger','Platform logo database column is missing. Run migrations on Hostinger, then try again.');
              return Redirect::back()->withInput();
          }

          if($currentPlatformLogo && $currentPlatformLogo !== $input['platform_logo']) {
              $oldLogoToDelete = $currentPlatformLogo;
          }
      }
      elseif($request->input('remove_platform_logo') === '1')
      {
          $input['platform_logo'] = null;
          if ($currentPlatformLogo) {
              $oldLogoToDelete = $currentPlatformLogo;
          }
      }
      else
      {
          unset($input['platform_logo']);
      }

      $input = $this->onlyExistingColumns('testimonial', $input);
      
      try {
          DB::table('testimonial')->where('testimonial_id','=',$testimonial_id)->update($input);
      } catch (\Exception $e) {
          if (!empty($input['image'])) {
              $this->deleteTestimonialImage($input['image']);
          }
          if (!empty($input['platform_logo'])) {
              $this->deletePlatformLogo($input['platform_logo']);
          }
          Log::error('Testimonial update failed.', ['testimonial_id' => $testimonial_id, 'message' => $e->getMessage()]);
          $request->session()->flash('alert-danger','The testimonial could not be updated. Please try again.');
          return Redirect::back()->withInput();
      }

      if ($oldImageToDelete) {
          $this->deleteTestimonialImage($oldImageToDelete);
      }

      if ($oldLogoToDelete) {
          $this->deletePlatformLogo($oldLogoToDelete);
      }

        $request->session()->flash('alert-success','Testimonial has been successfully updated.');
        return Redirect::route('testimonial');
    

    }

    public function testimonialDeleteFormat(Request $request,$testimonial_id)
  {
      AdminRecycleBinService::softDelete('testimonials', $testimonial_id);
      $request->session()->flash('alert-success','Testimonial moved to Recycle Bin.');
      return Redirect::route('testimonial');
  }

  private function safeInput(Request $request)
  {
      $input = array_merge($request->query->all(), $request->request->all());
      unset($input['existing_image']);
      unset($input['existing_platform_logo']);
      unset($input['remove_platform_logo']);
      unset($input['_token']);
      unset($input['testimonial_id']);

      return $input;
  }

  private function onlyExistingColumns($table, array $input)
  {
      if (!Schema::hasTable($table)) {
          return $input;
      }

      $columns = Schema::getColumnListing($table);
      return array_intersect_key($input, array_flip($columns));
  }

  private function makeImageName($filename)
  {
      $name = pathinfo($filename, PATHINFO_FILENAME);
      $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
      $safeName = trim(preg_replace('/[^A-Za-z0-9_-]+/', '-', $name), '-');

      return time() . '_' . mt_rand(100000, 999999) . '_' . ($safeName ?: 'testimonial') . '.' . $extension;
  }

  private function storeTestimonialImage($file)
  {
      $upload = app(AdminMediaService::class)->uploadImage($file, 'testimonials', 'testimonial');
      return $upload['path'];
  }

  private function storePlatformLogo($file)
  {
      $cloudinary = app(CloudinaryService::class);
      if ($cloudinary->isConfigured()) {
          $upload = $cloudinary->uploadImage($file, 'testimonial-logos');
          if (!empty($upload['url'])) {
              return $upload['url'];
          }
      }

      return $this->storeLocalPlatformLogo($file);
  }

  private function storeLocalPlatformLogo($file)
  {
      $directory = public_path('uploads/testimonial-logos');
      if (!File::exists($directory)) {
          File::makeDirectory($directory, 0755, true);
      }

      $filename = $this->makeImageName($file->getClientOriginalName());
      $file->move($directory, $filename);

      return 'uploads/testimonial-logos/' . $filename;
  }

  private function deleteTestimonialImage($filename)
  {
      $filename = trim((string) $filename);
      if ($filename === '') {
          return;
      }

      app(AdminMediaService::class)->deleteMedia($filename, 'testimonial', null, 'image');
  }

  private function deletePlatformLogo($filename)
  {
      $filename = trim((string) $filename);
      if ($filename === '') {
          return;
      }

      $localPath = public_path(ltrim(str_replace('\\', '/', $filename), '/'));
      if (!preg_match('#^https?://#i', $filename) && File::exists($localPath)) {
          File::delete($localPath);
          return;
      }

      app(AdminMediaService::class)->deleteMedia($filename, 'testimonial-logos', null, 'image');
  }
}
