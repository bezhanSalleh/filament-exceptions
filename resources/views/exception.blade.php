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
                class="rounded-lg bg-gray-100 dark:text-gray-200 dark:bg-gray-800 dark:border dark:border-gray-600">
                <div class="flex flex-wrap break-all">
                    <div x-on:click="section = !section"
                        class="w-full flex items-center justify-start font-semibold text-sm pr-2 rtl:pl-2 py-2">
                        <span x-show="section" aria-hidden="true" class="ml-4">
                            <x-heroicon-o-eye class="h-6 w-6 text-primary-700 dark:text-primary-500 rtl:rotate-0" />
                        </span>
                        <span x-show="!section" aria-hidden="true" class="ml-4">
                            <x-heroicon-o-eye-slash
                                class="h-6 w-6 text-primary-700 dark:text-primary-500 rtl:rotate-180 " />
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
                {{-- class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 font-mono break-all" --}}
                <div x-show="section" x-collapse>
                    <div class="pb-4 px-4 dark:bg-gray-800 dark:text-200 rounded-lg">
                        <pre data-start="{!! $frame->getCodeBlock()->getStartLine() - 1 !!}" data-line="{!! $frame->line() - $frame->getCodeBlock()->getStartLine() + 2 !!}" class="line-numbers">
                            <code class="language-php">
                                {!! $frame->getCodeBlock()->output() !!}
                            </code>
                        </pre>
                        @if (count($frame->args()))
                            <table
                                class="table args bg-gray-400 text-white rounded-sm mt-4 dark:even:bg-gray-600 dark:even:text-gray-50 p-1">
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
