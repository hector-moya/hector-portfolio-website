<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Activity Log') }}</flux:heading>
                <flux:text>{{ __('Track all content changes and actions') }}</flux:text>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Search activity...') }}" class="flex-grow" />
            <flux:select placeholder="{{ __('All Events') }}" wire:model.live="eventFilter">
                <flux:select.option value="">{{ __('All Events') }}</flux:select.option>
                <flux:select.option value="created">{{ __('Created') }}</flux:select.option>
                <flux:select.option value="updated">{{ __('Updated') }}</flux:select.option>
                <flux:select.option value="deleted">{{ __('Deleted') }}</flux:select.option>
            </flux:select>
            <flux:select placeholder="{{ __('All Users') }}" wire:model.live="userFilter">
                <flux:select.option value="">{{ __('All Users') }}</flux:select.option>
                @foreach ($this->users as $user)
                    <flux:select.option value="{{ $user->id }}">{{ $user->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        @if ($this->activities->isEmpty())
            <flux:card>
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <flux:icon.clock variant="outline" class="mx-auto size-12 text-zinc-400 dark:text-zinc-500" />
                        <flux:heading size="lg" class="mt-4">{{ __('No activity found') }}</flux:heading>
                        <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
                            {{ __('Activity will appear here as content is created and modified.') }}
                        </flux:text>
                    </div>
                </div>
            </flux:card>
        @else
            <flux:card>
                <flux:table :paginate="$this->activities">
                    <flux:table.columns>
                        <flux:table.column>{{ __('Event') }}</flux:table.column>
                        <flux:table.column>{{ __('Description') }}</flux:table.column>
                        <flux:table.column>{{ __('User') }}</flux:table.column>
                        <flux:table.column>{{ __('Subject') }}</flux:table.column>
                        <flux:table.column>{{ __('Date') }}</flux:table.column>
                        <flux:table.column>{{ __('Changes') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->activities as $activity)
                            <flux:table.row :key="$activity->id">
                                <flux:table.cell>
                                    <flux:badge size="sm" :color="match($activity->event) { 'created' => 'green', 'updated' => 'blue', 'deleted' => 'red', default => 'zinc' }">
                                        {{ ucfirst($activity->event ?? 'action') }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell>
                                    {{ $activity->description }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    {{ $activity->causer?->name ?? __('System') }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    @if ($activity->subject_type)
                                        <flux:badge size="sm" color="zinc">
                                            {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                                        </flux:badge>
                                    @else
                                        &mdash;
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell>
                                    <span title="{{ $activity->created_at->format('Y-m-d H:i:s') }}">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                </flux:table.cell>

                                <flux:table.cell>
                                    @if (!empty($activity->properties['old']) || !empty($activity->properties['new']))
                                        <flux:modal.trigger :name="'activity-detail-' . $activity->id">
                                            <flux:button size="xs" variant="ghost" icon="eye">{{ __('View') }}</flux:button>
                                        </flux:modal.trigger>

                                        <flux:modal :name="'activity-detail-' . $activity->id" class="w-full max-w-lg">
                                            <flux:heading size="lg" class="mb-4">{{ __('Change Details') }}</flux:heading>

                                            @if (!empty($activity->properties['old']))
                                                <div class="mb-4">
                                                    <flux:text class="mb-2 font-semibold text-red-600 dark:text-red-400">{{ __('Before') }}</flux:text>
                                                    <div class="rounded-lg bg-red-50 p-3 dark:bg-red-950/20">
                                                        @foreach ($activity->properties['old'] as $key => $value)
                                                            <div class="flex gap-2 text-sm">
                                                                <span class="font-medium text-zinc-600 dark:text-zinc-400">{{ $key }}:</span>
                                                                <span class="text-zinc-800 dark:text-zinc-200">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if (!empty($activity->properties['new']))
                                                <div>
                                                    <flux:text class="mb-2 font-semibold text-green-600 dark:text-green-400">{{ __('After') }}</flux:text>
                                                    <div class="rounded-lg bg-green-50 p-3 dark:bg-green-950/20">
                                                        @foreach ($activity->properties['new'] as $key => $value)
                                                            <div class="flex gap-2 text-sm">
                                                                <span class="font-medium text-zinc-600 dark:text-zinc-400">{{ $key }}:</span>
                                                                <span class="text-zinc-800 dark:text-zinc-200">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </flux:modal>
                                    @else
                                        &mdash;
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        @endif
    </div>
</div>
