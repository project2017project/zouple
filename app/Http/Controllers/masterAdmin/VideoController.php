<?php

namespace App\Http\Controllers\masterAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth,Redirect,View,File,Config,Image;
use Validator;
use DB;
use App\Services\AdminRecycleBinService;
use App\Services\AdminMediaService;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use Schema;

class VideoController extends Controller
{
    /* Main Video Code Start */
    
    public function mainVideoList(Request $request)
   {
        $this->ensureVideoRow(1);
        $users['video_data'] = AdminRecycleBinService::activeTable('video')->where('video_id', 1)->get();
        $page_title = "Main Video List - Zouple";
       return view('masters.video.mainvideo',compact('page_title'),$users);
   }
    
   public function mainVideoUpdatePage(Request $request)
   {
        $this->ensureVideoRow(1);
        $users['videos_data'] = AdminRecycleBinService::activeTable('video')->where('video_id', 1)->get();
        $page_title = "Main Video List - Zouple";
       return view('masters.video.edit_mainvideo',compact('page_title'),$users);
   }

   public function mainvideoUpdateSaveRedirect()
   {
       return Redirect::route('mainVideoUpdate');
   }
    
    public function mainvideoUpdateStore(Request $request)
    {
      try {
          $this->ensureVideoRow(1);
          $file = $this->safeVideoFile($request);
          if ($file && !$this->isValidVideoFile($file)) {
              $request->session()->flash('alert-danger', 'Please upload a valid MP4, MOV, AVI, WMV, or WebM video up to 100 MB.');
              return Redirect::route('mainVideoUpdate');
          }

          $input = [];
          $currentVideo = DB::table('video')->where('video_id', 1)->value('video');
          if($request->request->has('remove_video'))
          {
              if ($currentVideo) {
                  $this->deleteExistingVideo(1, $currentVideo);
              }
              $input['video'] = '';
              $input['video_public_id'] = null;
              $input['title'] = null;
              $input['description'] = null;
          }
          elseif($file)
          {
              if ($currentVideo) {
                  $this->deleteExistingVideo(1, $currentVideo);
              }
              
              $upload = app(AdminMediaService::class)->uploadVideo($file, 'videos', 'video');
              $input['video']= $upload['path'];
              $input['video_public_id'] = $upload['public_id'];
          } 
          else
          {
               $request->session()->flash('alert-info', $this->missingVideoMessage($request, $currentVideo, 'main'));
               return Redirect::route('mainVideoUpdate');
          }

          $this->saveVideoRow(1, $input);

          $request->session()->flash('alert-success','Video Updated !!');
          return Redirect::route('mainVideo');
      } catch (\Throwable $exception) {
          \Log::error('Main video update failed.', [
              'message' => $exception->getMessage(),
          ]);

          $request->session()->flash('alert-danger', $this->friendlyVideoFailureMessage($exception));
          return Redirect::route('mainVideoUpdate');
      }
    
    }
    
    /* Main Video Code End */
    
    /* Sub Video Code Start */
    
    public function subVideoList(Request $request)
   {
        $this->ensureVideoRow(2);
        $users['subvideo_data'] = AdminRecycleBinService::activeTable('video')->where('video_id', 2)->get();
        $page_title = "Sub Video List - Zouple";
       return view('masters.video.subvideo',compact('page_title'),$users);
   }
    
   public function subVideoUpdatePage(Request $request)
   {
        $this->ensureVideoRow(2);
        $users['videos_data'] = AdminRecycleBinService::activeTable('video')->where('video_id', 2)->get();
        $page_title = "Main Video List - Zouple";
       return view('masters.video.edit_subvideo',compact('page_title'),$users);
   }

   public function subvideoUpdateSaveRedirect()
   {
       return Redirect::route('subVideoUpdate');
   }
    
    public function subvideoUpdateStore(Request $request)
    {
      try {
          $this->ensureVideoRow(2);
          if (!$request->request->has('remove_video')) {
              $this->validate($request, [
                  'title' => 'required|string|max:255',
                  'description' => 'required|string|max:10000',
              ], [
                  'title.required' => 'Sub video title is required.',
                  'description.required' => 'Sub video description is required.',
              ]);
          }

          $file = $this->safeVideoFile($request);
          if ($file && !$this->isValidVideoFile($file)) {
              $request->session()->flash('alert-danger', 'Please upload a valid MP4, MOV, AVI, WMV, or WebM video up to 100 MB.');
              return Redirect::route('subVideoUpdate');
          }

          $input = [
              'title' => $request->input('title'),
              'description' => $request->input('description'),
          ];
          $currentVideo = DB::table('video')->where('video_id', 2)->value('video');
          if($request->request->has('remove_video'))
          {
              if ($currentVideo) {
                  $this->deleteExistingVideo(2, $currentVideo);
              }
              $input['video'] = '';
              $input['video_public_id'] = null;
              $input['title'] = null;
              $input['description'] = null;
          }
          else
          {
              if($file)
              {
                  if ($currentVideo) {
                      $this->deleteExistingVideo(2, $currentVideo);
                  }
                 $upload = app(AdminMediaService::class)->uploadVideo($file, 'videos', 'video');
                 $input['video']= $upload['path'];
                 $input['video_public_id'] = $upload['public_id'];
              }
              else
              {
                  if (!$currentVideo) {
                      $request->session()->flash('alert-info', $this->missingVideoMessage($request, $currentVideo, 'sub'));
                      return Redirect::route('subVideoUpdate');
                  }
              }
          }
          
          $this->saveVideoRow(2, $input);

          $request->session()->flash('alert-success','Video Updated !!');
          return Redirect::route('subVideo');
      } catch (\Throwable $exception) {
          \Log::error('Sub video update failed.', [
              'message' => $exception->getMessage(),
          ]);

          $request->session()->flash('alert-danger', $this->friendlyVideoFailureMessage($exception));
          return Redirect::route('subVideoUpdate');
      }
    
    }
    
    /* Sub Video Code End */

    /* =================== Delete + Recycle Bin =================== */

    /**
     * Move a video to the recycle bin (soft-delete).
     * Works for both main video (video_id=1) and sub video (video_id=2).
     */
    public function deleteVideo(Request $request, $video_id)
    {
        AdminRecycleBinService::softDelete('videos', $video_id);
        $request->session()->flash('alert-success', 'Video moved to Recycle Bin. You can restore it from the Recycle Bin.');

        if ($video_id == 1) {
            return Redirect::route('mainVideo');
        }
        return Redirect::route('subVideo');
    }

    private function ensureVideoRow($videoId)
    {
        if (!Schema::hasTable('video')) {
            throw new \RuntimeException('Video table is missing. Please run database migrations on Hostinger.');
        }

        $exists = DB::table('video')->where('video_id', $videoId)->exists();
        if ($exists) {
            return;
        }

        DB::table('video')->insert($this->filterVideoColumns([
            'video_id' => $videoId,
            'video' => '',
            'title' => null,
            'description' => null,
            '_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    }

    private function saveVideoRow($videoId, array $input)
    {
        $input['deleted_at'] = null;
        $input['deleted_by'] = null;
        $input['updated_at'] = now();

        $input = $this->filterVideoColumns($input);
        if (empty($input)) {
            return;
        }

        DB::table('video')->where('video_id', $videoId)->update($input);
    }

    private function filterVideoColumns(array $input)
    {
        if (!Schema::hasTable('video')) {
            return [];
        }

        $columns = array_flip(Schema::getColumnListing('video'));
        return array_intersect_key($input, $columns);
    }

    private function safeVideoFile(Request $request)
    {
        $file = $request->files->get('video');
        if ($file instanceof SymfonyUploadedFile) {
            return $file;
        }

        if (!isset($_FILES['video']) || !is_array($_FILES['video'])) {
            return null;
        }

        $raw = $_FILES['video'];
        $error = isset($raw['error']) ? (int) $raw['error'] : UPLOAD_ERR_NO_FILE;
        $tmpName = isset($raw['tmp_name']) ? $raw['tmp_name'] : null;
        $originalName = isset($raw['name']) ? $raw['name'] : 'video';
        $mimeType = isset($raw['type']) ? $raw['type'] : null;

        if ($error === UPLOAD_ERR_OK && $tmpName && is_file($tmpName)) {
            \Log::info('Recovered video upload from raw PHP file bag.', [
                'name' => $originalName,
                'size' => isset($raw['size']) ? (int) $raw['size'] : null,
                'mime' => $mimeType,
            ]);

            return new SymfonyUploadedFile($tmpName, $originalName, $mimeType, $error, true);
        }

        return null;
    }

    private function isValidVideoFile(SymfonyUploadedFile $file)
    {
        if (!$file->isValid() || $file->getSize() > $this->maxVideoUploadBytes()) {
            return false;
        }

        $allowedMimes = [
            'video/mp4',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-ms-wmv',
            'video/webm',
            'application/octet-stream',
        ];
        $allowedExtensions = ['mp4', 'mov', 'avi', 'wmv', 'webm'];

        return in_array($file->getMimeType(), $allowedMimes, true)
            && in_array(strtolower($file->getClientOriginalExtension()), $allowedExtensions, true);
    }

    private function missingVideoMessage(Request $request, $currentVideo, $label)
    {
        if ($currentVideo) {
            return 'No new video selected. Existing ' . $label . ' video kept.';
        }

        $contentLength = (int) $request->server('CONTENT_LENGTH');
        if ($contentLength > 0) {
            $serverLimit = $this->serverUploadLimitBytes();
            $uploadError = $this->rawUploadError('video');
            \Log::warning('Video upload request contained data but no uploaded file.', [
                'content_length' => $contentLength,
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'upload_tmp_dir' => ini_get('upload_tmp_dir'),
                'raw_upload_error' => $uploadError,
                'raw_upload_tmp_name' => isset($_FILES['video']['tmp_name']) ? $_FILES['video']['tmp_name'] : null,
                'raw_upload_tmp_exists' => isset($_FILES['video']['tmp_name']) ? is_file($_FILES['video']['tmp_name']) : false,
                'raw_upload_size' => isset($_FILES['video']['size']) ? (int) $_FILES['video']['size'] : null,
                'raw_upload_type' => isset($_FILES['video']['type']) ? $_FILES['video']['type'] : null,
                'file_keys' => array_keys($_FILES ?: []),
                'post_keys' => array_keys($request->request->all()),
            ]);

            if ($uploadError !== null && $uploadError !== UPLOAD_ERR_OK) {
                return $this->uploadErrorMessage($uploadError);
            }

            if ($serverLimit && $contentLength > $serverLimit) {
                return 'The selected video is larger than this server currently accepts. Current PHP upload limit is ' . $this->formatBytes($serverLimit) . ' (upload_max_filesize=' . ini_get('upload_max_filesize') . ', post_max_size=' . ini_get('post_max_size') . '). Set upload_max_filesize=110M and post_max_size=128M, restart Apache, then upload again.';
            }

            return 'The browser did not send the selected video file. Refresh the page, choose the file again, and upload a valid video.';
        }

        return 'Please choose a video to upload.';
    }

    private function rawUploadError($field)
    {
        return isset($_FILES[$field]['error']) ? (int) $_FILES[$field]['error'] : null;
    }

    private function uploadErrorMessage($error)
    {
        switch ((int) $error) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'The selected video is larger than the server upload limit. Use a smaller video or increase upload_max_filesize/post_max_size.';
            case UPLOAD_ERR_PARTIAL:
                return 'The video upload was interrupted before it finished. Please choose the file again and update.';
            case UPLOAD_ERR_NO_FILE:
                return 'Please choose a video to upload.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Server upload temp folder is missing. Set PHP upload_tmp_dir to a writable folder and try again.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Server could not write the uploaded video to the temp folder. Fix write permission for ' . (ini_get('upload_tmp_dir') ?: sys_get_temp_dir()) . ' and try again.';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the video upload. Disable the blocking extension or try another video file.';
            default:
                return 'The video upload failed before Laravel received the file. PHP upload error code: ' . (int) $error . '.';
        }
    }

    private function maxVideoUploadBytes()
    {
        return 100 * 1024 * 1024;
    }

    private function serverUploadLimitBytes()
    {
        $limits = array_filter([
            $this->phpSizeToBytes(ini_get('upload_max_filesize')),
            $this->phpSizeToBytes(ini_get('post_max_size')),
        ]);

        return $limits ? min($limits) : null;
    }

    private function phpSizeToBytes($value)
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '-1') {
            return 0;
        }

        $unit = strtolower(substr($value, -1));
        $bytes = (float) $value;

        switch ($unit) {
            case 'g':
                $bytes *= 1024;
            case 'm':
                $bytes *= 1024;
            case 'k':
                $bytes *= 1024;
        }

        return (int) $bytes;
    }

    private function formatBytes($bytes)
    {
        if ($bytes >= 1024 * 1024) {
            return round($bytes / 1024 / 1024, 1) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' bytes';
    }

    private function deleteExistingVideo($videoId, $video)
    {
        $publicId = Schema::hasColumn('video', 'video_public_id')
            ? DB::table('video')->where('video_id', $videoId)->value('video_public_id')
            : null;

        app(AdminMediaService::class)->deleteMedia($video, 'video', $publicId, 'video');
    }

    private function friendlyVideoFailureMessage(\Throwable $exception)
    {
        $message = $exception->getMessage();

        if (stripos($message, 'Cloudinary') !== false) {
            return 'Cloudinary video upload failed. Please check Cloudinary cloud name, API key, API secret, and account upload limits.';
        }

        if (stripos($message, 'Invalid upload file') !== false) {
            return 'The uploaded video temp file was not available when Cloudinary tried to save it. Please upload again; if it repeats, increase PHP upload_tmp_dir permissions on Hostinger.';
        }

        if (stripos($message, 'Base table or view not found') !== false || stripos($message, 'Unknown column') !== false) {
            return 'Video database columns are not ready on Hostinger. Please run migrations, then upload again.';
        }

        return 'Video upload failed on the server. Please check storage/logs/laravel.log for the exact message.';
    }
}
