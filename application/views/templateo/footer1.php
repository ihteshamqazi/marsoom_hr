</div> <footer class="mt-auto py-4 bg-white border-top no-print" style="box-shadow: 0 -5px 20px rgba(0,0,0,0.02);">
    <div class="container text-center">
        <div class="row align-items-center">
            <div class="col-md-4 text-md-start mb-2 mb-md-0">
                <span class="fw-bold text-dark" style="font-family:'El Messiri'">شركة مرسوم لتحصيل الديون</span>
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
                <small class="text-muted">جميع الحقوق محفوظة &copy; <?= date('Y') ?></small>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="#" class="text-secondary text-decoration-none mx-2"><i class="fas fa-headset"></i> دعم فني</a>
                <a href="<?= site_url('users/logout') ?>" class="text-danger text-decoration-none mx-2"><i class="fas fa-sign-out-alt"></i> خروج</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        if($('.select2').length) { $('.select2').select2({ theme: 'bootstrap-5', dir: 'rtl' }); }
        
        // Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) });
    });
</script>
</body>
</html>