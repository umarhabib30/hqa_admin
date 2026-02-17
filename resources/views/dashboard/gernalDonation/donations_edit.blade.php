@extends('layouts.layout')

@section('content')
    <div class="w-full bg-white p-6 rounded-xl shadow">

        <div class="flex items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-semibold">Edit Donation #{{ $donation->id }}</h2>
            <a href="{{ route('admin.donations.index') }}"
                class="px-6 py-3 border rounded-lg hover:bg-gray-50 transition">
                Back
            </a>
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

            <div x-data="{
                purpose: @js(old('donation_for', $donation->donation_for)),
                honorType: @js(old('honorType', $donation->honor_type)),
            }" class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                    <input type="email" name="email" value="{{ old('email', $donation->email) }}"
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
                        <option value="paid_now" {{ old('donation_mode', $donation->donation_mode) === 'paid_now' ? 'selected' : '' }}>Cash (paid_now)</option>
                        <option value="pledged" {{ old('donation_mode', $donation->donation_mode) === 'pledged' ? 'selected' : '' }}>Pledged (pay later)</option>
                        <option value="stripe" {{ old('donation_mode', $donation->donation_mode) === 'stripe' ? 'selected' : '' }}>Stripe</option>
                    </select>
                    @error('donation_mode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment ID</label>
                    <input type="text" name="payment_id" value="{{ old('payment_id', $donation->payment_id) }}"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('payment_id') border-red-500 @enderror"
                        placeholder="Optional / Stripe ID">
                    @error('payment_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Goal</label>
                    <select name="fund_raisa_id"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('fund_raisa_id') border-red-500 @enderror">
                        <option value="">— None / Latest —</option>
                        @foreach($goals ?? [] as $goal)
                            <option value="{{ $goal->id }}" {{ old('fund_raisa_id', $donation->fund_raisa_id) == $goal->id ? 'selected' : '' }}>
                                {{ $goal->goal_name ?? 'Goal #' . $goal->id }}
                            </option>
                        @endforeach
                    </select>
                    @error('fund_raisa_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Purpose (Donation for) <span class="text-red-500">*</span></label>
                    <select name="donation_for" required x-model="purpose"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('donation_for') border-red-500 @enderror">
                        <option value="">Select Purpose</option>
                        <option value="Greatest Need">Greatest Need</option>
                        <option value="Faculty/staff support">Faculty/staff support</option>
                        <option value="Hafiz Scholarship">Hafiz Scholarship</option>
                        <option value="Financial aid">Financial aid</option>
                        <option value="HQA Katy deficits">HQA Katy deficits</option>
                        <option value="HQA Richmond">HQA Richmond</option>
                        <option value="Other">Other</option>
                    </select>
                    @error('donation_for')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-1" x-show="purpose === 'Other'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Other Purpose <span class="text-red-500">*</span></label>
                    <input type="text" name="otherPurpose" value="{{ old('otherPurpose', $donation->other_purpose) }}"
                        x-bind:required="purpose === 'Other'"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('otherPurpose') border-red-500 @enderror"
                        placeholder="Please specify">
                    @error('otherPurpose')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Honor / Memory</label>
                    <select name="honorType" x-model="honorType"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('honorType') border-red-500 @enderror">
                        <option value="">— None —</option>
                        <option value="honor">In the honor of</option>
                        <option value="memory">In the memory of</option>
                    </select>
                    @error('honorType')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2" x-show="honorType !== ''" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Person Name <span class="text-red-500">*</span></label>
                    <input type="text" name="honorName" value="{{ old('honorName', $donation->honor_name) }}"
                        x-bind:required="honorType !== ''"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('honorName') border-red-500 @enderror"
                        placeholder="Name of person">
                    @error('honorName')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class=" pt-6 mt-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Address</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1 <span class="text-red-500">*</span></label>
                        <input type="text" name="address1" value="{{ old('address1', $donation->address1) }}" required
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('address1') border-red-500 @enderror">
                        @error('address1')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                        <input type="text" name="address2" value="{{ old('address2', $donation->address2) }}"
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('address2') border-red-500 @enderror">
                        @error('address2')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">City <span class="text-red-500">*</span></label>
                        <input type="text" name="city" value="{{ old('city', $donation->city) }}" required
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('city') border-red-500 @enderror">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">State <span class="text-red-500">*</span></label>
                        <input type="text" name="state" value="{{ old('state', $donation->state) }}" required
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('state') border-red-500 @enderror">
                        @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Country <span class="text-red-500">*</span></label>
                        <input type="text" name="country" value="{{ old('country', $donation->country) }}" required
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-[#00285E] focus:border-transparent @error('country') border-red-500 @enderror">
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            @if($donation->stripe_customer_id || $donation->stripe_subscription_id)
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                    <strong>Stripe (read-only):</strong>
                    @if($donation->stripe_customer_id) Customer: <code class="text-xs">{{ $donation->stripe_customer_id }}</code> @endif
                    @if($donation->stripe_subscription_id) Subscription: <code class="text-xs">{{ $donation->stripe_subscription_id }}</code> @endif
                </div>
            @endif

            <div class="flex gap-4 pt-4 ">
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
