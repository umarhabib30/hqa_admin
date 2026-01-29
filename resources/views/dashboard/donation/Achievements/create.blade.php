@extends('layouts.layout')
@section('content')

<div class=" mx-auto">

    <!-- CARD -->
    <div class="bg-white rounded-xl shadow-sm p-6">

        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            Achievement Details
        </h2>

        <form method="POST" action="{{ route('achievements.store') }}" class="space-y-6">
            @csrf


            <!-- MAIN TITLE -->
            {{-- <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Main Title
                </label>
                <input type="text" name="main_title"
                    placeholder="e.g. Gold Donation Campaign"
                    class="w-full px-4 py-3 rounded-lg
                           border border-gray-300
                           focus:ring-2 focus:ring-[#00285E]
                           focus:outline-none">
            </div>

            <!-- MAIN DESC -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Main Description
                </label>
                <textarea name="main_desc" rows="3"
                    placeholder="Short description about achievement"
                    class="w-full px-4 py-3 rounded-lg
                           border border-gray-300
                           focus:ring-2 focus:ring-[#00285E]
                           focus:outline-none"></textarea>
            </div> --}}

            <!-- DIVIDER -->
            <hr class="my-6">

            <h3 class="text-lg font-semibold text-gray-800">
                Card Information
            </h3>

            <!-- CARD TITLE -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Card Title
                </label>
                <input type="text" name="card_title"
                    placeholder="e.g. Platinum Supporter"
                    class="w-full px-4 py-3 rounded-lg
                           border border-gray-300
                           focus:ring-2 focus:ring-[#00285E]
                           focus:outline-none">
            </div>

            <!-- PRICE + PERCENTAGE -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- CARD PRICE -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Card Price
                    </label>
                    <input type="number" name="card_price"
                        placeholder="e.g. 5000"
                        class="w-full px-4 py-3 rounded-lg
                               border border-gray-300
                               focus:ring-2 focus:ring-[#00285E]
                               focus:outline-none">
                </div>

                <!-- CARD PERCENTAGE -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Card Percentage
                    </label>
                    <input type="number" name="card_percentage"
                        placeholder="e.g. 75"
                        class="w-full px-4 py-3 rounded-lg
                               border border-gray-300
                               focus:ring-2 focus:ring-[#00285E]
                               focus:outline-none">
                </div>

            </div>

            <!-- CARD DESC -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">
                    Card Description Points
                </label>

                <div id="points-wrapper" class="space-y-3">
                    <input type="text" name="card_desc[]"
                        placeholder="• First achievement point"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300
                   focus:ring-2 focus:ring-[#00285E] focus:outline-none">
                </div>

                <button type="button"
                    onclick="addPoint()"
                    class="mt-3 px-4 py-2 text-sm rounded-lg
               border border-[#00285E] text-[#00285E]
               hover:bg-[#00285E] hover:text-white transition">
                    + Add Point
                </button>
            </div>


            <!-- ACTION BUTTONS -->
            <div class="flex justify-end gap-4 pt-4">

                <a href="{{ route('achievements.index') }}"
                    class="px-6 py-3 rounded-lg
                          border border-gray-300
                          text-gray-600
                          hover:bg-gray-100
                          transition">
                    Cancel
                </a>

                <button type="submit"
                    class="px-8 py-3 rounded-lg cursor-pointer
                           border-2 border-[#00285E]
                           text-[#00285E] font-semibold
                           hover:bg-[#00285E] hover:text-white
                           transition-all duration-300
                           active:scale-95">
                    Save Achievement
                </button>

            </div>

        </form>

    </div>

</div>

@endsection



<script>
    function addPoint() {
        const wrapper = document.getElementById('points-wrapper');
        const input = document.createElement('input');

        input.type = 'text';
        input.name = 'card_desc[]';
        input.placeholder = '• Another achievement point';
        input.className = `
        w-full px-4 py-3 rounded-lg border border-gray-300
        focus:ring-2 focus:ring-[#00285E] focus:outline-none
    `;

        wrapper.appendChild(input);
    }
</script>