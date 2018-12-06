<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Image;
use File;
class ImageResizer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:resizer {width} {heigth}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fotoğrafları girilen genişlik ve yükselikte ölçekleyerek cache oluşturmak için kullanılır.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->alert('Fotoğraflar girilen ölçülere göre yeniden oluşturulup cachelenecektir.');    
        $w = $this->argument('width');
        $h = $this->argument('heigth');
        if(!is_numeric($w) or !is_numeric($h)):
            $this->error("Geçersiz genişlik ya da yükseklik değeri girdiniz. İşlem iptal edildi.");
            exit();
        endif;
        //$arguments = $this->arguments();
        $this->info('İşlem Başlıyor...');
        //Klasördeki dosyaları aldık. 'app/public/original'
        $path_original=config('imageresizer.path_original');
        $files=File::files($path_original);
        $filecount = 0;
        if ($files !== false):
            //dosya sayısını aldık.
            $filecount = count($files);
        endif;
        $bar = $this->output->createProgressBar($filecount);
        $bar->start();
		//'app/public/cache_'
        $path_cache=config('imageresizer.path_cache').$w.'_'.$h;
        if(File::exists($path_cache)):
            File::deleteDirectory($path_cache);
        endif;
        File::makeDirectory($path_cache); 
        foreach($files as $file):
            $name = pathinfo($file);
            $this->line(" İşleniyor: ".$name['basename']);
            $img=Image::make($path_original.'/'.$name['basename'])->orientate();
            $img->resize($w, $h, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $img->save(s$path_cache.'/'.$name['basename']);

            $cnv=Image::canvas($w, $h, '#ffffff');
            $cnv->insert($path_cache.'/'.$name['basename'], "center");
            $cnv->save($path_cache.'/'.$name['basename']);
            $bar->advance();
        endforeach;
        $bar->finish();
        $this->line(" ");
        $this->alert("İşlem Başarıyla Tamamlandı.");
    }
}
