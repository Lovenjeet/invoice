<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceApprovedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $pdfContent,
        public string $invoiceNo
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invoice Approved - ' . $this->invoiceNo)
            ->greeting('Hello!')
            ->line('Your invoice has been approved and is attached to this email.')
            ->line('Invoice Number: ' . $this->invoiceNo)
            ->line('Please find the invoice PDF attached.')
            ->attachData($this->pdfContent, 'Invoice_' . $this->invoiceNo . '.pdf', [
                'mime' => 'application/pdf',
            ])
            ->salutation('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_no' => $this->invoiceNo,
        ];
    }
}
