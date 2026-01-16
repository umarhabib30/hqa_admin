@extends('layouts.layout')

@section('content')

    <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">

        <!-- HEADER -->
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">
            Update Donation Event
        </h2>

        <form method="POST" action="{{ route('donationBooking.update', $event->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- EVENT TITLE -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Event Title
                </label>
                <input type="text" name="event_title" value="{{ old('event_title', $event->event_title) }}" class="w-full px-4 py-3 rounded-lg
                               border border-gray-300
                               focus:ring-2 focus:ring-[#00285E]
                               focus:outline-none">
            </div>

            <!-- EVENT DESCRIPTION -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Event Description
                </label>
                <textarea name="event_desc" rows="3" class="w-full px-4 py-3 rounded-lg
                               border border-gray-300
                               focus:ring-2 focus:ring-[#00285E]
                               focus:outline-none">{{ old('event_desc', $event->event_desc) }}</textarea>
            </div>

            <!-- START & END DATE -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Event Start Date
                    </label>
                    <input type="date" name="event_start_date"
                        value="{{ old('event_start_date', $event->event_start_date) }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Event End Date
                    </label>
                    <input type="date" name="event_end_date" value="{{ old('event_end_date', $event->event_end_date) }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300">
                </div>
            </div>

            <!-- START & END TIME -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Event Start Time
                    </label>
                    <input type="time" name="event_start_time"
                        value="{{ old('event_start_time', $event->event_start_time) }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Event End Time
                    </label>
                    <input type="time" name="event_end_time" value="{{ old('event_end_time', $event->event_end_time) }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300">
                </div>
            </div>

            <!-- LOCATION -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Event Location
                </label>
                <input type="text" name="event_location" value="{{ old('event_location', $event->event_location) }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300">
            </div>

            <!-- CONTACT NUMBER -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Contact Number
                </label>
                <input type="text" name="contact_number" value="{{ old('contact_number', $event->contact_number) }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300">
            </div>

            <!-- TOTAL TABLES -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Total Tables
                </label>
                <input type="number" name="total_tables" value="{{ old('total_tables', $event->total_tables) }}" min="1"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300">
            </div>

            <!-- SEATS PER TABLE -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Seats Per Table
                </label>
                <input type="number" name="seats_per_table" value="{{ old('seats_per_table', $event->seats_per_table) }}"
                    min="1" class="w-full px-4 py-3 rounded-lg border border-gray-300">
            </div>

            {{-- FULL TABLE SETTINGS --}}
            <div class="border rounded-lg p-4 bg-gray-50">
                <label class="flex items-center gap-3">
                    <input type="checkbox"
                        name="allow_full_table"
                        value="1"
                        class="w-4 h-4 text-[#00285E]"
                        {{ old('allow_full_table', $event->allow_full_table) ? 'checked' : '' }}>
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
                        value="{{ old('full_table_price', $event->full_table_price) }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300
                          focus:ring-2 focus:ring-[#00285E] focus:outline-none">
                </div>
            </div>

            {{-- TICKET TYPES --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">
                    Ticket Categories & Prices
                </label>

                <div id="ticket-wrapper" class="space-y-3">
                    @php
                        $oldTickets = old('ticket_types');
                        $ticketsRaw = $oldTickets ?? ($event->ticket_types ?? []);
                        $tickets = collect($ticketsRaw)
                            ->reject(fn($t) => strtolower($t['name'] ?? '') === 'baby sitting')
                            ->values();
                    @endphp
                    @forelse($tickets as $i => $ticket)
                        <div class="flex gap-3">
                            <input type="text" name="ticket_types[{{ $i }}][name]" value="{{ $ticket['name'] ?? '' }}"
                                class="w-1/2 px-3 py-2 border rounded-lg">

                            <input type="number" name="ticket_types[{{ $i }}][price]" value="{{ $ticket['price'] ?? '' }}"
                                class="w-1/2 px-3 py-2 border rounded-lg">

                            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 font-bold">
                                ✕
                            </button>
                        </div>
                    @empty
                        <div class="flex gap-3">
                            <input type="text" name="ticket_types[0][name]" placeholder="Category"
                                class="w-1/2 px-3 py-2 border rounded-lg">

                            <input type="number" name="ticket_types[0][price]" placeholder="Price"
                                class="w-1/2 px-3 py-2 border rounded-lg">

                            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 font-bold">
                                ✕
                            </button>
                        </div>
                    @endforelse
                </div>

                <div class="mt-3 border rounded-lg p-3 bg-gray-50">
                    <label class="flex items-center gap-3">
                        <input type="checkbox"
                            name="enable_baby_sitting"
                            value="1"
                            class="w-4 h-4 text-[#00285E]"
                            {{ old('enable_baby_sitting', collect($ticketsRaw ?? [])->contains(fn($t) => strtolower($t['name'] ?? '') === 'baby sitting')) ? 'checked' : '' }}>
                        <span class="font-medium text-gray-700">
                            Add Baby Sitting (Free)
                        </span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">If checked, a Baby Sitting ticket category will be added at $0.</p>
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
                    Update Event
                </button>

            </div>

        </form>

    </div>

@endsection


<script>
    @php
        $countTickets = isset($tickets) ? count($tickets) : count(
            collect($event->ticket_types ?? [])->reject(fn($t) => strtolower($t['name'] ?? '') === 'baby sitting')
        );
    @endphp
    let index = {{ $countTickets }};

    function addTicketType() {
        document.getElementById('ticket-wrapper').insertAdjacentHTML('beforeend', `
        <div class="flex gap-3">
            <input type="text"
                   name="ticket_types[${index}][name]"
                   placeholder="Category"
                   class="w-1/2 px-3 py-2 border rounded-lg">

            <input type="number"
                   name="ticket_types[${index}][price]"
                   placeholder="Price"
                   class="w-1/2 px-3 py-2 border rounded-lg">

            <button type="button"
                    onclick="this.parentElement.remove()"
                    class="text-red-500 font-bold">
                ✕
            </button>
        </div>
    `);
        index++;
    }
</script>