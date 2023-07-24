@props(['queries'])

@forelse (collect(json_decode($queries,true))->all() ?? [] as $query)
    <div class="p-4 mb-4 bg-gray-100 rounded-lg dark:text-100 dark:bg-white/[0.03]">

        <div
            class="flex items-center justify-start pb-2 space-x-2 text-xs font-medium leading-6 text-gray-900 dark:text-gray-50">
            <div
                class="flex items-center gap-x-1 rounded-lg px-2 py-1 text-xs font-medium outline-none transition duration-75 hover:bg-gray-950/5 focus:bg-gray-950/5 dark:hover:bg-white/5 dark:focus:bg-white/5 bg-gray-950/5 dark:bg-white/5 text-primary-600 dark:text-primary-400">
                <x-heroicon-s-clock class="h-4 w-4 text-gray-400 dark:text-gray-500" />
                <span>
                    {{ str($query['time'])->append('MS') }}
                </span>
            </div>
            <div
                class="flex items-center gap-x-1 rounded-lg px-2 py-1 text-xs font-medium outline-none transition duration-75 hover:bg-gray-950/5 focus:bg-gray-950/5 dark:hover:bg-white/5 dark:focus:bg-white/5 bg-gray-950/5 dark:bg-white/5 text-primary-600 dark:text-primary-400">
                <x-heroicon-s-circle-stack class="h-4 w-4 text-gray-400 dark:text-gray-500" />
                <span>
                    {{ str($query['connection_name'])->upper() }}
                </span>
            </div>
        </div>
        <div class="p-4 space-y-2 bg-white dark:bg-gray-900 rounded-lg">
            <code class="language-sql !text-primary-600 dark:text-primary-600">
                {{ $query['sql'] }}
            </code>
        </div>

        @if (count($query['bindings']))
            <div class="flex flex-row mt-2">
                <div
                    class="w-full overflow-hidden bg-gray-50 rounded-lg dark:bg-gray-900 dark:border dark:border-gray-800">
                    @foreach ($query['bindings'] as $key => $value)
                        <div class="flex px-4 py-2">
                            <div class="basis-1/12 text-gray-500 dark:text-gray-400 font-mono py-2">{{ $key + 1 }}
                            </div>
                            <div
                                class="basis-11/12 text-gray-500 dark:text-gray-200 bg-gray-950/5 dark:bg-white/5 font-medium py-2 px-4 rounded-lg">
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
