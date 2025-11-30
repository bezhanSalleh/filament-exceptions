@php
    use Illuminate\Foundation\Exceptions\Renderer\Renderer;
    use BezhanSalleh\FilamentExceptions\FilamentExceptionsServiceProvider;

    $exception = $this->getStoredException();
    $record = $exception->record();
@endphp

<div class="gap-8 flex">
    {{-- Include our custom CSS (class-based dark mode for Filament compatibility) --}}
    {{-- {!! Renderer::css() !!} --}}
    {!! FilamentExceptionsServiceProvider::css() !!}
    <div class="flex flex-col gap-y-8" dir="ltr">
        {{-- Use Laravel's header component --}}
        <x-laravel-exceptions-renderer::header :$exception />

        {{-- Use Laravel's trace component --}}
        <x-laravel-exceptions-renderer::trace :$exception />

        {{-- Use Laravel's query component --}}
        <x-laravel-exceptions-renderer::query :queries="$exception->applicationQueries()" />

        {{-- Use Laravel's request-header component --}}
        <x-laravel-exceptions-renderer::request-header :headers="$exception->requestHeaders()" />

        {{-- Use Laravel's request-body component --}}
        <x-laravel-exceptions-renderer::request-body :body="$exception->requestBody()" />

        {{-- Use Laravel's routing component --}}
        <x-laravel-exceptions-renderer::routing :routing="$exception->applicationRouteContext()" />

        {{-- Use Laravel's routing-parameter component --}}
        <x-laravel-exceptions-renderer::routing-parameter :routeParameters="$exception->applicationRouteParametersContext()" />

        {{-- Cookies Section (not in Laravel's renderer, but we store it) --}}
        @if (filled($record->cookies))
            <div class="flex flex-col gap-3">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Cookies</h2>
                <div class="flex flex-col gap-1">
                    @foreach ($record->cookies as $key => $value)
                        <div class="flex max-w-full items-baseline gap-2 text-sm font-mono py-1">
                            <div class="uppercase text-neutral-500 dark:text-neutral-400 shrink-0">{{ $key }}</div>
                            <div class="min-w-6 grow h-3 border-b-2 border-dotted border-neutral-300 dark:border-neutral-600"></div>
                            <div class="truncate text-neutral-900 dark:text-white" title="{{ is_array($value) ? json_encode($value) : $value }}">
                                {{ is_array($value) ? json_encode($value) : $value }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Timestamp & IP (extra info not in Laravel's renderer) --}}
        <div class="flex flex-wrap items-center gap-4 text-xs text-neutral-500 dark:text-neutral-400 pb-4">
            <div class="flex items-center gap-1.5">
                <x-laravel-exceptions-renderer::icons.info class="w-4 h-4" />
                <span>Recorded: {{ $record->created_at->format('M d, Y H:i:s') }}</span>
            </div>
            @if ($record->ip)
                <div class="flex items-center gap-1.5">
                    <x-laravel-exceptions-renderer::icons.globe class="w-4 h-4" />
                    <span class="font-mono">{{ $record->ip }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Include our custom JS (without Alpine.js to avoid Filament conflicts) --}}
    {!! FilamentExceptionsServiceProvider::js() !!}
</div>
