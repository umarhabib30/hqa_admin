{{-- resources/views/auth/verify-otp.blade.php --}}
@extends('layouts.auth')

@section('title', 'Verify OTP')

@section('content')

    <div class="flex min-h-screen w-full">

        <!-- LEFT : OTP FORM -->
        <div class="w-full lg:w-1/2 bg-white flex items-center justify-center px-6">
            <div class="w-full max-w-md shadow-lg">

                <form method="POST" action="{{ route('otp.verify') }}" class="bg-white p-8 rounded-xl shadow"
                    onsubmit="combineOTP()">
                    @csrf

                    <h2 class="text-2xl font-bold mb-2 text-center font-serif">
                        OTP Verification
                    </h2>

                    <p class="text-gray-500 text-sm text-center mb-6 font-serif">
                        Please enter the 6-digit code sent to your email
                    </p>

                    <!-- OTP BOXES -->
                    <div class="flex justify-between gap-2 mb-6">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-serif
                                           border rounded-lg
                                           focus:outline-none focus:ring-2 focus:ring-[#00285E]"
                                oninput="moveNext(this, {{ $i }})" onkeydown="moveBack(event, this)">
                        @endfor
                    </div>

                    <!-- Hidden input (actual OTP) -->
                    <input type="hidden" name="otp" id="otp">

                    <button type="submit" class="w-full py-3 rounded-xl cursor-pointer font-serif
                                   border-2 border-[#00285E]
                                   text-[#00285E] font-semibold text-lg
                                   hover:bg-[#00285E] hover:text-white
                                   transition-all duration-300
                                   active:scale-95">
                        Verify & Login
                    </button>
                </form>

            </div>
        </div>

        <!-- RIGHT : BRANDING -->
        <div class="w-full lg:w-1/2 lg:flex hidden
                    bg-gradient-to-br from-[#BCDDFC] to-[#00285E]
                    flex items-center justify-center
                    text-white px-6">

            <div class="text-center max-w-sm">
                <img src="{{ asset('image/logo.webp') }}" alt="HQA School Logo"
                    class="mx-auto mb-6 h-80 w-auto object-contain" />

                <h2 class="text-2xl font-semibold mb-2 font-serif">
                    HQA School Dashboard
                </h2>

                <p class="text-base opacity-90 leading-relaxed font-serif">
                    Manage students, teachers, achievements, and school activities
                    from one secure platform.
                </p>
            </div>
        </div>

    </div>

    <!-- OTP SCRIPT -->
    <script>
        function moveNext(el, index) {
            if (el.value.length === 1) {
                const next = document.getElementsByClassName('otp-input')[index + 1];
                if (next) next.focus();
            }
        }

        function moveBack(e, el) {
            if (e.key === "Backspace" && el.value === "") {
                const prev = el.previousElementSibling;
                if (prev) prev.focus();
            }
        }

        function combineOTP() {
            let otp = '';
            document.querySelectorAll('.otp-input').forEach(input => {
                otp += input.value;
            });
            document.getElementById('otp').value = otp;
        }
    </script>

@endsection