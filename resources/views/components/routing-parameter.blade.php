@props(['routeParameters'])

<div class="flex flex-col gap-3">
    <h2 class="text-lg font-semibold">Routing parameters</h2>
    @if ($routeParameters)
    <div class="bg-white dark:bg-white/[2%] border border-neutral-200 dark:border-neutral-800 rounded-md overflow-x-auto p-5 text-sm font-mono shadow-xs">
        <x-filament-exceptions::syntax-highlight :code="$routeParameters" language="json" />
    </div>
    @else
    <x-filament-exceptions::empty-state message="No routing parameters" />
    @endif
</div>
