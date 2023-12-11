<?php

namespace App\Console\Commands;

use App\Models\Mongo\Vehicle;
use Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;

class ElasticIndex extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elastic:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * Execute the console command.
     *
     * @return integer
     */
    public function handle()
    {
        $cars   = Vehicle::all();
        $client = ClientBuilder::create()->build();

        $params = [];

        foreach ($cars as $car) {
            $params['body']  = [
                'registrationPlate' => $car->registrationPlate,
                'brand'             => $car->brand,
                'model'             => $car->model,
                'color'             => $car->color,
                'entered'           => $car->entered,
                'exited'            => $car->exited,
                'created_at'        => $car->created_at,
            ];
            $params['index'] = strtolower('cars');
            $params['type']  = 'car';
            $client->index($params);
        }

        return 0;

    }//end handle()


}//end class
