<?php

declare(strict_types=1);

use BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin;
use BezhanSalleh\FilamentExceptions\Models\Exception;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;

function recordExceptionAt(DateTimeInterface $createdAt): Exception
{
    $exception = Exception::create([
        'type' => 'TypeError',
        'code' => '0',
        'message' => 'something went wrong',
        'file' => 'app/Example.php',
        'line' => 1,
        'trace' => [],
        'method' => 'GET',
        'path' => '/',
        'query' => [],
        'headers' => [],
    ]);

    $exception->created_at = $createdAt;
    $exception->save();

    return $exception;
}

beforeEach(function (): void {
    $this->freezeTime();
});

afterEach(function (): void {
    Date::useDefault();
});

it('defaults the prune interval to a week ago', function (): void {
    expect(FilamentExceptionsPlugin::get()->getModelPruneInterval())
        ->toEqual(now()->subWeek());
});

it('uses the configured prune interval', function (): void {
    $interval = now()->subDays(3);

    FilamentExceptionsPlugin::get()->modelPruneInterval($interval);

    expect(FilamentExceptionsPlugin::get()->getModelPruneInterval())->toEqual($interval);
});

it('prunes only the exceptions older than the interval', function (): void {
    FilamentExceptionsPlugin::get()->modelPruneInterval(now()->subDays(3));

    $stale = recordExceptionAt(now()->subDays(10));
    $recent = recordExceptionAt(now()->subDay());

    expect((new Exception)->prunable()->pluck('id')->all())->toBe([$stale->id]);

    (new Exception)->prunable()->delete();

    expect(Exception::pluck('id')->all())->toBe([$recent->id]);
});

it('resolves the prune interval when the app uses immutable dates', function (): void {
    Date::use(CarbonImmutable::class);

    expect(now())->toBeInstanceOf(CarbonImmutable::class);

    expect(FilamentExceptionsPlugin::get()->getModelPruneInterval())
        ->toEqual(now()->subWeek());
});

it('accepts an immutable prune interval', function (): void {
    Date::use(CarbonImmutable::class);

    $interval = now()->subDays(3);

    FilamentExceptionsPlugin::get()->modelPruneInterval($interval);

    expect(FilamentExceptionsPlugin::get()->getModelPruneInterval())->toEqual($interval);
});

it('prunes exceptions when the app uses immutable dates', function (): void {
    Date::use(CarbonImmutable::class);

    $stale = recordExceptionAt(now()->subDays(10));
    recordExceptionAt(now()->subDay());

    expect((new Exception)->prunable()->pluck('id')->all())->toBe([$stale->id]);
});
