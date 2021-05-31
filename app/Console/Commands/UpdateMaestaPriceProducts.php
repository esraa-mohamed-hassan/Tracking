<?php

namespace App\Console\Commands;

use App\UpdateMaestaPricesForProducts;
use Illuminate\Console\Command;

class UpdateMaestaPriceProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:maesta_price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update price for all products from Maesta site';

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
     * @return int
     */

    public function handle()
    {
        ini_set('max_execution_time', 0);
        UpdateMaestaPricesForProducts::UpdatePricesForMaesta();
    }
}
