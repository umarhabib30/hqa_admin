@extends('layouts.layout')

@section('content')

<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

    <!-- HEADER -->
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">
        Create Donation Event
    </h2>

    @if($errors->any())
        <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('donationBooking.store') }}" class="space-y-6">
        @csrf

        <!-- EVENT TITLE -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Event Title
            </label>
            <input type="text" name="event_title" value="{{ old('event_title') }}" placeholder="e.g. Annual Fundraising Gala" class="w-full px-4 py-3 rounded-lg
                               border border-gray-300
                               focus:ring-2 focus:ring-[#00285E]
                               focus:outline-none">
        </div>

        <!-- EVENT DESCRIPTION -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Event Description
            </label>
            <textarea name="event_desc" rows="3" placeholder="Short description about the fundraising event" class="w-full px-4 py-3 rounded-lg
                               border border-gray-300
                               focus:ring-2 focus:ring-[#00285E]
                               focus:outline-none">{{ old('event_desc') }}</textarea>
        </div>

        <!-- START & END DATE -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Event Start Date
                </label>
                <input type="date" name="event_start_date" value="{{ old('event_start_date') }}" class="w-full px-4 py-3 rounded-lg
                                   border border-gray-300
                                   focus:ring-2 focus:ring-[#00285E]
                                   focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Event End Date
                </label>
                <input type="date" name="event_end_date" value="{{ old('event_end_date') }}" class="w-full px-4 py-3 rounded-lg
                                   border border-gray-300
                                   focus:ring-2 focus:ring-[#00285E]
                                   focus:outline-none">
            </div>
        </div>

        <!-- START & END TIME -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Event Start Time
                </label>
                <input type="time" name="event_start_time" value="{{ old('event_start_time') }}" class="w-full px-4 py-3 rounded-lg
                                   border border-gray-300
                                   focus:ring-2 focus:ring-[#00285E]
                                   focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Event End Time
                </label>
                <input type="time" name="event_end_time" value="{{ old('event_end_time') }}" class="w-full px-4 py-3 rounded-lg
                                   border border-gray-300
                                   focus:ring-2 focus:ring-[#00285E]
                                   focus:outline-none">
            </div>
        </div>

        <!-- LOCATION -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Event Location
            </label>
            <input type="text" name="event_location" value="{{ old('event_location') }}" placeholder="Venue name & full address" class="w-full px-4 py-3 rounded-lg
                               border border-gray-300
                               focus:ring-2 focus:ring-[#00285E]
                               focus:outline-none">
        </div>

        <!-- CONTACT NUMBER -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Contact Number
            </label>
            <input type="text" name="contact_number" value="{{ old('contact_number') }}" placeholder="+1 281-501-4300" class="w-full px-4 py-3 rounded-lg
                               border border-gray-300
                               focus:ring-2 focus:ring-[#00285E]
                               focus:outline-none">
        </div>

        <!-- TOTAL TABLES -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Total Tables
            </label>
            <input type="number" name="total_tables" value="{{ old('total_tables') }}" placeholder="e.g. 30 (each table has 10 seats)" class="w-full px-4 py-3 rounded-lg
                               border border-gray-300
                               focus:ring-2 focus:ring-[#00285E]
                               focus:outline-none">
        </div>

        <!-- SEATS PER TABLE -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Seats Per Table
            </label>
            <input type="number" name="seats_per_table" value="{{ old('seats_per_table', 10) }}" min="1" class="w-full px-4 py-3 rounded-lg
                       border border-gray-300
                       focus:ring-2 focus:ring-[#00285E]
                       focus:outline-none">
        </div>

        {{-- FULL TABLE SETTINGS --}}
        <div class="border rounded-lg p-4 bg-gray-50">
            <label class="flex items-center gap-3">
                <input type="checkbox"
                    name="allow_full_table"
                    value="1"
                    class="w-4 h-4 text-[#00285E]"
                    {{ old('allow_full_table') ? 'checked' : '' }}>
                <span class="font-medium text-gray-700">
                    Allow Full Table Booking
                </span>
            </label>

            <div class="mt-3">
                <label class="block text-sm text-gray-600 mb-1">
                    Full Table Price
                </label>
                <input type="number"
                    name="full_table_price"
                    step="0.01"
                    placeholder="e.g. 25000"
                    value="{{ old('full_table_price') }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300
                      focus:ring-2 focus:ring-[#00285E] focus:outline-none">
            </div>
        </div>

        {{-- BABY SITTING --}}
        <div class="border rounded-lg p-4 bg-gray-50">
            <label class="flex items-center gap-3">
                <input type="checkbox"
                    name="enable_baby_sitting"
                    value="1"
                    class="w-4 h-4 text-[#00285E]"
                    {{ old('enable_baby_sitting') ? 'checked' : '' }}>
                <span class="font-medium text-gray-700">
                    Add Baby Sitting (Free)
                </span>
            </label>
            <p class="mt-1 text-xs text-gray-500">If checked, a Baby Sitting ticket category will be added at $0.</p>
        </div>


        <!-- TICKET TYPES -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-2">
                Ticket Categories & Prices
            </label>

            <div id="ticket-wrapper" class="space-y-3">
                @php
                    $oldTickets = old('ticket_types', []);
                @endphp
                @if(count($oldTickets))
                    @foreach($oldTickets as $i => $ticket)
                        <div class="flex gap-3">
                            <input type="text" name="ticket_types[{{ $i }}][name]" placeholder="Category (Adult / Youth)"
                                value="{{ $ticket['name'] ?? '' }}"
                                class="w-1/2 px-3 py-2 border rounded-lg">

                            <input type="number" name="ticket_types[{{ $i }}][price]" placeholder="Price"
                                value="{{ $ticket['price'] ?? '' }}"
                                class="w-1/2 px-3 py-2 border rounded-lg">
                        </div>
                    @endforeach
                    @php $initialIndex = count($oldTickets); @endphp
                @else
                    <div class="flex gap-3">
                        <input type="text" name="ticket_types[0][name]" placeholder="Category (Adult / Youth)"
                            class="w-1/2 px-3 py-2 border rounded-lg">

                        <input type="number" name="ticket_types[0][price]" placeholder="Price"
                            class="w-1/2 px-3 py-2 border rounded-lg">
                    </div>
                    @php $initialIndex = 1; @endphp
                @endif
            </div>

            <button type="button" onclick="addTicketType()" class="mt-3 text-sm text-[#00285E] font-semibold">
                + Add More
            </button>
        </div>



        <!-- ACTION BUTTONS -->
        <div class="flex justify-end gap-4 pt-4">

            <a href="{{ route('donationBooking.index') }}" class="px-6 py-3 rounded-lg
                              border border-gray-300
                              text-gray-600
                              hover:bg-gray-100
                              transition">
                Cancel
            </a>

            <button type="submit" class="px-8 py-3 rounded-lg cursor-pointer
                               border-2 border-[#00285E]
                               text-[#00285E] font-semibold
                               hover:bg-[#00285E] hover:text-white
                               transition-all duration-300
                               active:scale-95">
                Save Event
            </button>

        </div>

    </form>

</div>

@endsection


<script>
    let index = {{ $initialIndex ?? 1 }};

    function addTicketType() {
        const wrapper = document.getElementById('ticket-wrapper');

        wrapper.insertAdjacentHTML('beforeend', `
        <div class="flex gap-3">
            <input type="text"
                   name="ticket_types[${index}][name]"
                   placeholder="Category"
                   class="w-1/2 px-3 py-2 border rounded-lg">

            <input type="number"
                   name="ticket_types[${index}][price]"
                   placeholder="Price"
                   class="w-1/2 px-3 py-2 border rounded-lg">
        </div>
    `);

        index++;
    }
</script>