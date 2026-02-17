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
                <input type="text" name="card_title" value="{{ old('card_title') }}"
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
                    <input type="number" name="card_price" value="{{ old('card_price') }}"
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
                    <input type="number" name="card_percentage" value="{{ old('card_percentage') }}"
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
                    <div class="flex gap-2 items-center point-row">
                        <input type="text" name="card_desc[]" value="{{ old('card_desc[]') }}"
                            placeholder="• First achievement point"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300
                                   focus:ring-2 focus:ring-[#00285E] focus:outline-none">

                        <button type="button"
                            onclick="removePoint(this)"
                            class="shrink-0 px-3 py-3 text-sm rounded-lg
                                   border border-red-500 text-red-600
                                   hover:bg-red-500 hover:text-white transition">
                            Remove
                        </button>
                    </div>
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
        const row = document.createElement('div');
        row.className = 'flex gap-2 items-center point-row';

        const input = document.createElement('input');

        input.type = 'text';
        input.name = 'card_desc[]';
        input.placeholder = '• Another achievement point';
        input.className = `
        w-full px-4 py-3 rounded-lg border border-gray-300
        focus:ring-2 focus:ring-[#00285E] focus:outline-none
    `;

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = 'Remove';
        btn.className = `
            shrink-0 px-3 py-3 text-sm rounded-lg
            border border-red-500 text-red-600
            hover:bg-red-500 hover:text-white transition
        `;
        btn.addEventListener('click', function () {
            removePoint(btn);
        });

        row.appendChild(input);
        row.appendChild(btn);
        wrapper.appendChild(row);
    }

    function removePoint(buttonEl) {
        const wrapper = document.getElementById('points-wrapper');
        const rows = wrapper.querySelectorAll('.point-row');
        const row = buttonEl.closest('.point-row');
        if (!row) return;

        // Keep at least one input row. If it's the last one, just clear its input.
        if (rows.length <= 1) {
            const input = row.querySelector('input[name="card_desc[]"]');
            if (input) input.value = '';
            return;
        }

        row.remove();
    }
</script>