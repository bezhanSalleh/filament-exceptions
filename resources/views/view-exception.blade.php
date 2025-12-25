@use('BezhanSalleh\FilamentExceptions\FilamentExceptions')
@php
    $exception = $this->getStoredException();
    $exceptionAsMarkdown = $exception->markdown();
    $record = $exception->record();
@endphp

<x-filament-panels::page class="min-h-dvh [&_.fi-page-content]:gap-y-0!">

    {!! FilamentExceptions::renderCss() !!}

    <x-filament-exceptions::section-container class="px-6 py-0 sm:py-0">
        <x-filament-exceptions::topbar :title="$exception->title()" :markdown="$exceptionAsMarkdown" />
    </x-filament-exceptions::section-container>

    <x-filament-exceptions::separator />

    <x-filament-exceptions::section-container class="flex flex-col gap-8 py-0 sm:py-0 [&>div:last-child]:z-10 dark:[&>div>div:last-child]:bg-gray-900!">
        <x-filament-exceptions::header :$exception />
    </x-filament-exceptions::section-container>

    <x-filament-exceptions::separator class="-mt-5 -z-10" />

    <x-filament-exceptions::section-container class="flex flex-col gap-8 pt-14">
        <x-filament-exceptions::trace :$exception />

        <x-filament-exceptions::query :queries="$exception->applicationQueries()" />
    </x-filament-exceptions::section-container>

    <x-filament-exceptions::separator />

    <x-filament-exceptions::section-container class="flex flex-col gap-12">
        <x-filament-exceptions::request-header :headers="$exception->requestHeaders()" />

        <x-filament-exceptions::request-body :body="$exception->requestBody()" />

        <x-filament-exceptions::routing :routing="$exception->applicationRouteContext()" />

        <x-filament-exceptions::routing-parameter :routeParameters="$exception->applicationRouteParametersContext()" />

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
    </x-filament-exceptions::section-container>

    <x-filament-exceptions::separator />

    <x-filament-exceptions::section-container class="pb-0 sm:pb-0">
        <div class="flex flex-wrap items-center gap-4 text-xs text-neutral-500 dark:text-neutral-400 pb-2">
            <div class="flex items-center gap-1.5">
                <x-filament-exceptions::icons.info class="w-4 h-4" />
                <span>Recorded: {{ $record->created_at->format('M d, Y H:i:s') }}</span>
            </div>
            @if ($record->ip)
                <div class="flex items-center gap-1.5">
                    <x-filament-exceptions::icons.globe class="w-4 h-4" />
                    <span class="font-mono">{{ $record->ip }}</span>
                </div>
            @endif
        </div>
    </x-filament-exceptions::section-container>
    {!! FilamentExceptions::renderJs() !!}
</x-filament-panels::page>
