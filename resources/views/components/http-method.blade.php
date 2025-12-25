@props(['method'])

@php
$type = match ($method) {
    'GET', 'OPTIONS', 'ANY' => 'default',
    'POST' => 'success',
    'PUT', 'PATCH' => 'primary',
    'DELETE' => 'error',
    default => 'default',
};
@endphp

<x-filament-exceptions::badge type="{{ $type }}">
    <x-filament-exceptions::icons.globe class="w-2.5 h-2.5" />
    {{ $method }}
</x-filament-exceptions::badge>
