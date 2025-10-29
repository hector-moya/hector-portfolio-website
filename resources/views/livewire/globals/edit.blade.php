<div>
    <div class="flex flex-col gap-6">
        <div>
            <flux:heading size="2xl">Edit {{ $globalSet->name }}</flux:heading>
            <flux:text class="mt-2">Modify this global variables set.</flux:text>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="border border-zinc-200 rounded-lg bg-white divide-y divide-zinc-200">
                <div class="p-4 space-y-4">
                    <div>
                        <flux:input
                            wire:model="name"
                            label="Name"
                            placeholder="Company Information"
                        />
                    </div>

                    <div>
                        <flux:input
                            wire:model="handle"
                            label="Handle"
                            placeholder="company_info"
                        />
                    </div>

                    <div>
                        <flux:select
                            wire:model="blueprint_id"
                            :options="$blueprints"
                            option-label="name"
                            option-value="id"
                            label="Blueprint"
                            placeholder="Select a blueprint..."
                        />
                    </div>

                    @if($blueprint_id)
                        <div class="border-t border-zinc-200 pt-4">
                            @foreach($globalSet->blueprint->elements as $element)
                                @include('entries.fields.' . $element->type, [
                                    'element' => $element,
                                    'form' => $this,
                                ])
                            @endforeach
                        </div>
                    @else
                        <div class="border-t border-zinc-200 pt-4">
                            @foreach($globalSet->variables as $variable)
                                <div class="mb-4">
                                    <flux:textarea
                                        wire:model="variables.{{ $variable->handle }}"
                                        label="{{ $variable->handle }}"
                                        placeholder="Enter a value..."
                                    />
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="p-4 flex justify-between items-center">
                    <flux:button
                        wire:navigate
                        href="{{ route('admin.globals.index') }}"
                        variant="ghost"
                    >
                        Cancel
                    </flux:button>

                    <flux:button type="submit" variant="primary">
                        Update Set
                    </flux:button>
                </div>
            </div>
        </form>
    </div>
</div>
