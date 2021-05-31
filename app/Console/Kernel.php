<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

        Commands\FetchDorephaProducts::class,
        Commands\FetchGoldenProducts::class,
        Commands\FetchMaestaProducts::class,
        Commands\FetchNiceoneProducts::class,
        Commands\FetchAttributesFromMaesta::class,
        Commands\UpdateDorephaPriceProducts::class,
        Commands\UpdateGoldenScentPriceProducts::class,
        Commands\UpdateMaestaPriceProducts::class,
        Commands\UpdateNiceonePriceProducts::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /** -----------  Update price for all products from Dorepha site at daily ------------------- */
        $schedule->command('update:dorepha_price')
            ->dailyAt('5.00')
            ->withoutOverlapping();

        /** -----------  Update price for all products from Maesta site at daily ------------------- */
        $schedule->command('update:maesta_price')
            ->dailyAt('5.00')
            ->withoutOverlapping();

        /** -----------  Update price for all products from GoldenScent site at hourly ------------------- */
        $schedule->command('update:golden_scent_price')
            ->hourly()
            ->withoutOverlapping(60);

        /** -----------  Fetch all categories, size and brands from Maesta site at daily ------------------- */
        $schedule->command('fetch:maesta_attributes')
            ->dailyAt('12.30')
            ->withoutOverlapping(60);

        /** -----------  Fetch all products from Maesta site at daily ------------------- */
        $schedule->command('fetch:maesta_products')
            ->dailyAt('1.00')
            ->withoutOverlapping(60);

        /** -----------  Fetch all products from Dorepha site at daily ------------------- */
        $schedule->command('fetch:dorepha_products')
            ->dailyAt('1.00')
            ->withoutOverlapping(60);

        /** -----------  Fetch all products from Niceone site at daily ------------------- */
        $schedule->command('fetch:niceone_products')
            ->dailyAt('1.00')
            ->withoutOverlapping(60);

        /** -----------  Update price for all products from Niceone site at daily ------------------- */
        $schedule->command('update:niceone_price')
            ->dailyAt('5.00')
            ->withoutOverlapping(60);

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
