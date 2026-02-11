@extends('layouts.layout')
@section('content')

<div>

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
                Alumni Subscribe Emails
            </h1>
            <p class="text-sm text-gray-500">
                Dashboard / Alumni / Subscribe Emails
            </p>
        </div>

        <a href="{{ route('alumniMail.create') }}"
            class="w-full md:w-auto text-center
                   px-6 py-3 rounded-xl
                   border-2 border-[#00285E]
                   text-[#00285E] font-semibold
                   hover:bg-[#00285E] hover:text-white
                   transition active:scale-95">
            + Add Email
        </a>
    </div>

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto p-4">
            <table id="alumniMailTable" class="display w-full text-left" style="width:100%">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider font-bold">
                        <th class="px-4 py-3 border-b border-gray-200">#</th>
                        <th class="px-4 py-3 border-b border-gray-200">Email</th>
                        <th class="px-4 py-3 border-b border-gray-200 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($emails as $key => $mail)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        {{ $key + 1 }}
                    </td>

                    <td class="px-4 py-3 break-all">
                        {{ $mail->email }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-3">
                            <a href="{{ route('alumniMail.edit', $mail->id) }}"
                                class="px-4 py-2 rounded-lg
                                       border border-yellow-500
                                       text-yellow-600
                                       hover:bg-yellow-500 hover:text-white
                                       transition">
                                Edit
                            </a>

                            <form method="POST"
                                action="{{ route('alumniMail.destroy', $mail->id) }}"
                                onsubmit="return confirm('Delete this email?')">
                                @csrf
                                @method('DELETE')
                                <button
                                    class="px-4 py-2 rounded-lg
                                           border border-red-500
                                           text-red-600
                                           hover:bg-red-500 hover:text-white
                                           transition">
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
    </div>

    @push('scripts')
    <x-datatable-init table-id="alumniMailTable" />
    @endpush

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
                <a href="{{ route('alumniMail.edit', $mail->id) }}"
                    class="flex-1 text-center px-4 py-2 rounded-lg
                           border border-yellow-500 text-yellow-600
                           hover:bg-yellow-500 hover:text-white
                           transition">
                    Edit
                </a>

                <form method="POST"
                    action="{{ route('alumniMail.destroy', $mail->id) }}"
                    onsubmit="return confirm('Delete this email?')"
                    class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button
                        class="w-full px-4 py-2 rounded-lg
                               border border-red-500 text-red-600
                               hover:bg-red-500 hover:text-white
                               transition active:scale-95">
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