@extends('layouts.layout')

@section('content')

<h1 class="text-2xl font-bold mb-6">Edit Easy Join</h1>

<form method="POST"
    action="{{ route('easy-joins.update', $easyJoin) }}"
    class="bg-white rounded-xl shadow p-6 grid md:grid-cols-2 gap-6">
    @csrf @method('PUT')

    <input class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" name="first_name"
        value="{{ $easyJoin->first_name }}" required>

    <input class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" name="last_name"
        value="{{ $easyJoin->last_name }}" required>

    <input class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" type="email"
        name="email" value="{{ $easyJoin->email }}" required>

    <select class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" name="is_attending">
        <option value="yes" @selected($easyJoin->is_attending=='yes')>Yes</option>
        <option value="no" @selected($easyJoin->is_attending=='no')>No</option>
    </select>

    <select class="w-full px-4 py-3 rounded-lg
                          border border-gray-300
                          focus:ring-2 focus:ring-[#00285E]
                          focus:outline-none" name="guest_count">
        @for($i=1;$i<=10;$i++)
            <option value="{{ $i }}" @selected($easyJoin->guest_count==$i)>
            {{ $i }} Guest(s)
            </option>
            @endfor
    </select>

    <div class="md:col-span-2 bg-gray-50 p-3 rounded text-sm">
        <strong>Fee per person:</strong> {{ number_format($easyJoin->fee_per_person, 2) }} <br>
        <strong>Total fee:</strong> {{ number_format($easyJoin->total_fee, 2) }}
    </div>


    <div class="md:col-span-2 flex gap-4">
        <button class="px-6 py-2 bg-[#27A55B] text-white rounded-lg hover:bg-green-700 transition">
            Update
        </button>

        <a href="{{ route('easy-joins.index') }}"
            class="px-6 py-2 border rounded-lg hover:bg-gray-100 transition">
            Cancel
        </a>
    </div>
</form>

@endsection