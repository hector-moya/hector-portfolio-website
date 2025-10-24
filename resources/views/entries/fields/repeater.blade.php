@php
  $handle   = $element->handle;
  $items    = $form->fieldValues[$handle]['items'] ?? [];
  $children = $element->config['blueprint'] ?? [];
@endphp

<flux:card class="space-y-4">
  <div class="flex items-center justify-between">
    <flux:heading size="md">{{ $element->label }}</flux:heading>
    <flux:button size="xs" icon="plus" wire:click="addRepeaterItem('{{ $handle }}')">
      {{ __('Add item') }}
    </flux:button>
  </div>

  @if ($element->instructions)
    <flux:description>{{ $element->instructions }}</flux:description>
  @endif

  @forelse ($items as $i => $item)
    <flux:card class="space-y-3" wire:key="rep-{{ $handle }}-{{ $i }}">
      <div class="flex justify-between items-start">
        <flux:heading size="sm">{{ __('Item') }} #{{ $i+1 }}</flux:heading>
        <flux:button size="xs" variant="danger" wire:click="removeRepeaterItem('{{ $handle }}', {{ $i }})">
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
              wire:model="form.fieldValues.{{ $handle }}.items.{{ $i }}.{{ $nh }}"
            />
            @break
          @case('textarea')
            <flux:textarea
              label="{{ $child['label'] }}"
              rows="{{ $child['config']['rows'] ?? 4 }}"
              wire:model="form.fieldValues.{{ $handle }}.items.{{ $i }}.{{ $nh }}"
            />
            @break
          @case('richtext')
            <flux:editor
              label="{{ $child['label'] }}"
              wire:model="form.fieldValues.{{ $handle }}.items.{{ $i }}.{{ $nh }}"
            />
            @break
          @case('number')
            <flux:input type="number" step="any"
              label="{{ $child['label'] }}"
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
          @case('checkbox')
            <flux:checkbox
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
          <flux:text class="text-xs opacity-70">{{ $child['instructions'] }}</flux:text>
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
