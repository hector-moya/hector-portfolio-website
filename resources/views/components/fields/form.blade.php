@props(['section', 'assets' => collect()])

@php
    $title = $section['data']['title'] ?? '';
    $fields = $section['data']['fields'] ?? [];
@endphp

@if (!empty($fields))
    <section class="bg-zinc-50 py-16 dark:bg-zinc-800/50 sm:py-24">
        <div class="mx-auto max-w-2xl px-6 lg:px-8">
            @if ($title)
                <div data-animate class="mb-10 text-center">
                    <flux:heading class="text-3xl! font-bold sm:text-4xl!" level="2">
                        {{ $title }}
                    </flux:heading>
                </div>
            @endif

            <div data-animate data-delay="100" class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="space-y-5">
                    @foreach ($fields as $field)
                        <flux:field>
                            <flux:label>
                                {{ $field['label'] ?? $field['handle'] ?? '' }}
                                @if ($field['required'] ?? false)
                                    <flux:label class="text-red-500">*</flux:label>
                                @endif
                            </flux:label>

                            @switch($field['type'] ?? 'text')
                                @case('textarea')
                                    <flux:textarea
                                        rows="4"
                                        placeholder="{{ $field['label'] ?? '' }}"
                                    />
                                @break

                                @case('toggle')
                                    <flux:switch />
                                @break

                                @case('select')
                                    <flux:select placeholder="{{ __('Select...') }}">
                                        @foreach ($field['options'] ?? [] as $value => $label)
                                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                @break

                                @case('radio')
                                    <flux:radio.group>
                                        @foreach ($field['options'] ?? [] as $value => $label)
                                            <flux:radio value="{{ $value }}" label="{{ $label }}" />
                                        @endforeach
                                    </flux:radio.group>
                                @break

                                @default
                                    <flux:input
                                        type="{{ $field['type'] ?? 'text' }}"
                                        placeholder="{{ $field['label'] ?? '' }}"
                                    />
                                @break
                            @endswitch
                        </flux:field>
                    @endforeach

                    <div class="pt-2">
                        <flux:button variant="primary" class="w-full">
                            {{ __('Submit') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
