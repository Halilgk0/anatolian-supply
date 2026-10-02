<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductInquiryRequest;
use App\Mail\ProductInquiryMail;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ProductInquiryController extends Controller
{
    /**
     * Email a visitor's information request about a product to the store inbox.
     */
    public function store(StoreProductInquiryRequest $request, Product $product): JsonResponse|RedirectResponse
    {
        $inquiry = $request->safe()->except('website');

        if (! ProductInquiryMail::canBeDelivered()) {
            $unavailable = 'E-posta formu şu an kapalı. Hazır mesajı e-posta uygulamanda açabilir ya da Instagram’dan yazabilirsin.';

            return $request->expectsJson()
                ? response()->json(['message' => $unavailable], 503)
                : back()->withInput()->withErrors(['inquiry' => $unavailable]);
        }

        try {
            Mail::to(config('store.inquiry_email'))->send(new ProductInquiryMail($product, $inquiry));
        } catch (TransportExceptionInterface $exception) {
            report($exception);

            $failure = 'Talep şu anda gönderilemedi. E-posta uygulamanla göndermeyi ya da Instagram’dan yazmayı dene.';

            return $request->expectsJson()
                ? response()->json(['message' => $failure], 503)
                : back()->withInput()->withErrors(['inquiry' => $failure]);
        }

        $confirmation = "Talebin gönderildi. {$inquiry['email']} adresine en kısa sürede dönüş yapacağız.";

        return $request->expectsJson()
            ? response()->json(['message' => $confirmation])
            : back()->with('inquiry_sent', $confirmation);
    }
}
