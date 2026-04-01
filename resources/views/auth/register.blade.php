@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-[1.2fr_1fr]">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">

        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">
            Create Account 📝
        </h2>

        <!-- Errors -->
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <ul class="text-sm">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="/register" class="space-y-4 text-black">
            @csrf

            <input 
                type="text" 
                name="name" 
                placeholder="Full Name"
                value="{{ old('name') }}"
                required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-400 outline-none"
            >

            <input 
                type="email" 
                name="email" 
                placeholder="Email Address"
                value="{{ old('email') }}"
                required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-400 outline-none"
            >

            <input 
                type="password" 
                name="password" 
                placeholder="Password"
                required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-400 outline-none"
            >

            <input 
                type="password" 
                name="password_confirmation" 
                placeholder="Confirm Password"
                required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-400 outline-none"
            >

            <button 
                type="submit"
                class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition duration-300 font-semibold"
            >
                Register
            </button>
        </form>

        <!-- Links -->
        <div class="text-center mt-6 text-sm text-gray-600">
            <p>
                Already have an account? 
                <a href="/login" class="text-green-600 font-semibold hover:underline">Login</a>
            </p>

            <p class="mt-2">
                <a href="/home" class="text-gray-500 hover:underline">← Back to Home</a>
            </p>
        </div>

    </div>
<img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8YXR0ZW5kYW5jZSUyMHN5c3RlbXxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=800&q=60"
         alt="Attendance System" class="rounded-2xl shadow-2xl w-full object-cover">    
</div>
@endsection
