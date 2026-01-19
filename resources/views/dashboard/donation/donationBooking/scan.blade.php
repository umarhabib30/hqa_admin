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
        <div id="qr-reader" class="w-full" style="min-height:320px; background:#f9fafb; border:1px dashed #d1d5db; border-radius:8px;"></div>
        <div class="mt-3 text-xs text-gray-500">
            Grant camera permission. For mobile, prefer Chrome/Safari and rear camera. Camera access requires HTTPS or localhost.
        </div>
    </div>

    <div id="scan-status" class="hidden bg-blue-50 text-blue-800 px-4 py-3 rounded-lg text-sm"></div>
    <div id="scan-error" class="hidden bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm"></div>
    <button id="enable-camera"
        class="hidden px-4 py-2 text-sm font-semibold text-white bg-[#00285E] rounded-lg shadow hover:bg-[#001f49] transition">
        Enable Camera
    </button>

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

    {{-- CHECKED-IN LIST --}}
    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-gray-800">Checked-in People</h2>
            <div class="text-sm text-gray-600">
                Total: <span id="checkedin-count" class="font-semibold">{{ count($checkedIn ?? []) }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 border text-left">Time</th>
                        <th class="px-3 py-2 border text-left">Table</th>
                        <th class="px-3 py-2 border text-left">Name</th>
                        <th class="px-3 py-2 border text-left">Email</th>
                        <th class="px-3 py-2 border text-left">Type</th>
                        <th class="px-3 py-2 border text-left">Seats</th>
                    </tr>
                </thead>
                <tbody id="checkedin-body">
                    @forelse(($checkedIn ?? []) as $row)
                        <tr class="border-t" data-key="{{ ($row['payment_id'] ?? '') }}-{{ ($row['table_no'] ?? '') }}">
                            <td class="px-3 py-2 border">
                                {{ $row['checked_in_at'] ?? '' }}
                            </td>
                            <td class="px-3 py-2 border">
                                {{ $row['table_no'] ?? '' }}
                            </td>
                            <td class="px-3 py-2 border">
                                {{ ($row['first_name'] ?? '') }} {{ ($row['last_name'] ?? '') }}
                            </td>
                            <td class="px-3 py-2 border break-all">
                                {{ $row['email'] ?? '' }}
                            </td>
                            <td class="px-3 py-2 border">
                                {{ ucfirst($row['type'] ?? 'seats') }}
                            </td>
                            <td class="px-3 py-2 border">
                                {{ $row['total_seats'] ?? 0 }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-6 text-center text-gray-500">
                                No one has checked in yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="prefetch" href="https://unpkg.com/html5-qrcode@2.3.8/dist/html5-qrcode.min.js">

<script>
(function () {
  const readerId = "qr-reader";
  // Use the web check-in endpoint (same one the QR code opens)
  const apiUrl = "{{ url('/donation-booking/check-in') }}";
  const statusEl = document.getElementById('scan-status');
  const errorEl = document.getElementById('scan-error');
  const resultCard = document.getElementById('result-card');
  const detailsEl = document.getElementById('booking-details');
  const scanAgainBtn = document.getElementById('scan-again');
  const enableBtn = document.getElementById('enable-camera');
  const checkedInBody = document.getElementById('checkedin-body');
  const checkedInCount = document.getElementById('checkedin-count');

  let scanner = null;
  let starting = false;

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

  // ✅ Robust loader: tries multiple CDNs, removes failed script, no id-reuse bug.
  const loadHtml5QrCode = async () => {
    if (window.Html5Qrcode) return;

    const sources = [
      'https://unpkg.com/html5-qrcode@2.3.8/dist/html5-qrcode.min.js',
      'https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/dist/html5-qrcode.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js'
    ];

    for (const src of sources) {
      try {
        await new Promise((resolve, reject) => {
          const s = document.createElement('script');
          s.src = src;
          s.async = true;
          s.onload = resolve;
          s.onerror = () => { s.remove(); reject(new Error('Failed to load: ' + src)); };
          document.head.appendChild(s);
        });

        if (window.Html5Qrcode) return; // loaded OK
      } catch (e) {
        // try next CDN
      }
    }

    throw new Error('Could not load html5-qrcode from any CDN.');
  };

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

  const escapeHtml = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  }[c]));

  const upsertCheckedInRows = (payload) => {
    const bookings = payload.bookings || [];
    if (!checkedInBody || !checkedInCount || !bookings.length) return;

    let added = 0;
    bookings.forEach((b) => {
      const key = `${payload.payment_id || ''}-${b.table_no || ''}`;
      if (!key.trim()) return;

      // If row exists, don't duplicate
      if (checkedInBody.querySelector(`tr[data-key="${CSS.escape(key)}"]`)) return;

      const tr = document.createElement('tr');
      tr.className = 'border-t';
      tr.setAttribute('data-key', key);
      tr.innerHTML = `
        <td class="px-3 py-2 border">${escapeHtml(b.checked_in_at || '')}</td>
        <td class="px-3 py-2 border">${escapeHtml(b.table_no || '')}</td>
        <td class="px-3 py-2 border">${escapeHtml((b.first_name || '') + ' ' + (b.last_name || ''))}</td>
        <td class="px-3 py-2 border break-all">${escapeHtml(b.email || '')}</td>
        <td class="px-3 py-2 border">${escapeHtml((b.type || 'seats').toString().charAt(0).toUpperCase() + (b.type || 'seats').toString().slice(1))}</td>
        <td class="px-3 py-2 border">${escapeHtml(b.total_seats ?? 0)}</td>
      `;

      // Insert at top (after header/empty row)
      const emptyRow = checkedInBody.querySelector('tr td[colspan]');
      if (emptyRow) {
        emptyRow.parentElement.remove();
      }
      checkedInBody.insertBefore(tr, checkedInBody.firstChild);
      added += 1;
    });

    if (added > 0) {
      checkedInCount.textContent = String((Number(checkedInCount.textContent) || 0) + added);
    }
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
      upsertCheckedInRows(data.data || {});
    } catch (e) {
      hideStatus();
      showError('Network error. Please try again.');
    }
  };

  const onScanSuccess = (decodedText) => {
    if (!decodedText || !scanner) return;
    scanner.pause(true);
    sendCheckIn(decodedText);
  };

  const startScanner = async () => {
    if (scanner || starting) return;
    starting = true;

    try {
      await loadHtml5QrCode();

      scanner = new Html5Qrcode(readerId);

      await scanner.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        onScanSuccess,
        () => {}
      );

      starting = false;
    } catch (err) {
      starting = false;
      showError('Unable to start camera: ' + (err?.message || err) + '. Ensure HTTPS/localhost and camera permission.');
      enableBtn.classList.remove('hidden');
    }
  };

  const requestPermissionAndStart = async () => {
    hideError();
    showStatus('Requesting camera permission...');
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
      stream.getTracks().forEach(t => t.stop());
      hideStatus();
      enableBtn.classList.add('hidden');
      startScanner();
    } catch (err) {
      hideStatus();
      showError('Camera permission denied or unavailable: ' + (err?.message || err));
      enableBtn.classList.remove('hidden');
    }
  };

  scanAgainBtn.addEventListener('click', () => {
    resultCard.classList.add('hidden');
    hideError();
    hideStatus();
    if (scanner) scanner.resume();
  });

  enableBtn.addEventListener('click', requestPermissionAndStart);

  // Boot
  (async () => {
    try {
      await loadHtml5QrCode();
      if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        requestPermissionAndStart();
      } else {
        showError('Camera API not supported in this browser/device.');
        enableBtn.classList.remove('hidden');
      }
    } catch (e) {
      showError(e.message || 'Failed to load scanner library.');
      enableBtn.classList.remove('hidden');
    }
  })();
})();
</script>
@endpush

