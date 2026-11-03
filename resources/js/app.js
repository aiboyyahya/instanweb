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

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initMobileMenu();
    initProfileMenu();
    initConfirmDialog();
    initCopyButtons();
    initDismissibleAlerts();
});
