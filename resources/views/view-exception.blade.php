@php
    $method = $record->method;
    $methodColor = match ($method) {
        'DELETE' => \Illuminate\Support\Arr::toCssClasses(['text-danger-700 bg-danger-500/10 dark:text-danger-500']),
        'POST' => \Illuminate\Support\Arr::toCssClasses(['text-primary-700 bg-primary-500/10 dark:text-primary-500']),
        'GET' => \Illuminate\Support\Arr::toCssClasses(['text-success-700 bg-success-500/10 dark:text-success-500']),
        'PUT' => \Illuminate\Support\Arr::toCssClasses(['text-warning-700 bg-warning-500/10 dark:text-warning-500']),
        'PATCH', 'OPTIONS' => \Illuminate\Support\Arr::toCssClasses(['text-gray-700 bg-gray-500/10 dark:text-gray-300 dark:bg-gray-500/20']),
        default => \Illuminate\Support\Arr::toCssClasses(['text-gray-700 bg-gray-500/10 dark:text-gray-300 dark:bg-gray-500/20']),
    };
@endphp
<x-filament::page
    @class([
        'fi-resource-view-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . $record->getKey(),
    ])
>

    <div
        class="px-6 py-5 bg-white border-b border-gray-200 rounded-lg shadow-none dark:bg-gray-900 dark:border-gray-950 sm:px-6">
        <h3 class="flex items-center text-lg font-medium leading-6 text-gray-900 dark:text-gray-50">
            <span class="{{ $methodColor }} rounded-md px-4 py-1 mr-2">{{ $method }}</span>
            <span
                class="px-4 py-1 ml-2 text-gray-800 bg-gray-100 rounded-md dark:bg-gray-800 dark:text-gray-100">{{ $record->path }}
            </span>
        </h3>
        <div class="flex items-center max-w-2xl mt-1 text-sm leading-5 text-gray-500">
            <span class="mt-1 font-mono text-xs text-gray-600 dark:text-gray-200">
                {{ __('filament-exceptions::filament-exceptions.columns.occurred_at') }}:

                {{ $record->created_at->toDateTimeString() }}
            </span>
        </div>
        <div class="py-5">
            {{ $record->message }}
        </div>
    </div>
    @php
        $relationManagers = $this->getRelationManagers();
    @endphp

    @if ((! $this->hasCombinedRelationManagerTabsWithContent()) || (! count($relationManagers)))
        @if ($this->hasInfolist())
            {{ $this->infolist }}
        @else
            <div
                wire:key="{{ $this->getId() }}.forms.{{ $this->getFormStatePath() }}"
            >
                {{ $this->form }}
            </div>
        @endif
    @endif
</x-filament::page>

