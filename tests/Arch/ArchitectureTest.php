<?php

/**
 * Architecture Tests
 *
 * Enforces the Clean Architecture Dependency Rule for all modules.
 * These tests run via: php artisan test --filter ArchitectureTest
 *                   or: ./vendor/bin/pest tests/Arch/
 */
test('domain layer does not depend on infrastructure, presentation, or laravel framework')
    ->expect('App\Modules\Account\Domain')
    ->not->toUse([
        'App\Modules\Account\Infrastructure',
        'App\Modules\Account\Presentation',
        'Illuminate\Database',
        'Illuminate\Http',
        'Illuminate\Support\Facades',
    ]);

test('category domain layer does not depend on infrastructure or presentation')
    ->expect('App\Modules\Category\Domain')
    ->not->toUse([
        'App\Modules\Category\Infrastructure',
        'App\Modules\Category\Presentation',
        'Illuminate\Database',
        'Illuminate\Http',
    ]);

test('transaction domain layer does not depend on infrastructure or presentation')
    ->expect('App\Modules\Transaction\Domain')
    ->not->toUse([
        'App\Modules\Transaction\Infrastructure',
        'App\Modules\Transaction\Presentation',
        'Illuminate\Database',
        'Illuminate\Http',
    ]);

test('transfer domain layer does not depend on infrastructure or presentation')
    ->expect('App\Modules\Transfer\Domain')
    ->not->toUse([
        'App\Modules\Transfer\Infrastructure',
        'App\Modules\Transfer\Presentation',
        'Illuminate\Database',
        'Illuminate\Http',
    ]);

test('user domain layer does not depend on infrastructure or presentation')
    ->expect('App\Modules\User\Domain')
    ->not->toUse([
        'App\Modules\User\Infrastructure',
        'App\Modules\User\Presentation',
        'Illuminate\Database',
        'Illuminate\Http',
    ]);

test('ledger domain layer does not depend on infrastructure or presentation')
    ->expect('App\Modules\Ledger\Domain')
    ->not->toUse([
        'App\Modules\Ledger\Infrastructure',
        'App\Modules\Ledger\Presentation',
        'Illuminate\Database',
        'Illuminate\Http',
    ]);

test('audit domain layer does not depend on infrastructure or presentation')
    ->expect('App\Modules\Audit\Domain')
    ->not->toUse([
        'App\Modules\Audit\Infrastructure',
        'App\Modules\Audit\Presentation',
        'Illuminate\Database',
        'Illuminate\Http',
    ]);

test('application layer does not depend on presentation or infrastructure')
    ->expect('App\Modules\Account\Application')
    ->not->toUse([
        'App\Modules\Account\Presentation',
        'App\Modules\Account\Infrastructure',
        'Illuminate\Http',
        'Illuminate\Database\Eloquent',
    ]);

test('presentation layer does not reach into infrastructure')
    ->expect('App\Modules\Account\Presentation')
    ->not->toUse('App\Modules\Account\Infrastructure\Persistence\Repositories');

test('presentation layer does not reach into infrastructure for category')
    ->expect('App\Modules\Category\Presentation')
    ->not->toUse('App\Modules\Category\Infrastructure\Persistence\Repositories');

test('modules do not cross-import domain entities')
    ->expect('App\Modules\Account\Domain')
    ->not->toUse('App\Modules\Transaction\Domain')
    ->not->toUse('App\Modules\Category\Domain')
    ->not->toUse('App\Modules\Transfer\Domain');

test('transaction domain does not import account domain entities')
    ->expect('App\Modules\Transaction\Domain')
    ->not->toUse('App\Modules\Account\Domain');
