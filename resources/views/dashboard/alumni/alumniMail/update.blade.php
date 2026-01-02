@extends('layouts.layout')
@section('content')

<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-2xl font-semibold text-gray-800 mb-6">
        Update Email
    </h2>

    <form method="POST"
        action="{{ route('alumniMail.update', $email->id) }}"
        class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Email Address
            </label>
            <input type="email"
                name="email"
                value="{{ old('email', $email->email) }}"
                class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none">
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('alumniMail.index') }}"
                class="px-6 py-3 rounded-lg
                      border border-gray-300
                      text-gray-600
                      hover:bg-gray-100 transition">
                Cancel
            </a>

            <button type="submit"
                class="px-8 py-3 rounded-lg
                       border-2 border-[#00285E]
                       text-[#00285E] font-semibold
                       hover:bg-[#00285E] hover:text-white
                       transition-all active:scale-95">
                Update
            </button>
        </div>

    </form>

</div>

@endsection