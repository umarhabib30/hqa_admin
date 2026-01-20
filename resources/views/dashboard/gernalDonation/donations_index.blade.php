@extends('layouts.layout')

@section('content')
    <div>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
                    General Donations
                </h1>
                <div class="text-sm text-gray-500 mt-1">
                    Dashboard / General Donations
                </div>
            </div>

            <div class="bg-green-100 text-green-700 px-6 py-3 rounded-xl border-2 border-green-500 font-bold text-lg">
                Total Received: ${{ number_format($donations->sum('amount'), 2) }}
            </div>
        </div>

        <div class="hidden md:block bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Donor Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Stripe ID</th>
                        <th class="px-6 py-4 text-right">Date</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($donations as $donation)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-500">#{{ $donation->id }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $donation->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $donation->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold">
                                    ${{ number_format($donation->amount, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <code
                                    class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-500">{{ $donation->payment_id }}</code>
                            </td>
                            <td class="px-6 py-4 text-right text-gray-500 text-sm">
                                {{ $donation->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No donations recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-4">
            @forelse($donations as $donation)
                <div class="bg-white rounded-xl shadow-sm p-4 space-y-3 border-l-4 border-green-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $donation->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $donation->email }}</p>
                        </div>
                        <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-700 font-semibold">
                            ${{ number_format($donation->amount, 2) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center text-xs text-gray-500 pt-2 border-t border-gray-100">
                        <span><strong>ID:</strong> #{{ $donation->id }}</span>
                        <span>{{ $donation->created_at->format('M d, Y h:i A') }}</span>
                    </div>

                    <div class="text-[10px] text-gray-400 truncate">
                        STRIPE: {{ $donation->payment_id }}
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    No donations recorded yet.
                </div>
            @endforelse
        </div>
    </div>
@endsection
