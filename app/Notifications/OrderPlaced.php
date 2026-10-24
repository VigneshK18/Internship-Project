<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Order confirmation e-mail. Implements ShouldQueue, so it is pushed to the "jobs" table and sent
 * by `php artisan queue:work` instead of slowing down the checkout request.
 * With MAIL_MAILER=log the e-mail is written to storage/logs/laravel.log.
 */
class OrderPlaced extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order->loadMissing('items');
        $currency = config('shop.currency');

        $mail = (new MailMessage)
            ->subject("Order {$order->order_number} confirmed")
            ->greeting("Hello {$order->customer_name},")
            ->line("Thank you for shopping with ".config('shop.name').'. We have received your order.');

        foreach ($order->items as $item) {
            $mail->line("{$item->quantity} x {$item->product_name} - {$currency}".number_format((float) $item->line_total, 2));
        }

        return $mail
            ->line("Total: {$currency}".number_format((float) $order->total, 2).' (Cash on Delivery)')
            ->action('View your order', route('orders.show', $order))
            ->line('We will let you know when it ships.');
    }
}
