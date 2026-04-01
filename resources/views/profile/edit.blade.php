@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <div class="max-w-4xl mx-auto p-6 text-black">
        <div class="bg-white rounded-3xl shadow-xl p-8 mb-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Edit Profile</h1>
                    <p class="text-slate-500 mt-2">Update your name, email, or profile picture.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="rounded-2xl bg-slate-900 px-5 py-3 text-white font-semibold hover:bg-slate-800 transition">Back to Dashboard</a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-emerald-100 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-3xl mb-6">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="bg-rose-100 border border-rose-200 text-rose-800 rounded-3xl p-4 mb-6">
                <ul class="space-y-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-xl p-8">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div>
                    <label class="text-sm font-medium text-slate-700">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="mt-2 w-full rounded-3xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-2 w-full rounded-3xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $user->whatsapp_number) }}" placeholder="e.g. +923001234567" class="mt-2 w-full rounded-3xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" />
                    <p class="mt-2 text-sm text-slate-500">Enter your WhatsApp phone number in international format (E.164), including the leading +. Example: <strong>+923001234567</strong>.</p>
                    @if (! $user->whatsapp_number)
                        <p class="mt-2 text-sm font-semibold text-rose-700">Your WhatsApp number is required for attendance and task notifications.</p>
                    @endif
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Profile Photo</label>
                    <input type="file" name="profile_photo" accept="image/*" class="mt-2 block w-full text-slate-700" />
                </div>
                @if ($user->profile_photo_path)
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm text-slate-500 mb-3">Current profile picture:</p>
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile photo" class="h-28 w-28 rounded-3xl object-cover" />
                    </div>
                @endif
                <button type="submit" class="rounded-3xl bg-indigo-600 px-6 py-3 text-white font-semibold hover:bg-indigo-700 transition">Save Profile</button>
            </form>
        </div>
    </div>
@endsection
