<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class RecurringTransactionExecutedNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public string $title;

    public string $message;

    public string $amount;

    public string $type;

    public int $recurringId;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $title, string $message, string $amount, string $type, int $recurringId)
    {
        $this->title = $title;
        $this->message = $message;
        $this->amount = $amount;
        $this->type = $type;
        $this->recurringId = $recurringId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification for the database.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'amount' => $this->amount,
            'type' => $this->type,
            'recurring_id' => $this->recurringId,
            'category' => 'recurring',
            'action_url' => '/recurring-transactions',
            'action_text' => 'Kelola Transaksi Berulang',
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => $this->title,
            'message' => $this->message,
            'amount' => $this->amount,
            'type' => $this->type,
            'recurring_id' => $this->recurringId,
            'category' => 'recurring',
            'action_url' => '/recurring-transactions',
            'action_text' => 'Kelola Transaksi Berulang',
            'created_at' => now()->toIso8601String(),
        ]);
    }
}
