<?php

namespace App\Console\Commands;

use App\MaestaSkinType;
use App\MaestaBrand;
use App\MaestaCategory;
use App\MaestaSize;
use App\ScrappingLogs;
use Illuminate\Console\Command;

class FetchAttributesFromMaesta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:maesta_attributes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all categories, size and brands from Maesta site';

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
        try {

            MaestaSkinType::query()->truncate();
            MaestaSize::query()->truncate();
            MaestaBrand::query()->truncate();
            MaestaCategory::query()->truncate();

            MaestaSkinType::GetSkinType();
            MaestaSize::GetSize();
            MaestaBrand::GetBrands();
            MaestaCategory::GetCategories();

        } catch (\Exception $e) {
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'FetchAttributesFromMaesta error', var_export($e->getMessage(), true), 'Error');
        }
    }
}
