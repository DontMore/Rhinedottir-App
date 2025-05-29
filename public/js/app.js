// Mobile menu initialization
document.addEventListener('DOMContentLoaded', function() {
    const offcanvas = document.getElementById('mobileMenu');
    if (offcanvas) {
        const bsOffcanvas = new bootstrap.Offcanvas(offcanvas);
        
        offcanvas.addEventListener('shown.bs.offcanvas', function () {
            document.body.style.overflow = 'hidden';
        });
        
        offcanvas.addEventListener('hidden.bs.offcanvas', function () {
            document.body.style.overflow = '';
        });
    }
});
