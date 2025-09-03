<?php

use App\Models\Chapter;
use App\Models\User;
use Livewire\Volt\Component;
use Spatie\Permission\Models\Role;

new class extends Component {
    public $total_conclaves;
    public $total_members;
    public $total_leaders;

    public function mount()
    {
        $role = Role::where('name', '=', 'team_lead')->first();
        
        $this->total_conclaves = Chapter::count();
        $this->total_members = User::count();
        $this->total_leaders = User::role('team-lead')->count();

    }
}; ?>

<div>
   
    <div class="grid sm:grid-cols-3 gap-6 ">
        <x-card header="Conclaves" minimize>
            <div class="text-5xl m-3 font-[montserat]">{{ $total_conclaves }}</div>
            <span><small>there are {{ $total_conclaves }} conclaves would you like to add more??</small></span>
            <x-link href="{{ route('super-admin.conclaves.create') }}" text="Add More" icon="arrow-up-right" position="right" wire:navigate/>
        </x-card>
         
        
        <div class="dark:bg-zinc-900 rounded-md border dark:border-gray-200 hover:shadow-2xl hover:shadow-gray-300 transition-all duration-75">
            <div class="flex p-3">
                <div class="text-5xl m-3 font-[montserat]">{{ $total_conclaves }}</div>
                <div class="text-3xl font-[sigmar] m-3">Conclaves</div>
            </div>
            <div class="foot dark:bg-zinc-800 flex justify-center p-2">
                <flux:link style="w-full w-[100%]">More >></flux:link>
            </div>
        </div>
        <div class="dark:bg-zinc-900 rounded-md border dark:border-gray-200 hover:shadow-2xl hover:shadow-gray-300 transition-all duration-75">
            <div class="flex p-3">
                <div class="text-5xl m-3 font-[montserat]">{{ $total_members }}</div>
                <div class="text-3xl font-[sigmar] m-3">Members</div>
            </div>
            <div class="foot dark:bg-zinc-800 flex justify-center p-2">
                <flux:link style="w-full w-[100%]">More >></flux:link>
            </div>
        </div>
        <div class="dark:bg-zinc-900 rounded-md border dark:border-gray-200 hover:shadow-2xl hover:shadow-gray-300 transition-all duration-75">
            <div class="flex p-3">
                <div class="text-5xl m-3 font-[montserat]">{{ $total_leaders }}</div>
                <div class="text-3xl font-[sigmar] m-3">Leaders</div>
            </div>
            <div class="foot dark:bg-zinc-800 flex justify-center p-2">
                <flux:link style="w-full w-[100%]">More >></flux:link>
            </div>
        </div>
    </div>
</div>