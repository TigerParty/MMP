<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use  App\Argo\Attachment;

class ListMissedAttachments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'argo:list-missed-attachments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $attachs = Attachment::select(['id', 'path'])->get();

        foreach($attachs as $attach)
        {
            if(!file_exists(storage_path("upload/$attach->path")))
            {
                $this->info("($attach->id): $attach->path");
            }
        }

    }
}
