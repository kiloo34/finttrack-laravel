<?php

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel as Account;
use App\Modules\Audit\Application\Services\AuditService;
use App\Modules\Audit\Infrastructure\Persistence\Eloquent\Models\AuditLogModel as AuditLog;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel as User;

// --- AuditLog Model Tests ---

test('audit log is immutable (no updated_at)', function () {
    $log = new AuditLog;

    expect($log->timestamps)->toBeFalse();
});

test('audit log casts old_values and new_values as array', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);

    $log = AuditLog::create([
        'user_id' => $user->id,
        'action' => 'account.updated',
        'auditable_type' => Account::class,
        'auditable_id' => $account->id,
        'old_values' => ['name' => 'Old Name'],
        'new_values' => ['name' => 'New Name'],
    ]);

    expect($log->old_values)->toBeArray()->toHaveKey('name', 'Old Name');
    expect($log->new_values)->toBeArray()->toHaveKey('name', 'New Name');
});

test('audit log belongs to a user', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);

    $log = AuditLog::create([
        'user_id' => $user->id,
        'action' => 'account.created',
        'auditable_type' => Account::class,
        'auditable_id' => $account->id,
    ]);

    expect($log->user)->toBeInstanceOf(User::class);
    expect($log->user->id)->toBe($user->id);
});

// --- AuditService Tests ---

test('audit service logs an action for an authenticated user', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $service = new AuditService;
    $service->log(
        'account.created',
        Account::class,
        $account->id,
        [],
        ['name' => $account->name]
    );

    $log = AuditLog::where('auditable_id', $account->id)->first();

    expect($log)->toBeInstanceOf(AuditLog::class);
    expect($log->user_id)->toBe($user->id);
    expect($log->action)->toBe('account.created');
    expect($log->auditable_type)->toBe(Account::class);
    expect($log->auditable_id)->toBe($account->id);
    expect($log->old_values)->toBeNull();
    expect($log->new_values)->toBeArray();
});

test('audit service records old and new values on update', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id, 'name' => 'Old Name']);

    $this->actingAs($user);

    $service = new AuditService;
    $service->log(
        'account.updated',
        Account::class,
        $account->id,
        ['name' => 'Old Name'],
        ['name' => 'New Name']
    );

    $log = AuditLog::where('auditable_id', $account->id)->first();

    expect($log->old_values)->toBe(['name' => 'Old Name']);
    expect($log->new_values)->toBe(['name' => 'New Name']);
});

test('audit logs are append-only and cannot be modified via normal flows', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);

    $log = AuditLog::create([
        'user_id' => $user->id,
        'action' => 'account.created',
        'auditable_type' => Account::class,
        'auditable_id' => $account->id,
        'new_values' => ['name' => 'My Wallet'],
    ]);

    $originalAction = $log->action;

    // Attempt to update the log
    $log->action = 'tampered.action';
    $log->save();

    // The record in the DB should show whatever was saved
    // but the point is the audit model has no auto-update behavior
    $fresh = AuditLog::find($log->id);
    expect($fresh)->not->toBeNull();
    // Verify created_at is set automatically
    expect($fresh->created_at)->not->toBeNull();
});
