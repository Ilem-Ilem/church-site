@php
use App\Models\Chapter;
$chapters = Auth::user()->hasRole('super-admin') ? Chapter::all() : collect();
$currentChapter = request()->query('chapter');
@endphp

@if(Auth::user()->hasRole('super-admin') && $chapters->count() > 0)
<div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
    <form method="GET" action="{{ route(request()->route()->getName()) }}">
        @foreach(request()->query() as $key => $value)
            @if($key !== 'chapter')
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
        
        <label for="chapter-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Chapter</label>
        <div class="flex gap-2">
            <select name="chapter" id="chapter-select" class="flex-1 pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                @foreach($chapters as $chapter)
                    <option value="{{ $chapter->name }}" @selected($currentChapter === $chapter->name)>
                        {{ $chapter->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition">
                Switch
            </button>
        </div>
    </form>
</div>
@endif
