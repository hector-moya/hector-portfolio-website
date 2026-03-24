<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Form Submissions') }}</flux:heading>
                <flux:text>
                    {{ __('Contact form messages') }}
                    @if ($this->unreadCount > 0)
                        &mdash; <span class="font-medium text-blue-600 dark:text-blue-400">{{ $this->unreadCount }} {{ __('unread') }}</span>
                    @endif
                </flux:text>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Search submissions...') }}" class="flex-grow" />
            <flux:select placeholder="{{ __('All') }}" wire:model.live="readFilter">
                <flux:select.option value="">{{ __('All') }}</flux:select.option>
                <flux:select.option value="unread">{{ __('Unread') }}</flux:select.option>
                <flux:select.option value="read">{{ __('Read') }}</flux:select.option>
            </flux:select>
        </div>

        @if ($this->submissions->isEmpty())
            <flux:card>
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <flux:icon.envelope variant="outline" class="mx-auto size-12 text-zinc-400 dark:text-zinc-500" />
                        <flux:heading size="lg" class="mt-4">{{ __('No submissions yet') }}</flux:heading>
                        <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
                            {{ __('Contact form submissions will appear here.') }}
                        </flux:text>
                    </div>
                </div>
            </flux:card>
        @else
            <flux:card>
                <flux:table :paginate="$this->submissions">
                    <flux:table.columns>
                        <flux:table.column>{{ __('Status') }}</flux:table.column>
                        <flux:table.column>{{ __('Name') }}</flux:table.column>
                        <flux:table.column>{{ __('Email') }}</flux:table.column>
                        <flux:table.column>{{ __('Message') }}</flux:table.column>
                        <flux:table.column>{{ __('Received') }}</flux:table.column>
                        <flux:table.column>{{ __('Actions') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->submissions as $submission)
                            <flux:table.row :key="$submission->id" class="{{ $submission->isRead() ? '' : 'font-medium' }}">
                                <flux:table.cell>
                                    @if ($submission->isRead())
                                        <flux:badge size="sm" color="zinc">{{ __('Read') }}</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="blue">{{ __('Unread') }}</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>{{ $submission->name }}</flux:table.cell>
                                <flux:table.cell>
                                    <a href="mailto:{{ $submission->email }}" class="hover:underline">{{ $submission->email }}</a>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <span class="line-clamp-2 max-w-xs text-sm">{{ $submission->message }}</span>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <span title="{{ $submission->created_at->format('Y-m-d H:i:s') }}">
                                        {{ $submission->created_at->diffForHumans() }}
                                    </span>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:dropdown>
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                        <flux:menu>
                                            <flux:modal.trigger :name="'submission-' . $submission->id">
                                                <flux:menu.item icon="eye" wire:click="markAsRead({{ $submission->id }})">
                                                    {{ __('View') }}
                                                </flux:menu.item>
                                            </flux:modal.trigger>
                                            @if (!$submission->isRead())
                                                <flux:menu.item icon="check" wire:click="markAsRead({{ $submission->id }})">
                                                    {{ __('Mark as read') }}
                                                </flux:menu.item>
                                            @endif
                                            <flux:menu.separator />
                                            <flux:menu.item icon="trash" variant="danger" wire:click="delete({{ $submission->id }})" wire:confirm="Delete this submission?">
                                                {{ __('Delete') }}
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>

                                    <flux:modal :name="'submission-' . $submission->id" class="w-full max-w-lg">
                                        <flux:heading size="lg" class="mb-1">{{ $submission->name }}</flux:heading>
                                        <flux:text class="mb-4 text-sm text-zinc-500">
                                            <a href="mailto:{{ $submission->email }}">{{ $submission->email }}</a>
                                            &middot; {{ $submission->created_at->format('M d, Y \a\t g:i A') }}
                                        </flux:text>
                                        <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800">
                                            <p class="whitespace-pre-wrap text-sm">{{ $submission->message }}</p>
                                        </div>
                                        <div class="mt-4 flex justify-end">
                                            <flux:button :href="'mailto:' . $submission->email" icon="paper-airplane" variant="primary">
                                                {{ __('Reply via Email') }}
                                            </flux:button>
                                        </div>
                                    </flux:modal>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        @endif
    </div>
</div>
