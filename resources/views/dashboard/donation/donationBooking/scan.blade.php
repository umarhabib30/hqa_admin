@extends('layouts.layout')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Donation Booking Check-in</h1>
            <p class="text-sm text-gray-600">Use your mobile camera to scan the QR code from the ticket PDF.</p>
        </div>
        <a href="{{ route('donationBooking.index') }}"
            class="text-sm text-[#00285E] border border-[#00285E] px-3 py-2 rounded-lg hover:bg-[#00285E] hover:text-white transition">
            Back to Booking
        </a>
    </div>

    <div class="bg-white rounded-xl shadow p-4">
        <div id="qr-reader" class="w-full"></div>
        <div class="mt-3 text-xs text-gray-500">
            Grant camera permission. For mobile, prefer Chrome/Safari and rear camera.
        </div>
    </div>

    <div id="scan-status" class="hidden bg-blue-50 text-blue-800 px-4 py-3 rounded-lg text-sm"></div>
    <div id="scan-error" class="hidden bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm"></div>

    <div id="result-card" class="hidden bg-white rounded-xl shadow p-4 space-y-3">
        <div class="flex items-center justify-between">
            <div class="text-lg font-semibold text-gray-800">Booking Details</div>
            <button id="scan-again"
                class="text-sm text-[#00285E] border border-[#00285E] px-3 py-1 rounded-lg hover:bg-[#00285E] hover:text-white transition">
                Scan Again
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-700" id="booking-details"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.10/html5-qrcode.min.js"></script>
<script>
    (function() {
        const readerId = "qr-reader";
        const apiUrl = "{{ url('/api/donationBooking/check-in') }}";
        const statusEl = document.getElementById('scan-status');
        const errorEl = document.getElementById('scan-error');
        const resultCard = document.getElementById('result-card');
        const detailsEl = document.getElementById('booking-details');
        const scanAgainBtn = document.getElementById('scan-again');

        let scanner;

        const showStatus = (msg) => {
            statusEl.textContent = msg;
            statusEl.classList.remove('hidden');
        };
        const hideStatus = () => statusEl.classList.add('hidden');
        const showError = (msg) => {
            errorEl.textContent = msg;
            errorEl.classList.remove('hidden');
        };
        const hideError = () => errorEl.classList.add('hidden');

        const renderDetails = (payload) => {
            const booking = payload.bookings?.[0] || {};
            const seatTypes = booking.seat_types || {};
            const seatSummary = Object.keys(seatTypes).length
                ? Object.entries(seatTypes).map(([t, q]) => `${t}: ${q}`).join(', ')
                : 'N/A';

            detailsEl.innerHTML = `
                <div><span class="font-semibold">Name:</span> ${booking.first_name || ''} ${booking.last_name || ''}</div>
                <div><span class="font-semibold">Email:</span> ${booking.email || ''}</div>
                <div><span class="font-semibold">Phone:</span> ${booking.phone || ''}</div>
                <div><span class="font-semibold">Booking Type:</span> ${booking.type || ''}</div>
                <div><span class="font-semibold">Tables:</span> ${booking.table_no ? booking.table_no : (booking.tables || []).join(', ')}</div>
                <div><span class="font-semibold">Seats:</span> ${booking.total_seats ?? 0}</div>
                <div><span class="font-semibold">Seat Types:</span> ${seatSummary}</div>
                <div><span class="font-semibold">Payment ID:</span> ${payload.payment_id || ''}</div>
                <div><span class="font-semibold">Checked In At:</span> ${booking.checked_in_at || 'Just now'}</div>
                <div><span class="font-semibold">Already Checked In:</span> ${booking.already_checked_in ? 'Yes' : 'No'}</div>
            `;
            resultCard.classList.remove('hidden');
        };

        const sendCheckIn = async (token) => {
            hideError();
            showStatus('Validating ticket...');
            try {
                const res = await fetch(apiUrl + '?qr_token=' + encodeURIComponent(token), {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                hideStatus();
                if (!res.ok || !data.success) {
                    showError(data.message || 'Check-in failed.');
                    return;
                }
                renderDetails(data.data || {});
            } catch (e) {
                hideStatus();
                showError('Network error. Please try again.');
            }
        };

        const onScanSuccess = (decodedText) => {
            if (!decodedText) return;
            scanner.pause(true);
            sendCheckIn(decodedText);
        };

        const initScanner = () => {
            scanner = new Html5Qrcode(readerId);
            scanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: 250 },
                onScanSuccess,
                () => {}
            ).catch(err => {
                showError('Unable to start camera: ' + err);
            });
        };

        scanAgainBtn.addEventListener('click', () => {
            resultCard.classList.add('hidden');
            hideError();
            hideStatus();
            scanner.resume();
        });

        initScanner();
    })();
</script>
@endpush
