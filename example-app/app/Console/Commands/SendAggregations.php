<?php

namespace App\Console\Commands;

use App\Mail\AggregationsMail;
use App\Models\Mongo\Car;
use App\Notifications\AggregationNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Mockery\Exception;

class SendAggregations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:aggregations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle()
    {
        Cache::put('sum', 10,now()->addHour());
        try {
            $yesterday = Carbon::yesterday();
            $tomorrow = Carbon::tomorrow();
            $sumAggregation = Cache::remember('daily-aggregations:sum',now()->addHour(), function () use ($yesterday,$tomorrow) {
                $carsExited = Car::whereBetween('exited', [$yesterday, $tomorrow])->get();
                $sum = 0;
                foreach ($carsExited as $item) {
                    $sum += $item->sumPaid;
                }
                return $sum;
            });

            $carsRegisteredAggregation = Cache::remember('daily-aggregations:cars-registered', now()->addHour(), function() use ($yesterday,$tomorrow) {
                return Car::whereBetween('entered', [$yesterday, $tomorrow])->count();
            });
            $today = Carbon::today()->format('d-m-Y');
            Notification::route('mail','hello@admin.com')->notify(new AggregationNotification($sumAggregation, $carsRegisteredAggregation, $today));
            return 0;
        } catch (\Exception $e) {
            dump($e->getMessage());
            return 1;
        }
    }
}
