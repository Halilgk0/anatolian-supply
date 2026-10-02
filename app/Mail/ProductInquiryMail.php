<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ProductInquiryMail extends Mailable
{
    use Queueable;

    /**
     * @param  array{name: string, email: string, phone?: ?string, color?: ?string, size?: ?string, message: string}  $inquiry
     */
    public function __construct(
        public Product $product,
        public array $inquiry,
    ) {}

    /**
     * On the live site, the log and array mailers would accept the inquiry without anyone ever receiving it.
     */
    public static function canBeDelivered(): bool
    {
        return ! app()->isProduction() || ! in_array(config('mail.default'), ['log', 'array'], true);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->inquiry['email'], $this->inquiry['name'])],
            subject: "Bilgi talebi: {$this->product->name} ({$this->product->code})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.product-inquiry',
        );
    }
}
