@extends('layouts.layout')
@section('content')

    <div class="` mx-auto px-3 sm:px-4 md:px-6 py-6">

        {{-- HEADER --}}
        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                Donation Booking
            </h1>

            {{-- RIGHT SIDE ACTIONS --}}
            <div class="flex flex-wrap gap-3">

                {{-- CREATE EVENT --}}
                @if(!$event)
                    <a href="{{ route('donationBooking.create') }}" class="bg-[#00285E] text-white px-4 py-2 rounded">
                        Create New Event
                    </a>
                @endif

                {{-- EDIT / DELETE (ONLY IF EVENT EXISTS) --}}
                @if($event)
                    <a href="{{ route('donationBooking.edit', $event->id) }}" class="bg-[#00285E] text-white px-4 py-2 rounded">
                        Edit Event
                    </a>

                    <form action="{{ route('donationBooking.destroy', $event->id) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" onclick="return confirm('Are you sure you want to delete this event?')"
                            class="bg-red-600 text-white px-4 py-2 rounded">
                            Delete Event
                        </button>
                    </form>
                @endif

            </div>
        </div>


        @if($event)
            @php
                $bookings = $event->table_bookings ?? [];

                $bookedSeats = collect($bookings)->flatten(1)->sum(function ($entry) use ($event) {
                    return \App\Models\DonationBooking::occupiedSeatsForBookingEntry((array) $entry, (int) $event->seats_per_table);
                });
                $remainingSeats = $event->total_seats - $bookedSeats;

                $seatTypeTotals = [];
                $babyByPayment = [];
                foreach ($bookings as $table) {
                    foreach ($table as $booking) {
                        $pid = (string) ($booking['payment_id'] ?? '');
                        if ($pid !== '') {
                            $babyByPayment[$pid] = max(
                                (int) ($babyByPayment[$pid] ?? 0),
                                \App\Models\DonationBooking::babySittingForBookingEntry((array) $booking)
                            );
                        }
                        if (($booking['type'] ?? '') === 'seats') {
                            foreach (($booking['seat_types'] ?? []) as $type => $qty) {
                                if (\App\Models\DonationBooking::isBabySittingType((string) $type)) {
                                    continue;
                                }
                                $seatTypeTotals[$type] = ($seatTypeTotals[$type] ?? 0) + $qty;
                            }
                        }
                    }
                }
                $babySittingTotal = array_sum($babyByPayment);
            @endphp

            {{-- MAIN SUMMARY CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white shadow rounded-lg p-4 text-center">
                    <h2 class="text-gray-500 text-sm">Total Tables</h2>
                    <p class="text-2xl font-bold mt-1">{{ $event->total_tables }}</p>
                </div>

                <div class="bg-white shadow rounded-lg p-4 text-center">
                    <h2 class="text-gray-500 text-sm">Seats / Table</h2>
                    <p class="text-2xl font-bold mt-1">{{ $event->seats_per_table }}</p>
                </div>

                <div class="bg-white shadow rounded-lg p-4 text-center">
                    <h2 class="text-gray-500 text-sm">Total Seats</h2>
                    <p class="text-2xl font-bold mt-1">{{ $event->total_seats }}</p>
                </div>

                <div class="bg-white shadow rounded-lg p-4 text-center">
                    <h2 class="text-gray-500 text-sm">Remaining Seats</h2>
                    <p class="text-2xl font-bold mt-1 text-green-600">
                        {{ $remainingSeats }}
                    </p>
                </div>

                <div class="bg-white shadow rounded-lg p-4 text-center">
                    <h2 class="text-gray-500 text-sm">Full Tables</h2>
                    <p class="text-2xl font-bold mt-1 text-[#00285E]">
                        {{ $event->full_tables_booked }}
                    </p>
                </div>
            </div>

            {{-- SEAT TYPE SUMMARY --}}
            @if(count($seatTypeTotals) || ($babySittingTotal ?? 0) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
                    @if(($babySittingTotal ?? 0) > 0)
                        <div class="bg-white shadow rounded-lg p-4 text-center border-t-4 border-amber-500">
                            <h2 class="text-gray-500 text-xs uppercase">
                                Baby Sitting
                            </h2>
                            <p class="text-3xl font-bold mt-1 text-amber-600">
                                {{ $babySittingTotal }}
                            </p>
                            <p class="text-xs text-gray-400">
                                Total Baby Sitting
                            </p>
                        </div>
                    @endif
                    @foreach($seatTypeTotals as $type => $total)
                        <div class="bg-white shadow rounded-lg p-4 text-center border-t-4 border-[#00285E]">
                            <h2 class="text-gray-500 text-xs uppercase">
                                {{ ucfirst($type) }}
                            </h2>
                            <p class="text-3xl font-bold mt-1 text-[#00285E]">
                                {{ $total }}
                            </p>
                            <p class="text-xs text-gray-400">
                                Total {{ ucfirst($type) }} Seats
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif



            {{-- TABLE WISE BOOKINGS --}}
            @for($i = 1; $i <= $event->total_tables; $i++)
                @php
                    $tableUsers = $bookings[$i] ?? [];
                    $tableBookedSeats = collect($tableUsers)->sum(function ($entry) use ($event) {
                        return \App\Models\DonationBooking::occupiedSeatsForBookingEntry((array) $entry, (int) $event->seats_per_table);
                    });
                    $isFullTable = $i <= $event->full_tables_booked;
                @endphp

                <div class="border rounded-lg border-[#00285E] mb-4 shadow-sm overflow-hidden">

                    {{-- TABLE HEADER --}}
                    <div class="flex flex-col sm:flex-row justify-between gap-2 px-4 py-2 font-semibold
                                        {{ $isFullTable ? 'bg-green-100' : 'bg-gray-100' }}">
                        <span>
                            Table {{ $i }}
                            @if($isFullTable)
                                <span class="ml-2 text-xs bg-green-500 text-white px-2 py-1 rounded-full">
                                    FULL TABLE
                                </span>
                            @endif
                        </span>
                        <span class="text-sm">
                            {{ $tableBookedSeats }} / {{ $event->seats_per_table }} seats
                        </span>
                    </div>

                    @if(count($tableUsers))
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm border-collapse">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="px-3 py-2 border">Name</th>
                                        <th class="px-3 py-2 border">Email</th>
                                        <th class="px-3 py-2 border">Phone</th>
                                        <th class="px-3 py-2 border">Type</th>
                                        <th class="px-3 py-2 border">Seats</th>
                                        <th class="px-3 py-2 border">Baby Sitting</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tableUsers as $user)
                                        @php
                                            $occupiedSeats = \App\Models\DonationBooking::occupiedSeatsForBookingEntry((array) $user, (int) $event->seats_per_table);
                                            $pid = (string) ($user['payment_id'] ?? '');
                                            $babyCount = $pid !== '' ? (int) ($babyByPayment[$pid] ?? 0) : \App\Models\DonationBooking::babySittingForBookingEntry((array) $user);
                                        @endphp
                                        <tr class="border-t hover:bg-gray-50">
                                            <td class="px-3 py-2">
                                                {{ $user['first_name'] }} {{ $user['last_name'] }}
                                            </td>
                                            <td class="px-3 py-2 break-all">
                                                {{ $user['email'] }}
                                            </td>
                                            <td class="px-3 py-2">
                                                {{ $user['phone'] }}
                                            </td>
                                            <td class="px-3 py-2">
                                                @if($user['type'] === 'full_table')
                                                    <span class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-xs">
                                                        Full Table
                                                    </span>
                                                @else
                                                    <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-xs">
                                                        Seats
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 space-x-1">
                                                @if($user['type'] === 'full_table')
                                                    <span class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-xs">
                                                        {{ $occupiedSeats }}
                                                    </span>
                                                @else
                                                    @foreach(($user['seat_types'] ?? []) as $type => $qty)
                                                        @if(\App\Models\DonationBooking::isBabySittingType((string) $type))
                                                            @continue
                                                        @endif
                                                        @if($qty > 0)
                                                            <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-xs">
                                                                {{ ucfirst($type) }}: {{ $qty }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td class="px-3 py-2">
                                                @if(($babyCount ?? 0) > 0)
                                                    <span class="bg-amber-100 text-amber-800 px-2 py-1 rounded-full text-xs font-semibold">
                                                        {{ $babyCount }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 text-xs">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 p-4">No bookings on this table</p>
                    @endif
                </div>
            @endfor

        @else
            <div class="text-center py-20">
                <p class="text-gray-500 text-lg">No event created yet.</p>
            </div>
        @endif

    </div>
@endsection