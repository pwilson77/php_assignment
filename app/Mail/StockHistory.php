<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StockHistory extends Mailable
{
    use Queueable, SerializesModels;
    protected $stockData;
    protected $emailMsg;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $msg, $subject)
    {
        $this->stockData = $data;
        $this->emailMsg = $msg;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.stockHistory')->with([
            'stockData' => $this->stockData,
            'emailMsg' => $this->emailMsg,
        ]);
    }
}
