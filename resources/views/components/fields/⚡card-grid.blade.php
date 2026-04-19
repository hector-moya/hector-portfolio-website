<?php

use App\Models\Field;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public Field $field;

    #[Modelable]
    public array $data = [];

    public ?int $activeCardIndex = null;

    public function mount(Field $field): void
    {
        $this->field = $field;
    }

    public function updatedData(): void
    {
        $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
    }

    #[On('asset-selected')]
    public function onAssetSelected(string $handle, mixed $value): void
    {
        if ($handle === $this->field->handle . '_card' && $this->activeCardIndex !== null) {
            $this->data['cards'][$this->activeCardIndex]['image'] = $value;
            $this->activeCardIndex = null;
            $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
        }
    }

    public function openImagePicker(int $index): void
    {
        $this->activeCardIndex = $index;
        $this->dispatch('open-modal', name: 'asset-browser-' . $this->field->handle . '_card');
    }

    public function removeCardImage(int $index): void
    {
        $this->data['cards'][$index]['image'] = null;
        $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
    }

    public function addCard(): void
    {
        $this->data['cards'][] = ['image' => null, 'title' => '', 'excerpt' => '', 'link' => ''];
        $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
    }

    public function removeCard(int $index): void
    {
        array_splice($this->data['cards'], $index, 1);
        $this->data['cards'] = array_values($this->data['cards']);
        $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
    }
};
?>

<div>
<div class="space-y-4">
    <flux:input label="{{ __('Title') }}" wire:model.blur="data.title" />
    <flux:input label="{{ __('Subtitle') }}" wire:model.blur="data.subtitle" />

    <div class="space-y-3">
        <flux:label>{{ __('Cards') }}</flux:label>

        @foreach ($data['cards'] ?? [] as $cardIdx => $card)
            @php $cardAsset = ($card['image'] ?? null) ? \App\Models\Asset::find($card['image']) : null; @endphp
            <div wire:key="card-{{ $field->handle }}-{{ $cardIdx }}" class="space-y-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <flux:text size="sm" class="font-medium">{{ __('Card :number', ['number' => $cardIdx + 1]) }}</flux:text>
                    <flux:button type="button" variant="ghost" size="sm" icon="trash" wire:click="removeCard({{ $cardIdx }})" class="text-red-500" />
                </div>

                {{-- Card Image --}}
                <div class="space-y-2">
                    <flux:label>{{ __('Image') }}</flux:label>
                    @if ($cardAsset)
                        <div class="group relative w-40 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <img src="{{ $cardAsset->thumbnail_url ?? $cardAsset->url }}" alt="{{ $cardAsset->alt_text ?? '' }}" class="aspect-video w-full object-cover" />
                            <button type="button" wire:click="removeCardImage({{ $cardIdx }})" class="absolute right-2 top-2 rounded-full bg-white p-1 opacity-0 shadow transition-opacity group-hover:opacity-100 dark:bg-zinc-800">
                                <flux:icon.x-mark class="size-4 text-zinc-500" />
                            </button>
                        </div>
                    @endif
                    <flux:button type="button" variant="ghost" size="sm" icon="photo" wire:click="openImagePicker({{ $cardIdx }})">
                        {{ $cardAsset ? __('Change Image') : __('Select Image') }}
                    </flux:button>
                </div>

                <flux:input label="{{ __('Title') }}" wire:model.blur="data.cards.{{ $cardIdx }}.title" />
                <flux:textarea label="{{ __('Excerpt') }}" wire:model.blur="data.cards.{{ $cardIdx }}.excerpt" rows="2" />
                <flux:input label="{{ __('Link URL') }}" wire:model.blur="data.cards.{{ $cardIdx }}.link" placeholder="/" />
            </div>
        @endforeach

        <flux:button type="button" variant="ghost" size="sm" icon="plus" wire:click="addCard">
            {{ __('Add Card') }}
        </flux:button>
    </div>
</div>

<flux:modal name="asset-browser-{{ $field->handle }}_card" max-width="3xl">
    <livewire:assets.browser :fieldHandle="$field->handle . '_card'" :wire:key="$field->handle . '_card_browser'" />
</flux:modal>
</div>
