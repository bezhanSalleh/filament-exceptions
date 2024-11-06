@props(['queries'])

@forelse (collect(json_decode($queries,true))->all() ?? [] as $query)
    <div class="p-4 mb-4 bg-gray-100 rounded-lg dark:text-100 dark:bg-white/[0.03]">

        <div
            class="flex items-center justify-start pb-2 space-x-2 text-xs font-medium leading-6 text-gray-900 dark:text-gray-50">
            <div
                class="flex items-center px-2 py-1 text-xs font-medium transition duration-75 rounded-lg outline-none gap-x-1 hover:bg-gray-950/5 focus:bg-gray-950/5 dark:hover:bg-white/5 dark:focus:bg-white/5 bg-gray-950/5 dark:bg-white/5 text-primary-600 dark:text-primary-400">
                <x-filament::icon alias="fe::queryClock" class="w-4 h-4 text-gray-400 dark:text-gray-500"
                    icon="heroicon-s-clock" />
                <span>
                    {{ str($query['time'])->append('MS') }}
                </span>
            </div>
            <div
                class="flex items-center px-2 py-1 text-xs font-medium transition duration-75 rounded-lg outline-none gap-x-1 hover:bg-gray-950/5 focus:bg-gray-950/5 dark:hover:bg-white/5 dark:focus:bg-white/5 bg-gray-950/5 dark:bg-white/5 text-primary-600 dark:text-primary-400">
                <x-filament::icon alias="fe::queryConnection" class="w-4 h-4 text-gray-400 dark:text-gray-500"
                    icon="heroicon-s-circle-stack" />
                <span>
                    {{ str($query['connection_name'])->upper() }}
                </span>
            </div>
        </div>
        <div class="p-4 space-y-2 bg-white rounded-lg dark:bg-gray-900">
            <code class="language-sql !text-primary-600 dark:text-primary-600">
                {{ $query['sql'] }}
            </code>
        </div>

        @if (count($query['bindings']))
            <div class="flex flex-row mt-2">
                <div
                    class="w-full overflow-hidden rounded-lg bg-gray-50 dark:bg-gray-900 dark:border dark:border-gray-800">
                    @foreach ($query['bindings'] as $key => $value)
                        <div class="flex px-4 py-2">
                            <div class="py-2 font-mono text-gray-500 basis-1/12 dark:text-gray-400">{{ intVal($key) + 1 }}
                            </div>
                            <div
                                class="px-4 py-2 font-medium text-gray-500 rounded-lg basis-11/12 dark:text-gray-200 bg-gray-950/5 dark:bg-white/5">
                                {{ $value }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@empty
    []
@endforelse
