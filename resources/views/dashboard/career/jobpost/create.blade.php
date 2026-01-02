@extends('layouts.layout')
@section('content')

    <h1 class="text-2xl font-semibold mb-6">Create Job Post</h1>

    <form action="{{ route('jobPost.store') }}" method="POST" class="space-y-4">
        @csrf

        <input type="text" name="job_category" placeholder="Job Category" class="w-full border p-3 rounded">

        <input type="text" name="job_location" placeholder="Job Location" class="w-full border p-3 rounded">

        <button class="px-6 py-2 bg-[#00285E] text-white rounded">
            Save
        </button>
    </form>

@endsection