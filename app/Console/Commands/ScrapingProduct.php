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

        //for local
        //$curl->get('http://127.0.0.1:8000/api/scraping-product/1');
        //for heroku app

        $curl->get('https://laravel-products-api.herokuapp.com/api/scraping-product/1');

        echo 'Scraping Successfully completed  ';

    }

}
