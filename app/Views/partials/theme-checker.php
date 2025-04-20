<script>
    var currentTheme = 'dark';

    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        currentTheme = 'dark';
    } else {
        document.documentElement.classList.remove('dark')
        currentTheme = 'light';
    }
</script>