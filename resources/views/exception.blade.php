<div
    x-data="{ active: 0 }"
    class="space-y-2"
    dir="ltr">
    @foreach ($this->frames as $index => $frame)
    <div
        x-data="{
            id: {{ $index }},
            darkMode: false,
            get section() { return active === this.id },
            set section(value) { active = value ? this.id : null },
            init() {
                this.darkMode = window.theme === 'dark';
                const observer = new MutationObserver(() => {
                    this.darkMode = document.documentElement.classList.contains('dark');
                });
                observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            }
        }"
        x-init="init()"
        class="bg-gray-100 fi-sc-tabs fi-contained dark:text-gray-200 dark:bg-gray-800 dark:border dark:border-gray-600"
    >
        <div class="flex flex-wrap break-all">
            <div x-on:click="section = !section" class="flex items-center justify-start w-full py-2 pr-2 text-sm font-semibold rtl:pl-2">
                <span x-show="section" aria-hidden="true" class="ml-4">
                    <x-filament::icon alias="fe::sectionOpen" class="w-6 h-6 text-primary-700 dark:text-primary-500 rtl:rotate-0" icon="heroicon-o-eye" />
                </span>
                <span x-show="!section" aria-hidden="true" class="ml-4">
                    <x-filament::icon alias="fe::sectionClose" class="w-6 h-6 text-primary-700 dark:text-primary-500 rtl:rotate-180" icon="heroicon-o-eye-slash" />
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
            @if($index === 0)
                {{-- Render first frame immediately --}}
                <template x-if="!darkMode">
                    {!! $frame->getCodeBlock()->output($frame->line(), \Phiki\Theme\Theme::GithubLight) !!}
                </template>
                <template x-if="darkMode">
                    {!! $frame->getCodeBlock()->output($frame->line(), \Phiki\Theme\Theme::GithubDark) !!}
                </template>
            @else
                {{-- Lazy load other frames when section opens --}}
                <div
                    x-data="{ rendered: false, loading: false, html: '', showContent: false }"
                    x-init="
                        $watch('section', async (value) => {
                            if (value && !rendered && !loading) {
                                // Small delay to let section expand first
                                await new Promise(resolve => setTimeout(resolve, 150));
                                loading = true;
                                
                                try {
                                    // Load content in background while skeleton shows
                                    html = await $wire.call('renderFrame', {{ $index }}, darkMode);
                                } catch (error) {
                                    html = '<div class=\'text-red-500 p-4 border border-red-200 rounded-lg bg-red-50 dark:bg-red-900/20 dark:border-red-800\'>⚠️ Error loading frame content</div>';
                                } finally {
                                    rendered = true;
                                    loading = false;
                                    // Short delay for smooth transition
                                    setTimeout(() => showContent = true, 200);
                                }
                            }
                        });
                    "
                >
                    {{-- Show skeleton while loading --}}
                    <div 
                        x-show="loading" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-out duration-300"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="animate-pulse bg-gray-100 dark:bg-gray-800 rounded p-4 min-h-[200px] flex items-center justify-center"
                    >
                        <div class="text-gray-500 dark:text-gray-400 text-sm">Loading code...</div>
                    </div>
                    
                    {{-- Show content when ready --}}
                    <div 
                        x-show="showContent" 
                        x-transition:enter="transition ease-out duration-400"
                        x-transition:enter-start="opacity-0 transform translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-html="html"
                    ></div>
                </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
