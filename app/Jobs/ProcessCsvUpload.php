<?php

namespace App\Jobs;

use App\Models\Yoprintimport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class ProcessCsvUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $file)
    {
        $this->file =$file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Redis::throttle ('upload-csv')->allow(1)->every(20)->then(function () {
            
            dump('processing this file:---', $this->file);

            $data = array_map('str_getcsv', file($this->file));

            foreach ($data as $row){
                Yoprintimport::updateOrCreate([
                    'UNIQUE_KEY' =>$row[0]
                ], [
                    'PRODUCT_TITLE'=>$row[1],
                    'PRODUCT_DESCRIPTION'=>$row[2],
                    'STYLE#'=>$row[3],
                    'SANMAR_MAINFRAME_COLOR'=>$row[28],
                    'SIZE'=>$row[18],
                    'COLOR_NAME'=>$row[14],
                    'PIECE_PRICE'=>$row[21]
                ]);
            }

            dump('done this file:---', $this->file);

            unlink($this->file);

        }, function () {

            return $this->release(10);
        });

        
    }
}
