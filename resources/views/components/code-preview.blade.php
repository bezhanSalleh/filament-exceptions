@props([
    'contents' => '',
])

<div class="flex flex-col md:flex-row">
    <div class="bg-white dark:bg-gray-800 overflow-hidden dark:rounded-lg  dark:border dark:border-gray-700 w-full">
        <div>
            @forelse (collect(json_decode($contents,true))->sortKeys()->all() as $name => $values)
                <div
                    class="bg-gray-100 even:bg-white dark:even:bg-gray-700 dark:bg-gray-800 px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 font-mono">
                    <dt
                        class="text-sm leading-5 font-medium dark:text-gray-200 text-gray-700 dark:border-r dark:border-r-gray-200">
                        {{ implode('-', array_map('ucfirst', explode('-', $name))) }}
                    </dt>
                    <dd
                        class="mt-1 text-sm leading-5 dark:text-gray-200 text-gray-900 sm:mt-0 sm:col-span-2 break-words">
                        {{ is_array($values) ? implode(' ', $values) : $values }}
                    </dd>
                </div>
            @empty
                <pre class="language_"><code>[]</code></pre>
            @endforelse

        </div>
    </div>
</div>
