<?php

use App\Models\Chapter;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Livewire\Volt\Component;

new class extends Component {

    public $total_conclaves;

    public $chapter_id;
    public $oldAdminId;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount()
    {
        $this->total_conclaves = Chapter::count();
    }

    public function getChaptersProperty()
    {
        return Chapter::with(['admin'])->paginate();
    }

    public function edit()
    {
        $chapter = Chapter::findOrFail($this->chapter_id);
        $user = User::where('id', '=', $this->oldAdminId)->first();

        $user->removeRole('admin');
        $user->assignRole('member');
        $this->saveAdmin();

        $this->dispatch('$refresh');
        $this->dispatch("admin-changed");
    }


    /**
     * Handle an incoming registration request.
     */
    public function save(): void
    {
       $this->saveAdmin();
        $this->dispatch('$refresh');
        $this->dispatch("admin-created");
    }

    protected function saveAdmin()
    {
        $validated               = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);
        $validated['password']   = Hash::make($validated['password']);
        $validated['chapter_id'] = $this->chapter_id;
        $user                    = User::create($validated);

        $user->assignRole('admin');
        $this->reset();

    }


}; ?>

<div>
    <x-card>

        <ul class="max-w-3xl mx-auto mt-8 overflow-hidden rounded-2xl border border-zinc-200/70 dark:border-zinc-900 bg-white/70 dark:bg-zinc-900/60 backdrop-blur divide-y divide-zinc-200/70 dark:divide-zinc-800/80">
            <!-- Item -->
            <li class="group flex items-start gap-4 p-4 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors mb-4">
                <!-- Icon -->
                <div class="mt-0.5 rounded-xl p-2 ring-1 ring-inset ring-zinc-200 dark:ring-zinc-900">
                    <!-- heroicon: document-text -->
                    <svg class="h-5 w-5 text-zinc-900 dark:text-zinc-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-4.5a2.25 2.25 0 0 0-2.25-2.25H8.25A2.25 2.25 0 0 0 6 9.75v8.25A2.25 2.25 0 0 0 8.25 20.25h7.5A2.25 2.25 0 0 0 18 18v-3" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 9h6M9 12h6M9 15h4" />
                    </svg>
                </div>
                <!-- Content -->
                <div class="flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Chapters (Conclaves)</h3>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Total of {{ $total_conclaves }}
                            chapters</span>
                    </div>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">.</p>
                    <div class="mt-3 flex items-center gap-2">

                        <button class="rounded-xl px-3 py-1.5 text-sm bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:opacity-90">Download
                            As PDF</button>
                    </div>
                </div>
            </li>

            @foreach ($this->chapters as $chapter)
            <li class="group flex items-start border-b-1 border-gray-200 gap-4 p-4 hover:bg-zinc-50 dark:hover:bg-zinc-900/80 transition-colors">
                <div class="flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $chapter->name }}</h3>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">
                            Created {{ \Carbon\Carbon::parse($chapter->cerated_at)->toDayDateTimeString() }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                        @if($chapter->admin)
                        @php
                            $admin = $chapter->admin
                        @endphp
                        {{ $admin->name }} &nbsp; {{ $admin->email }}
                        @endif
                    </p>
                    <div class="mt-3 flex items-center gap-2">
                        @if($chapter->admin)
                        <button class="rounded-xl px-3 py-1.5 text-sm bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:opacity-90" x-on:click="$modalOpen('edit-admin-modal'); $wire.set('chapter_id', {{ $chapter->id }}); $wire.set('oldAdminId', {{$chapter->admin->id}})">Change
                            Admin</button>
                        @else
                        <button class="rounded-xl px-3 py-1.5 text-sm bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:opacity-90" x-on:click="$modalOpen('create-admin-modal'); $wire.set('chapter_id', {{ $chapter->id }}); ">Create
                            Admin</button>
                        @endif
                    </div>
                </div>
            </li> @endforeach
        </ul>
    </x-card>

    <x-modal id="create-admin-modal" center>

        <form method="POST" wire:submit="save" class="flex flex-col gap-6">
            <!-- Name -->
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" :placeholder="__('Full name')" />

            <!-- Email Address -->
            <flux:input wire:model="email" :label="__('Email address')" type="email" required autocomplete="email" placeholder="email@example.com" />

            <!-- Password -->
            <flux:input wire:model="password" :label="__('Password')" type="password" required autocomplete="new-password" :placeholder="__('Password')" viewable />

            <!-- Confirm Password -->
            <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password" required autocomplete="new-password" :placeholder="__('Confirm password')" viewable />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <flux:button x-on:click="$modalClose('create-admin-modal')" class="w-full mt-2 bg-zinc-950">cancel
            </flux:button>
        </div>
    </x-modal>

    <x-modal id="edit-admin-modal" center>

        <form method="POST" wire:submit="edit" class="flex flex-col gap-6">
            <!-- Name -->
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" :placeholder="__('Full name')" />

            <!-- Email Address -->
            <flux:input wire:model="email" :label="__('Email address')" type="email" required autocomplete="email" placeholder="email@example.com" />

            <!-- Password -->
            <flux:input wire:model="password" :label="__('Password')" type="password" required autocomplete="new-password" :placeholder="__('Password')" viewable />

            <!-- Confirm Password -->
            <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password" required autocomplete="new-password" :placeholder="__('Confirm password')" viewable />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <flux:button x-on:click="$modalClose('edit-admin-modal')" class="w-full mt-2 bg-zinc-950">cancel
            </flux:button>
        </div>
    </x-modal>
    @script
    <script>
        $wire.on('admin-created', (event) => {
            $modalClose('create-admin-modal')
        });
        $wire.on('admin-changed', (event)=>{
            $modalClose('edit-admin-modal')
        });
    </script>
    @endscript

</div>
