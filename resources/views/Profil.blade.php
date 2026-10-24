@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <section class="min-h-screen bg-white py-16 px-6">
        <div class="max-w-3xl mx-auto bg-white shadow-sm rounded-3xl p-10 border border-gray-100">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Profil Saya</h1>
                <p class="text-gray-500">Perbarui informasi akun dan foto profil Anda</p>
            </div>

            @if (session('success'))
                <div class="bg-green-100 text-green-800 px-5 py-3 rounded-xl text-center mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                @csrf
                @method('PUT')

                <div class="flex flex-col items-center space-y-4">
                    <div class="relative group">
                        @if ($user->profile_photo)
                            <img src="{{ asset('storage/' . $user->profile_photo) . '?v=' . time() }}"
                                alt="{{ $user->name }}"
                                class="w-32 h-32 rounded-full object-cover shadow-md border-4 border-white group-hover:opacity-75 transition">
                        @else
                            <div
                                class="w-32 h-32 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 
                                    flex items-center justify-center text-white text-4xl font-bold shadow-md">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif

                        <label for="profile_photo"
                            class="absolute bottom-0 right-0 bg-black bg-opacity-60 text-white p-2 rounded-full cursor-pointer opacity-0 group-hover:opacity-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M9 13l3-3L20.485 1.515a2.121 2.121 0 113 3L12 15l-3 3H6v-3l3-3z" />
                            </svg>
                        </label>
                    </div>

                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="hidden">
                    @error('profile_photo')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500">Klik foto untuk mengganti </p>
                </div>

                <div class="space-y-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Informasi Pribadi</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm text-gray-600 mb-2">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-2">Nomor Telepon</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-2">Alamat</label>
                            <input type="text" name="address" value="{{ old('address', $user->address) }}"
                                class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Ubah Password</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm text-gray-600 mb-2">Password Baru</label>
                            <input type="password" name="password"
                                class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-2">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation"
                                class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div class="text-center pt-4">
                    <button type="submit"
                        class="px-10 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full font-semibold shadow-md hover:shadow-lg hover:opacity-90 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </section>

    <script>
        document.querySelectorAll('label[for="profile_photo"]').forEach(label => {
            label.addEventListener('click', () => {
                document.getElementById('profile_photo').click();
            });
        });

        // Preview image immediately after selection
        document.getElementById('profile_photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    // Find the image element and update it
                    const img = document.querySelector('img[alt="{{ $user->name }}"]');
                    if (img) {
                        img.src = event.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
