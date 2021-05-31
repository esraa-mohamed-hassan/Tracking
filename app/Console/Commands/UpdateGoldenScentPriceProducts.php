<?php

namespace App\Console\Commands;

use App\UpdateGoldenScentPricesForProducts;
use Illuminate\Console\Command;

class UpdateGoldenScentPriceProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:golden_scent_price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update price for all products from GoldenScent site';

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
        ini_set('memory_limit', '-1');
        UpdateGoldenScentPricesForProducts::GetProductsFromTable();
    }
}
