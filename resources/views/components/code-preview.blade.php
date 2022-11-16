@props([
    'contents' => '',
])

<div class="flex flex-col md:flex-row">
    <div class="w-full overflow-hidden bg-white dark:bg-gray-800 dark:rounded-lg dark:border dark:border-gray-700">
        <div>
            @forelse (collect(json_decode($contents,true))->sortKeys()->all() as $name => $values)
                <div
                    class="px-4 py-3 font-mono bg-gray-100 even:bg-white dark:even:bg-gray-700 dark:bg-gray-800 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt
                        class="text-sm font-medium leading-5 text-gray-700 break-words dark:text-gray-200 dark:border-r dark:border-r-gray-200">
                        {{ implode('-', array_map('ucfirst', explode('-', $name))) }}
                    </dt>
                    <dd
                        class="mt-1 text-sm leading-5 text-gray-900 break-words dark:text-gray-200 sm:mt-0 sm:col-span-2">
                        {{ is_array($values) ? implode(' ', collect($values)->flatten()->toArray()) : $values }}
                    </dd>
                </div>
            @empty
                <pre class="language_"><code>[]</code></pre>
            @endforelse

        </div>
    </div>
</div>
