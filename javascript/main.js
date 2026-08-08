
(function () {
    const burger = document.getElementById('burgerBtn');
    const sidebar = document.getElementById('sideNav');
    const overlay = document.getElementById('sidebarOverlay');
    const closeBtn = document.getElementById('sidebarClose');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    }
    if (burger) burger.addEventListener('click', function (e) {
        e.stopPropagation();
        openSidebar();
    });
    if (closeBtn) closeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        closeSidebar();
    });
    if (overlay) overlay.addEventListener('click', function () {
        closeSidebar();
    });
    window.addEventListener('resize', function () {
        if (window.innerWidth > 992) closeSidebar();
    });
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function (e) {
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            if (window.innerWidth <= 992) closeSidebar();
        });
    });
})();
