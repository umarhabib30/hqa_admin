@extends('layouts.layout')
@section('content')

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">
        <h2 class="text-2xl font-semibold mb-6">Add Top Achiever</h2>

        <form method="POST" enctype="multipart/form-data" action="{{ route('topAchievers.store') }}" class="space-y-6">
            @csrf

            {{-- <input name="title" placeholder="Title" class="w-full border p-3 rounded" required>

            <textarea name="desc" placeholder="Main Description" class="w-full border p-3 rounded h-32"></textarea> --}}

            <input type="file" name="image" class="w-full border-[#00285E] border-2 p-3">

            <input name="class_achiever" placeholder="Class (e.g FSC, BS)" class="w-full border p-3 rounded" required>

            <input name="achiever_name" placeholder="Achiever Name" class="w-full border p-3 rounded" required>

            <textarea name="achiever_desc" placeholder="Achiever Description"
                class="w-full border p-3 rounded h-32"></textarea>

            <!-- META ENTRIES -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-lg">Meta Titles & Images</h3>
                    <button type="button" onclick="addMetaEntry()" class="text-[#00285E] text-sm font-semibold">+ Add
                        More</button>
                </div>

                <div id="metaEntryWrapper" class="space-y-4">
                    <div class="flex gap-4 items-end border p-4 rounded bg-gray-50">
                        <div class="flex-1">
                            <label class="block text-sm font-medium mb-1">Meta Title</label>
                            <input name="meta_titles[]" class="w-full border p-2 rounded"
                                placeholder="e.g. 1st Position in Board">
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

            <button class="w-full bg-[#00285E] text-white p-3 rounded hover:bg-blue-700 transition">
                Save Achiever
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
                <input name="meta_titles[]" class="w-full border p-2 rounded" placeholder="e.g. 1st Position in Board">
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