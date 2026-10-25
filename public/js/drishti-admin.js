/**
 * TECH ERA COMPUTER - Admin Panel Scripts
 */
document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');

    if (sidebarToggle && sidebar && backdrop) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        });

        backdrop.addEventListener('click', function () {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        });
    }

    // Confirmation modal helper for forms with data-confirm
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', function (e) {
            const message = form.getAttribute('data-confirm') || 'Are you sure you want to proceed with this action?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // Auto-detect overflowing tables on mobile and insert swipe hint
    function checkTableScrollHints() {
        if (window.innerWidth < 992) {
            document.querySelectorAll('.table-responsive').forEach(function (el) {
                if (el.scrollWidth > el.clientWidth && !el.previousElementSibling?.classList.contains('table-scroll-hint')) {
                    const hint = document.createElement('div');
                    hint.className = 'table-scroll-hint d-lg-none';
                    hint.innerHTML = '<i class="fa-solid fa-arrows-left-right me-1"></i> Swipe table horizontally';
                    el.parentNode.insertBefore(hint, el);
                }
            });
        }
    }
    checkTableScrollHints();
    window.addEventListener('resize', checkTableScrollHints);

    // Auto-dismiss alert banners after 5 seconds
    setTimeout(function () {
        document.querySelectorAll('.alert.alert-dismissible:not(.alert-important)').forEach(alert => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        });
    }, 5000);
});
