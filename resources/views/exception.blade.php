<div
    x-data="{
        active: 0,
        darkMode: false,
        frameCache: {},
        preloadQueue: [],
        frames: @js($this->frames),

        init() {
            this.updateDarkMode();
            this.watchThemeChanges();
            this.startBackgroundPreload();
        },

        updateDarkMode() {
            this.darkMode = document.documentElement.classList.contains('dark') || window.theme === 'dark';
            this.invalidateCache();
            this.startBackgroundPreload();
        },

        watchThemeChanges() {
            const observer = new MutationObserver(() => this.updateDarkMode());
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        },

        startBackgroundPreload() {
            setTimeout(() => {
                for (let i = 1; i < this.frames.length; i++) {
                    this.preloadQueue.push(i);
                }
                this.processPreloadQueue();
            }, 500);
        },

        async processPreloadQueue() {
            if (this.preloadQueue.length === 0) return;

            const frameIndex = this.preloadQueue.shift();
            const cacheKey = frameIndex + '_' + (this.darkMode ? 'dark' : 'light');

            if (!this.frameCache[cacheKey]) {
                try {
                    this.frameCache[cacheKey] = await $wire.call('renderFrame', frameIndex, this.darkMode);
                } catch (error) {
                    this.frameCache[cacheKey] = '<div class=\'text-red-500 p-4\'>Error loading frame</div>';
                }
            }

            setTimeout(() => this.processPreloadQueue(), 100);
        },

        invalidateCache() {
            this.frameCache = {};
            this.preloadQueue = [];
        },

        getFrameContent(frameIndex) {
            const cacheKey = frameIndex + '_' + (this.darkMode ? 'dark' : 'light');
            return this.frameCache[cacheKey] || '';
        }
    }"
    x-init="init()"
    class="space-y-4"
    dir="ltr"
>
    <style>
        .highlighted-line {
            background-color: oklch(from var(--primary-500) l c h / 0.2);
            padding-block: 4px;
            line-height: 2;
            padding-inline-end: 4px;
        }

        .dark .highlighted-line {
            background-color: oklch(from var(--primary-600) l c h / 0.5);
        }
    </style>
    @foreach ($this->frames as $index => $frame)
        @if ($frame->line() > 0)
            @php
                $isVendor = method_exists($frame, 'isVendorFrame') && $frame->isVendorFrame();
            @endphp

            <div
                x-data="{
                    id: {{ $index }},
                    isVendor: {{ $isVendor ? 'true' : 'false' }},
                    get section() {
                        return active === this.id
                    },
                    set section(value) {
                        active = value ? this.id : null
                    },
                }"
                @class([
                    'rounded-lg bg-gray-50 shadow-xs ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700',
                ])
            >
                <div class="flex cursor-pointer flex-wrap break-all">
                    <div x-on:click="section = !section" class="flex w-full items-center justify-start py-2 pr-2 text-sm font-semibold rtl:pl-2">
                        <span x-show="section" aria-hidden="true" class="ml-4">
                            <x-filament::icon
                                alias="fe::sectionOpen"
                                class="text-primary-700 dark:text-primary-500 h-6 w-6 rtl:rotate-0"
                                icon="heroicon-o-eye"
                            />
                        </span>
                        <span x-show="!section" aria-hidden="true" class="ml-4">
                            <x-filament::icon
                                alias="fe::sectionClose"
                                class="text-primary-700 dark:text-primary-500 h-6 w-6 rtl:rotate-180"
                                icon="heroicon-o-eye-slash"
                            />
                        </span>
                        <div class="px-4 py-1">
                            <h3 class="font-mono text-base">
                                {{ str($frame->file())->replace(base_path() . '/', '')->afterLast('/') }}
                                <span
                                    class="text-primary-600 bg-primary-500/10 dark:text-primary-500 ml-auto inline-flex min-h-4 items-center justify-center rounded-xl px-2 py-0.5 text-xs font-medium tracking-tight whitespace-normal rtl:mr-auto rtl:ml-0"
                                >
                                    {{ $frame->line() }}
                                </span>
                                @if ($isVendor)
                                    <span
                                        class="ml-2 inline-flex items-center justify-center rounded bg-gray-200 px-1.5 py-0.5 text-xs font-medium text-gray-500 dark:bg-gray-700 dark:text-gray-400"
                                    >
                                        vendor
                                    </span>
                                @endif
                            </h3>
                            <span class="font-regular text-left !text-sm break-words text-gray-500">
                                {{ str($frame->file())->replace(base_path() . '/', '') }}
                                in {{ $frame->method() }} at line {{ $frame->line() }}
                            </span>
                        </div>
                    </div>
                </div>

                <div
                    x-cloak
                    x-show="section"
                    x-collapse
                    class="fi-contaied mx-2 pb-3 transition-all duration-300 ease-in-out [&_.line]:text-wrap [&>pre]:mx-1! [&>pre]:my-0!"
                >
                    @if ($index === 0)
                        <template x-if="!darkMode">
                            {!! $frame->getCodeBlock()->output($frame->line(), \Phiki\Theme\Theme::GithubLight) !!}
                        </template>
                        <template x-if="darkMode">
                            {!! $frame->getCodeBlock()->output($frame->line(), \Phiki\Theme\Theme::GithubDark) !!}
                        </template>
                    @else
                        <template x-if="section && getFrameContent({{ $index }})">
                            <div x-html="getFrameContent({{ $index }})" x-transition:opacity.duration.400ms></div>
                        </template>
                    @endif
                </div>
            </div>
        @endif
    @endforeach
</div>
