<div x-data="{ active: 0 }" class="space-y-2 ">
    @foreach ($this->frames as $index => $frame)
        <div x-data="{
            id: {{ $index }},
            get section() {
                return this.active === this.id
            },
            set section(value) {
                this.active = value ? this.id : null
            }
        }"
            class="bg-gray-100 rounded-lg dark:text-gray-200 dark:bg-gray-800 dark:border dark:border-gray-600">
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
                            class="inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl whitespace-normal text-primary-600 bg-primary-500/10 dark:text-primary-500">{{ $frame->line() }}</span>
                    </span>

                </div>
            </div>
            {{-- class="font-mono break-all sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6" --}}
            <div x-show="section" x-collapse>
                <div class="px-4 pb-4 rounded-lg dark:bg-gray-800 dark:text-200">
                    <pre data-start="{!! $frame->getCodeBlock()->getStartLine() - 1 !!}" data-line="{!! $frame->line() - $frame->getCodeBlock()->getStartLine() + 2 !!}" class="line-numbers">
                            <code class="language-php">
                                {!! $frame->getCodeBlock()->output() !!}
                            </code>
                        </pre>
                    @if (count($frame->args()))
                        <table
                            class="table p-1 mt-4 text-white bg-gray-400 rounded-sm args dark:even:bg-gray-600 dark:even:text-gray-50">
                            <tbody>
                                @foreach ($frame->args() as $name => $val)
                                    <tr>
                                        <td style="">&nbsp;</td>
                                        <td class="name"><strong>{{ $name }}</strong></td>
                                        <td class="value">{{ $val }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
