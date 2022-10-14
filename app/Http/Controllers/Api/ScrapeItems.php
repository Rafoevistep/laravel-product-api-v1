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

            $curl->get('https://www.list.am/category/23/' . $id . '?po=1&n=0&bid=0&price1=100&price2=&crc=-1&_a27=0&_a2_1=&_a2_2=&_a1_1=&_a1_2=&_a15=0&_a28_1=&_a28_2=&_a13=0&_a23=0&_a43=0&_a22=0&_a16=0');

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


                $strAmd = $name;
                $patternAmd = "/֏/";

                if ((preg_match($patternAmd, $strAmd))) {
                    $amdUpd = rtrim($name, " ֏");
                    $var2 = str_replace(",", "", $amdUpd);
                    $convertAmd = $var2 / $amd;
                    $name = $itemData['price'] = '$' . number_format($convertAmd);

                }

                $strEur = $name;
                $patternEur = "/€/";

                $euroTotal = $amd / $euro;

                if ((preg_match($patternEur, $strEur))) {
                    $eurUpd = mb_substr($name, 1);
                    $var3 = str_replace(",", "", $eurUpd);
                    $eurUpd = '$' . number_format($var3 * $euroTotal);
                    $name = $itemData['price'] = $eurUpd;
                }

                $strRub = $name;
                $patternRub = "/руб./i";
                $rubTotal = $rub / $amd;

                if ((preg_match($patternRub, $strRub))) {
                    $rubUpd = str_replace(" руб.", "", $strRub);
                    $var4 = str_replace(",", "", $rubUpd);
                    $rubUpd = '$' . number_format($var4 * $rubTotal);
                    $name = $itemData['price'] = $rubUpd;
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
