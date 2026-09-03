<?php

use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel as User;
use App\Notifications\RecurringTransactionExecutedNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

it('lists unread and read notifications for the authenticated user', function () {
    $this->user->notify(new RecurringTransactionExecutedNotification(
        title: 'Monthly Recurring Bill',
        message: 'Hosting payment processed',
        amount: '150000',
        type: 'expense',
        recurringId: 1
    ));

    $response = $this->actingAs($this->user)
        ->getJson(route('notifications.api.index'));

    $response->assertOk()
        ->assertJsonStructure([
            'notifications' => [
                '*' => [
                    'id',
                    'type',
                    'data' => [
                        'title',
                        'message',
                        'amount',
                        'type',
                        'recurring_id',
                        'category',
                        'action_url',
                        'action_text',
                    ],
                    'read_at',
                    'created_at',
                ],
            ],
        ]);

    expect($response->json('notifications'))->toHaveCount(1);
    expect($response->json('notifications.0.data.category'))->toBe('recurring');
    expect($response->json('notifications.0.data.action_url'))->toBe('/recurring-transactions');
});

it('marks a single notification as read', function () {
    $this->user->notify(new RecurringTransactionExecutedNotification(
        title: 'Subscription',
        message: 'Netflix subscription paid',
        amount: '186000',
        type: 'expense',
        recurringId: 2
    ));

    $notification = $this->user->notifications()->first();
    expect($notification->read_at)->toBeNull();

    $response = $this->actingAs($this->user)
        ->postJson(route('notifications.api.read', ['id' => $notification->id]));

    $response->assertOk()
        ->assertJson(['success' => true]);

    expect($this->user->fresh()->unreadNotifications)->toHaveCount(0);
});

it('cannot mark another users notification as read', function () {
    $this->otherUser->notify(new RecurringTransactionExecutedNotification(
        title: 'Secret Alert',
        message: 'Private transaction',
        amount: '500000',
        type: 'income',
        recurringId: 3
    ));

    $otherNotification = $this->otherUser->notifications()->first();

    $response = $this->actingAs($this->user)
        ->postJson(route('notifications.api.read', ['id' => $otherNotification->id]));

    $response->assertNotFound();
    expect($otherNotification->fresh()->read_at)->toBeNull();
});

it('marks all notifications as read', function () {
    $this->user->notify(new RecurringTransactionExecutedNotification(
        title: 'Tx 1',
        message: 'Message 1',
        amount: '10000',
        type: 'expense',
        recurringId: 1
    ));
    $this->user->notify(new RecurringTransactionExecutedNotification(
        title: 'Tx 2',
        message: 'Message 2',
        amount: '20000',
        type: 'income',
        recurringId: 2
    ));

    expect($this->user->unreadNotifications)->toHaveCount(2);

    $response = $this->actingAs($this->user)
        ->postJson(route('notifications.api.read-all'));

    $response->assertOk()
        ->assertJson(['success' => true]);

    expect($this->user->fresh()->unreadNotifications)->toHaveCount(0);
});
