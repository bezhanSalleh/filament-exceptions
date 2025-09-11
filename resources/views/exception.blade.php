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
            
            console.log('Processing frame', frameIndex, 'with key', cacheKey);
            
            if (!this.frameCache[cacheKey]) {
                try {
                    console.log('Loading frame', frameIndex);
                    this.frameCache[cacheKey] = await $wire.call('renderFrame', frameIndex, this.darkMode);
                    console.log('Loaded frame', frameIndex, 'success');
                } catch (error) {
                    console.log('Error loading frame', frameIndex, error);
                    this.frameCache[cacheKey] = '<div class=\'text-red-500 p-4\'>Error loading frame</div>';
                }
            }
            
            setTimeout(() => this.processPreloadQueue(), 100);
        },
        
        invalidateCache() {
            this.frameCache = {};
        },
        
        getFrameContent(frameIndex) {
            const cacheKey = frameIndex + '_' + (this.darkMode ? 'dark' : 'light');
            console.log('Getting frame', frameIndex, 'with key', cacheKey, 'cached:', !!this.frameCache[cacheKey]);
            return this.frameCache[cacheKey] || '';
        }
    }"
    x-init="init()"
    class="space-y-2"
    dir="ltr"
>
    @foreach ($this->frames as $index => $frame)
        <div
            x-data="{
                id: {{ $index }},
                get section() { return active === this.id },
                set section(value) { active = value ? this.id : null }
            }"
            class="bg-gray-100 fi-sc-tabs fi-contained dark:text-gray-200 dark:bg-gray-800 dark:border dark:border-gray-600"
        >
            <div class="flex flex-wrap break-all">
                <div x-on:click="section = !section"
                    class="flex items-center justify-start w-full py-2 pr-2 text-sm font-semibold rtl:pl-2">
                    <span x-show="section" aria-hidden="true" class="ml-4">
                        <x-filament::icon
                            alias="fe::sectionOpen"
                            class="w-6 h-6 text-primary-700 dark:text-primary-500 rtl:rotate-0"
                            icon="heroicon-o-eye"
                        />
                    </span>
                    <span x-show="!section" aria-hidden="true" class="ml-4">
                        <x-filament::icon
                            alias="fe::sectionClose"
                            class="w-6 h-6 text-primary-700 dark:text-primary-500 rtl:rotate-180"
                            icon="heroicon-o-eye-slash"
                        />
                    </span>
                    <span class="px-4 py-3 font-mono text-left break-words">
                        {{ str($frame->file())->replace(base_path() . '/', '') }}
                        in {{ $frame->method() }}
                        at line
                        <span class="inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl whitespace-normal text-primary-600 bg-primary-500/10 dark:text-primary-500">
                            {{ $frame->line() }}
                        </span>
                    </span>
                </div>
            </div>

            <div x-show="section" x-collapse class="[&>pre]:!mx-1 [&>pre]:!my-0 mx-2 pb-3 fi-contaied">
                @if ($index === 0)
                    <template x-if="!darkMode">
                        {!! $frame->getCodeBlock()->output($frame->line(), \Phiki\Theme\Theme::GithubLight) !!}
                    </template>
                    <template x-if="darkMode">
                        {!! $frame->getCodeBlock()->output($frame->line(), \Phiki\Theme\Theme::GithubDark) !!}
                    </template>
                @else
                    <template x-if="section && getFrameContent({{ $index }})">
                        <div x-html="getFrameContent({{ $index }})"
                            x-transition:opacity.duration.400ms></div>
                    </template>
                @endif
            </div>
        </div>
    @endforeach
</div>
