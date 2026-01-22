@extends('layouts.layout')

@section('content')

<div class="mx-auto">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div>
            <h1 class="text-[28px] font-semibold text-gray-800">
                Create FundRaise Goal
            </h1>

            <div class="text-sm text-gray-500 mt-1">
                Dashboard / FundRaise Goals / Create
            </div>
        </div>

        <a href="{{ route('fundRaise.index') }}"
            class="px-6 py-3 rounded-lg
                  border border-gray-300
                  text-gray-600
                  hover:bg-gray-100
                  transition">
            Back
        </a>
    </div>

    <!-- CARD -->
    <div class="bg-white rounded-xl shadow-sm p-6">

        <form method="POST"
            action="{{ route('fundRaise.store') }}"
            class="space-y-6">

            @csrf

            <!-- GOAL NAME -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Goal Name
                </label>
                <input type="text"
                    name="goal_name"
                    value="{{ old('goal_name') }}"
                    placeholder="Optional"
                    class="w-full px-4 py-3 rounded-lg
                              border border-gray-300
                              focus:ring-2 focus:ring-[#00285E]
                              focus:outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- START DATE -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Start Date
                    </label>
                    <input type="date"
                        name="start_date"
                        value="{{ old('start_date') }}"
                        class="w-full px-4 py-3 rounded-lg
                                  border border-gray-300
                                  focus:ring-2 focus:ring-[#00285E]
                                  focus:outline-none">
                </div>

                <!-- END DATE -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        End Date
                    </label>
                    <input type="date"
                        name="end_date"
                        value="{{ old('end_date') }}"
                        class="w-full px-4 py-3 rounded-lg
                                  border border-gray-300
                                  focus:ring-2 focus:ring-[#00285E]
                                  focus:outline-none">
                </div>
            </div>

            <!-- STARTING GOAL -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Starting Goal
                </label>
                <input type="number"
                    name="starting_goal"
                    value="{{ old('starting_goal') }}"
                    placeholder="e.g. 100000"
                    class="w-full px-4 py-3 rounded-lg
                              border border-gray-300
                              focus:ring-2 focus:ring-[#00285E]
                              focus:outline-none">
            </div>

            <!-- ENDING GOAL -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Ending Goal
                </label>
                <input type="number"
                    name="ending_goal"
                    value="{{ old('ending_goal') }}"
                    placeholder="e.g. 500000"
                    class="w-full px-4 py-3 rounded-lg
                              border border-gray-300
                              focus:ring-2 focus:ring-[#00285E]
                              focus:outline-none">
            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex justify-end gap-4 pt-4">

                <a href="{{ route('fundRaise.index') }}"
                    class="px-6 py-3 rounded-lg
                          border border-gray-300
                          text-gray-600
                          hover:bg-[#00285E]
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
                    Save FundRaise Goal
                </button>

            </div>

        </form>

    </div>

</div>

@endsection