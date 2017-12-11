<?php

namespace App;

use Image;
use App\Photo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Photo extends Model {

  /**
   * Fillable fields for a photo
   *
   * @var array
   */
  protected $fillable = ['path', 'name', 'thumbnail_path'];

  protected $appends = ['url', 'thumbnail_url'];


  /**
   * Make a new photo instance from an uploaded file
   *
   * @var $file
   * @return self
   */
  public static function fromForm($file) {
    $photo = new Photo;
    $photo->fill([
        'path' => $filePath = $file->store($photo->baseDir(), 'public'),
        'name' => $fileName = str_replace($photo->baseDir() . '/', '', $filePath),
        'thumbnail_path' => $photo->makeThumbnail($file, $fileName)
      ])->save();
    return $photo;
  }

  public static function fromFormDeal($file) {
    $photo = new Photo;
     $photo->fill([
        'path' => $filePath = $file->store($photo->baseDir(), 'public'),
        'name' => $fileName = str_replace($photo->baseDir() . '/', '', $filePath),
        'thumbnail_path' => $photo->makeThumbnailDeal($file, $fileName)
      ])->save();
    return $photo;
  }

  /**
   * Get the base directory for photo uploads
   *
   * @return string
   */
  public function baseDir() {
    return 'images/photos';
  }


  /**
   * Create a thumbnail for the photo
   *
   * @return void
   */
  protected function makeThumbnail($file, $fileName) {
    $thumbnail = Image::make($file)
      ->fit(200, 200, function($constraint) {
        $constraint->upsize();
      }, 'center')->encode('png');
    $thumbnailPath = $this->baseDir() . '/tn-' . $fileName;
    Storage::disk('public')->put($thumbnailPath, $thumbnail);
    return $thumbnailPath;
  }

  protected function makeThumbnailDeal($file, $fileName) {
    $thumbnail = Image::make($file)
      ->fit(400, 250, function($constraint) {
        $constraint->upsize();
      }, 'center')->encode('png');
    $thumbnailPath = $this->baseDir() . '/tn-' . $fileName;
    Storage::disk('public')->put($thumbnailPath, $thumbnail);
    return $thumbnailPath;
  }

  public function delete() {
    Storage::delete([
      'public/' . $this->path,
      'public/' . $this->thumbnail_path
    ]);

    parent::delete();
  }

  public function getUrlAttribute() {
    return Storage::url($this->path);
  }

  public function getThumbnailUrlAttribute() {
    return Storage::url($this->thumbnail_path);
  }

  public function getApiUrlAttribute() {
    return url(Storage::url($this->path));
  }

  public function getApiThumbnailUrlAttribute() {
    return url(Storage::url($this->thumbnail_path));
  }
}
