<?php

namespace App\Console\Commands;

use App\UpdateNiceonePricesForProducts;
use Illuminate\Console\Command;

class UpdateNiceonePriceProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:niceone_price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update price for all products from Niceone site';

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
        UpdateNiceonePricesForProducts::GetProductsFromTable();
    }
}
