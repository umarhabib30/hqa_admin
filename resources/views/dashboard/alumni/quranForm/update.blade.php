@extends('layouts.layout')
@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">

    <h2 class="text-2xl font-semibold text-gray-800 mb-6">
        Edit Alumni Form
    </h2>

    <form method="POST"
        enctype="multipart/form-data"
        action="{{ route('alumniForm.update',$form->id) }}"
        class="space-y-6">
        @csrf
        @method('PUT')

        <!-- NAME -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input name="first_name" value="{{ $form->first_name }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                          focus:ring-2 focus:ring-[#00285E] outline-none">

            <input name="last_name" value="{{ $form->last_name }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                          focus:ring-2 focus:ring-[#00285E] outline-none">
        </div>

        <!-- GRADUATION / STATUS -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input name="graduation_year" value="{{ $form->graduation_year }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                          focus:ring-2 focus:ring-[#00285E] outline-none">

            <select name="status"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                           focus:ring-2 focus:ring-[#00285E] outline-none">
                <option value="single" @selected($form->status=='single')>Single</option>
                <option value="married" @selected($form->status=='married')>Married</option>
            </select>
        </div>

        <!-- CONTACT -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input name="email" value="{{ $form->email }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                          focus:ring-2 focus:ring-[#00285E] outline-none">

            <input name="phone" value="{{ $form->phone }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300
                          focus:ring-2 focus:ring-[#00285E] outline-none">
        </div>

        <!-- ADDRESS -->
        <input name="address" value="{{ $form->address }}"
            class="w-full px-4 py-3 rounded-lg border border-gray-300
                      focus:ring-2 focus:ring-[#00285E] outline-none">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <input name="city" value="{{ $form->city }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300">

            <input name="state" value="{{ $form->state }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300">

            <input name="zipcode" value="{{ $form->zipcode }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300">
        </div>

        <!-- EDUCATION -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input name="college" value="{{ $form->college }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300">

            <input name="degree" value="{{ $form->degree }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300">
        </div>

        <!-- JOB -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input name="company" value="{{ $form->company }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300">

            <input name="job_title" value="{{ $form->job_title }}"
                class="w-full px-4 py-3 rounded-lg border border-gray-300">
        </div>

        <!-- ACHIEVEMENTS -->
        <textarea name="achievements" rows="3"
            class="w-full px-4 py-3 rounded-lg border border-gray-300">
        {{ $form->achievements }}
        </textarea>

        <!-- FILES -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- IMAGE -->
            <div>
                <label class="text-sm text-gray-600">Image</label>

                @if($form->image)
                <img src="{{ asset('storage/'.$form->image) }}"
                    class="w-32 h-32 object-cover rounded mb-2 border">
                @endif

                <input type="file" name="image"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <!-- DOCUMENT -->
            <div>
                <label class="text-sm text-gray-600">Document</label>

                @if($form->document)
                <a href="{{ asset('storage/'.$form->document) }}"
                    target="_blank"
                    class="text-blue-600 underline text-sm block mb-2">
                    View Current Document
                </a>
                @endif

                <input type="file" name="document"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

        </div>


        <!-- ACTION -->
        <div class="flex justify-end gap-4 pt-4">
            <a href="{{ route('alumniForm.index') }}"
                class="px-6 py-3 rounded-lg border border-gray-300 text-gray-600">
                Back
            </a>

            <button type="submit"
                class="px-8 py-3 rounded-lg border-2 border-[#00285E]
                       text-[#00285E] font-semibold
                       hover:bg-[#00285E] hover:text-white
                       transition-all duration-300 active:scale-95">
                Update Form
            </button>
        </div>

    </form>
</div>

@endsection