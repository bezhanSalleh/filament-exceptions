<x-filament::page :widget-data="['record' => $record]" class="filament-resources-view-record-page">
    @php
        $method = $record->method;
        $methodColor = match($method) {
            'DELETE' => \Illuminate\Support\Arr::toCssClasses(['text-danger-700 bg-danger-500/10', 'dark:text-danger-500' => config('tables.dark_mode')]),
            'POST' => \Illuminate\Support\Arr::toCssClasses(['text-primary-700 bg-primary-500/10', 'dark:text-primary-500' => config('tables.dark_mode')]),
            'GET' => \Illuminate\Support\Arr::toCssClasses(['text-success-700 bg-success-500/10', 'dark:text-success-500' => config('tables.dark_mode')]),
            'PUT' => \Illuminate\Support\Arr::toCssClasses(['text-warning-700 bg-warning-500/10', 'dark:text-warning-500' => config('tables.dark_mode')]),
            'PATCH', 'OPTIONS' => \Illuminate\Support\Arr::toCssClasses(['text-gray-700 bg-gray-500/10', 'dark:text-gray-300 dark:bg-gray-500/20' => config('tables.dark_mode')]),
            default => \Illuminate\Support\Arr::toCssClasses(['text-gray-700 bg-gray-500/10', 'dark:text-gray-300 dark:bg-gray-500/20' => config('tables.dark_mode')])
        }
    @endphp
    <div
        class="dark:bg-gray-700 px-4 py-5 border-b dark:border-gray-700 border-gray-200 sm:px-6 bg-white rounded-lg shadow-none">
        <h3 class="text-lg leading-6 font-medium dark:text-gray-50 text-gray-900 flex items-center">
            <span class="{{ $methodColor }} text-white rounded-md px-4 py-1 mr-2">{{ $method }}</span>
            <span
                class="bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100 rounded-md px-4 py-1 ml-2">{{ $record->path }}
            </span>
        </h3>
        <div class="mt-1 max-w-2xl text-sm leading-5 text-gray-500 flex items-center">
            <span class="text-xs text-gray-600 dark:text-gray-200 font-mono mt-1">
                {{__('filament-exceptions::filament-exceptions.columns.occurred_at')}}:

                {{ $record->created_at->toDateTimeString() }}
            </span>
        </div>
        <div class="py-5">
            {{ $record->message }}
        </div>
    </div>
    {{ $this->form }}

    @if (count($relationManagers = $this->getRelationManagers()))
        <x-filament::hr />

        <x-filament::resources.relation-managers :active-manager="$activeRelationManager" :managers="$relationManagers" :owner-record="$record" />
    @endif
</x-filament::page>
