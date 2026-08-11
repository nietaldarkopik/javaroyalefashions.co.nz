<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Order $order) {}

    public function build(): self
    {
        return $this->subject("Invoice {$this->order->order_number} — ".config('app.name'))
            ->view('emails.invoice');
    }
}
