<?php

namespace App\Http\Livewire;

use App\Models\GlobalSetting;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;
use Illuminate\Support\Facades\Auth;

new  class extends Component {
    use WithFileUploads, Interactions;
    public GlobalSetting $settings;

    public string $church_name = '';
    public string $denomination = '';
    public string $tagline = '';
    public $logo = null;
    public $favicon = null;
    public $banner_image = null;
    public string $livestream_url = '';
    public string $podcast_url = '';
    public string $giving_url = '';
    public $social_links;
    public string $meta_title = '';
    public string $meta_description = '';
    public string $meta_keywords = '';
    public $extras;

    public function mount()
    {
        $this->settings = GlobalSetting::firstOrCreate([]);

       
        $this->church_name      = $this->settings->church_name ?? '';
        $this->denomination     = $this->settings->denomination ?? '';
        $this->tagline          = $this->settings->tagline ?? '';
        $this->livestream_url   = $this->settings->livestream_url ?? '';
        $this->podcast_url      = $this->settings->podcast_url ?? '';
        $this->giving_url       = $this->settings->giving_url ?? '';
        $this->social_links     = json_decode($this->settings->social_links, true) ?? [];
        $this->meta_title       = $this->settings->meta_title ?? '';
        $this->meta_description = $this->settings->meta_description ?? '';
        $this->meta_keywords    = $this->settings->meta_keywords ?? '';
        $this->extras           = $this->settings->extras ?? [];
    }

    public function save()
    {
        // Handle uploads
        if ($this->logo) {
            $this->settings->logo = $this->logo->store('uploads', 'public');
        }
        if ($this->favicon) {
            $this->settings->favicon = $this->favicon->store('uploads', 'public');
        }
        if ($this->banner_image) {
            $this->settings->banner_image = $this->banner_image->store('uploads', 'public');
        }

        $this->settings->update([
            'church_name' => $this->church_name,
            'denomination' => $this->denomination,
            'tagline' => $this->tagline,
            'livestream_url' => $this->livestream_url,
            'podcast_url' => $this->podcast_url,
            'giving_url' => $this->giving_url,
            'social_links' => json_encode($this->social_links),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'extras' => $this->extras,
        ]);

        $this->toast()
            ->success('Done!', 'Site Site Changed Successfully')
            ->send();
    }


    public function deleteLogo()
    {
        $this->deleteUpload('logo');
        $this->toast()->success('Deleted', 'Logo removed')->send();
    }

    public function deleteFavicon()
    {
        $this->deleteUpload('favicon');
        $this->toast()->success('Deleted', 'Favicon removed')->send();
    }
    public function deleteBannerImage()
    {
        $this->deleteUpload('banner_image');
        $this->toast()->success('Deleted', 'Banner image removed')->send();
    }
    // Delete uploaded file
    public function deleteUpload($field)
    {
        if ($this->settings->$field) {
            Storage::disk('public')->delete($this->settings->$field);
            $this->settings->$field = null;
            $this->settings->save();
        }
        $this->$field = null;

    }
};

?>
<div class="p-6 space-y-6">
    <x-fancy-header title="Global Settings" subtitle="Manage church-wide settings for all chapters" :breadcrumbs="[
        ['label' => 'Home', 'url' => route('admin.dashboard')],
        ['label' => 'Settings']
    ]">
    </x-fancy-header>
    <x-button wire:click="save" class="mb-4">Save Settings</x-button>

    <x-tab class="bg-zinc-800 text-white rounded-lg p-4 space-y-4" selected="General">

        <!-- General Info -->
        <x-tab.items tab="General" class="hover:bg-zinc-700">
            <div class="space-y-3">
                <x-input label="Church Name" wire:model="church_name" />
                <x-input label="Denomination" wire:model="denomination" />
                <x-input label="Tagline" wire:model="tagline" />

                <!-- Uploads -->
                <x-upload label="Logo" wire:model="logo" :current="$settings->logo" delete delete-method="deleteLogo" />

                <x-upload label="Favicon" wire:model="favicon" :current="$settings->favicon" delete
                    delete-method="deleteFavicon" />
                <span class="text-sm text-gray-400">A favicon is a small icon displayed in the browser tab and
                    bookmarks, representing your website.</span>

                <x-upload label="Banner Image" wire:model="banner_image" :current="$settings->banner_image" delete
                    delete-method="deleteBannerImage" />
            </div>
        </x-tab.items>

        <!-- Media & Links -->
        <x-tab.items tab="Media & Links" class="hover:bg-zinc-700">
            <div class="space-y-3">
                <x-input label="Livestream URL" wire:model="livestream_url" />
                <x-input label="Podcast URL" wire:model="podcast_url" />
                <x-input label="Giving URL" wire:model="giving_url" />
            </div>
        </x-tab.items>

        <!-- Social Links -->
        <x-tab.items tab="Social Links" class="hover:bg-zinc-700">
            <div class="space-y-3">
            <x-input label="Facebook" wire:model="social_links.facebook" />
            <x-input label="YouTube" wire:model="social_links.youtube" />
            <x-input label="Instagram" wire:model="social_links.instagram" />
            <x-input label="Twitter" wire:model="social_links.twitter" />
            <x-input label="Telegram" wire:model="social_links.telegram" />
            </div>
        </x-tab.items>

        <!-- SEO -->
        <x-tab.items tab="SEO" class="hover:bg-zinc-700">
            <div class="space-y-3">
                <x-input label="Meta Title" wire:model="meta_title" />
                <x-textarea label="Meta Description" wire:model="meta_description" />
                <x-input label="Meta Keywords (comma separated)" wire:model="meta_keywords" />
            </div>
        </x-tab.items>

        <!-- Extras -->
        <x-tab.items tab="Extras" class="hover:bg-zinc-700">
            <div class="space-y-3">
                <x-textarea label="Extras JSON" wire:model="extras" placeholder='{"key":"value"}' />
            </div>
        </x-tab.items>

    </x-tab>
</div>