<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HQA Fundraiser Ticket - Premium Edition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap");

        body {
            font-family: "Plus Jakarta Sans", sans-serif;
            background-color: #f8fafc;
        }

        .ticket-cutout {
            position: relative;
        }

        .ticket-cutout::before,
        .ticket-cutout::after {
            content: "";
            position: absolute;
            width: 30px;
            height: 30px;
            background: #f8fafc;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }

        .ticket-cutout::before {
            left: -15px;
        }

        .ticket-cutout::after {
            right: -15px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
                padding: 0;
            }

            .max-w-4xl {
                border: none;
                shadow: none;
            }
        }
    </style>
</head>

@php
    use Carbon\Carbon;

    $eventTitle = $event['event_title'] ?? 'Fundraiser';

    $rawId = (string) ($paymentIntentId ?? '');
    $ticketNo = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $rawId), -6));
    $ticketNo = $ticketNo !== '' ? $ticketNo : '---';

    $startDate = !empty($event['event_start_date'])
        ? Carbon::parse($event['event_start_date'])->format('F j, Y')
        : 'TBA';
    $endDate = !empty($event['event_end_date'])
        ? Carbon::parse($event['event_end_date'])->format('F j, Y')
        : null;
    $dateLabel = $endDate && $endDate !== $startDate ? ($startDate . ' - ' . $endDate) : $startDate;

    $startTime = !empty($event['event_start_time']) ? Carbon::parse($event['event_start_time'])->format('g:i A') : null;
    $endTime = !empty($event['event_end_time']) ? Carbon::parse($event['event_end_time'])->format('g:i A') : null;
    $timeLabel = $startTime && $endTime ? ($startTime . ' - ' . $endTime) : ($startTime ?: ($endTime ?: 'TBA'));

    $location = $event['event_location'] ?? 'TBA';

    $name = trim(($booking['first_name'] ?? '') . ' ' . ($booking['last_name'] ?? ''));
    $name = $name !== '' ? $name : 'Guest';
    $email = $booking['email'] ?? '';

    $bookingType = (string) ($booking['type'] ?? 'seats');
    $typeLabel = $bookingType === 'full_table' ? 'FULL TABLE' : 'SEATS';

    $seatTypes = is_array($booking['seat_types'] ?? null) ? $booking['seat_types'] : [];
    $totalSeats = (int) ($booking['total_seats'] ?? 0);

    $tables = (array) ($booking['tables'] ?? []);
    $tablesLabel = count($tables) ? implode(', ', $tables) : 'N/A';

    $paidLabel = '$' . number_format((float) ($paidAmount ?? 0), 2);
@endphp

<body class="p-6 md:p-12">
    <div
        class="max-w-4xl mx-auto bg-white shadow-[0_32px_64px_-15px_rgba(0,0,0,0.1)] rounded-[2rem] overflow-hidden border border-slate-100">
        <div class="flex justify-between items-center p-10">
            <div class="space-y-1">
                <h1 class="text-4xl font-[800] text-slate-900 tracking-tight leading-none">
                    {{ $eventTitle }}
                </h1>
                <p class="text-slate-500 font-medium text-lg uppercase tracking-wider">
                    Houston Quran Academy
                </p>
            </div>
            <div class="flex flex-col items-center">
                <div
                    class="w-20 h-20 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="text-[10px] font-bold text-slate-400 mt-2 tracking-widest uppercase text-center">
                    Ticket #{{ $ticketNo }}
                </span>
            </div>
        </div>

        <div class="px-10">
            <div class="relative group h-64 overflow-hidden rounded-3xl">
                <img src="https://images.unsplash.com/photo-1541339907198-e08756ebafe3?q=80&w=2070&auto=format&fit=crop"
                    alt="Academy Campus" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-0 p-10 ticket-cutout">
            <div class="md:col-span-5 pr-0 md:pr-10 border-r-0 md:border-r border-dashed border-slate-200">
                <div class="flex flex-col items-center md:items-start">
                    <div class="p-3 bg-white rounded-2xl border border-slate-100 shadow-sm mb-8">
                        <img src="{{ $qrCodeDataUrl }}"
                            alt="Ticket QR Code" class="w-36 h-36" />
                    </div>

                    <div class="space-y-6 w-full">
                        <div class="flex items-start group">
                            <div
                                class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center mr-4 text-indigo-600 transition-colors group-hover:bg-indigo-600 group-hover:text-white">
                                📅
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">
                                    Date
                                </p>
                                <p class="font-bold text-slate-800">{{ $dateLabel }}</p>
                            </div>
                        </div>

                        <div class="flex items-start group">
                            <div
                                class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center mr-4 text-indigo-600 transition-colors group-hover:bg-indigo-600 group-hover:text-white">
                                🕒
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">
                                    Time
                                </p>
                                <p class="font-bold text-slate-800">{{ $timeLabel }}</p>
                            </div>
                        </div>

                        <div class="flex items-start group">
                            <div
                                class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center mr-4 text-indigo-600 transition-colors group-hover:bg-indigo-600 group-hover:text-white">
                                📍
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">
                                    Location
                                </p>
                                <p class="font-bold text-slate-800 text-sm">
                                    {!! nl2br(e($location)) !!}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-7 pl-0 md:pl-10 mt-10 md:mt-0">
                <div class="bg-slate-50 rounded-[2rem] p-8 border border-slate-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/5 rounded-full -mr-16 -mt-16"></div>

                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-[0.2em] mb-1">
                                Ticket Type
                            </p>
                            <h2 class="text-3xl font-black text-slate-900 italic">
                                {{ $typeLabel }}
                            </h2>
                            <p class="text-slate-500 font-medium text-sm mt-2">
                                <span class="font-bold">Tables:</span> {{ $tablesLabel }}
                                <span class="mx-2">•</span>
                                <span class="font-bold">Paid:</span> {{ $paidLabel }}
                            </p>
                        </div>
                    </div>

                    <div class="mb-10">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">
                            Primary Attendee
                        </p>
                        <p class="text-slate-900 font-extrabold text-xl">{{ $name }}</p>
                        <p class="text-slate-500 font-medium text-sm italic">
                            {{ $email }}
                        </p>
                    </div>

                    <div class="pt-8 border-t border-slate-200">
                        @if($bookingType === 'full_table')
                            <div class="text-center">
                                <p class="text-3xl font-black text-slate-900">{{ str_pad((string) $totalSeats, 2, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                    Seats (Full Table)
                                </p>
                            </div>
                        @else
                            @php
                                $filteredSeatTypes = [];
                                foreach ($seatTypes as $t => $q) {
                                    if ((int) $q > 0) $filteredSeatTypes[$t] = (int) $q;
                                }
                            @endphp

                            @if(count($filteredSeatTypes))
                                <div class="grid grid-cols-3 gap-4">
                                    @foreach($filteredSeatTypes as $t => $q)
                                        <div class="text-center">
                                            <p class="text-2xl font-black text-slate-900">{{ str_pad((string) $q, 2, '0', STR_PAD_LEFT) }}</p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                                {{ ucwords(str_replace(['_', '-'], ' ', (string) $t)) }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center">
                                    <p class="text-3xl font-black text-slate-900">{{ str_pad((string) $totalSeats, 2, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                        Total Seats
                                    </p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 p-8 text-center relative">
            <div class="absolute inset-0 opacity-10"
                style="
            background-image: radial-gradient(#ffffff 1px, transparent 1px);
            background-size: 20px 20px;
          ">
            </div>
            <div class="relative z-10">
                <p class="text-indigo-300 font-bold text-xs uppercase tracking-[0.3em] mb-2">
                    Thank you for your generous support
                </p>
                <p class="text-slate-400 text-[11px] leading-relaxed max-w-md mx-auto">
                    This ticket is required for entry. Please have the QR code ready for
                    scanning at the reception desk. All proceeds benefit the HQA
                    Education Fund.
                </p>
            </div>
        </div>
    </div>
</body>

</html>
