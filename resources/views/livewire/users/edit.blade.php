<div>
    <div class="mx-auto w-full max-w-3xl">
        <div class="flex flex-col gap-6">
            {{-- Header --}}
            <div>
                <flux:heading size="xl">Edit User</flux:heading>
                <flux:text>Update user account details</flux:text>
            </div>

            {{-- Form Card --}}
            <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
                <form wire:submit="save" class="flex flex-col gap-6">
                    {{-- Name --}}
                    <flux:field>
                        <flux:label>Name <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="form.name" placeholder="John Doe" />
                        <flux:error name="form.name" />
                    </flux:field>

                    {{-- Email --}}
                    <flux:field>
                        <flux:label>Email <span class="text-red-500">*</span></flux:label>
                        <flux:input type="email" wire:model="form.email" placeholder="john@example.com" />
                        <flux:error name="form.email" />
                    </flux:field>

                    {{-- Password --}}
                    <flux:field>
                        <flux:label>Password</flux:label>
                        <flux:input type="password" wire:model="form.password" placeholder="••••••••" />
                        <flux:error name="form.password" />
                        <flux:text>Leave blank to keep current password</flux:text>
                    </flux:field>

                    {{-- Password Confirmation --}}
                    <flux:field>
                        <flux:label>Confirm Password</flux:label>
                        <flux:input type="password" wire:model="form.password_confirmation" placeholder="••••••••" />
                        <flux:error name="form.password_confirmation" />
                    </flux:field>

                    {{-- Role --}}
                    <flux:field>
                        <flux:label>Role <span class="text-red-500">*</span></flux:label>
                        <flux:select wire:model="form.role">
                            <option value="viewer">Viewer</option>
                            <option value="editor">Editor</option>
                            <option value="admin">Admin</option>
                        </flux:select>
                        <flux:error name="form.role" />
                        <flux:text>
                            <strong>Viewer:</strong> Can view content only<br>
                            <strong>Editor:</strong> Can create and edit content<br>
                            <strong>Admin:</strong> Full access to all features
                        </flux:text>
                    </flux:field>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 border-t border-neutral-200 pt-6 dark:border-neutral-700">
                        <flux:button wire:navigate href="{{ route('users.index') }}" variant="ghost">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            Update User
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
