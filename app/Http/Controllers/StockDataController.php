<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockDataRequest;
use App\Mail\StockHistory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Mail;

class StockDataController extends Controller
{
    public function getStockData(StockDataRequest $request)
    {
        $url = "https://yh-finance.p.rapidapi.com/stock/v3/get-historical-data?symbol=" . $request->symbol;

        if (Cache::has('company-data')) {
            $companyJson = Cache::get('company-data');
        } else {
            $url2 = "https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json";
            $response = Http::get($url2);
            $companyJson = $response->json();
            Cache::put('company-data', $companyJson, now()->addMinutes(10));
        }

        $companyDetails = array_values(array_filter($companyJson, function ($data) use ($request) {
            return $data['Symbol'] === $request->symbol;
        }));

        $emailMsg = $request->startdate . " to " . $request->enddate;

        $startDateTimeStamp = strtotime($request->startdate);
        $endDateTimeStamp = strtotime($request->enddate);

        $response = Http::withHeaders([
            "X-RapidAPI-Key" => env('RAPID_API_KEY', ''),
            "X-RapidAPI-Host" => "yh-finance.p.rapidapi.com",
        ])->get($url);

        $stockData = $response->json()["prices"];

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
