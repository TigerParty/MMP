<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AttachmentController;
use Storage;
use File;
use DB;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Argo\Container;
use Carbon\Carbon;


class ImportImages extends Command
{
    private $controller;

    private $dafaultContainerId;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'argo:import_images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import project images';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->controller = new AttachmentController();
        $this->dafaultContainerId = config('argodf.default_container_id');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $container = Container::find($this->dafaultContainerId);
        $defaultContinaerFormId = $container->form_id;

        $disk = Storage::disk('import_images');
        $dirs = $disk->directories();
        foreach($dirs as $dir) {
            $projectId = str_before($dir, '_');

            $files = File::files(storage_path("import_images/$dir"));
            foreach($files as $file) {
                $content = $this->getFileObjectFromPath($file);
                $response = $this->controller->doUpload($content);
                $attachment = json_decode($response->getContent());

                DB::table('attachables')->insert([
                    'attachment_id' => $attachment->id,
                    'attachable_id' => $projectId,
                    'attachable_type' => 'App\Argo\Project',
                    'attached_form_id' => $defaultContinaerFormId,
                    'attached_at' => Carbon::now()
                ]);
            }
        }
    }

    private function getFileObjectFromPath($path) {
        $originName = File::name($path).'.'.File::extension($path);
        $mimeType = File::mimeType($path);
        $size = File::size($path);

        return new UploadedFile(
            $path,
            $originName,
            $mimeType,
            $size,
            null,
            false
        );
    }
}
