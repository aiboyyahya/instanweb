@extends('layouts.app')

@section('title', 'Kontak ')

@section('content')
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-6 lg:px-16">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-3 tracking-tight">📞 Kontak Kami</h1>
            <p class="text-gray-500 text-base">Hubungi kami untuk informasi lebih lanjut atau bantuan.</p>
        </div>

        @if($store)
            <div class="space-y-8">
          
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Informasi Toko</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($store->logo)
                            <div class="flex justify-center md:justify-start">
                                <img src="{{ asset('storage/' . $store->logo) }}" alt="Logo" class="w-32 h-32 object-cover rounded-lg shadow-md">
                            </div>
                        @endif
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Toko</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $store->store_name ?? 'N/A' }}</p>
                            </div>
                            @if($store->address)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Alamat</label>
                                    <p class="text-gray-600">{{ $store->address }}</p>
                                </div>
                            @endif
                            @if($store->description)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                    <p class="text-gray-600">{{ $store->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Detail Kontak -->
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Detail Kontak</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($store->whatsapp)
                            <div class="flex items-center space-x-3">
                                <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                </svg>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $store->whatsapp) }}" target="_blank" class="text-blue-600 hover:underline">
                                    {{ $store->whatsapp }}
                                </a>
                            </div>
                        @endif
                     
                    </div>
                </div>

         
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Sosial Media</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @if($store->instagram)
                            <div class="flex items-center space-x-3">
                                <svg class="w-6 h-6 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C8.396 0 7.996.014 6.79.067 5.584.12 4.775.26 4.086.5c-.691.24-1.28.56-1.87.98-.59.42-1.08.9-1.47 1.49-.39.59-.71 1.18-.95 1.87-.24.69-.38 1.5-.43 2.71C.014 7.996 0 8.396 0 12.017s.014 4.02.067 5.23c.05 1.21.19 2.02.43 2.71.24.69.56 1.28.98 1.87.42.59.9 1.08 1.49 1.47.59.39 1.18.71 1.87.95.69.24 1.5.38 2.71.43 1.21.05 1.61.067 5.23.067s4.02-.014 5.23-.067c1.21-.05 2.02-.19 2.71-.43.69-.24 1.28-.56 1.87-.98.59-.42 1.08-.9 1.47-1.49.39-.59.71-1.18.95-1.87.24-.69.38-1.5.43-2.71.05-1.21.067-1.61.067-5.23s-.014-4.02-.067-5.23c-.05-1.21-.19-2.02-.43-2.71-.24-.69-.56-1.28-.98-1.87-.42-.59-.9-1.08-1.49-1.47-.59-.39-1.18-.71-1.87-.95-.69-.24-1.5-.38-2.71-.43C16.037.014 15.637 0 12.017 0zm0 2.25c3.56 0 3.98.014 5.39.078 1.36.06 2.1.29 2.59.48.64.25 1.1.55 1.58.98.48.43.88.93 1.2 1.41.32.48.58 1.04.73 1.58.15.54.23 1.27.28 2.63.05 1.41.07 1.82.07 5.39s-.014 3.98-.07 5.39c-.05 1.36-.13 2.1-.28 2.59-.15.64-.41 1.1-.73 1.58-.32.48-.72.98-1.2 1.41-.48.43-.94.73-1.58.98-.49.19-1.23.42-2.59.48-1.41.06-1.82.07-5.39.07s-3.98-.014-5.39-.07c-1.36-.06-2.1-.29-2.59-.48-.64-.25-1.1-.55-1.58-.98-.48-.43-.88-.93-1.2-1.41-.32-.48-.58-1.04-.73-1.58-.15-.54-.23-1.27-.28-2.63-.05-1.41-.07-1.82-.07-5.39s.014-3.98.07-5.39c.05-1.36.13-2.1.28-2.59.15-.64.41-1.1.73-1.58.32-.48.72-.98 1.2-1.41.48-.43.94-.73 1.58-.98.49-.19 1.23-.42 2.59-.48 1.41-.06 1.82-.07 5.39-.07zm0 7.25a4.77 4.77 0 100 9.54 4.77 4.77 0 000-9.54zm0 7.88a3.11 3.11 0 110-6.22 3.11 3.11 0 010 6.22zm5.92-8.81a1.12 1.12 0 11-2.24 0 1.12 1.12 0 012.24 0z"/>
                                </svg>
                                <a href="https://instagram.com/{{ ltrim($store->instagram, '@') }}" target="_blank" class="text-blue-600 hover:underline">
                                    {{ $store->instagram }}
                                </a>
                            </div>
                        @endif
                        @if($store->tiktok)
                            <div class="flex items-center space-x-3">
                                <svg class="w-6 h-6 text-black" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                                </svg>
                                <a href="https://www.tiktok.com/@{{ ltrim($store->tiktok, '@') }}" target="_blank" class="text-blue-600 hover:underline">
                                    {{ $store->tiktok }}
                                </a>
                            </div>
                        @endif
                        @if($store->facebook)
                            <div class="flex items-center space-x-3">
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                <a href="{{ $store->facebook }}" target="_blank" class="text-blue-600 hover:underline">
                                    Facebook
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

             
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Marketplace</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($store->shopee)
                            <div class="flex items-center space-x-3">
                                <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                               
                                    <circle cx="12" cy="12" r="10" fill="currentColor"/>
                                </svg>
                                <a href="{{ $store->shopee }}" target="_blank" class="text-blue-600 hover:underline">
                                    Shopee
                                </a>
                            </div>
                        @endif
                        @if($store->tokopedia)
                            <div class="flex items-center space-x-3">
                                <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                  
                                    <circle cx="12" cy="12" r="10" fill="currentColor"/>
                                </svg>
                                <a href="{{ $store->tokopedia }}" target="_blank" class="text-blue-600 hover:underline">
                                    Tokopedia
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center">
                <p class="text-gray-500">Informasi toko belum tersedia</p>
            </div>
        @endif
    </div>
</section>
@endsection
