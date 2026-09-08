/* auth.js — shared across all blade auth pages */
(function () {

    /* ============ DARK MODE DETECTION ============ */
    function applyTheme() {
        var isDark = false;
        // check all possible skillnest theme keys
        for (var i = 0; i < localStorage.length; i++) {
            var k = localStorage.key(i);
            if (k && k.indexOf('skillnest-theme-') === 0) {
                if (localStorage.getItem(k) === 'dark') {
                    isDark = true;
                    break;
                }
            }
        }
        // also check generic 'theme' / 'data-theme' keys
        if (!isDark) {
            var fallback = localStorage.getItem('theme') || localStorage.getItem('data-theme');
            if (fallback === 'dark') isDark = true;
        }

        if (isDark) {
            document.documentElement.setAttribute('data-theme', 'dark');
        } else {
            document.documentElement.removeAttribute('data-theme');
        }
    }
    applyTheme();
    window.addEventListener('storage', function () { applyTheme(); });

    /* ============ GRID GENERATION (tms.vokks.in style) ============ */
    function buildGrid() {
        var grid = document.getElementById('authGrid');
        if (!grid) return;

        var cellSize = 64;
        var cols = Math.ceil(window.innerWidth / cellSize) + 1;
        var rows = Math.ceil(window.innerHeight / cellSize) + 1;
        var total = cols * rows;

        grid.innerHTML = '';

        for (var i = 0; i < total; i++) {
            var span = document.createElement('span');
            grid.appendChild(span);
        }
    }
    buildGrid();
    var resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(buildGrid, 200);
    });

    /* ============ MOUSE GLOW ============ */
    document.addEventListener('mousemove', function (e) {
        var grid = document.getElementById('authGrid');
        if (grid) {
            grid.style.setProperty('--mouse-x', e.clientX + 'px');
            grid.style.setProperty('--mouse-y', e.clientY + 'px');
        }
    });

})();
