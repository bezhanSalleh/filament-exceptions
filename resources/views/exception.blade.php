<div x-data="{
    active: 0,
    darkMode: false,
    init() {
        this.updateDarkMode();
        this.watchThemeChanges();
    },
    updateDarkMode() {
        this.darkMode = document.documentElement.classList.contains('dark') || window.theme === 'dark';
    },
    watchThemeChanges() {
        const observer = new MutationObserver(() => this.updateDarkMode());
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    }
}" x-init="init()" class="space-y-2" dir="ltr">
    @foreach ($this->frames as $index => $frame)
        <div x-data="{
            id: {{ $index }},
            get section() { return active === this.id },
            set section(value) { active = value ? this.id : null }
        }"
            class="bg-gray-100 fi-sc-tabs fi-contained dark:text-gray-200 dark:bg-gray-800 dark:border dark:border-gray-600">
            <div class="flex flex-wrap break-all">
                <div x-on:click="section = !section"
                    class="flex items-center justify-start w-full py-2 pr-2 text-sm font-semibold rtl:pl-2">
                    <span x-show="section" aria-hidden="true" class="ml-4">
                        <x-filament::icon alias="fe::sectionOpen"
                            class="w-6 h-6 text-primary-700 dark:text-primary-500 rtl:rotate-0" icon="heroicon-o-eye" />
                    </span>
                    <span x-show="!section" aria-hidden="true" class="ml-4">
                        <x-filament::icon alias="fe::sectionClose"
                            class="w-6 h-6 text-primary-700 dark:text-primary-500 rtl:rotate-180"
                            icon="heroicon-o-eye-slash" />
                    </span>
                    <span class="px-4 py-3 font-mono text-left break-words">
                        {{ str($frame->file())->replace(base_path() . '/', '') }}
                        in {{ $frame->method() }}
                        at line
                        <span
                            class="inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl whitespace-normal text-primary-600 bg-primary-500/10 dark:text-primary-500">
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
                    <div 
                        x-data="{
                            rendered: false,
                            loading: false,
                            html: '',
                            showContent: false,
                            lastTheme: null,
                            containerHeight: 0
                        }" 
                        x-init="
                            const loadFrame = async () => {
                                if (loading) return;
                                
                                const currentTheme = darkMode ? 'dark' : 'light';
                                
                                if (!section || (rendered && lastTheme === currentTheme)) return;
                                
                                await new Promise(resolve => setTimeout(resolve, 150));
                                loading = true;
                                showContent = false;
                                
                                if (!rendered) {
                                    containerHeight = 200;
                                } else {
                                    containerHeight = $el.querySelector('[x-html]')?.offsetHeight || 200;
                                }

                                try {
                                    html = await $wire.call('renderFrame', {{ $index }}, darkMode);
                                    lastTheme = currentTheme;
                                } catch (error) {
                                    html = '<div class=\'text-red-500 p-4 border border-red-200 rounded-lg bg-red-50 dark:bg-red-900/20 dark:border-red-800\'>⚠️ Error loading frame content</div>';
                                } finally {
                                    rendered = true;
                                    setTimeout(() => {
                                        loading = false;
                                        setTimeout(() => {
                                            showContent = true;
                                            setTimeout(() => containerHeight = 0, 100);
                                        }, 100);
                                    }, 200);
                                }
                            };
                            
                            $watch('section', loadFrame);
                            $watch('darkMode', () => {
                                if (!section || !rendered) return;
                                loadFrame();
                            });
                        "
                        :style="containerHeight > 0 ? 'min-height: ' + containerHeight + 'px' : ''"
                    >
                        <div x-show="loading" 
                            x-transition:opacity.duration.300ms
                            class="animate-pulse bg-gray-100 dark:bg-gray-800 rounded p-4 min-h-[200px] flex items-center justify-center">
                            <div class="text-gray-500 dark:text-gray-400 text-sm">Loading code...</div>
                        </div>

                        <div x-show="showContent" 
                            x-transition:opacity.duration.400ms
                            x-html="html"></div>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
