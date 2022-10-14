<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Curl\Curl;
use Illuminate\Http\JsonResponse;
use voku\helper\HtmlDomParser;
use Illuminate\Support\Facades\DB;


class ScrapeItems extends Controller
{
    public function scrape_all_posts(): JsonResponse
    {

        $curl = new Curl();

        for ($id = 1; $id < 100; $id++) {

            $curl->get('https://www.list.am/category/23/' . $id . '?type=1&po=1&n=0&bid=0&price1=100&price2=&crc=-1&_a27=0&_a2_1=&_a2_2=&_a1_1=&_a1_2=&_a15=0&_a28_1=&_a28_2=&_a13=0&_a23=0&_a43=0&_a22=0&_a16=0');

            $html = $curl->response;

            $htmlDomParser = HtmlDomParser::str_get_html($html);

            $curl1 = new Curl();
            $curl1->get('https://rate.am/am');
            $html1 = $curl1->response;
            $htmlDomParser1 = HtmlDomParser::str_get_html($html1);

            $amd = $htmlDomParser1->findOne("#e1a68c2e-bc47-4f58-afd2-3b80a8465b14 > td:nth-child(7)")->text;
            $euro = $htmlDomParser1->findOne('#\31 33240fd-5910-421d-b417-5a9cedd5f5f7 > td:nth-child(9)')->text;
            $rub = $htmlDomParser1->findOne('#\31 33240fd-5910-421d-b417-5a9cedd5f5f7 > td:nth-child(11)')->text;

            $itemDataList = [];

            $itemElements = $htmlDomParser->findOne("#contentr > div.dl > div.gl");

            foreach ($itemElements as $itemElement) {

                $image = $itemElement->findOne("img")->getAttribute("data-original");
                $name = $itemElement->findOne(".p")->text;
                $price = $itemElement->findOne(".l")->text;
                $location = $itemElement->findOne(".at")->text;


                if ((preg_match("/֏/", $name))) {
                    $amdUpd = rtrim($name, " ֏");
                    $convertAmd = str_replace(",", "", $amdUpd) / $amd ;
                    $name = $itemData['price'] = '$' . number_format($convertAmd);
                }


                $euroTotal = $amd / $euro;

                if ((preg_match("/€/", $name))) {
                    $eurUpd = mb_substr($name, 1);
                    $eurUpd = str_replace(",", "", $eurUpd) * $euroTotal;
                    $name = $itemData['price'] = '$' . number_format($eurUpd);
                }

                $rubTotal = $rub / $amd;

                if ((preg_match("/руб./i", $name))) {
                    $rubUpd = str_replace(" руб.", "", $name);
                    $rubUpd = str_replace(",", "", $rubUpd) * $rubTotal;
                    $name = $itemData['price'] =  '$' . number_format($rubUpd);
                }

                $itemDataList[] = [
                    "image" => $image,
                    "name" => $price,
                    "price" => $name,
                    'description' => $location,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ];

            }
            $test = DB::table('products')->insert($itemDataList);
        }

        return response()->json($test);

    }
}
