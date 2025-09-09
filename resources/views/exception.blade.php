<div x-data="{ active: 0 }" class="space-y-2" dir="ltr">


@capture($frameHtml, $frame, $theme)
    {!! $frame->getCodeBlock()->output($frame->line(), $theme) !!}
@endcapture

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
            <template x-if="!darkMode">
                {!! $frameHtml($frame, \Phiki\Theme\Theme::GithubLight) !!}
            </template>
            <template x-if="darkMode">
                {!! $frameHtml($frame, \Phiki\Theme\Theme::GithubDark) !!}
            </template>
        </div>
    </div>
@endforeach
</div>
