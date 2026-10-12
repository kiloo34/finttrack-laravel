<?php

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel as Account;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel as Category;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel as Transaction;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel as User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'ConstantsSeeder']);
    Storage::fake('public');

    $this->user = User::factory()->create();
    $this->account = Account::factory()->create(['user_id' => $this->user->id]);
    $this->category = Category::factory()->create(['user_id' => $this->user->id, 'type' => 'expense']);
    $this->transaction = Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
    ]);
});

it('can upload attachment successfully', function () {
    $file = UploadedFile::fake()->image('receipt.jpg');

    $response = $this->actingAs($this->user, 'sanctum')->postJson("/api/transactions/{$this->transaction->id}/attachment", [
        'attachment' => $file,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Attachment uploaded successfully',
        ])
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'attachment_url',
            ],
        ]);

    $attachmentUrl = $response->json('data.attachment_url');
    expect($attachmentUrl)->not->toBeEmpty();

    $this->assertDatabaseHas('transactions', [
        'id' => $this->transaction->id,
        'attachment_url' => $attachmentUrl,
    ]);

    $relativePath = Str::after($attachmentUrl, '/storage/');
    Storage::disk('public')->assertExists($relativePath);
});

it('fails to upload if attachment is not an image', function () {
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $response = $this->actingAs($this->user, 'sanctum')->postJson("/api/transactions/{$this->transaction->id}/attachment", [
        'attachment' => $file,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['attachment']);
});

it('fails to upload if attachment exceeds maximum size limit of 5MB', function () {
    $file = UploadedFile::fake()->create('large_receipt.jpg', 6000, 'image/jpeg');

    $response = $this->actingAs($this->user, 'sanctum')->postJson("/api/transactions/{$this->transaction->id}/attachment", [
        'attachment' => $file,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['attachment']);
});

it('fails to upload if attachment field is missing', function () {
    $response = $this->actingAs($this->user, 'sanctum')->postJson("/api/transactions/{$this->transaction->id}/attachment", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['attachment']);
});

it('prevents user from uploading attachment to another users transaction', function () {
    $userB = User::factory()->create();
    $accountB = Account::factory()->create(['user_id' => $userB->id]);
    $transactionB = Transaction::factory()->create([
        'account_id' => $accountB->id,
    ]);

    $file = UploadedFile::fake()->image('receipt.jpg');

    $response = $this->actingAs($this->user, 'sanctum')->postJson("/api/transactions/{$transactionB->id}/attachment", [
        'attachment' => $file,
    ]);

    $response->assertStatus(403);
});

it('returns 404 when uploading attachment to non-existent transaction', function () {
    $file = UploadedFile::fake()->image('receipt.jpg');

    $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/transactions/999999/attachment', [
        'attachment' => $file,
    ]);

    $response->assertStatus(404);
});

it('serializes attachment_url in transaction show response', function () {
    $file = UploadedFile::fake()->image('receipt.jpg');

    $uploadResponse = $this->actingAs($this->user, 'sanctum')->postJson("/api/transactions/{$this->transaction->id}/attachment", [
        'attachment' => $file,
    ]);

    $attachmentUrl = $uploadResponse->json('data.attachment_url');

    $response = $this->actingAs($this->user, 'sanctum')->getJson("/api/transactions/{$this->transaction->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.attachment_url', $attachmentUrl);
});
