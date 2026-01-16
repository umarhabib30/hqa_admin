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
</div>
@endsection

@push('scripts')
<link rel="prefetch" href="https://unpkg.com/html5-qrcode@2.3.8/dist/html5-qrcode.min.js">

<script>
(function () {
  const readerId = "qr-reader";
  const apiUrl = "{{ url('/api/donationBooking/check-in') }}";
  const statusEl = document.getElementById('scan-status');
  const errorEl = document.getElementById('scan-error');
  const resultCard = document.getElementById('result-card');
  const detailsEl = document.getElementById('booking-details');
  const scanAgainBtn = document.getElementById('scan-again');
  const enableBtn = document.getElementById('enable-camera');

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
    const bookings = payload.bookings || [];
    const first = bookings[0] || {};
    const tables = [...new Set(bookings.map(b => b.table_no).filter(Boolean))];
    const totalSeats = bookings.reduce((sum, b) => sum + (Number(b.total_seats) || 0), 0);

    // Merge seat types across all matched bookings (keys may differ per table)
    const seatTypes = bookings.reduce((acc, b) => {
      const st = b.seat_types || {};
      Object.entries(st).forEach(([t, q]) => {
        acc[t] = (acc[t] || 0) + (Number(q) || 0);
      });
      return acc;
    }, {});
    const seatSummary = Object.keys(seatTypes).length
      ? Object.entries(seatTypes).map(([t, q]) => `${t}: ${q}`).join(', ')
      : 'N/A';

    const perTableHtml = bookings.length > 1
      ? `
        <div class="md:col-span-2">
          <div class="font-semibold text-gray-800 mb-1">Per-table breakdown</div>
          <div class="space-y-1">
            ${bookings.map(b => `
              <div class="text-sm text-gray-700">
                <span class="font-semibold">Table ${b.table_no}:</span>
                Seats ${b.total_seats ?? 0}
              </div>
            `).join('')}
          </div>
        </div>
      `
      : '';

    detailsEl.innerHTML = `
      <div><span class="font-semibold">Name:</span> ${first.first_name || ''} ${first.last_name || ''}</div>
      <div><span class="font-semibold">Email:</span> ${first.email || ''}</div>
      <div><span class="font-semibold">Phone:</span> ${first.phone || ''}</div>
      <div><span class="font-semibold">Booking Type:</span> ${first.type || ''}</div>
      <div><span class="font-semibold">Tables:</span> ${tables.join(', ') || 'N/A'}</div>
      <div><span class="font-semibold">Seats:</span> ${totalSeats}</div>
      <div><span class="font-semibold">Seat Types:</span> ${seatSummary}</div>
      <div><span class="font-semibold">Payment ID:</span> ${payload.payment_id || ''}</div>
      <div><span class="font-semibold">Checked In At:</span> ${first.checked_in_at || 'Just now'}</div>
      <div><span class="font-semibold">Already Checked In:</span> ${first.already_checked_in ? 'Yes' : 'No'}</div>
      ${perTableHtml}
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

