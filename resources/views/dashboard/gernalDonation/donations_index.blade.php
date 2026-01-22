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
                    Total Received: ${{ number_format($donations->where('donation_mode', 'paid_now')->sum('amount'), 2) }}
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

        <div class="mb-6 bg-white rounded-xl shadow-sm p-4 md:p-6" x-show="showAdd" x-cloak>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Add Manual Donation</h2>

            <form method="POST" action="{{ route('admin.donations.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                @csrf

                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input name="name" value="{{ old('name') }}" placeholder="Optional"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('name') border-red-500 @enderror" />
                </div>

                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input name="email" value="{{ old('email') }}" placeholder="Optional"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('email') border-red-500 @enderror" />
                </div>

                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount <span class="text-red-500">*</span></label>
                    <input name="amount" value="{{ old('amount') }}" type="number" step="0.01" min="0"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('amount') border-red-500 @enderror"
                        placeholder="e.g. 50" required />
                </div>

                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mode <span class="text-red-500">*</span></label>
                    <select name="donation_mode" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('donation_mode') border-red-500 @enderror">
                        <option value="paid_now" {{ old('donation_mode', 'paid_now') === 'paid_now' ? 'selected' : '' }}>
                            Cash
                        </option>
                        <option value="pledged" {{ old('donation_mode') === 'pledged' ? 'selected' : '' }}>
                            Pledged (pay later)
                        </option>
                    </select>
                </div>

                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment ID</label>
                    <input name="payment_id" value="{{ old('payment_id') }}" placeholder="Optional / Stripe ID"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('payment_id') border-red-500 @enderror" />
                </div>

                <div class="md:col-span-5 flex justify-end">
                    <button type="submit"
                        class="px-8 py-3 border-2 border-[#00285E] text-[#00285E] rounded-lg hover:bg-[#00285E] hover:text-white transition">
                        Save Donation
                    </button>
                </div>
            </form>
        </div>

        <div class="hidden md:block bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Goal</th>
                        <th class="px-6 py-4">Donor Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Mode</th>
                        <th class="px-6 py-4">Payment ID</th>
                        <th class="px-6 py-4">Actions</th>
                        <th class="px-6 py-4 text-right">Date</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($donations as $donation)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-500">#{{ $donation->id }}</td>
                            <td class="px-6 py-4 text-gray-700">
                                @php($goal = $donation->goal)
                                {{ $goal?->goal_name ?: ($donation->fund_raisa_id ? ('Fund #' . $donation->fund_raisa_id) : '—') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $donation->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $donation->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold">
                                    ${{ number_format($donation->amount, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php($mode = $donation->donation_mode ?? 'paid_now')
                                @if ($mode === 'pledged')
                                    <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 font-semibold">
                                        Pledged
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 font-semibold">
                                        Cash
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-500">
                                    {{ $donation->payment_id ?: '-' }}
                                </code>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.donations.edit', $donation->id) }}"
                                        class="px-3 py-2 text-sm border rounded-lg hover:bg-gray-50 transition">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.donations.destroy', $donation->id) }}"
                                        class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="delete-btn px-3 py-2 text-sm border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition"
                                            data-name="{{ $donation->name ?: 'Donation #' . $donation->id }}">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                                @if (($donation->donation_mode ?? 'paid_now') === 'pledged')
                                    <div class="mt-2 text-xs text-gray-500">
                                        Later paid? <a class="text-[#00285E] underline"
                                            href="{{ route('admin.donations.edit', $donation->id) }}">Mark as paid</a>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-gray-500 text-sm">
                                {{ $donation->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">
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
                            <p class="text-xs text-gray-500 mt-1">
                                <strong>Goal:</strong>
                                @php($goal = $donation->goal)
                                {{ $goal?->goal_name ?: ($donation->fund_raisa_id ? ('Fund #' . $donation->fund_raisa_id) : '—') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                <strong>Mode:</strong> {{ ($donation->donation_mode ?? 'paid_now') === 'pledged' ? 'Pledged' : 'Cash' }}
                            </p>
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
                        PAYMENT ID: {{ $donation->payment_id ?: '-' }}
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex items-center justify-end gap-2">
                        <a href="{{ route('admin.donations.edit', $donation->id) }}"
                            class="px-3 py-2 text-xs border rounded-lg hover:bg-gray-50 transition">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.donations.destroy', $donation->id) }}"
                            class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="delete-btn px-3 py-2 text-xs border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition"
                                data-name="{{ $donation->name ?: 'Donation #' . $donation->id }}">
                                Delete
                            </button>
                        </form>
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
