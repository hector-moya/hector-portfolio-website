@php
  $handle   = $field->handle;
  $items    = $form->fieldValues[$handle]['items'] ?? [];
  $children = $field->config['blueprint'] ?? [];
  $min = $field->config['min'] ?? 0;
  $max = $field->config['max'] ?? null;
  $canAdd = $max === null || count($items) < $max;
  $canRemove = count($items) > $min;
@endphp

<flux:card class="space-y-4">
  <div class="flex items-center justify-between">
    <div>
      <flux:heading size="md">{{ $field->label }}</flux:heading>
      @if ($field->instructions)
        <flux:description>{{ $field->instructions }}</flux:description>
      @endif
    </div>
    <flux:button
      size="xs"
      icon="plus"
      wire:click="addRepeaterItem('{{ $handle }}')"
      :disabled="!$canAdd"
    >
      {{ __('Add item') }}
    </flux:button>
  </div>

  @if($min > 0 || $max !== null)
    <flux:text size="xs" class="opacity-70">
      @if($min > 0 && $max !== null)
        {{ __('Required: :min - :max items', ['min' => $min, 'max' => $max]) }}
      @elseif($min > 0)
        {{ __('Minimum: :min items', ['min' => $min]) }}
      @elseif($max !== null)
        {{ __('Maximum: :max items', ['max' => $max]) }}
      @endif
      ({{ __('Current: :count', ['count' => count($items)]) }})
    </flux:text>
  @endif

  @forelse ($items as $i => $item)
    <flux:card class="space-y-3" wire:key="rep-{{ $handle }}-{{ $i }}">
      <div class="flex justify-between items-start">
        <flux:heading size="sm">{{ __('Item') }} #{{ $i+1 }}</flux:heading>
        <flux:button
          size="xs"
          variant="danger"
          wire:click="removeRepeaterItem('{{ $handle }}', {{ $i }})"
          :disabled="!$canRemove"
        >
          {{ __('Remove') }}
        </flux:button>
      </div>

      @foreach ($children as $child)
        @php($nh = $child['handle'])
        @switch($child['type'])
          @case('text')
            <flux:input
              label="{{ $child['label'] }}"
              placeholder="{{ $child['config']['placeholder'] ?? '' }}"
              maxlength="{{ $child['config']['max'] ?? '' }}"
              wire:model="form.fieldValues.{{ $handle }}.items.{{ $i }}.{{ $nh }}"
            />
            @break
          @case('textarea')
            <flux:textarea
              label="{{ $child['label'] }}"
              placeholder="{{ $child['config']['placeholder'] ?? '' }}"
              rows="{{ $child['config']['rows'] ?? 4 }}"
              wire:model="form.fieldValues.{{ $handle }}.items.{{ $i }}.{{ $nh }}"
            />
            @break
          @case('richtext')
            <flux:editor
              label="{{ $child['label'] }}"
              placeholder="{{ $child['config']['placeholder'] ?? '' }}"
              wire:model="form.fieldValues.{{ $handle }}.items.{{ $i }}.{{ $nh }}"
            />
            @break
          @case('number')
            <flux:input
              type="number"
              label="{{ $child['label'] }}"
              min="{{ $child['config']['min'] ?? '' }}"
              max="{{ $child['config']['max'] ?? '' }}"
              step="{{ $child['config']['step'] ?? 1 }}"
              wire:model="form.fieldValues.{{ $handle }}.items.{{ $i }}.{{ $nh }}"
            />
            @break
          @case('email')
            <flux:input
              type="email"
              label="{{ $child['label'] }}"
              placeholder="{{ $child['config']['placeholder'] ?? '' }}"
              wire:model="form.fieldValues.{{ $handle }}.items.{{ $i }}.{{ $nh }}"
            />
            @break
          @case('url')
            <flux:input
              type="url"
              label="{{ $child['label'] }}"
              placeholder="{{ $child['config']['placeholder'] ?? '' }}"
              wire:model="form.fieldValues.{{ $handle }}.items.{{ $i }}.{{ $nh }}"
            />
            @break
          @case('select')
            <flux:select
              label="{{ $child['label'] }}"
              wire:model="form.fieldValues.{{ $handle }}.items.{{ $i }}.{{ $nh }}">
              <option value="">{{ __('Select an option...') }}</option>
              @foreach(($child['config']['options'] ?? []) as $opt)
                <option value="{{ $opt['value'] ?? $opt }}">{{ $opt['label'] ?? $opt }}</option>
              @endforeach
            </flux:select>
            @break
          @case('toggle')
            <flux:switch
              label="{{ $child['label'] }}"
              wire:model="form.fieldValues.{{ $handle }}.items.{{ $i }}.{{ $nh }}"
            />
            @break
          @default
            <flux:input
              label="{{ $child['label'] }}"
              wire:model="form.fieldValues.{{ $handle }}.items.{{ $i }}.{{ $nh }}"
            />
        @endswitch

        @if (!empty($child['instructions']))
          <flux:description>{{ $child['instructions'] }}</flux:description>
        @endif
      @endforeach

      {{-- surface nested errors --}}
      @foreach ($children as $child)
        @php($nh = $child['handle'])
        <flux:error name="form.fieldValues.{{ $handle }}.items.{{ $i }}.{{ $nh }}" />
      @endforeach
    </flux:card>
  @empty
    <flux:text>{{ __('No items yet.') }}</flux:text>
  @endforelse
</flux:card>

<flux:error name="form.fieldValues.{{ $handle }}.items" />
