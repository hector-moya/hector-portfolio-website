<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Security')" :subheading="__('Control who can register for an account')">
        {{-- Registration Mode --}}
        <form wire:submit="saveMode" class="my-6 w-full space-y-6">
            <div class="space-y-4">
                <flux:heading size="sm">{{ __('Registration Mode') }}</flux:heading>
                <flux:radio.group wire:model="registrationMode" class="space-y-3">
                    <flux:radio
                        value="closed"
                        :label="__('Closed')"
                        :description="__('Registration is disabled. No one can create an account.')"
                    />
                    <flux:radio
                        value="invitation"
                        :label="__('Invitation Only')"
                        :description="__('Only users you invite via email can register.')"
                    />
                    <flux:radio
                        value="approval"
                        :label="__('Approval Required')"
                        :description="__('Anyone can register, but you must manually approve each account.')"
                    />
                    <flux:radio
                        value="open"
                        :label="__('Open')"
                        :description="__('Anyone can register immediately. Use with caution.')"
                    />
                </flux:radio.group>
            </div>

            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
        </form>

        {{-- Invite User Form (invitation mode only) --}}
        @if ($registrationMode === 'invitation')
            <flux:separator class="my-8" />

            <div class="space-y-6">
                <div>
                    <flux:heading size="sm">{{ __('Invite a User') }}</flux:heading>
                    <flux:subheading>{{ __('Send a 48-hour invitation link to a specific email address.') }}</flux:subheading>
                </div>

                <form wire:submit="sendInvite" class="space-y-4">
                    <flux:input
                        wire:model="inviteEmail"
                        :label="__('Email Address')"
                        type="email"
                        :placeholder="__('colleague@example.com')"
                    />

                    <flux:select wire:model="inviteRole" :label="__('Role')">
                        <flux:select.option value="viewer">{{ __('Viewer') }}</flux:select.option>
                        <flux:select.option value="editor">{{ __('Editor') }}</flux:select.option>
                        <flux:select.option value="admin">{{ __('Admin') }}</flux:select.option>
                    </flux:select>

                    <flux:button variant="primary" type="submit" icon="envelope">
                        {{ __('Send Invitation') }}
                    </flux:button>
                </form>

                {{-- Sent Invitations --}}
                @if ($this->invitations->isNotEmpty())
                    <div>
                        <flux:heading size="sm" class="mb-3">{{ __('Sent Invitations') }}</flux:heading>
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('Email') }}</flux:table.column>
                                <flux:table.column>{{ __('Role') }}</flux:table.column>
                                <flux:table.column>{{ __('Sent') }}</flux:table.column>
                                <flux:table.column>{{ __('Status') }}</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach ($this->invitations as $invitation)
                                    <flux:table.row wire:key="invitation-{{ $invitation->id }}">
                                        <flux:table.cell>{{ $invitation->email }}</flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge color="{{ $invitation->role === 'admin' ? 'blue' : ($invitation->role === 'editor' ? 'indigo' : 'zinc') }}">
                                                {{ __($invitation->role) }}
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell>{{ $invitation->created_at->diffForHumans() }}</flux:table.cell>
                                        <flux:table.cell>
                                            @if ($invitation->isAccepted())
                                                <flux:badge color="green">{{ __('Accepted') }}</flux:badge>
                                            @elseif ($invitation->isExpired())
                                                <flux:badge color="red">{{ __('Expired') }}</flux:badge>
                                            @else
                                                <flux:badge color="yellow">{{ __('Pending') }}</flux:badge>
                                            @endif
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </div>
                @endif
            </div>
        @endif
    </x-settings.layout>
</section>
