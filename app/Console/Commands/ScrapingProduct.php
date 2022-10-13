<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Curl\Curl;


class ScrapingProduct extends Command
{

    protected $signature = 'scrape:product';


    protected $description = 'Scraping Product trough Artisan';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $curl = new Curl();

        $curl->get('https://laravel-products-api.herokuapp.com/api/scraping-product');

            echo 'Scraping Successfully completed  ';

    }

}
