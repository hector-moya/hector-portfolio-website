@props(['section', 'assets' => collect()])

@php
    $title = $section['data']['title'] ?? '';
    $fields = $section['data']['fields'] ?? [];
@endphp

@if (!empty($fields))
    <section class="bg-white py-16 dark:bg-zinc-900">
        <div class="mx-auto max-w-2xl px-6 lg:px-8">
            @if ($title)
                <h2 class="mb-8 text-center text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ $title }}</h2>
            @endif

            <div class="space-y-6">
                @foreach ($fields as $field)
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            {{ $field['label'] ?? $field['handle'] ?? '' }}
                            @if ($field['required'] ?? false)
                                <span class="text-red-500">*</span>
                            @endif
                        </label>

                        @switch($field['type'] ?? 'text')
                            @case('textarea')
                                <textarea
                                    class="w-full rounded-lg border border-zinc-300 px-4 py-2.5 text-zinc-900 shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white"
                                    rows="4"
                                    placeholder="{{ $field['label'] ?? '' }}"
                                ></textarea>
                            @break

                            @case('toggle')
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" class="h-5 w-5 rounded border-zinc-300 text-teal-600 focus:ring-teal-500" />
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $field['label'] ?? '' }}</span>
                                </div>
                            @break

                            @case('select')
                                <select class="w-full rounded-lg border border-zinc-300 px-4 py-2.5 text-zinc-900 shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">
                                    <option value="">{{ __('Select...') }}</option>
                                </select>
                            @break

                            @default
                                <input
                                    type="{{ $field['type'] ?? 'text' }}"
                                    class="w-full rounded-lg border border-zinc-300 px-4 py-2.5 text-zinc-900 shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white"
                                    placeholder="{{ $field['label'] ?? '' }}"
                                />
                            @break
                        @endswitch
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
