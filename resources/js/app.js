import './bootstrap';

/**
 * InstanWeb — interaksi ringan (tanpa dependency tambahan).
 * Semua umpan balik visual hanya melengkapi state nyata dari server.
 */
const themeKey = 'iw-theme';

function applyTheme(theme, persist = true) {
    const isDark = theme === 'dark';

    document.documentElement.dataset.theme = isDark ? 'dark' : 'light';
    document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', isDark ? 'true' : 'false');

        const label = button.querySelector('[data-theme-label]');
        if (label) {
            label.textContent = isDark ? 'Mode terang' : 'Mode gelap';
        }

        button.querySelector('[data-theme-icon="sun"]')?.classList.toggle('hidden', !isDark);
        button.querySelector('[data-theme-icon="moon"]')?.classList.toggle('hidden', isDark);
    });

    if (persist) {
        try {
            localStorage.setItem(themeKey, isDark ? 'dark' : 'light');
        } catch (error) {
            /* Penyimpanan diblokir — tema tetap berlaku untuk sesi ini. */
        }
    }
}

function initTheme() {
    const current = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
    applyTheme(current, false);

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            applyTheme(next);
        });
    });
}

function initMobileMenu() {
    const button = document.querySelector('[data-mobile-menu-button]');
    const panel = document.querySelector('[data-mobile-menu]');

    if (!button || !panel) {
        return;
    }

    const close = () => {
        panel.classList.add('hidden');
        button.setAttribute('aria-expanded', 'false');
    };

    button.addEventListener('click', () => {
        const isOpen = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        panel.classList.toggle('hidden', isOpen);
    });

    panel.querySelectorAll('a').forEach((link) => link.addEventListener('click', close));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
            close();
            button.focus();
        }
    });
}

function initProfileMenu() {
    const button = document.querySelector('[data-profile-menu-button]');
    const menu = document.querySelector('[data-profile-menu]');

    if (!button || !menu) {
        return;
    }

    const close = () => {
        menu.classList.add('hidden');
        button.setAttribute('aria-expanded', 'false');
    };

    button.addEventListener('click', (event) => {
        event.stopPropagation();
        const isOpen = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        menu.classList.toggle('hidden', isOpen);
    });

    menu.addEventListener('click', (event) => event.stopPropagation());

    document.addEventListener('click', close);
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            close();
        }
    });
}

/**
 * Dialog konfirmasi global. Form dengan atribut data-confirm akan
 * menampilkan dialog native sebelum benar-benar dikirim.
 */
function initConfirmDialog() {
    const dialog = document.querySelector('[data-confirm-dialog]');

    if (!dialog || typeof dialog.showModal !== 'function') {
        return;
    }

    const message = dialog.querySelector('[data-confirm-message]');
    const accept = dialog.querySelector('[data-confirm-accept]');
    const cancel = dialog.querySelector('[data-confirm-cancel]');
    let pendingForm = null;

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true') {
                return;
            }

            event.preventDefault();
            pendingForm = form;

            if (message) {
                message.textContent = form.dataset.confirm;
            }

            dialog.showModal();
            accept?.focus();
        });
    });

    accept?.addEventListener('click', () => {
        if (!pendingForm) {
            return;
        }

        pendingForm.dataset.confirmed = 'true';
        const form = pendingForm;
        pendingForm = null;
        dialog.close();
        form.submit();
    });

    cancel?.addEventListener('click', () => {
        pendingForm = null;
        dialog.close();
    });

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            pendingForm = null;
            dialog.close();
        }
    });
}

/** Salin tautan dengan umpan balik teks, bukan alert(). */
function initCopyButtons() {
    const liveRegion = document.querySelector('[data-copy-feedback]');

    document.querySelectorAll('[data-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            const value = button.dataset.copy || window.location.href;

            try {
                await navigator.clipboard.writeText(value);
                if (liveRegion) {
                    liveRegion.textContent = 'Tautan berhasil disalin.';
                }
                const original = button.dataset.copyLabel || button.textContent;
                button.dataset.copyLabel = original;
                button.textContent = 'Tautan disalin';
                setTimeout(() => {
                    button.textContent = original;
                }, 2500);
            } catch (error) {
                window.prompt('Salin tautan berikut:', value);
            }
        });
    });
}

/** Tutup alert yang dapat ditutup (tanpa mengubah state server). */
function initDismissibleAlerts() {
    document.querySelectorAll('[data-alert-dismiss]').forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('[data-alert]')?.remove();
        });
    });
}

/**
 * Animasi scroll khusus landing page. AOS dimuat dinamis agar halaman lain
 * tidak menanggung biaya JavaScript tambahan.
 */
async function initScrollAnimations() {
    const elements = document.querySelectorAll('[data-aos]');

    if (!elements.length) {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        elements.forEach((element) => {
            element.removeAttribute('data-aos');
            element.removeAttribute('data-aos-delay');
        });

        return;
    }

    try {
        const { default: AOS } = await import('aos');

        AOS.init({
            duration: 650,
            easing: 'ease-out-cubic',
            offset: 72,
            once: true,
            disable: false,
        });

        window.addEventListener('load', () => AOS.refresh(), { once: true });
    } catch (error) {
        // AOS gagal dimuat: biarkan konten tampil tanpa animasi.
        elements.forEach((element) => element.classList.remove('aos-init'));
    }
}

/**
 * Combobox dengan pencarian untuk pilihan wilayah bertingkat.
 * Data diambil dari endpoint server; perubahan induk mereset turunannya.
 */
function initSearchableSelects() {
    const roots = Array.from(document.querySelectorAll('[data-searchable-select]'));

    if (!roots.length) {
        return;
    }

    const emitChange = (root, name, value) => {
        root.dispatchEvent(
            new CustomEvent('iw:searchable-change', {
                bubbles: true,
                detail: { name, value },
            }),
        );
    };

    roots.forEach((root) => {
        const hidden = root.querySelector('input[type="hidden"]');
        const input = root.querySelector('[data-combobox-input]');
        const panel = root.querySelector('[data-combobox-panel]');
        const list = root.querySelector('[data-combobox-list]');
        const empty = root.querySelector('[data-combobox-empty]');
        const state = root.querySelector('[data-combobox-state]');
        const clearButton = root.querySelector('[data-combobox-clear]');

        if (!hidden || !input || !panel || !list) {
            return;
        }

        const endpoint = root.dataset.endpoint;
        const parentName = root.dataset.parentField || null;
        const parentParam = root.dataset.parentParam || 'parent_id';
        const parentInput = parentName ? document.getElementById(parentName) : null;
        const parentPlaceholder =
            root.dataset.parentPlaceholder || 'Pilih wilayah induk terlebih dahulu.';

        let options = [];
        let activeIndex = -1;
        let requestController = null;
        let searchTimer = null;
        let loadedKey = null;
        let lastSelected = { value: hidden.value, label: input.value };

        const parentValue = () => (parentInput ? parentInput.value.trim() : '');

        const setExpanded = (expanded) => {
            input.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            panel.classList.toggle('hidden', !expanded);
        };

        const isOpen = () => !panel.classList.contains('hidden');

        const hideMessages = () => {
            empty?.classList.add('hidden');
            state?.classList.add('hidden');
        };

        const showState = (message) => {
            if (!state) {
                return;
            }

            state.textContent = message;
            state.classList.remove('hidden');
            empty?.classList.add('hidden');
        };

        const highlight = () => {
            Array.from(list.children).forEach((item, index) => {
                item.classList.toggle('bg-surface-soft', index === activeIndex);
            });

            if (activeIndex >= 0) {
                input.setAttribute('aria-activedescendant', `${input.id}-option-${activeIndex}`);
                list.children[activeIndex]?.scrollIntoView({ block: 'nearest' });
            } else {
                input.removeAttribute('aria-activedescendant');
            }
        };

        const render = () => {
            list.innerHTML = '';
            hideMessages();

            if (!options.length) {
                empty?.classList.remove('hidden');
                return;
            }

            options.forEach((option, index) => {
                const item = document.createElement('li');
                item.id = `${input.id}-option-${index}`;
                item.setAttribute('role', 'option');
                item.dataset.value = option.id;
                item.dataset.label = option.name;
                item.className =
                    'flex min-h-10 cursor-pointer items-center rounded-md px-3 text-sm text-fg transition hover:bg-surface-soft';
                item.textContent = option.name;

                item.addEventListener('mousedown', (event) => {
                    event.preventDefault();
                    select(option);
                });

                list.appendChild(item);
            });

            highlight();
        };

        const close = () => {
            setExpanded(false);
            activeIndex = -1;
            highlight();
        };

        const open = (search = '') => {
            if (input.disabled) {
                return;
            }

            setExpanded(true);
            load(search);
        };

        const load = (search = '') => {
            if (!endpoint) {
                return;
            }

            if (parentName && !parentValue()) {
                options = [];
                loadedKey = null;
                render();
                showState(parentPlaceholder);
                return;
            }

            const key = `${parentValue()}|${search}`;

            if (loadedKey === key) {
                render();
                return;
            }

            const url = new URL(endpoint, window.location.origin);

            if (parentName) {
                url.searchParams.set(parentParam, parentValue());
            }

            if (search !== '') {
                url.searchParams.set('search', search);
            }

            requestController?.abort();
            requestController = new AbortController();
            showState('Memuat...');

            fetch(url, {
                headers: { Accept: 'application/json' },
                signal: requestController.signal,
            })
                .then((response) => (response.ok ? response.json() : []))
                .then((payload) => {
                    options = Array.isArray(payload) ? payload : payload.data || [];
                    loadedKey = key;
                    activeIndex = -1;
                    render();
                })
                .catch((error) => {
                    if (error.name === 'AbortError') {
                        return;
                    }

                    options = [];
                    loadedKey = null;
                    render();
                    showState('Gagal memuat data. Coba lagi.');
                });
        };

        const select = (option) => {
            hidden.value = option.id;
            input.value = option.name;
            lastSelected = { value: option.id, label: option.name };
            clearButton?.classList.remove('hidden');
            emitChange(root, hidden.name, option.id);
            close();
        };

        const reset = (clearLabel = true) => {
            hidden.value = '';
            options = [];
            loadedKey = null;
            activeIndex = -1;
            lastSelected = { value: '', label: '' };
            list.innerHTML = '';

            if (clearLabel) {
                input.value = '';
            }

            clearButton?.classList.add('hidden');
            render();
        };

        const move = (direction) => {
            if (!options.length) {
                return;
            }

            activeIndex = (activeIndex + direction + options.length) % options.length;
            highlight();
        };

        input.addEventListener('focus', () => open(''));
        input.addEventListener('click', () => {
            if (!isOpen()) {
                open('');
            }
        });

        input.addEventListener('input', () => {
            if (hidden.value !== '') {
                hidden.value = '';
                lastSelected = { value: '', label: '' };
                clearButton?.classList.add('hidden');
                emitChange(root, hidden.name, '');
            }

            if (!isOpen()) {
                open(input.value.trim());
                return;
            }

            window.clearTimeout(searchTimer);
            searchTimer = window.setTimeout(() => load(input.value.trim()), 180);
        });

        input.addEventListener('blur', () => {
            window.setTimeout(() => {
                if (!isOpen()) {
                    return;
                }

                close();

                if (hidden.value === '' && lastSelected.value !== '') {
                    hidden.value = lastSelected.value;
                    input.value = lastSelected.label;
                    clearButton?.classList.remove('hidden');
                } else if (hidden.value === '' && input.value.trim() === '') {
                    input.value = '';
                }
            }, 120);
        });

        input.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown') {
                event.preventDefault();

                if (!isOpen()) {
                    open(input.value.trim());
                } else {
                    move(1);
                }
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                move(-1);
            } else if (event.key === 'Enter') {
                if (isOpen() && activeIndex >= 0 && options[activeIndex]) {
                    event.preventDefault();
                    select(options[activeIndex]);
                }
            } else if (event.key === 'Escape' || event.key === 'Tab') {
                close();
            }
        });

        clearButton?.addEventListener('mousedown', (event) => event.preventDefault());
        clearButton?.addEventListener('click', () => {
            reset();
            emitChange(root, hidden.name, '');
            input.focus();
            open('');
        });

        document.addEventListener('iw:searchable-change', (event) => {
            if (!parentName || event.detail.name !== parentName) {
                return;
            }

            reset();
            emitChange(root, hidden.name, '');
        });

        document.addEventListener('click', (event) => {
            if (!root.contains(event.target)) {
                close();
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initMobileMenu();
    initProfileMenu();
    initConfirmDialog();
    initCopyButtons();
    initDismissibleAlerts();
    initSearchableSelects();
    initScrollAnimations();
});
