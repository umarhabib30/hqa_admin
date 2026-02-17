@extends('layouts.layout')
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

@section('content')
    <div x-data="{
        showAdd: false,
        purpose: @js(old('donation_for', '')),
        honorType: @js(old('honorType', '')),
    }" class="w-full px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-8 border-b border-gray-100 pb-6">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    Giving
                </h1>

            </div>

            <div class="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center">
                <div
                    class="bg-emerald-50 text-emerald-700 px-6 py-3 rounded-2xl border border-emerald-200 flex flex-col shadow-sm">
                    <span class="text-xs uppercase tracking-wider font-semibold opacity-70">Total Received</span>
                    <span class="text-2xl font-black">${{ number_format($donations->sum('amount'), 2) }}</span>
                </div>

                <button type="button" @click="showAdd = !showAdd"
                    class="inline-flex items-center justify-center px-6 py-3 border-2 border-[#00285E] text-[#00285E] font-bold rounded-xl hover:bg-[#00285E] hover:text-white transform active:scale-95 transition-all duration-200">
                    <svg x-show="!showAdd" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <svg x-show="showAdd" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                    <span x-text="showAdd ? 'Close Form' : 'Add Donation'"></span>
                </button>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div
                class="mb-6 rounded-xl border-l-4 border-green-500 bg-green-50 px-5 py-4 text-green-800 shadow-sm animate-fade-in-down">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border-l-4 border-red-500 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
                <div class="font-bold mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Please fix the following:
                </div>
                <ul class="list-disc pl-8 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Add Donation Form --}}
        <div class="mb-10 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden" x-show="showAdd"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0" x-cloak>
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-800">New Donation Entry</h2>
            </div>

            <form method="POST" action="{{ route('admin.donations.store') }}" class="p-6 md:p-8">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Name</label>
                        <input name="name" value="{{ old('name') }}" placeholder="John Doe"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#00285E]/10 focus:border-[#00285E] outline-none transition-all" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Email</label>
                        <input name="email" value="{{ old('email') }}" placeholder="email@example.com"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#00285E]/10 focus:border-[#00285E] outline-none transition-all" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-400">$</span>
                            <input name="amount" value="{{ old('amount') }}" type="number" step="0.01" required
                                class="w-full pl-8 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#00285E]/10 focus:border-[#00285E] outline-none transition-all" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Mode <span class="text-red-500">*</span>
                        </label>
                        <select name="donation_mode" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#00285E]/10 focus:border-[#00285E] outline-none transition-all">
                            <option value="paid_now">Cash</option>
                            <option value="pledged">Pledged</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Goal</label>
                        <select name="fund_raisa_id"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#00285E]/10 focus:border-[#00285E] outline-none transition-all">
                            <option value="">Latest goal (default)</option>
                            @foreach($goals ?? [] as $goal)
                                <option value="{{ $goal->id }}" {{ old('fund_raisa_id') == $goal->id ? 'selected' : '' }}>
                                    {{ $goal->goal_name ?? 'Goal #' . $goal->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Purpose <span class="text-red-500">*</span>
                        </label>
                        <select name="donation_for" required x-model="purpose"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#00285E]/10 focus:border-[#00285E] outline-none transition-all">
                            <option value="">Select Purpose</option>
                            <option value="Greatest Need" {{ old('donation_for') == 'Greatest Need' ? 'selected' : '' }}>Greatest Need</option>
                            <option value="Faculty/staff support" {{ old('donation_for') == 'Faculty/staff support' ? 'selected' : '' }}>Faculty/staff support</option>
                            <option value="Hafiz Scholarship" {{ old('donation_for') == 'Hafiz Scholarship' ? 'selected' : '' }}>Hafiz Scholarship</option>
                            <option value="Financial aid" {{ old('donation_for') == 'Financial aid' ? 'selected' : '' }}>Financial aid</option>
                            <option value="HQA Katy deficits" {{ old('donation_for') == 'HQA Katy deficits' ? 'selected' : '' }}>HQA Katy deficits</option>
                            <option value="HQA Richmond" {{ old('donation_for') == 'HQA Richmond' ? 'selected' : '' }}>HQA Richmond</option>
                            <option value="Other" {{ old('donation_for') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="md:col-span-2" x-show="purpose === 'Other'" x-cloak>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Other Purpose <span class="text-red-500">*</span>
                        </label>
                        <input name="otherPurpose" value="{{ old('otherPurpose') }}"
                            x-bind:required="purpose === 'Other'"
                            placeholder="Please specify"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#00285E]/10 focus:border-[#00285E] outline-none transition-all" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Honor / Memory
                        </label>
                        <select name="honorType" x-model="honorType"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#00285E]/10 focus:border-[#00285E] outline-none transition-all">
                            <option value="">— None —</option>
                            <option value="honor" {{ old('honorType') == 'honor' ? 'selected' : '' }}>In the honor of</option>
                            <option value="memory" {{ old('honorType') == 'memory' ? 'selected' : '' }}>In the memory of</option>
                        </select>
                    </div>

                    <div class="md:col-span-2" x-show="honorType !== ''" x-cloak>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Relationship to the donor <span class="text-red-500">*</span>
                        </label>
                        <input name="honorName" value="{{ old('honorName') }}"
                            x-bind:required="honorType !== ''"
                            placeholder="Relationship to the donor"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#00285E]/10 focus:border-[#00285E] outline-none transition-all" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Address Line 1 <span class="text-red-500">*</span>
                        </label>
                        <input name="address1" value="{{ old('address1') }}" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#00285E]/10 focus:border-[#00285E] outline-none transition-all" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Address Line 2</label>
                        <input name="address2" value="{{ old('address2') }}"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#00285E]/10 focus:border-[#00285E] outline-none transition-all" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">City <span
                                class="text-red-500">*</span></label>
                        <input name="city" value="{{ old('city') }}" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#00285E]/10 focus:border-[#00285E] outline-none transition-all" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">State <span
                                class="text-red-500">*</span></label>
                        <input name="state" value="{{ old('state') }}" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#00285E]/10 focus:border-[#00285E] outline-none transition-all" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Country <span
                                class="text-red-500">*</span></label>
                        <input name="country" value="{{ old('country') }}" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#00285E]/10 focus:border-[#00285E] outline-none transition-all" />
                    </div>

                    <div class="md:col-span-4 pt-4">
                        <button type="submit"
                            class="w-full md:w-auto px-10 py-4 bg-[#00285E] text-white font-bold rounded-xl shadow-lg hover:bg-[#001a3d] transition-colors">
                            Save Transaction
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Donations DataTable --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto p-4">
                <table id="donationsTable" class="display w-full text-left" style="width:100%">
                    <thead>
                        <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider font-bold">
                            <th class="px-4 py-3 border-b border-gray-200">ID</th>
                            <th class="px-4 py-3 border-b border-gray-200">Goal</th>
                            <th class="px-4 py-3 border-b border-gray-200">Donor</th>
                            <th class="px-4 py-3 border-b border-gray-200">Purpose</th>
                            <th class="px-4 py-3 border-b border-gray-200">Amount</th>
                            <th class="px-4 py-3 border-b border-gray-200">Mode</th>
                            <th class="px-4 py-3 border-b border-gray-200 text-right no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donations as $donation)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-4 py-3 font-mono text-sm text-gray-500">#{{ $donation->id }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold">
                                        {{ $donation->goal?->goal_name ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-gray-800">{{ $donation->name ?? 'Anonymous' }}</span>
                                        <span class="text-xs text-gray-500">{{ $donation->email ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 max-w-[220px]">
                                    {{ $donation->donation_for }}
                                    @if($donation->donation_for === 'Other' && !empty($donation->other_purpose))
                                        <div class="text-xs text-gray-500 mt-0.5">({{ $donation->other_purpose }})</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900">${{ number_format($donation->amount, 2) }}</td>
                                {{-- make badge for donation mode --}}
                                <td class="px-4 py-3 text-right">
                                    <span class="px-2.5 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold">
                                       @if ($donation->donation_mode === 'paid_now')
                                           Cash
                                       @elseif ($donation->donation_mode === 'pledged')
                                           Pledged
                                       @elseif ($donation->donation_mode === 'stripe')
                                           Stripe
                                       @elseif ($donation->donation_mode === 'paypal')
                                           PayPal
                                       @else
                                           Unknown
                                       @endif
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.donations.show', $donation) }}"
                                        class="inline-flex items-center px-3 py-1.5 text-sm font-bold text-[#00285E] bg-white border-2 border-[#00285E] rounded-lg hover:bg-[#00285E] hover:text-white transition-all">
                                        Details
                                        <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                                    No donations recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(function() {
            var $t = $('#donationsTable');
            if ($t.length && $t.find('tbody tr').length > 0 && !$t.find('tbody tr td[colspan]').length) {
                $t.DataTable({
                    order: [[0, 'desc']],
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                    language: { search: 'Search:', lengthMenu: 'Show _MENU_ entries', info: 'Showing _START_ to _END_ of _TOTAL_', infoEmpty: 'Showing 0 to 0 of 0', infoFiltered: '(filtered from _MAX_)', paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' }, zeroRecords: 'No matching records.' },
                    columnDefs: [{ orderable: false, targets: -1 }]
                });
            }
        });
    </script>
    @endpush
@endsection
