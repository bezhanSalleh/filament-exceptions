@props(['queries'])

@forelse (collect(json_decode($queries,true))->all() ?? [] as $query)
    <div class="p-4 mb-4 dark:bg-gray-800 dark:text-200 rounded-lg bg-gray-100">

        <div
            class=" text-xs leading-6 font-medium dark:text-gray-50 text-gray-900 flex items-center justify-start space-x-2 pb-2">
            <div @class([
                'inline-flex items-center justify-center space-x-1 rtl:space-x-reverse min-h-6 px-2 py-0.5 text-sm font-medium tracking-tight rounded-lg whitespace-nowrap text-primary-700 bg-primary-500/10',
                'dark:text-primary-500' => config('tables.dark_mode'),
            ])>
                <x-heroicon-s-clock class="h-4 w-4" />
                <span>
                    {{ str($query['time'])->append('MS') }}
                </span>
            </div>
            <div @class([
                'inline-flex items-center justify-center space-x-1 rtl:space-x-reverse min-h-6 px-2 py-0.5 text-sm font-medium tracking-tight rounded-lg whitespace-nowrap text-primary-700 bg-primary-500/10',
                'dark:text-primary-500' => config('tables.dark_mode'),
            ])>
                <x-heroicon-s-database class="h-4 w-4" />
                <span>
                    {{ str($query['connection_name'])->upper() }}
                </span>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg space-y-2">
            <code class="language-sql">
                {{ $query['sql'] }}
            </code>
        </div>

        {{-- @if (count($query['bindings']))
            <table @class([
                'text-gray-700 bg-gray-500/10 mt-2 p-1 rounded-lg',
                'dark:text-gray-300 dark:bg-gray-500/20' => config('tables.dark_mode'),
            ])>
                <tbody>
                    @foreach ($query['bindings'] as $key => $value)
                        <tr>
                            <td>
                                <div
                                    class="flex items-center justify-around px-3 pt-2 space-x-4 @if ($loop->last) pb-2 @endif">
                                    <span>
                                        <span class="text-gray-400">{{ $key + 1 }}</span>
                                        <b class="bg-primary-500/20 text-primary-500">?</b>
                                    </span>
                                    <x-heroicon-s-arrow-sm-right class="h-4 w-4 text-primary-500" />
                                    <span class="text-gray-500 font-medium">{{ $value }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif --}}
    </div>
@empty
    []
@endforelse
