@extends('layouts.layout')
@section('content')

    <div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">

        <h2 class="text-xl font-semibold mb-4">
            Upload PTO Images
        </h2>

        <form method="POST" enctype="multipart/form-data" action="{{ route('ptoImages.store') }}">
            @csrf

            <!-- FILE INPUT -->
            <input type="file" name="images[]" id="imageInput" multiple accept="image/*"
                class="mb-4 border-[#00285E] border-2 p-3 w-full rounded">

            <!-- PREVIEW AREA -->
            <div id="preview" class="grid grid-cols-3 gap-3 mb-4 hidden">
            </div>

            <button class="w-full border-2 border-[#00285E] text-[#00285E] p-3 rounded
                       hover:bg-[#00285E] hover:text-white transition">
                Upload Images
            </button>

        </form>
    </div>

    <!-- IMAGE PREVIEW SCRIPT -->
    <script>
        const input = document.getElementById('imageInput');
        const preview = document.getElementById('preview');

        input.addEventListener('change', () => {
            preview.innerHTML = '';
            const files = input.files;

            if (files.length > 0) {
                preview.classList.remove('hidden');
            }

            Array.from(files).forEach(file => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className =
                        'w-full h-24 object-cover rounded-lg border';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>

@endsection