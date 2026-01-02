@extends('layouts.layout')
@section('content')

<div>

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
                PTO Subscribe Emails
            </h1>
            <p class="text-sm text-gray-500">
                Dashboard / PTO / Subscribe Emails
            </p>
        </div>

        <a href="{{ route('ptoSubscribemails.create') }}"
            class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-[#00285E]
                   text-[#00285E] font-semibold
                   hover:bg-[#00285E] hover:text-white
                   transition-all duration-300
                   active:scale-95">
            + Add Email
        </a>
    </div>

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block bg-white rounded-xl shadow overflow-hidden">

        <table class="w-full border-collapse">
            <thead class="bg-gray-100 text-sm text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y text-sm">
                @forelse($emails as $key => $mail)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">{{ $key + 1 }}</td>
                    <td class="px-4 py-3 break-all">{{ $mail->email }}</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-center gap-3">
                            <a href="{{ route('ptoSubscribemails.edit', $mail->id) }}"
                                class="px-4 py-2 rounded-lg
                                       border border-yellow-500 text-yellow-600
                                       hover:bg-yellow-500 hover:text-white transition">
                                Edit
                            </a>

                            <form method="POST"
                                action="{{ route('ptoSubscribemails.destroy', $mail->id) }}"
                                onsubmit="return confirm('Delete this email?')">
                                @csrf
                                @method('DELETE')
                                <button
                                    class="px-4 py-2 rounded-lg
                                           border border-red-500 text-red-600
                                           hover:bg-red-500 hover:text-white transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-gray-400">
                        No emails found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    <!-- MOBILE CARDS -->
    <div class="md:hidden space-y-4">

        @forelse($emails as $key => $mail)
        <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">

            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-500">
                    #{{ $key + 1 }}
                </span>
            </div>

            <div class="text-sm text-gray-800 break-all">
                {{ $mail->email }}
            </div>

            <div class="flex gap-2 pt-2">
                <a href="{{ route('ptoSubscribemails.edit', $mail->id) }}"
                    class="flex-1 text-center px-4 py-2 rounded-lg
                           border border-yellow-500 text-yellow-600
                           hover:bg-yellow-500 hover:text-white transition">
                    Edit
                </a>

                <form method="POST"
                    action="{{ route('ptoSubscribemails.destroy', $mail->id) }}"
                    onsubmit="return confirm('Delete this email?')"
                    class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button
                        class="w-full px-4 py-2 rounded-lg
                               border border-red-500 text-red-600
                               hover:bg-red-500 hover:text-white transition active:scale-95">
                        Delete
                    </button>
                </form>
            </div>

        </div>
        @empty
        <div class="text-center text-gray-400 py-8">
            No emails found
        </div>
        @endforelse

    </div>

</div>

@endsection