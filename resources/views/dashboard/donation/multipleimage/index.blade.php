@extends('layouts.layout')
@section('content')

<div>
    <div class="flex justify-between mb-6 md:flex-row flex-col ">
        <h1 class="text-2xl font-semibold">donation Gallery</h1>

        <a href="{{ route('donationImage.create') }}"
            class="px-6 py-3 border-2 border-[#00285E] text-[#00285E]
                   rounded-xl hover:bg-[#00285E] hover:text-white transition">
            Upload Images
        </a>
    </div>

    @foreach($galleries as $gallery)
    <div class="bg-white rounded-xl shadow p-6 mb-6">

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            @foreach($gallery->images as $img)
            <img src="{{ asset('storage/'.$img) }}"
                class="w-full h-40 object-cover rounded-lg">
            @endforeach
        </div>

        <div class="flex gap-3">
            <a href="{{ route('donationImage.edit',$gallery->id) }}"
                class="px-5 py-2 border border-[#00285E] text-[#00285E]
                      rounded-lg hover:bg-[#00285E] hover:text-white transition">
                Edit
            </a>

            <form method="POST" action="{{ route('donationImage.destroy',$gallery->id) }}">
                @csrf
                @method('DELETE')
                <button
                    onclick="return confirm('Delete full gallery?')"
                    class="px-5 py-2 border border-red-600 text-red-600
                           rounded-lg hover:bg-red-600 hover:text-white transition">
                    Delete
                </button>
            </form>
        </div>

    </div>
    @endforeach
</div>

@endsection