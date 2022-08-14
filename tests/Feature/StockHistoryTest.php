<?php

namespace Tests\Feature;

use App\Mail\StockHistory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StockHistoryTest extends TestCase
{

    public function test_stock_history_form_route()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_if_errors_are_rendered()
    {
        $view = $this->withViewErrors([
            'symbol' => ['Symbol is invalid'],
            'startdate' => ['Start date must not be greater than enddate'],
            'enddate' => ['End date is required'],
            'email' => ['Email is invalid'],
        ])->view('stockDataForm');

        $view->assertSee('Symbol is invalid');
        $view->assertSee('Start date must not be greater than enddate');
        $view->assertSee('End date is required');
        $view->assertSee('Email is invalid');
    }

    public function test_if_validation_errors_are_thrown()
    {

        $this->configure_http_mocks();

        $response = $this->post('/request-stock-history', [
            'symbol' => 'A',
            'email' => 'test.com',
            'startdate' => '2022-08-13',
            'enddate' => '2022-08-12',
        ]);

        $response->assertSessionHasErrors(["symbol", "email", "startdate", "enddate"]);
    }

    public function test_it_redirects_to_stock_history_route()
    {
        $this->configure_http_mocks();

        $response = $this->post('/request-stock-history', [
            'symbol' => 'AAPL',
            'email' => 'test@gmail.com',
            'startdate' => '2022-08-01',
            'enddate' => '2022-08-13',
        ]);

        $response->assertViewIs('.stockdata');
    }

    public function test_if_email_was_sent()
    {
        Mail::fake();
        $this->configure_http_mocks();

        $response = $this->post('/request-stock-history', [
            'symbol' => 'AAPL',
            'email' => 'test@gmail.com',
            'startdate' => '2022-08-01',
            'enddate' => '2022-08-13',
        ]);

        $response->assertStatus(200);

        Mail::assertSent(StockHistory::class);
    }

    public function test_if_stock_history_is_rendered()
    {
        $view = $this->view('stockdata', [
            'stockData' => [
                [
                    "date" => 1660311000,
                    "open" => 169.82000732421875,
                    "high" => 172.1699981689453,
                    "low" => 169.39999389648438,
                    "close" => 172.10000610351562,
                    "volume" => 67946400,
                    "adjclose" => 172.10000610351562,
                ],
            ],
            'symbol' => "AAPL",
            'startDate' => "2022-08-01",
            'endDate' => "2022-08-12",
        ]);

        $view->assertSee('169.82000732421875');
        $view->assertSee('172.1699981689453');
        $view->assertSee('169.39999389648438');
        $view->assertSee('172.10000610351562');
        $view->assertSee('67946400');
    }

    private function configure_http_mocks()
    {
        Http::fake([
            // Stub a JSON response for endpoints
            'pkgstore.datahub.io/*' => Http::response([
                [
                    "Company Name" => "Apple Inc.",
                    "Financial Status" => "N",
                    "Market Category" => "Q",
                    "Round Lot Size" => 100,
                    "Security Name" => "Apple Inc. - Common Stock",
                    "Symbol" => "AAPL",
                    "Test Issue" => "N",
                ],
                [
                    "Company Name" => "Avalanche Biotechnologies, Inc.",
                    "Financial Status" => "N",
                    "Market Category" => "G",
                    "Round Lot Size" => 100,
                    "Security Name" => "Avalanche Biotechnologies, Inc. - Common Stock",
                    "Symbol" => "AAVL",
                    "Test Issue" => "N",
                ],
            ], 200, ['$headers']),

            // Stub a string response for  endpoints
            'yh-finance.p.rapidapi.com/*' => Http::response(
                array("prices" => [
                    [
                        "date" => 1660311000,
                        "open" => 169.82000732421875,
                        "high" => 172.1699981689453,
                        "low" => 169.39999389648438,
                        "close" => 172.10000610351562,
                        "volume" => 67946400,
                        "adjclose" => 172.10000610351562,
                    ],
                    [
                        "date" => 1660224600,
                        "open" => 170.05999755859375,
                        "high" => 170.99000549316406,
                        "low" => 168.19000244140625,
                        "close" => 168.49000549316406,
                        "volume" => 57149200,
                        "adjclose" => 168.49000549316406,
                    ],
                    [
                        "date" => 1660138200,
                        "open" => 167.67999267578125,
                        "high" => 169.33999633789062,
                        "low" => 166.89999389648438,
                        "close" => 169.24000549316406,
                        "volume" => 70170500,
                        "adjclose" => 169.24000549316406,
                    ]]), 200, [
                    "X-RapidAPI-Key" => env('RAPID_API_KEY', ''),
                    "X-RapidAPI-Host" => "yh-finance.p.rapidapi.com",
                ]),
        ]);
    }
}
