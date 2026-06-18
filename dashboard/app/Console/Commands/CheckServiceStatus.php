<?php

namespace App\Console\Commands;

use App\Notifications\ServiceStatusNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

class CheckServiceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:service-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifie les status des services de base de données, cache et api';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        //return Command::SUCCESS;
        $services = [];
        try {
            DB::connection()->getPdo();
            $services['database'] = 'Operational';
        }catch (\Exception $e){
            $services['database'] = 'Down';
        }
        try {
            Cache::store('file')->put('test', 'value', 10);
            $services['cache'] = 'Operational';
        }catch (\Exception $e){
            $services['cache'] = 'Down';
        }
        try{
            $response = Http::get('http://locahost:8000/api/recipes');
            logger('response api', ['response' => $response->successful()]);
            if ($response->successful())
            {
                $services['api'] = 'Operational';
            }else{
                $services['api'] = 'Down';
            }
        }catch (\Exception $e)
        {
            $services['api'] = 'Down';
        }
        try {
            $serverResponse = Http::get('https://koool.mayaapps.site');
            logger('success',['response' => $serverResponse->successful()]);
            if ($serverResponse->successful()) {
                $services['server'] = 'Operational';
            } else {
                $services['server'] = 'Down';
            }
        } catch (\Exception $e) {
            $services['server'] = 'Down';
        }
        try {
            if (!App::isDownForMaintenance())
            {
                $services['maintenance'] = 'Operational';
            }else{
                $services['maintenance'] = 'Down';
            }
        }catch (\Exception $e){
            $services['maintenance'] = 'Down';
        }
        logger($services);
        if (in_array('Down', $services)){
            logger('hey');
             Notification::route('mail', 'meissagningue7@gmail.com')
            ->notify(new ServiceStatusNotification($services));
             logger('salut');
        }
        $this->info(json_encode($services, true));

    }
}
