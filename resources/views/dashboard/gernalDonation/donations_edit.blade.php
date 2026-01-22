@extends('layouts.layout')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">

        <div class="flex items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-semibold">Edit Donation</h2>
            <a href="{{ route('admin.donations.index') }}"
                class="px-6 py-3 border rounded-lg hover:bg-gray-50 transition">
                Back
            </a>
        </div>

        <div class="mb-4 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
            <strong>Goal ID:</strong> {{ $donation->fund_raisa_id ?: '—' }}
        </div>

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

        <form method="POST" action="{{ route('admin.donations.update', $donation->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name', $donation->name) }}"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="Optional">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="text" name="email" value="{{ old('email', $donation->email) }}"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('email') border-red-500 @enderror"
                        placeholder="Optional">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount', $donation->amount) }}"
                        required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('amount') border-red-500 @enderror"
                        placeholder="e.g. 50">
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mode <span class="text-red-500">*</span></label>
                    <select name="donation_mode" required
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('donation_mode') border-red-500 @enderror">
                        <option value="paid_now" {{ old('donation_mode', $donation->donation_mode ?? 'paid_now') === 'paid_now' ? 'selected' : '' }}>
                            Cash
                        </option>
                        <option value="pledged" {{ old('donation_mode', $donation->donation_mode) === 'pledged' ? 'selected' : '' }}>
                            Pledged (pay later)
                        </option>
                    </select>
                    @error('donation_mode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment ID</label>
                    <input type="text" name="payment_id" value="{{ old('payment_id', $donation->payment_id) }}"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('payment_id') border-red-500 @enderror"
                        placeholder="Optional / Stripe ID">
                    @error('payment_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-4 pt-4 border-t">
                <a href="{{ route('admin.donations.index') }}"
                    class="px-6 py-3 border rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>

                <button type="submit"
                    class="px-8 py-3 border-2 border-[#00285E] text-[#00285E] rounded-lg hover:bg-[#00285E] hover:text-white transition">
                    Update Donation
                </button>
            </div>
        </form>
    </div>
@endsection

