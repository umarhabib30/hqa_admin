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
    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        <div class="p-4 sm:p-5 flex items-start sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Checked-in People</h2>
                <p class="text-xs text-gray-500 mt-0.5">Live updates as you scan tickets.</p>
            </div>
            <div class="shrink-0 text-sm text-gray-600 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg">
                Total: <span id="checkedin-count" class="font-semibold">{{ count($checkedIn ?? []) }}</span>
            </div>
        </div>

        {{-- Desktop/tablet: table --}}
        <div class="hidden md:block">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr class="text-xs uppercase tracking-wide text-gray-600">
                            <th class="px-4 py-3 text-left border-t border-b">Checked-in</th>
                            <th class="px-4 py-3 text-left border-t border-b">Table</th>
                            <th class="px-4 py-3 text-left border-t border-b">Attendee</th>
                            <th class="px-4 py-3 text-left border-t border-b">Email</th>
                            <th class="px-4 py-3 text-left border-t border-b">Type</th>
                            <th class="px-4 py-3 text-left border-t border-b">Seats</th>
                            <th class="px-4 py-3 text-left border-t border-b">Baby Sitting</th>
                        </tr>
                    </thead>
                    <tbody id="checkedin-body" class="divide-y">
                        @forelse(($checkedIn ?? []) as $row)
                            <tr class="hover:bg-gray-50" data-key="{{ ($row['payment_id'] ?? '') }}-{{ ($row['table_no'] ?? '') }}">
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                    @if(!empty($row['checked_in_at']))
                                        {{ \Carbon\Carbon::parse($row['checked_in_at'])->format('d:m:Y g:i A') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-[#00285E]/10 text-[#00285E] font-semibold">
                                        {{ $row['table_no'] ?? '' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-800">
                                        {{ ($row['first_name'] ?? '') }} {{ ($row['last_name'] ?? '') }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $row['phone'] ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 break-all text-gray-700">
                                    {{ $row['email'] ?? '' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php $t = strtolower($row['type'] ?? 'seats'); @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ $t === 'full_table' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $t === 'full_table' ? 'Full Table' : 'Seats' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-gray-800 font-semibold">
                                        {{ $row['total_seats'] ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if(($row['baby_sitting'] ?? 0) > 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold">
                                            {{ $row['baby_sitting'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr id="checkedin-empty-row">
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                    No one has checked in yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile: card list --}}
        <div class="md:hidden border-t border-gray-100">
            <div id="checkedin-cards" class="divide-y">
                @forelse(($checkedIn ?? []) as $row)
                    <div class="p-4" data-key="{{ ($row['payment_id'] ?? '') }}-{{ ($row['table_no'] ?? '') }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-semibold text-gray-800">
                                    {{ ($row['first_name'] ?? '') }} {{ ($row['last_name'] ?? '') }}
                                </div>
                                <div class="text-xs text-gray-500 break-all">{{ $row['email'] ?? '' }}</div>
                            </div>
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-[#00285E]/10 text-[#00285E] font-semibold">
                                Table {{ $row['table_no'] ?? '' }}
                            </span>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2">
                            @php $t = strtolower($row['type'] ?? 'seats'); @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $t === 'full_table' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $t === 'full_table' ? 'Full Table' : 'Seats' }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold">
                                Seats: {{ $row['total_seats'] ?? 0 }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">
                                Baby Sitting: {{ $row['baby_sitting'] ?? 0 }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-50 text-gray-600 text-xs font-semibold">
                                @if(!empty($row['checked_in_at']))
                                    {{ \Carbon\Carbon::parse($row['checked_in_at'])->format('d:m:Y g:i A') }}
                                @endif
                            </span>
                        </div>
                    </div>
                @empty
                    <div id="checkedin-empty-card" class="p-6 text-center text-gray-500">
                        No one has checked in yet.
                    </div>
                @endforelse
            </div>
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
  const checkedInCards = document.getElementById('checkedin-cards');
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
    const bookings = payload.bookings || [];
    const booking = bookings?.[0] || {};
    const seatTypes = booking.seat_types || {};
    const seatSummary = Object.keys(seatTypes).length
      ? Object.entries(seatTypes).map(([t, q]) => `${t}: ${q}`).join(', ')
      : 'N/A';
    const babySitting = bookings.reduce((sum, b) => sum + (Number(b?.baby_sitting ?? 0) || 0), 0);

    detailsEl.innerHTML = `
      <div><span class="font-semibold">Name:</span> ${booking.first_name || ''} ${booking.last_name || ''}</div>
      <div><span class="font-semibold">Email:</span> ${booking.email || ''}</div>
      <div><span class="font-semibold">Phone:</span> ${booking.phone || ''}</div>
      <div><span class="font-semibold">Booking Type:</span> ${booking.type || ''}</div>
      <div><span class="font-semibold">Tables:</span> ${booking.table_no ? booking.table_no : (booking.tables || []).join(', ')}</div>
      <div><span class="font-semibold">Seats:</span> ${booking.total_seats ?? 0}</div>
      <div><span class="font-semibold">Baby Sitting:</span> ${babySitting}</div>
      <div><span class="font-semibold">Seat Types:</span> ${seatSummary}</div>
      <div><span class="font-semibold">Payment ID:</span> ${payload.payment_id || ''}</div>
      <div><span class="font-semibold">Checked In At:</span> ${booking.checked_in_at ? formatCheckIn(booking.checked_in_at) : 'Just now'}</div>
      <div><span class="font-semibold">Already Checked In:</span> ${booking.already_checked_in ? 'Yes' : 'No'}</div>
    `;
    resultCard.classList.remove('hidden');
  };

  const escapeHtml = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  }[c]));

  // Format "YYYY-MM-DD HH:MM:SS" (or ISO) into "DD:MM:YYYY H:MM AM/PM"
  const formatCheckIn = (dt) => {
    const raw = String(dt ?? '').trim();
    if (!raw) return '';

    const pad2 = (n) => String(n).padStart(2, '0');

    // Common Laravel format: "YYYY-MM-DD HH:MM:SS"
    const m = raw.match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/);
    if (m) {
      const yyyy = m[1];
      const mm = m[2];
      const dd = m[3];
      let hh24 = parseInt(m[4], 10);
      const min = m[5];

      const ampm = hh24 >= 12 ? 'PM' : 'AM';
      let hh12 = hh24 % 12;
      if (hh12 === 0) hh12 = 12;
      return `${dd}:${mm}:${yyyy} ${hh12}:${min} ${ampm}`;
    }

    // Fallback: Date parsing
    const d = new Date(raw);
    if (!isNaN(d.getTime())) {
      const dd = pad2(d.getDate());
      const mm = pad2(d.getMonth() + 1);
      const yyyy = String(d.getFullYear());
      let hh24 = d.getHours();
      const min = pad2(d.getMinutes());
      const ampm = hh24 >= 12 ? 'PM' : 'AM';
      let hh12 = hh24 % 12;
      if (hh12 === 0) hh12 = 12;
      return `${dd}:${mm}:${yyyy} ${hh12}:${min} ${ampm}`;
    }

    return raw;
  };

  const upsertCheckedInRows = (payload) => {
    const bookings = payload.bookings || [];
    if (!checkedInBody || !checkedInCount || !bookings.length) return;

    let added = 0;
    bookings.forEach((b) => {
      const key = `${payload.payment_id || ''}-${b.table_no || ''}`;
      if (!key.trim()) return;

      // If row exists, don't duplicate
      const existsInTable = checkedInBody.querySelector(`tr[data-key="${CSS.escape(key)}"]`);
      const existsInCards = checkedInCards ? checkedInCards.querySelector(`div[data-key="${CSS.escape(key)}"]`) : null;
      if (existsInTable || existsInCards) return;

      const tr = document.createElement('tr');
      tr.className = 'hover:bg-gray-50';
      tr.setAttribute('data-key', key);
      const type = (b.type || 'seats').toString();
      const isFull = type.toLowerCase() === 'full_table';
      const typeHtml = isFull
        ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Full Table</span>'
        : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Seats</span>';
      const babyHtml = (Number(b.baby_sitting ?? 0) || 0) > 0
        ? `<span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold">${escapeHtml(b.baby_sitting)}</span>`
        : `<span class="text-gray-400">—</span>`;
      tr.innerHTML = `
        <td class="px-4 py-3 whitespace-nowrap text-gray-700">${escapeHtml(formatCheckIn(b.checked_in_at || ''))}</td>
        <td class="px-4 py-3 whitespace-nowrap">
          <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-[#00285E]/10 text-[#00285E] font-semibold">${escapeHtml(b.table_no || '')}</span>
        </td>
        <td class="px-4 py-3">
          <div class="font-semibold text-gray-800">${escapeHtml((b.first_name || '') + ' ' + (b.last_name || ''))}</div>
          <div class="text-xs text-gray-500">${escapeHtml(b.phone || '')}</div>
        </td>
        <td class="px-4 py-3 break-all text-gray-700">${escapeHtml(b.email || '')}</td>
        <td class="px-4 py-3 whitespace-nowrap">${typeHtml}</td>
        <td class="px-4 py-3 whitespace-nowrap">
          <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-gray-800 font-semibold">${escapeHtml(b.total_seats ?? 0)}</span>
        </td>
        <td class="px-4 py-3 whitespace-nowrap">${babyHtml}</td>
      `;

      // Insert at top (after header/empty row)
      const emptyRow = document.getElementById('checkedin-empty-row');
      if (emptyRow) emptyRow.remove();
      checkedInBody.insertBefore(tr, checkedInBody.firstChild);

      if (checkedInCards) {
        const emptyCard = document.getElementById('checkedin-empty-card');
        if (emptyCard) emptyCard.remove();

        const card = document.createElement('div');
        card.className = 'p-4';
        card.setAttribute('data-key', key);
        card.innerHTML = `
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="font-semibold text-gray-800">${escapeHtml((b.first_name || '') + ' ' + (b.last_name || ''))}</div>
              <div class="text-xs text-gray-500 break-all">${escapeHtml(b.email || '')}</div>
            </div>
            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-[#00285E]/10 text-[#00285E] font-semibold">
              Table ${escapeHtml(b.table_no || '')}
            </span>
          </div>
          <div class="mt-3 flex flex-wrap gap-2">
            ${typeHtml}
            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold">Seats: ${escapeHtml(b.total_seats ?? 0)}</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">Baby Sitting: ${escapeHtml(b.baby_sitting ?? 0)}</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-50 text-gray-600 text-xs font-semibold">${escapeHtml(formatCheckIn(b.checked_in_at || ''))}</span>
          </div>
        `;
        checkedInCards.insertBefore(card, checkedInCards.firstChild);
      }

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

  // QR codes may contain the full check-in URL (e.g. https://site.com/donation-booking/check-in?qr_token=XXX)
  // Extract qr_token reliably; otherwise fallback to the raw scanned value.
  const extractQrToken = (scannedText) => {
    const raw = String(scannedText || '').trim();
    if (!raw) return '';

    // Try URL parsing (works for absolute URLs and also relative URLs with a base)
    try {
      const u = new URL(raw, window.location.origin);
      const t = u.searchParams.get('qr_token');
      if (t) return t.trim();
    } catch (e) {
      // ignore
    }

    // Fallback: querystring-like
    const m = raw.match(/[?&]qr_token=([^&#]+)/i);
    if (m && m[1]) {
      try { return decodeURIComponent(m[1]).trim(); } catch (e) { return m[1].trim(); }
    }

    return raw;
  };

  const onScanSuccess = (decodedText) => {
    if (!decodedText || !scanner) return;
    scanner.pause(true);
    const token = extractQrToken(decodedText);
    if (!token) {
      showError('Invalid QR code (missing token).');
      return;
    }
    sendCheckIn(token);
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

