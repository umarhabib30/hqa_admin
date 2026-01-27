<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stripe Payments (One-time + Subscription)</title>

    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .StripeElement {
            padding: 12px;
            border-radius: 0.75rem;
            border: 1px solid rgb(229 231 235);
            background: white;
        }

        .StripeElement--focus {
            border-color: rgb(59 130 246);
            box-shadow: 0 0 0 3px rgb(59 130 246 / 0.15);
        }

        .StripeElement--invalid {
            border-color: rgb(239 68 68);
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50">
    <div class="max-w-xl mx-auto px-4 py-10">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h1 class="text-xl font-semibold text-gray-900">Payments</h1>
                <p class="text-sm text-gray-600 mt-1">Choose one-time or recurring payment.</p>
            </div>

            <!-- Tabs -->
            <div class="px-6 pt-5">
                <div class="inline-flex rounded-xl bg-gray-100 p-1">
                    <button id="tabOneTime"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition bg-white text-gray-900 shadow-sm"
                        type="button">
                        One-time
                    </button>
                    <button id="tabRecurring"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition text-gray-700 hover:text-gray-900"
                        type="button">
                        Recurring
                    </button>
                </div>
            </div>

            <!-- Messages -->
            <div class="px-6 pt-4">
                <div id="result" class="hidden rounded-xl px-4 py-3 text-sm"></div>
            </div>

            <!-- Shared Card Element -->
            <div class="px-6 pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Card</label>
                <div id="card-element" class="bg-white"></div>
                <p class="text-xs text-gray-500 mt-2">Secure card entry via Stripe.</p>
            </div>

            <!-- One-time form -->
            <form id="oneTimeForm" action="{{ route('one-time-donation') }}" method="POST" class="px-6 py-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="ot_email" type="email" required
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500"
                        placeholder="you@example.com" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name (optional)</label>
                    <input id="ot_name" type="text"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500"
                        placeholder="Your name" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (USD)</label>
                    <input id="ot_amount" type="number" min="0.5" step="0.01" value="9.99" required
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500"
                        placeholder="9.99" />
                    <p class="text-xs text-gray-500 mt-1">We’ll convert dollars to cents.</p>
                </div>

                <button id="otPayBtn" type="submit"
                    class="w-full rounded-xl bg-gray-900 text-white px-4 py-3 font-medium hover:bg-black transition disabled:opacity-60 disabled:cursor-not-allowed">
                    Pay once
                </button>

                <p class="text-xs text-gray-500">
                    One-time donation is processed on server and stored in DB.
                </p>
            </form>

            <!-- Recurring form -->
            <form id="recurringForm" class="hidden px-6 py-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="sub_email" type="email" required
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500"
                        placeholder="you@example.com" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name (optional)</label>
                    <input id="sub_name" type="text"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500"
                        placeholder="Your name" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (USD)</label>
                    <input id="sub_amount" type="number" min="0.5" step="0.01" value="9.99" required
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500"
                        placeholder="9.99" />
                    <p class="text-xs text-gray-500 mt-1">We’ll convert dollars to cents.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Interval</label>
                    <select id="sub_interval"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-900 focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500">
                        <option value="month" selected>Monthly</option>
                        <option value="year">Yearly</option>
                    </select>
                </div>

                <button id="subPayBtn" type="submit"
                    class="w-full rounded-xl bg-blue-600 text-white px-4 py-3 font-medium hover:bg-blue-700 transition disabled:opacity-60 disabled:cursor-not-allowed">
                    Subscribe & Pay
                </button>

                <p class="text-xs text-gray-500">
                    This form submits to <span class="font-mono">/subscribe</span>.
                </p>
            </form>
        </div>
    </div>

    <script>
        const stripe = Stripe(
            'pk_test_51NbHOzEeycRryfGbno3AYq0YVBqhisQh8ekZDiJq723gcjChBJvgmoQxnDMBGUZ2ix8GZWfgdctunVpbDCHmmvB800Pp9QaJY3'
        );
        const elements = stripe.elements();
        const card = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px'
                }
            }
        });
        card.mount('#card-element');

        const tabOneTime = document.getElementById('tabOneTime');
        const tabRecurring = document.getElementById('tabRecurring');
        const oneTimeForm = document.getElementById('oneTimeForm');
        const recurringForm = document.getElementById('recurringForm');

        const result = document.getElementById('result');

        function showMessage(type, text) {
            result.classList.remove('hidden');
            result.className = "rounded-xl px-4 py-3 text-sm " + (type === 'error' ?
                "bg-red-50 text-red-700 border border-red-200" :
                "bg-emerald-50 text-emerald-700 border border-emerald-200");
            result.textContent = text;
        }

        function clearMessage() {
            result.className = "hidden";
            result.textContent = "";
        }

        function dollarsToCents(value) {
            const n = Number(value);
            if (!Number.isFinite(n)) return null;
            return Math.round(n * 100);
        }

        function getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        function setActiveTab(which) {
            clearMessage();
            if (which === 'one') {
                oneTimeForm.classList.remove('hidden');
                recurringForm.classList.add('hidden');
                tabOneTime.className =
                    "px-4 py-2 rounded-lg text-sm font-medium transition bg-white text-gray-900 shadow-sm";
                tabRecurring.className =
                    "px-4 py-2 rounded-lg text-sm font-medium transition text-gray-700 hover:text-gray-900";
            } else {
                oneTimeForm.classList.add('hidden');
                recurringForm.classList.remove('hidden');
                tabRecurring.className =
                    "px-4 py-2 rounded-lg text-sm font-medium transition bg-white text-gray-900 shadow-sm";
                tabOneTime.className =
                    "px-4 py-2 rounded-lg text-sm font-medium transition text-gray-700 hover:text-gray-900";
            }
        }

        tabOneTime.addEventListener('click', () => setActiveTab('one'));
        tabRecurring.addEventListener('click', () => setActiveTab('recurring'));

        card.on('change', (event) => {
            if (event.error) showMessage('error', event.error.message);
            else clearMessage();
        });

        async function createPaymentMethod(email, name) {
            const {
                error,
                paymentMethod
            } = await stripe.createPaymentMethod({
                type: 'card',
                card,
                billing_details: {
                    email,
                    name: name || undefined
                }
            });
            if (error) throw new Error(error.message);
            if (!paymentMethod?.id) throw new Error('Could not create payment method.');
            return paymentMethod.id;
        }

        async function postJson(url, payload) {
            const resp = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify(payload)
            });

            const raw = await resp.text();
            let data = null;
            try {
                data = JSON.parse(raw);
            } catch (e) {}

            return {
                resp,
                raw,
                data
            };
        }

        // ✅ ONE-TIME donation (stores in DB)
        const otPayBtn = document.getElementById('otPayBtn');
        // ✅ ONE-TIME donation (single endpoint)
        oneTimeForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            otPayBtn.disabled = true;
            clearMessage();

            try {
                const email = document.getElementById('ot_email').value.trim();
                const name = document.getElementById('ot_name').value.trim();
                const amountDollars = document.getElementById('ot_amount').value.trim();
                const amount = dollarsToCents(amountDollars);

                if (!email) {
                    showMessage('error', 'Email is required.');
                    return;
                }
                if (!amount || amount < 50) {
                    showMessage('error', 'Amount must be at least 0.50');
                    return;
                }

                const pmId = await createPaymentMethod(email, name);
                const oneTimeUrl = oneTimeForm.getAttribute('action');

                const {
                    resp,
                    raw,
                    data
                } = await postJson(oneTimeUrl, {
                    payment_method: pmId,
                    email,
                    name: name || null,
                    amount
                });

                console.log('One-time raw response:', raw);

                if (!resp.ok) {
                    const msg =
                        data?.message ||
                        (data?.errors ? Object.values(data.errors).flat().join(' ') : null) ||
                        'Server error';
                    showMessage('error', msg);
                    return;
                }

                // ✅ already paid (no 3DS)
                if (data?.paid === true) {
                    showMessage('success', `Paid! Donation stored. PI: ${data.payment_intent_id}`);
                    return;
                }

                // ✅ needs 3DS
                if (data?.client_secret) {
                    const {
                        error: confirmError,
                        paymentIntent
                    } =
                    await stripe.confirmCardPayment(data.client_secret);

                    if (confirmError) {
                        showMessage('error', confirmError.message);
                        return;
                    }

                    if (paymentIntent.status === 'succeeded') {
                        showMessage('success',
                            `Payment succeeded! PI: ${paymentIntent.id} (DB will update via webhook)`);
                    } else {
                        showMessage('success', `Payment status: ${paymentIntent.status}.`);
                    }
                    return;
                }

                showMessage('error', data?.message || 'Unexpected response.');
            } catch (err) {
                console.error(err);
                showMessage('error', err?.message || 'Unexpected error');
            } finally {
                otPayBtn.disabled = false;
            }
        });


        // ✅ RECURRING submit (POST /subscribe)
        const subPayBtn = document.getElementById('subPayBtn');

        recurringForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            subPayBtn.disabled = true;
            clearMessage();

            try {
                const email = document.getElementById('sub_email').value.trim();
                const name = document.getElementById('sub_name').value.trim();
                const interval = document.getElementById('sub_interval').value;
                const amountDollars = document.getElementById('sub_amount').value.trim();
                const amount = dollarsToCents(amountDollars);

                if (!email) {
                    showMessage('error', 'Email is required.');
                    return;
                }
                if (!amount || amount < 50) {
                    showMessage('error', 'Amount must be at least 0.50');
                    return;
                }
                if (!['month', 'year'].includes(interval)) {
                    showMessage('error', 'Interval must be month or year.');
                    return;
                }

                const pmId = await createPaymentMethod(email, name);

                const {
                    resp,
                    raw,
                    data
                } = await postJson('/subscribe', {
                    payment_method: pmId,
                    email,
                    name: name || null,
                    amount,
                    interval
                });

                console.log('Recurring raw response:', raw);

                if (!resp.ok) {
                    const msg =
                        data?.message ||
                        (data?.errors ? Object.values(data.errors).flat().join(' ') : null) ||
                        'Server error';
                    showMessage('error', msg);
                    return;
                }

                if (data?.paid === true) {
                    showMessage('success', `Paid on server! Subscription: ${data.subscription_id}`);
                    return;
                }

                if (!data?.client_secret) {
                    showMessage('error', data?.message || 'Backend response missing client_secret.');
                    return;
                }

                const {
                    error: confirmError,
                    paymentIntent
                } = await stripe.confirmCardPayment(data.client_secret);
                if (confirmError) {
                    showMessage('error', confirmError.message);
                    return;
                }

                showMessage('success', `Paid! Subscription: ${data.subscription_id} | PI: ${paymentIntent.id}`);
            } catch (err) {
                console.error(err);
                showMessage('error', err?.message || 'Unexpected error');
            } finally {
                subPayBtn.disabled = false;
            }
        });
    </script>
</body>

</html>
