<?php

namespace App\Models;

use App\Jobs\ProcessCsvUpload;
use Illuminate\Database\Eloquent\Model;

class Yoprintimport extends Model
{
    protected $guarded = [];
    public function importToDb()
    {
        $path = resource_path('pending-files/*.csv');

        $files = glob($path);

        foreach ($files as $file){
            
            ProcessCsvUpload::dispatch($file);
        }
         
    }
    
}
