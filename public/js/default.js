initFlowbite();


const dispatchThemeEvent = function (theme) {
    window.dispatchEvent(new CustomEvent("clicks", {
        detail: {
            type: "theme",
            value: theme,
        }
    }));
    currentTheme = theme;
}

const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

// Change the icons inside the button based on previous settings
if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    themeToggleLightIcon.classList.remove('hidden');
} else {
    themeToggleDarkIcon.classList.remove('hidden');
}

var setHighlightTheme = function (theme) {
    let themeStylesheet = document.getElementById('themeStylesheet');

    if (themeStylesheet) {
        themeStylesheet.href = `https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/${theme}.min.css`;
    } else {
        themeStylesheet = document.createElement('link');
        themeStylesheet.id = 'themeStylesheet';
        themeStylesheet.rel = 'stylesheet';
        themeStylesheet.href = `https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/${theme}.min.css`;
        document.head.appendChild(themeStylesheet);
    }
}

var themeToggleBtn = document.getElementById('theme-toggle');



themeToggleBtn.addEventListener('click', function () {

    // toggle icons inside button
    themeToggleDarkIcon.classList.toggle('hidden');
    themeToggleLightIcon.classList.toggle('hidden');

    // if set via local storage previously
    if (localStorage.getItem('color-theme')) {
        if (localStorage.getItem('color-theme') === 'light') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
            setHighlightTheme('agate');
            dispatchThemeEvent('dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
            setHighlightTheme('default');
            dispatchThemeEvent('light');
        }
        // if NOT set via local storage previously
    } else {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
            setHighlightTheme('default');
            dispatchThemeEvent('light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
            setHighlightTheme('agate');
            dispatchThemeEvent('dark');
        }
    }


});
