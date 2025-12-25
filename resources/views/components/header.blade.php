@props(['exception'])

<div class="flex flex-col pt-8 sm:pt-16 overflow-x-auto">
    <div class="flex flex-col gap-5 mb-8">
        <h1 class="text-3xl font-semibold text-neutral-950 dark:text-white">{{ $exception->class() }}</h1>
        <x-filament-exceptions::file-with-line :frame="$exception->frames()->first()" class="-mt-3 text-xs" />
        <p class="text-xl font-light text-neutral-800 dark:text-neutral-300">
            {{ $exception->message() }}
        </p>
    </div>

    <div class="flex items-start gap-2 mb-8 sm:mb-16">
        <div class="bg-white dark:bg-white/[3%] border border-neutral-200 dark:border-white/10 divide-x divide-neutral-200 dark:divide-white/10 rounded-md shadow-xs flex items-center gap-0.5">
            <div class="flex items-center gap-1.5 h-6 px-[6px] font-mono text-[13px]">
                <span class="text-neutral-400 dark:text-neutral-500">LARAVEL</span>
                <span class="text-neutral-500 dark:text-neutral-300">{{ app()->version() }}</span>
            </div>
            <div class="flex items-center gap-1.5 h-6 px-[6px] font-mono text-[13px]">
                <span class="text-neutral-400 dark:text-neutral-500">PHP</span>
                <span class="text-neutral-500 dark:text-neutral-300">{{ PHP_VERSION }}</span>
            </div>
        </div>
        <x-filament-exceptions::badge type="error">
            <x-filament-exceptions::icons.alert class="w-2.5 h-2.5" />
            UNHANDLED
        </x-filament-exceptions::badge>
        <x-filament-exceptions::badge type="error" variant="solid">
            CODE {{ $exception->code() }}
        </x-filament-exceptions::badge>
    </div>

    <x-filament-exceptions::request-url :$exception :request="$exception->request()" class="relative z-50" />
</div>
