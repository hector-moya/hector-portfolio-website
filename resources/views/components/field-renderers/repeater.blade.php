@props(['field', 'value'])
@if($value && is_array($value))
    <div class="space-y-4">
        @foreach($value as $item)
            <flux:card class="space-y-2">
                @foreach($field->children as $childField)
                    @php $childValue = $item[$childField->handle] ?? null; @endphp
                    @if($childValue !== null && $childValue !== '')
                        <div>
                            <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $childField->label }}</flux:text>
                            <x-dynamic-component
                                :component="'field-renderers.' . $childField->type"
                                :field="$childField"
                                :value="$childValue"
                            />
                        </div>
                    @endif
                @endforeach
            </flux:card>
        @endforeach
    </div>
@endif
