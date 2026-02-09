@extends('layouts.layout')

@section('content')
    <div x-data="{ showAdd: false }">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-[24px] md:text-[28px] font-semibold text-gray-800">
                    General Donations
                </h1>
                <div class="text-sm text-gray-500 mt-1">
                    Dashboard / General Donations
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-end">
                <div class="bg-green-100 text-green-700 px-6 py-3 rounded-xl border-2 border-green-500 font-bold text-lg">
                    Total Received: ${{ number_format($donations->sum('amount'), 2) }}
                </div>

                <button type="button" @click="showAdd = !showAdd"
                    class="px-6 py-3 border-2 border-[#00285E] text-[#00285E] rounded-lg hover:bg-[#00285E] hover:text-white transition">
                    <span x-text="showAdd ? 'Close' : 'Add Donation'"></span>
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                <div class="font-semibold mb-1">Please fix the following:</div>
                <ul class="list-disc pl-5 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Add Donation Form --}}
        <div class="mb-6 bg-white rounded-xl shadow-sm p-4 md:p-6" x-show="showAdd" x-cloak>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Add Manual Donation</h2>

            <form method="POST" action="{{ route('admin.donations.store') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input name="name" value="{{ old('name') }}" placeholder="Optional"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E]" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input name="email" value="{{ old('email') }}" placeholder="Optional"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E]" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Amount <span class="text-red-500">*</span>
                    </label>
                    <input name="amount" value="{{ old('amount') }}" type="number" step="0.01" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E]" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Mode <span class="text-red-500">*</span>
                    </label>
                    <select name="donation_mode" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E]">
                        <option value="paid_now">Cash</option>
                        <option value="pledged">Pledged</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Purpose <span class="text-red-500">*</span>
                    </label>
                    <select name="donation_for" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E]">
                        <option value="">Select Purpose</option>
                        <option value="Scholarship-Hafiz">Scholarship-Hafiz</option>
                        <option value="Katy campus – Maintenance Expenses">Katy campus – Maintenance Expenses</option>
                        <option value="Construction of Richmond Campus">Construction of Richmond Campus</option>
                        <option value="HQA Annual fundraiser">HQA Annual fundraiser</option>
                        <option value="HQA Semi-annual fundraiser">HQA Semi-annual fundraiser</option>
                        <option value="Other">Other</option>
                        <option value="In the memory of">In the memory of</option>
                        <option value="In the honor of">In the honor of</option>
                    </select>
                </div>

                {{-- Address --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Address Line 1 <span class="text-red-500">*</span>
                    </label>
                    <input name="address1" value="{{ old('address1') }}" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E]" />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                    <input name="address2" value="{{ old('address2') }}"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E]" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        City <span class="text-red-500">*</span>
                    </label>
                    <input name="city" value="{{ old('city') }}" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E]" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        State <span class="text-red-500">*</span>
                    </label>
                    <input name="state" value="{{ old('state') }}" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E]" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Country <span class="text-red-500">*</span>
                    </label>
                    <input name="country" value="{{ old('country') }}" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E]" />
                </div>

                <div class="md:col-span-4">
                    <button type="submit"
                        class="px-8 py-3 border-2 border-[#00285E] text-[#00285E] rounded-lg hover:bg-[#00285E] hover:text-white transition">
                        Save Donationå
                    </button>
                </div>
            </form>
        </div>

        {{-- Donations Table --}}
        <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1200px]">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Goal</th>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Purpose</th>
                        <th class="px-6 py-4">Address</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Mode</th>
                        <th class="px-6 py-4">Payment</th>
                        <th class="px-6 py-4 text-right">Date</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($donations as $donation)
                        <tr>
                            <td class="px-6 py-4">#{{ $donation->id }}</td>
                            <td class="px-6 py-4">{{ $donation->goal?->goal_name ?? '—' }}</td>
                            <td class="px-6 py-4">{{ $donation->name }}</td>
                            <td class="px-6 py-4">{{ $donation->email }}</td>
                            <td class="px-6 py-4">{{ $donation->donation_for }}</td>
                            <td class="px-6 py-4 text-sm">
                                {{ $donation->address1 }}
                                @if ($donation->address2)
                                    , {{ $donation->address2 }}
                                @endif
                                <br>
                                {{ $donation->city }}, {{ $donation->state }}, {{ $donation->country }}
                            </td>
                            <td class="px-6 py-4">${{ number_format($donation->amount, 2) }}</td>
                            <td class="px-6 py-4">{{ ucfirst($donation->donation_mode) }}</td>
                            <td class="px-6 py-4">{{ $donation->payment_id ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">{{ $donation->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                                No donations recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
