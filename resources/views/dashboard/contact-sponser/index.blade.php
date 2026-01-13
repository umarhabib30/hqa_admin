@extends('layouts.layout')
@section('content')

<div>

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 text-sm text-gray-600">
                <tr>
                    <th class="p-4 text-left">Full Name</th>
                    <th class="p-4 text-left">Company</th>
                    <th class="p-4 text-left">Email</th>
                    <th class="p-4 text-left">Phone</th>
                    <th class="p-4 text-center">Sponsor Type</th>
                    <th class="p-4 text-center">Message</th>
                    <th class="p-4 text-center">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($contacts as $contact)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-medium text-gray-800">
                            {{ $contact->full_name ?? '-' }}
                        </td>

                        <td class="p-4 text-gray-700">
                            {{ $contact->company_name ?? '-' }}
                        </td>

                        <td class="p-4 text-gray-700">
                            {{ $contact->email ?? '-' }}
                        </td>

                        <td class="p-4 text-gray-700">
                            {{ $contact->phone ?? '-' }}
                        </td>

                        <td class="p-4 text-center">
                            @php
                                $type = strtolower($contact->sponsor_type ?? '');
                            @endphp

                            @if($type === 'gold')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    Gold
                                </span>
                            @elseif($type === 'silver')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    Silver
                                </span>
                            @elseif($type === 'platinum')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    Platinum
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    {{ $contact->sponsor_type ?? 'N/A' }}
                                </span>
                            @endif
                        </td>

                        <td class="p-4 text-center">
                            <span class="text-sm text-gray-600">
                                {{ \Illuminate\Support\Str::limit($contact->message ?? '-', 40) }}
                            </span>
                        </td>

                        <td class="p-4 text-center">
                            <a href="{{ route('contact-sponser.show', $contact->id) }}"
                                class="px-3 py-1 rounded border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white transition">
                                 View
                             </a>                             
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-gray-500">
                            No contacts found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


    <!-- MOBILE CARDS -->
    <div class="md:hidden space-y-4">
        @forelse($contacts as $contact)
            <div class="bg-white rounded-xl shadow-sm p-4 space-y-3">
                <div class="flex justify-between items-start gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-800">
                            {{ $contact->full_name ?? '-' }}
                        </h3>

                        <div class="text-sm text-gray-600 mt-2 space-y-1">
                            @if($contact->company_name)
                                <p><strong>Company:</strong> {{ $contact->company_name }}</p>
                            @endif
                            @if($contact->email)
                                <p><strong>Email:</strong> {{ $contact->email }}</p>
                            @endif
                            @if($contact->phone)
                                <p><strong>Phone:</strong> {{ $contact->phone }}</p>
                            @endif
                        </div>
                    </div>

                    @php
                        $type = strtolower($contact->sponsor_type ?? '');
                    @endphp

                    @if($type === 'gold')
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                            Gold
                        </span>
                    @elseif($type === 'silver')
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                            Silver
                        </span>
                    @elseif($type === 'platinum')
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            Platinum
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            {{ $contact->sponsor_type ?? 'N/A' }}
                        </span>
                    @endif
                </div>

                <div class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500 font-medium mb-1">Message</p>
                    <p>{{ $contact->message ?? '-' }}</p>
                </div>

                <div class="pt-2">
                    <a href="{{ route('contact-sponser.show', $contact->id) }}"
                       class="w-full block text-center px-4 py-2 rounded-lg border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white transition">
                        View
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-8">
                No contacts found.
            </div>
        @endforelse
    </div>

</div>

@endsection
