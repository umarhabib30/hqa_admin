@extends('layouts.layout')
@section('content')

    <div class="flex justify-between mb-6">
        <h1 class="text-2xl font-semibold">Teacher Job Posts</h1>

        <a href="{{ route('jobPost.create') }}" class="px-6 py-2 bg-[#00285E] text-white rounded-lg">
            Create
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <table class="w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-3 border">Category</th>
                <th class="p-3 border">Location</th>
                <th class="p-3 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobPosts as $post)
                <tr>
                    <td class="p-3 border">{{ $post->job_category }}</td>
                    <td class="p-3 border">{{ $post->job_location }}</td>
                    <td class="p-3 border flex gap-2">
                        <a href="{{ route('jobPost.edit', $post->id) }}" class="text-[#00285E]">Edit</a>

                        <form action="{{ route('jobPost.destroy', $post->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection