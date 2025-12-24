@use('BezhanSalleh\FilamentExceptions\FilamentExceptions')
@php
    $exception = $this->getStoredException();
    $exceptionAsMarkdown = $exception->markdown();
    $record = $exception->record();
@endphp

<x-filament-panels::page class="min-h-dvh [&_.fi-page-content]:gap-y-0!">

    {!! FilamentExceptions::renderCss() !!}

    <x-laravel-exceptions-renderer::section-container class="px-6 py-0 sm:py-0">
        <x-laravel-exceptions-renderer::topbar :title="$exception->title()" :markdown="$exceptionAsMarkdown" />
    </x-laravel-exceptions-renderer::section-container>

    <x-laravel-exceptions-renderer::separator />

    <x-laravel-exceptions-renderer::section-container class="flex flex-col gap-8 py-0 sm:py-0 [&>div:last-child]:z-10">
        <x-laravel-exceptions-renderer::header :$exception />
    </x-laravel-exceptions-renderer::section-container>

    <x-laravel-exceptions-renderer::separator class="-mt-5 -z-10" />

    <x-laravel-exceptions-renderer::section-container class="flex flex-col gap-8 pt-14">
        <x-laravel-exceptions-renderer::trace :$exception />

        <x-laravel-exceptions-renderer::query :queries="$exception->applicationQueries()" />
    </x-laravel-exceptions-renderer::section-container>

    <x-laravel-exceptions-renderer::separator />

    <x-laravel-exceptions-renderer::section-container class="flex flex-col gap-12">
        <x-laravel-exceptions-renderer::request-header :headers="$exception->requestHeaders()" />

        <x-laravel-exceptions-renderer::request-body :body="$exception->requestBody()" />

        <x-laravel-exceptions-renderer::routing :routing="$exception->applicationRouteContext()" />

        <x-laravel-exceptions-renderer::routing-parameter :routeParameters="$exception->applicationRouteParametersContext()" />

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
    </x-laravel-exceptions-renderer::section-container>

    <x-laravel-exceptions-renderer::separator />

    <x-laravel-exceptions-renderer::section-container class="pb-0 sm:pb-0">
        <div class="flex flex-wrap items-center gap-4 text-xs text-neutral-500 dark:text-neutral-400 pb-2">
            <div class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4">
                    <path d="M4.5 4.5a3 3 0 0 0-3 3v9a3 3 0 0 0 3 3h8.25a3 3 0 0 0 3-3v-9a3 3 0 0 0-3-3H4.5ZM19.94 18.75l-2.69-2.69V7.94l2.69-2.69c.944-.945 2.56-.276 2.56 1.06v11.38c0 1.336-1.616 2.005-2.56 1.06Z" />
                </svg>


                <span>Recorded: {{ $record->created_at->format('M d, Y H:i:s') }}</span>
            </div>
            @if ($record->ip)
                <div class="flex items-center gap-1.5">
                    <x-laravel-exceptions-renderer::icons.globe class="w-4 h-4" />
                    <span class="font-mono">{{ $record->ip }}</span>
                </div>
            @endif
        </div>
    </x-laravel-exceptions-renderer::section-container>
    {!! FilamentExceptions::renderJs() !!}
</x-filament-panels::page>
