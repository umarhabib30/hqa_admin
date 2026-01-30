@extends('layouts.layout')
@section('content')
    <div>

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
                Alumni Forms
            </h1>

            {{-- <a href="{{ route('alumniForm.create') }}"
                class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-[#00285E]
                   text-[#00285E] font-semibold
                   hover:bg-[#00285E] hover:text-white
                   transition active:scale-95">
                + Add
            </a> --}}
        </div>

        <!-- DESKTOP TABLE -->
        <div class="hidden md:block bg-white rounded-xl shadow overflow-hidden">

            <table class="w-full">
                <thead class="bg-gray-100 text-sm text-gray-700">
                    <tr>
                        <th class="p-4 text-left">Name</th>
                        <th class="p-4 text-center">Year</th>
                        <th class="p-4 text-center">Email</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($forms as $form)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-medium">
                                {{ $form->first_name }} {{ $form->last_name }}
                            </td>

                            <td class="p-4 text-center">
                                {{ $form->graduation_year }}
                            </td>

                            <td class="p-4 text-center break-all">
                                {{ $form->email }}
                            </td>

                            <td class="p-4 text-center">
                                <span
                                    class="px-3 py-1 rounded-full text-sm
                                     bg-blue-100 text-[#00285E] font-semibold">
                                    {{ ucfirst($form->status) }}
                                </span>
                            </td>

                            <td class="p-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('alumniForm.show', $form->id) }}"
                                        class="px-3 py-1 rounded border border-green-600 text-green-600
                  hover:bg-green-600  transition">
                                        Details
                                    </a>

                                    <a href="{{ route('alumniForm.edit', $form->id) }}"
                                        class="px-3 py-1 rounded border border-[#00285E] text-[#00285E]
                  hover:bg-[#00285E] hover:text-white transition">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('alumniForm.destroy', $form->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="px-3 py-1 rounded border border-red-600 text-red-600
                           hover:bg-red-600 hover:text-white transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500">
                                No alumni forms found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

        <!-- MOBILE CARDS -->
        <div class="md:hidden space-y-4">

            @forelse($forms as $form)
                <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">

                    <div>
                        <h3 class="font-semibold text-gray-800">
                            {{ $form->first_name }} {{ $form->last_name }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ $form->email }}
                        </p>
                    </div>

                    <div class="flex justify-between text-sm text-gray-700">
                        <span>
                            <strong>Year:</strong> {{ $form->graduation_year }}
                        </span>

                        <span class="px-3 py-1 rounded-full
                     bg-blue-100 text-[#00285E] font-semibold">
                            {{ ucfirst($form->status) }}
                        </span>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <!-- Details Button -->
                        <a href="{{ route('alumniForm.show', $form->id) }}"
                            class="flex-1 text-center px-4 py-2 rounded-lg
                          border border-green-600 text-green-600
                          hover:bg-green-600 hover:text-white transition">
                            Details
                        </a>

                        <!-- Edit Button -->
                        <a href="{{ route('alumniForm.edit', $form->id) }}"
                            class="flex-1 text-center px-4 py-2 rounded-lg
                          border border-[#00285E] text-[#00285E]
                          hover:bg-[#00285E] hover:text-white transition">
                            Edit
                        </a>

                        <!-- Delete Button -->
                        <form method="POST" action="{{ route('alumniForm.destroy', $form->id) }}" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button
                                class="w-full px-4 py-2 rounded-lg
                               border border-red-600 text-red-600
                               hover:bg-red-600 hover:text-white
                               transition active:scale-95">
                                Delete
                            </button>
                        </form>
                    </div>

                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    No alumni forms found.
                </div>
            @endforelse

        </div>


    </div>
@endsection
