(function ($) {
    "use strict";

    var of_theme = {
        window: $(window),
        document: $(document),
        body: $("body"),

        get_rtl: function () {
            return of_theme.body.hasClass("rtl");
        },

        /*
         * راه‌اندازی قابلیت‌های اصلی
         */
        document_ready: function () {
            of_theme.my_account_modal();
            of_theme.search_header();
            of_theme.canvas_menu_mobile();
            of_theme.of_sticky_header();
        },

        /*
         * پنجره ورود و ثبت‌نام
         */
        my_account_modal: function () {
            var accForm = $(".account_action");
            var maskOverlay = $(".of-site-mask");

            $("span.login_user_btn")
                .off("click.romaAccount")
                .on("click.romaAccount", function (event) {
                    event.preventDefault();

                    maskOverlay.addClass("active");
                    accForm.addClass("active");
                    accForm.find(".acc_div").hide();
                    accForm.removeClass("trbox");

                    $(".login_form").show();
                });

            $("span.singup_user_btn")
                .off("click.romaAccount")
                .on("click.romaAccount", function (event) {
                    event.preventDefault();

                    maskOverlay.addClass("active");
                    accForm.addClass("active");
                    accForm.find(".acc_div").hide();

                    $(".register_form").show();
                    accForm.addClass("trbox");
                });

            $(".of-site-mask, .close_modal")
                .off("click.romaAccount")
                .on("click.romaAccount", function () {
                    accForm.removeClass("active");
                    maskOverlay.removeClass("active");
                });
        },

        /*
         * باز و بسته کردن جست‌وجوی هدر
         */
        search_header: function () {
            $(".m_search")
                .off("click.romaSearch")
                .on("click.romaSearch", function (event) {
                    event.preventDefault();

                    var $button = $(this);
                    var $searchWrap = $(".search_wrap");
                    var $icon = $button.find("i");

                    $searchWrap.toggleClass("active");

                    if ($searchWrap.hasClass("active")) {
                        $icon
                            .removeClass("fa-search")
                            .addClass("fa-times");
                    } else {
                        $icon
                            .removeClass("fa-times")
                            .addClass("fa-search");
                    }
                });
        },

        /*
         * منوی موبایل
         */
        canvas_menu_mobile: function () {
            var $body = of_theme.body;
            var $trigger = $("#of-trigger");
            var $closeButton = $("#of-close-off-canvas");
            var $mask = $(".of-site-mask");
            var $mobileMenu = $(".mobile-menu-wrap");

            /*
             * باز و بسته شدن منو با دکمه اصلی
             */
            $trigger
                .off("click.romaMobileMenu")
                .on("click.romaMobileMenu", function (event) {
                    event.preventDefault();

                    $body.toggleClass("mobile-js-menu");

                    // قفل اسکرول باید با وضعیت منو هماهنگ باشد.
                    if ($body.hasClass("mobile-js-menu")) {
                        $body.addClass("lock-scroll");
                    } else {
                        $body.removeClass("lock-scroll");
                    }
                });

            /*
             * بستن منو
             */
            $closeButton
                .off("click.romaMobileMenu")
                .on("click.romaMobileMenu", function (event) {
                    event.preventDefault();

                    $body.removeClass("mobile-js-menu lock-scroll");
                });

            /*
             * بستن منو با کلیک روی فضای بیرون
             */
            $mask
                .off("click.romaMobileMenu")
                .on("click.romaMobileMenu", function () {
                    $body.removeClass("mobile-js-menu lock-scroll");
                });

            /*
             * اگر منوی موبایل در این صفحه وجود نداشته باشد،
             * تنظیمات مربوط به آن اجرا نمی‌شوند.
             */
            if (!$mobileMenu.length) {
                return;
            }

            /*
             * جلوگیری از افزودن تکراری آیکون زیرمنو
             */
            var $subMenuLinks = $mobileMenu.find(
                "li.menu-item-has-children > a"
            );

            $subMenuLinks.each(function () {
                var $link = $(this);

                if (!$link.children(".explain-menu").length) {
                    $link.append(
                        '<i class="explain-menu fal fa-angle-left" aria-hidden="true"></i>'
                    );
                }
            });

            /*
             * باز و بسته کردن زیرمنوها
             */
            $mobileMenu
                .find(".explain-menu")
                .off("click.romaSubmenu")
                .on("click.romaSubmenu", function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    var $link = $(this).closest("a");
                    var $submenu = $link.siblings("ul").first();

                    $link.toggleClass("active");

                    if ($submenu.length) {
                        $submenu.stop(true, true).slideToggle(250);
                    }
                });

            /*
             * جلوگیری از انتشار کلیک به بیرون منو
             */
            $mobileMenu
                .off("click.romaMobileMenu")
                .on("click.romaMobileMenu", function (event) {
                    event.stopPropagation();
                });
        },

        /*
         * هدر چسبان
         */
        of_sticky_header: function () {
            var $stickyMenu = $(".menu_sticky");

            if (!$stickyMenu.length) {
                return;
            }

            function updateStickyHeader() {
                var headerHeight = $("header").first().outerHeight() || 0;
                var scrollPosition = of_theme.window.scrollTop();

                if (scrollPosition > headerHeight) {
                    $stickyMenu.addClass("my_sticky");
                } else {
                    $stickyMenu.removeClass("my_sticky");
                }
            }

            of_theme.window
                .off("scroll.romaSticky")
                .on("scroll.romaSticky", updateStickyHeader);

            // تنظیم وضعیت هدر هنگام باز شدن صفحه
            updateStickyHeader();
        }
    };

    /*
     * اجرای کدها پس از آماده شدن صفحه
     */
    $(function () {
        of_theme.document_ready();
    });

})(jQuery);