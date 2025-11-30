@php
    $method = $record->method;
    $methodColor = match ($method) {
        'DELETE' => \Illuminate\Support\Arr::toCssClasses(['text-danger-700 bg-danger-500/10 dark:text-danger-500 border border-danger-400/20']),
        'POST' => \Illuminate\Support\Arr::toCssClasses(['text-primary-700 bg-primary-500/10 dark:text-primary-500 border border-primary-400/20']),
        'GET' => \Illuminate\Support\Arr::toCssClasses(['text-success-700 bg-success-500/10 dark:text-success-500 border border-success-400/20']),
        'PUT' => \Illuminate\Support\Arr::toCssClasses(['text-warning-700 bg-warning-500/10 dark:text-warning-500 border border-warning-400/20']),
        'PATCH', 'OPTIONS' => \Illuminate\Support\Arr::toCssClasses(['text-gray-700 bg-gray-500/10 dark:text-gray-300 dark:bg-gray-500/20 border border-gray-400/20']),
        default => \Illuminate\Support\Arr::toCssClasses(['text-gray-700 bg-gray-500/10 dark:text-gray-300 dark:bg-gray-500/20 border border-gray-400/20']),
    };
@endphp

<x-filament-panels::page>
    <div
        class="rounded-xl border border-gray-200 bg-white px-6 py-5 shadow-none sm:px-6 dark:border-gray-950 dark:bg-gray-900"
        x-data="{
            init() {
                this.updateClasses()
                this.setTableFirstTdWidth()

                // Optional: observe dynamic changes
                const observer = new MutationObserver(() => {
                    this.updateClasses()
                    this.setTableFirstTdWidth()
                })
                this.$el.querySelectorAll('li.fi-in-repeatable-item').forEach((li) => {
                    observer.observe(li, { childList: true, subtree: true })
                })
                this.$el
                    .querySelectorAll('table.fi-in-key-value tbody')
                    .forEach((tbody) => {
                        observer.observe(tbody, { childList: true, subtree: true })
                    })
            },

            updateClasses() {
                this.$el.querySelectorAll('li.fi-in-repeatable-item').forEach((li) => {
                    const visibleCols = Array.from(
                        li.querySelectorAll('div.fi-sc > div.fi-grid-col'),
                    ).filter((div) => ! div.classList.contains('fi-hidden')) // filter out hidden cols

                    const classes = [
                        '!p-4',
                        '!bg-gray-50',
                        '!rounded-xl',
                        '!dark:bg-gray-800',
                    ] // multiple Tailwind classes
                    if (visibleCols.length > 1) {
                        li.classList.add(...classes)
                    } else {
                        li.classList.remove(...classes)
                    }
                })
            },
            setTableFirstTdWidth() {
                document
                    .querySelectorAll('table.fi-in-key-value tbody')
                    .forEach((tbody) => {
                        tbody.querySelectorAll('tr').forEach((tr) => {
                            const firstTd = tr.querySelector('td')
                            if (firstTd) {
                                firstTd.style.width = '20%'
                            }
                        })
                    })
            },
        }"
    >
        <h3 class="flex items-center text-base leading-6 font-semibold text-gray-900 dark:text-gray-50">
            <span class="{{ $methodColor }} rounded-s-lg px-3 py-0.5">{{ $method }}</span>
            <span
                class="rounded-e-lg border border-gray-200 bg-gray-100 px-3 py-0.5 text-base text-gray-700 dark:border-gray-800 dark:bg-gray-800 dark:text-gray-100"
            >
                {{ $record->path }}
            </span>
        </h3>
        <div class="mt-1 flex max-w-2xl items-center text-sm leading-5 text-gray-500">
            <span class="mt-1 font-mono text-xs text-gray-600 dark:text-gray-200">
                {{ __('filament-exceptions::filament-exceptions.columns.occurred_at') }}:

                {{ $record->created_at->toDateTimeString() }}
            </span>
        </div>
        <div class="pt-5">
            {{ $record->message }}
        </div>
    </div>

    {{ $this->content }}
</x-filament-panels::page>
