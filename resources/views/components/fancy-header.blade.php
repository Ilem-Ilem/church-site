@props([
    'title' => null,
    'subtitle' => null,
    'breadcrumbs' => [],
])

<header class="relative overflow-hidden pt-6 pb-6 sm:pt-6 sm:pb-6 mb-4 rounded-lg shadow-lg dark:bg-zinc-800">
    <!-- Background SVG Pattern -->
    <svg class="absolute inset-0 w-full h-full text-gray-200 dark:text-gray-800" viewBox="0 0 100 100" preserveAspectRatio="none" fill="currentColor">
        <defs>
            <radialGradient id="ring-gradient-1" cx="50%" cy="50%" r="50%">
                <stop offset="0%" class="stop-color-fuchsia-500" />
                <stop offset="100%" class="stop-color-transparent" />
            </radialGradient>
            <radialGradient id="ring-gradient-2" cx="50%" cy="50%" r="50%">
                <stop offset="0%" class="stop-color-teal-500" />
                <stop offset="100%" class="stop-color-transparent" />
            </radialGradient>
        </defs>
        <circle cx="20" cy="80" r="10" fill="url(#ring-gradient-1)" class="animate-[spin_10s_linear_infinite]" />
        <circle cx="80" cy="20" r="15" fill="url(#ring-gradient-2)" class="animate-[pulse_4s_cubic-bezier(0.4,0,0.6,1)_infinite]" />
        <circle cx="45" cy="50" r="12" fill="url(#ring-gradient-1)" class="animate-[spin_7s_linear_infinite_reverse]" />
        <circle cx="70" cy="65" r="8" fill="url(#ring-gradient-2)" class="animate-[pulse_3s_cubic-bezier(0.4,0,0.6,1)_infinite]" />
        <circle cx="30" cy="30" r="18" fill="url(#ring-gradient-1)" class="animate-[spin_15s_linear_infinite]" />
    </svg>

    <!-- Overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-gray-50 dark:from-gray-900 to-transparent"></div>
    <div class="absolute inset-0 bg-white/5 dark:bg-black/5 backdrop-blur-sm"></div>

    <!-- Main Content -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-1">
        @if ($breadcrumbs)
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 sm:space-x-2">
                    @foreach ($breadcrumbs as $breadcrumb)
                        <li>
                            <div class="flex items-center">
                                @if (!$loop->first)
                                    <span class="text-gray-400">/</span>
                                @endif
                                <a href="{{ $breadcrumb['url'] ?? '#' }}"
                                   class="ml-1 text-sm font-medium {{ $loop->last ? 'text-gray-700 dark:text-gray-200 cursor-default' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }} rounded-md"
                                   aria-current="{{ $loop->last ? 'page' : false }}">
                                    @if ($loop->first)
                                        <svg class="w-4 h-4 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                        </svg>
                                    @endif
                                    {{ $breadcrumb['label'] }}
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        @if ($title)
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl lg:text-5xl">
                {{ $title }}
            </h1>
        @endif

        @if ($subtitle)
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                {{ $subtitle }}
            </p>
        @endif

        {{-- Action Slot --}}
        @if (trim($slot))
            <div class="mt-4">
                {{ $slot }}
            </div>
        @endif
    </div>
</header>
