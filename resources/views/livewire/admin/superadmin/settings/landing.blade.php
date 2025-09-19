<?php

namespace App\Http\Livewire;

use App\Models\LandingPageSetting;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

new class extends Component {
    use WithFileUploads, Interactions;

    public LandingPageSetting $landing;
    public $carousel = [];
    public $isSaving = false;

    public function mount()
    {
        $this->landing = LandingPageSetting::firstOrCreate([]);
        $this->carousel = $this->landing->carousel ?? [];
        foreach ($this->carousel as &$item) {
            $item['temp_image'] = null;
            $item['preview'] = null;
        }
    }

    /** ✅ Save entire carousel */
    public function save()
    {
        $this->isSaving = true;

        $this->validate([
            'carousel.*.title' => 'required|string|max:255',
            'carousel.*.subtitle' => 'nullable|string|max:255',
            'carousel.*.image' => 'nullable|string',
            'carousel.*.temp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $this->persist();
        $this->toast()->success('Saved!', 'Landing carousel updated')->send();

        $this->dispatch('saved');
        $this->isSaving = false;
    }

    /** ✅ Add new carousel item */
    public function addCarouselItem()
    {
        $this->carousel[] = [
            'image' => null,
            'title' => '',
            'subtitle' => '',
            'temp_image' => null,
            'preview' => null,
        ];
        $this->persist();
    }

    /** ✅ Remove carousel + delete file if exists */
    public function removeCarouselItem($index)
    {
        if (isset($this->carousel[$index])) {
            $item = $this->carousel[$index];

            if (isset($item['image']) && is_string($item['image']) && Storage::disk('public')->exists($item['image'])) {
                Storage::disk('public')->delete($item['image']);
                Log::info('Deleted image', ['path' => $item['image']]);
            }

            unset($this->carousel[$index]);
            $this->carousel = array_values($this->carousel);
            $this->persist();
        }
    }

    /** ✅ Handle real-time updates */
    public function updated($propertyName)
    {
        $parts = explode('.', $propertyName);
        if (count($parts) === 3 && $parts[2] === 'temp_image') {
            $index = $parts[1];
            $value = $this->carousel[$index]['temp_image'] ?? null;

            Log::info('File upload attempt', [
                'property' => $propertyName,
                'file' => $value ? $value->getClientOriginalName() : 'null',
            ]);

            if ($value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                try {
                    // Ensure the upload directory exists
                    $uploadPath = 'uploads/carousel';
                    if (!Storage::disk('public')->exists($uploadPath)) {
                        Storage::disk('public')->makeDirectory($uploadPath);
                        Log::info('Created directory', ['path' => $uploadPath]);
                    }

                    // Delete old image if exists
                    if (isset($this->carousel[$index]['image']) && is_string($this->carousel[$index]['image']) && Storage::disk('public')->exists($this->carousel[$index]['image'])) {
                        Storage::disk('public')->delete($this->carousel[$index]['image']);
                        Log::info('Deleted old image', ['path' => $this->carousel[$index]['image']]);
                    }

                    // Store new image
                    $filename = uniqid() . '.' . $value->getClientOriginalExtension();
                    $path = $value->storeAs($uploadPath, $filename, 'public');

                    // Verify file was stored
                    if (Storage::disk('public')->exists($path)) {
                        $this->carousel[$index]['image'] = $path;
                        $this->carousel[$index]['preview'] = Storage::disk('public')->url($path);
                        $this->carousel[$index]['temp_image'] = null;
                        Log::info('Image uploaded successfully', [
                            'path' => $path,
                            'url' => $this->carousel[$index]['preview'],
                        ]);
                        $this->persist();
                    } else {
                        Log::error('Failed to store image', ['path' => $path]);
                        $this->toast()->error('Error', 'Failed to upload image')->send();
                    }
                } catch (\Exception $e) {
                    Log::error('Image upload exception', ['error' => $e->getMessage()]);
                    $this->toast()->error('Error', 'Image upload failed: ' . $e->getMessage())->send();
                }
            } else {
                Log::warning('No valid file uploaded', ['property' => $propertyName]);
            }
            return;
        }

        // Persist text input changes (debouncing handled in Blade)
        $this->persist();
    }

    /** 🔄 Persist to DB after any change */
    protected function persist()
    {
        $cleanedCarousel = array_map(function ($item) {
            // Remove temp_image and preview from saved data
            unset($item['temp_image'], $item['preview']);
            return $item;
        }, $this->carousel);

        $this->landing->update([
            'carousel' => $cleanedCarousel,
        ]);

        Log::info('Persisted carousel data', ['carousel' => $cleanedCarousel]);
    }
};
?>
<div>
    <div class="p-6 space-y-6">
        <h2 class="text-xl font-bold">Landing Page Settings</h2>
        <p class="text-gray-500">Manage the homepage carousel dynamically.</p>

        <div class="space-y-4">
            @foreach ($carousel as $index => $item)
                <div class="p-4 border rounded-lg space-y-3 bg-zinc-800 text-white"
                     wire:key="carousel-{{ $index }}">

                    <!-- Image Preview -->
                    <div class="space-y-2">
                        @if (isset($item['preview']) || (isset($item['image']) && is_string($item['image'])))
                            <div class="relative">
                                <img src="{{ $item['preview'] ?? Storage::disk('public')->url($item['image']) }}"
                                     class="w-full h-40 object-cover rounded"
                                     alt="Carousel image preview">
                                <button type="button"
                                        wire:click="removeCarouselItem({{ $index }})"
                                        class="absolute top-2 right-2 bg-red-600 text-white px-2 py-1 text-xs rounded">
                                    ✕
                                </button>
                            </div>
                        @endif

                        <input type="file"
                               wire:model.live="carousel.{{ $index }}.temp_image"
                               accept="image/*"
                               class="block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0 file:text-sm file:font-semibold
                                      file:bg-green-600 file:text-white hover:file:bg-green-700" />
                        @error("carousel.$index.temp_image")
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium">Title</label>
                        <input type="text" 
                               wire:model.live.debounce.500ms="carousel.{{ $index }}.title"
                               class="w-full px-3 py-2 rounded bg-zinc-700 border border-zinc-600 text-white">
                        @error("carousel.$index.title")
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Subtitle -->
                    <div>
                        <label class="block text-sm font-medium">Subtitle</label>
                        <input type="text" 
                               wire:model.live.debounce.500ms="carousel.{{ $index }}.subtitle"
                               class="w-full px-3 py-2 rounded bg-zinc-700 border border-zinc-600 text-white">
                        @error("carousel.$index.subtitle")
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Remove whole item -->
                    <button type="button" 
                            wire:click="removeCarouselItem({{ $index }})"
                            class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                        Remove Carousel Item
                    </button>
                </div>
            @endforeach

            <!-- Action Buttons -->
            <div class="flex space-x-4">
                <button type="button" 
                        wire:click="addCarouselItem"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    + Add Carousel Item
                </button>
                
                <button type="button" 
                        wire:click="save"
                        wire:loading.attr="disabled"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2">
                    <span wire:loading wire:target="save" class="animate-spin">↻</span>
                    <span>{{ $isSaving ? 'Saving...' : 'Save Changes' }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Loading indicator -->
    <div wire:loading wire:target="carousel" class="fixed top-4 right-4 bg-gray-800 text-white px-4 py-2 rounded">
        Saving...
    </div>
</div>