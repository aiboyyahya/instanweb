<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="iw-settings-footer">
            <div class="iw-settings-footer__copy">
                <span class="iw-settings-footer__title">Simpan perubahan</span>
                <span class="iw-settings-footer__hint">Periksa kembali informasi toko sebelum menyimpan.</span>
            </div>

            <div class="iw-settings-footer__actions">
                <a
                    class="iw-settings-link"
                    href="{{ route('home') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" />
                    Lihat Toko
                </a>

                <x-filament::button
                    type="submit"
                    icon="heroicon-m-check"
                    wire:loading.attr="disabled"
                    wire:target="save"
                >
                    Simpan Perubahan
                </x-filament::button>
            </div>
        </div>
    </form>
</x-filament-panels::page>
