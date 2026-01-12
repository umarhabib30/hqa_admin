@extends('layouts.layout')
@section('content')

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">
        <h2 class="text-2xl font-semibold mb-6">Edit Top Achiever</h2>

        <form method="POST" enctype="multipart/form-data" action="{{ route('topAchievers.update', $achiever->id) }}"
            class="space-y-6">
            @csrf
            @method('PUT')

            {{-- <input name="title" value="{{ $achiever->title }}" class="w-full border p-3 rounded" required>

            <textarea name="desc" class="w-full border p-3 rounded h-32">{{ $achiever->desc }}</textarea> --}}

            @if($achiever->image)
                <img src="{{ Storage::url($achiever->image) }}" class="w-32 h-32 object-cover rounded-lg mb-2">
            @endif
            <input type="file" name="image" class="w-full">

            <input name="class_achiever" value="{{ $achiever->class_achiever }}" class="w-full border p-3 rounded" required>

            <input name="achiever_name" value="{{ $achiever->achiever_name }}" class="w-full border p-3 rounded" required>

            <textarea name="achiever_desc" class="w-full border p-3 rounded h-32">{{ $achiever->achiever_desc }}</textarea>

            <!-- META ENTRIES -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-lg">Meta Titles & Images</h3>
                    <button type="button" onclick="addMetaEntry()" class="text-[#00285E] text-sm font-semibold">+ Add
                        More</button>
                </div>

                <div id="metaEntryWrapper" class="space-y-4">
                    @foreach($achiever->meta_data ?? [] as $index => $meta)
                        <div class="flex gap-4 items-end border p-4 rounded bg-gray-50">
                            <div class="flex-1">
                                <label class="block text-sm font-medium mb-1">Meta Title</label>
                                <input name="meta_titles[]" value="{{ $meta['title'] ?? '' }}" class="w-full border p-2 rounded"
                                    placeholder="Meta Title">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium mb-1">Meta Image</label>
                                @if(!empty($meta['image']))
                                    <img src="{{ Storage::url($meta['image']) }}" class="w-full h-24 object-cover rounded mb-2">
                                @endif
                                <input type="file" name="meta_images[]" class="w-full">
                            </div>
                            <button type="button" onclick="this.closest('.border').remove()"
                                class="text-red-600 text-sm">Remove</button>
                        </div>
                    @endforeach

                    <!-- At least one empty for new -->
                    <div class="flex gap-4 items-end border p-4 rounded bg-gray-50">
                        <div class="flex-1">
                            <label class="block text-sm font-medium mb-1">Meta Title</label>
                            <input name="meta_titles[]" class="w-full border p-2 rounded" placeholder="New Meta Title">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium mb-1">Meta Image</label>
                            <input type="file" name="meta_images[]" class="w-full">
                        </div>
                        <button type="button" onclick="this.closest('.border').remove()"
                            class="text-red-600 text-sm">Remove</button>
                    </div>
                </div>
            </div>

            <button class="w-full bg-blue-600 text-white p-3 rounded hover:bg-blue-700 transition">
                Update Achiever
            </button>
        </form>
    </div>

    <script>
        function addMetaEntry() {
            const wrapper = document.getElementById('metaEntryWrapper');
            const div = document.createElement('div');
            div.className = 'flex gap-4 items-end border p-4 rounded bg-gray-50';
            div.innerHTML = `
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Meta Title</label>
                <input name="meta_titles[]" class="w-full border p-2 rounded" placeholder="New Meta Title">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Meta Image</label>
                <input type="file" name="meta_images[]" class="w-full">
            </div>
            <button type="button" onclick="this.closest('.border').remove()" class="text-red-600 text-sm">Remove</button>
        `;
            wrapper.appendChild(div);
        }
    </script>

@endsection