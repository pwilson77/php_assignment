<?php

namespace Tests\Feature;

use App\Mail\StockHistory;
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

        $response = $this->post('/request-stock-history', [
            'symbol' => 'AAPL',
            'email' => 'test@gmail.com',
            'startdate' => '2022-08-01',
            'enddate' => '2022-08-13',
        ]);

        $response->assertStatus(200);

        Mail::assertSent(StockHistory::class);
    }
}
