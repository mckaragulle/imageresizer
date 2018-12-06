<?php 
namespace Karagulle\ImageResizer;

use Storage;
use Image;

class ImageResizer {

	protected $path_cache = 'public/cache_';
    protected $path_original;
    protected $path_storage;
    protected $path_write;
	protected $filename;

	public function __construct()
	{
        $this->path_original = config('imageresizer.path_original');
        $this->path_cache    = config('imageresizer.path_cache');
        $this->path_storage  = config('imageresizer.path_storage');
	}
	public function open($id, $model, $fileName, $arg = null)
	{
        $this->fileName = $fileName;
		$this->model = new $model;
		$this->model->where('id')->with('photos')->first();

		if(is_null($arg)):
			$this->w = $this->model->imageDimension["normal"]['width'];
			$this->h = $this->model->imageDimension["normal"]['height'];
		else:
			$this->w = $this->model->imageDimension[$arg]['width'];
			$this->h = $this->model->imageDimension[$arg]['height'];
		endif;
        $this->path_cache.=$this->w.'_'.$this->h;

		$found = $this->checkCache();

		if($found == false):
			return $this->resizer();
		else:
			return $found;
		endif;
	}
	protected function checkCache()
	{
		if(!Storage::exists($this->path_cache)):
			Storage::makeDirectory($this->path_cache);
			$this->path_write = $this->path_cache.'/'.$this->fileName;
			return false;
		endif;

		try {
			if(Storage::exists($this->path_cache.'/'.$this->fileName)):
				$this->path_write = $this->path_cache.'/'.$this->fileName;
				return Storage::url($this->path_write);
			else:
				$this->path_write = $this->path_cache.'/'.$this->fileName;
				return false;
			endif;        	
        } catch (\Exception $e) {
        	$this->path_write = $this->path_cache.'/'.$this->fileName;
        	return false;
        }
        return $this->path_cache.'/'.$this->fileName;
	}
	protected function resizer()
	{
        $w = $this->model->imageDimension['normal']['width'];
        $h = $this->model->imageDimension['normal']['height'];

        $img=Image::make(Storage::get($this->path_original.'/'.$this->fileName))->orientate();
        $img->resize($w, $h, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

       	$img->save(Storage::path($this->path_write));

       	$cnv=Image::canvas($w, $h, '#ffffff');

       	$cnv->insert(Storage::path($this->path_write), "center");
        $cnv->save(Storage::path($this->path_write) );
        return Storage::url($this->path_write);
	}
}
