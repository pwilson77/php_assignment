<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockDataRequest;
use App\Mail\StockHistory;
use Mail;

class StockDataController extends Controller
{
    public function getStockData(StockDataRequest $request)
    {
        $url = "https://yh-finance.p.rapidapi.com/stock/v3/get-historical-data?symbol=" . $request->symbol . "&region=US";
        $url2 = "https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json";

        $companyJson = file_get_contents($url2);
        $companyData = json_decode($companyJson, true);
        $companyDetails = array_values(array_filter($companyData, function ($data) use ($request) {
            return $data['Symbol'] === $request->symbol;
        }));

        $emailMsg = $request->startdate . " to " . $request->enddate;

        $startDateTimeStamp = strtotime($request->startdate);
        $endDateTimeStamp = strtotime($request->enddate);

        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "X-RapidAPI-Key: " . env('RAPID_API_KEY', '') . "\r\n" .
                "X-RapidAPI-Host: yh-finance.p.rapidapi.com\r\n",
            ),
        );

        $context = stream_context_create($opts);
        $jsonRes = file_get_contents($url, false, $context);
        $stockData = json_decode($jsonRes, true)["prices"];

        usort($stockData, function ($item1, $item2) {
            return $item1["date"] <=> $item2["date"];
        });
        $filteredStockData = array_filter($stockData, function ($stock) use ($startDateTimeStamp, $endDateTimeStamp) {
            return $stock["date"] >= $startDateTimeStamp && $stock["date"] <= $endDateTimeStamp && !array_key_exists('type', $stock);
        });

        Mail::to($request->email)
            ->send(new StockHistory($filteredStockData, $emailMsg, $companyDetails[0]["Company Name"]));

        return view('/stockdata', [
            'stockData' => $filteredStockData,
            'symbol' => $request->symbol,
            'startDate' => $request->startdate,
            'endDate' => $request->enddate,
        ]);
    }
}
