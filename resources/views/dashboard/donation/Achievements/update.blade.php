@extends('layouts.layout')

@section('content')

    <div class="mx-auto">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

            <div>
                <h1 class="text-[28px] font-semibold text-gray-800">
                    Edit Achievement
                </h1>

                <div class="text-sm text-gray-500 mt-1">
                    Dashboard / Achievements / Edit
                </div>
            </div>

            <a href="{{ route('achievements.index') }}" class="px-6 py-3 rounded-lg
                              border border-gray-300
                              text-gray-600
                              hover:bg-gray-100
                              transition">
                Back
            </a>
        </div>

        <!-- CARD -->
        <div class="bg-white rounded-xl shadow-sm p-6">

            <form method="POST" action="{{ route('achievements.update', $achievement->id) }}" class="space-y-6">

                @csrf
                @method('PUT')

                <!-- MAIN TITLE -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Main Title
                    </label>
                    <input type="text" name="main_title" value="{{ $achievement->main_title }}" class="w-full px-4 py-3 rounded-lg
                                          border border-gray-300
                                          focus:ring-2 focus:ring-[#00285E]
                                          focus:outline-none">
                </div>

                <!-- MAIN DESC -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Main Description
                    </label>
                    <textarea name="main_desc" rows="3" class="w-full px-4 py-3 rounded-lg
                                       border border-gray-300
                                       focus:ring-2 focus:ring-[#00285E]
                                       focus:outline-none">{{ $achievement->main_desc }}</textarea>
                </div>

                <hr class="my-6">

                <h3 class="text-lg font-semibold text-gray-800">
                    Card Information
                </h3>

                <!-- CARD TITLE -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Card Title
                    </label>
                    <input type="text" name="card_title" value="{{ $achievement->card_title }}" class="w-full px-4 py-3 rounded-lg
                                          border border-gray-300
                                          focus:ring-2 focus:ring-[#00285E]
                                          focus:outline-none">
                </div>

                <!-- PRICE + PERCENTAGE -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Card Price
                        </label>
                        <input type="number" name="card_price" value="{{ $achievement->card_price }}" class="w-full px-4 py-3 rounded-lg
                                              border border-gray-300
                                              focus:ring-2 focus:ring-[#00285E]
                                              focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Card Percentage
                        </label>
                        <input type="number" name="card_percentage" value="{{ $achievement->card_percentage }}" class="w-full px-4 py-3 rounded-lg
                                              border border-gray-300
                                              focus:ring-2 focus:ring-[#00285E]
                                              focus:outline-none">
                    </div>

                </div>

                <!-- CARD DESC -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Card Description (one per line)
                    </label>

                    <textarea name="card_desc[]" rows="4"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00285E] focus:outline-none">
    @foreach(old('card_desc', $achievement->card_desc ?? []) as $desc){{ $desc }}
    @endforeach
        </textarea>

                    @error('card_desc')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>



                <!-- ACTION BUTTONS -->
                <div class="flex justify-end gap-4 pt-4">

                    <a href="{{ route('achievements.index') }}" class="px-6 py-3 rounded-lg
                                      border border-gray-300
                                      text-gray-600
                                      hover:bg-[#00285E]
                                      transition">
                        Cancel
                    </a>

                    <button type="submit" class="px-8 py-3 rounded-lg cursor-pointer
                                       border-2 border-[#00285E]
                                       text-[#00285E] font-semibold
                                    hover:bg-#00285E hover:text-white
                                       transition-all duration-300
                                       active:scale-95">
                        Update Achievement
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection