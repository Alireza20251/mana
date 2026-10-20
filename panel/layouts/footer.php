<!-- فوتر پنل مدیریت روما -->
<footer class="roma-panel-footer">

    <div class="roma-panel-footer-inner">

        <div class="roma-panel-footer-brand">

            <div class="roma-panel-footer-logo">
                ر
            </div>

            <div class="roma-panel-footer-brand-text">
                <strong>روما</strong>
                <span>رسانه / مجله</span>
            </div>

        </div>

        <div class="roma-panel-footer-copy">
            <span>
                © <?php echo date('Y'); ?> روما
            </span>

            <span class="roma-panel-footer-separator">•</span>

            <span>
                تمامی حقوق محفوظ است.
            </span>
        </div>

        <a
            href="<?php echo url('panel/index.php'); ?>"
            class="roma-panel-footer-home"
        >
            <span aria-hidden="true">⌂</span>
            بازگشت به داشبورد
        </a>

    </div>

</footer>


<!-- فایل‌های اصلی JavaScript قالب مدیریت -->
<script src="<?php echo asset('panel/vendors/bundle.js'); ?>"></script>
<script src="<?php echo asset('panel/vendors/slick/slick.min.js'); ?>"></script>
<script src="<?php echo asset('panel/assets/js/app.js'); ?>"></script>

</body>
</html>