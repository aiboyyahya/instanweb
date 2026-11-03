<script>
    (() => {
        try {
            const storefrontTheme = localStorage.getItem('iw-theme');
            const panelTheme = localStorage.getItem('theme');

            if (!panelTheme && (storefrontTheme === 'light' || storefrontTheme === 'dark')) {
                localStorage.setItem('theme', storefrontTheme);
            }

            const activeTheme = localStorage.getItem('theme');

            if (activeTheme === 'light' || activeTheme === 'dark') {
                document.documentElement.dataset.theme = activeTheme;
            }

            window.addEventListener('theme-changed', (event) => {
                if (event.detail === 'light' || event.detail === 'dark') {
                    localStorage.setItem('iw-theme', event.detail);
                    document.documentElement.dataset.theme = event.detail;
                }
            });
        } catch (error) {
            /* Penyimpanan diblokir: panel tetap memakai tema bawaan. */
        }
    })();
</script>
