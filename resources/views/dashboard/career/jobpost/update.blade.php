@extends('layouts.layout')
@section('content')

    <h1 class="text-2xl font-semibold mb-6">Edit Job Post</h1>

    <form action="{{ route('jobPost.update', $jobPost->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <input type="text" name="job_category" value="{{ $jobPost->job_category }}" class="w-full border p-3 rounded">

        <input type="text" name="job_location" value="{{ $jobPost->job_location }}" class="w-full border p-3 rounded">

        <button class="px-6 py-2 bg-[#00285E] text-white rounded">
            Update
        </button>
    </form>

@endsection