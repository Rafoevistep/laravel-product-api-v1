<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Curl\Curl;
use Illuminate\Http\JsonResponse;
use voku\helper\HtmlDomParser;


class ScrapeItems extends Controller
{

    public function scrape_all_posts(): JsonResponse
    {

        $curl = new Curl();
        $curl->get('https://www.list.am/category/23');

        $html = $curl->response;
        $htmlDomParser = HtmlDomParser::str_get_html($html);

        $itemDataList = [];

        $itemElements = $htmlDomParser->findOne("#contentr > div.dl > div.gl");

        foreach ($itemElements as $itemElement) {

            $image = $itemElement->findOne("img")->getAttribute("data-original");
            $name = $itemElement->findOne(".p")->text;
            $price = $itemElement->findOne(".l")->text;
            $location = $itemElement->findOne(".at")->text;

            $dram = 413;
            $amd = mb_substr($name, -1);

            if ($amd == '֏') {
                $amdUpd = rtrim($name, " ֏",);
                $var2 = str_replace(",", "", $amdUpd);
                $convertAmd = $var2 / $dram;
                $name = $itemData['price'] = '$' . number_format($convertAmd);
            }

            $itemData = Product::updateOrCreate([
                "image" => $image,
                "name" => $price,
                "price" => $name,
                'description' => $location
            ]);


            $itemDataList[] = $itemData;
        }

        return response()->json($itemDataList);

    }

}
