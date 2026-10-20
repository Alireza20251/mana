"use strict";

/* =========================================
   Roma - Custom JavaScript
========================================= */

/*
|--------------------------------------------------------------------------
| ابزار مشترک برای اجرای امن اسلایدرها
|--------------------------------------------------------------------------
*/

function initRomaOwl(selector, options) {
    if (
        typeof jQuery === "undefined" ||
        !jQuery.fn ||
        typeof jQuery.fn.owlCarousel !== "function"
    ) {
        return;
    }

    jQuery(selector).each(function () {
        var $carousel = jQuery(this);

        // جلوگیری از راه‌اندازی دوباره اسلایدر
        if ($carousel.hasClass("owl-loaded")) {
            return;
        }

        $carousel.owlCarousel(options);
    });
}


/*
|--------------------------------------------------------------------------
| باز و بسته شدن بخش‌های collapsible
|--------------------------------------------------------------------------
*/

function initCollapsible() {
    var collapsibles = document.getElementsByClassName("collapsible");

    Array.prototype.forEach.call(collapsibles, function (element) {
        if (element.dataset.romaCollapsibleInitialized === "true") {
            return;
        }

        element.dataset.romaCollapsibleInitialized = "true";

        element.addEventListener("click", function () {
            this.classList.toggle("active");

            var content = this.nextElementSibling;

            if (!content) {
                return;
            }

            if (content.style.maxHeight) {
                content.style.maxHeight = null;
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
            }
        });
    });
}


/*
|--------------------------------------------------------------------------
| اسلایدر اصلی
|--------------------------------------------------------------------------
*/

function mainslider() {
    initRomaOwl("#owl-mainslider", {
        items: 1,
        nav: true,
        dots: false,
        autoplay: true,
        loop: true,
        mouseDrag: true,
        touchDrag: true,
        autoplayHoverPause: true,
        smartSpeed: 1500,
        autoplayTimeout: 5000
    });
}


/*
|--------------------------------------------------------------------------
| اسلایدر رویدادها - نوع ۱
|--------------------------------------------------------------------------
*/

function owlevent1() {
    initRomaOwl(".owl-event1", {
        rtl: true,
        loop: true,
        nav: true,
        autoplay: true,
        autoplayHoverPause: true,
        smartSpeed: 1500,
        autoplayTimeout: 8000,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1
            },
            410: {
                items: 2
            },
            576: {
                items: 3
            },
            769: {
                items: 4
            },
            1200: {
                items: 5
            }
        }
    });
}


/*
|--------------------------------------------------------------------------
| اسلایدر رویدادها - نوع ۴
|--------------------------------------------------------------------------
*/

function owlevent4() {
    initRomaOwl(".owl-event4", {
        rtl: true,
        loop: true,
        nav: true,
        autoplay: true,
        autoplayHoverPause: true,
        smartSpeed: 1500,
        autoplayTimeout: 8000,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1
            },
            490: {
                items: 2
            },
            769: {
                items: 3
            }
        }
    });
}


/*
|--------------------------------------------------------------------------
| اسلایدر رویدادها - نوع ۵
|--------------------------------------------------------------------------
*/

function owlevent5() {
    initRomaOwl(".owl-event5", {
        rtl: true,
        loop: true,
        nav: true,
        autoplay: true,
        autoplayHoverPause: true,
        smartSpeed: 1500,
        autoplayTimeout: 8000,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1
            },
            410: {
                items: 2
            },
            576: {
                items: 3
            },
            769: {
                items: 4
            },
            1200: {
                items: 3
            }
        }
    });
}


/*
|--------------------------------------------------------------------------
| اسلایدر نمایشی
|--------------------------------------------------------------------------
*/

function owldemo5() {
    initRomaOwl(".owl-demo5", {
        rtl: true,
        loop: true,
        dots: true,
        autoplay: true,
        autoplayHoverPause: true,
        smartSpeed: 1500,
        autoplayTimeout: 8000,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1
            },
            490: {
                items: 2
            },
            577: {
                items: 3
            },
            769: {
                items: 4
            },
            1200: {
                items: 5
            }
        }
    });
}


/*
|--------------------------------------------------------------------------
| اسلایدر مقالات برتر
|--------------------------------------------------------------------------
*/

function owltoparticle() {
    initRomaOwl("#owl-toparticle", {
        rtl: true,
        loop: true,
        nav: true,
        autoplay: true,
        smartSpeed: 2000,
        autoplayTimeout: 4000,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1
            },
            480: {
                items: 1
            },
            768: {
                items: 2
            },
            1200: {
                items: 4
            },
            1367: {
                items: 4
            }
        }
    });
}


/*
|--------------------------------------------------------------------------
| اسلایدر مطالب ویژه
|--------------------------------------------------------------------------
*/

function owlNtejari() {
    initRomaOwl("#owl-Ntejari", {
        items: 1,
        nav: false,
        dots: true,
        margin: 10,
        autoplay: true,
        loop: true,
        mouseDrag: true,
        touchDrag: true,
        autoplayHoverPause: true,
        smartSpeed: 1500,
        autoplayTimeout: 5000
    });
}


/*
|--------------------------------------------------------------------------
| اسلایدر تأمین‌کنندگان
|--------------------------------------------------------------------------
*/

function owlTamin() {
    initRomaOwl("#owl-Tamin", {
        rtl: true,
        loop: true,
        dots: true,
        nav: false,
        autoplay: true,
        autoplayHoverPause: true,
        smartSpeed: 1300,
        autoplayTimeout: 4000,
        responsiveClass: true,
        responsive: {
            0: {
                items: 2
            },
            480: {
                items: 3
            },
            767: {
                items: 5
            },
            992: {
                items: 3
            },
            1200: {
                items: 5
            }
        }
    });
}


/*
|--------------------------------------------------------------------------
| اسلایدر مشتریان
|--------------------------------------------------------------------------
*/

function owlcustomer() {
    initRomaOwl("#owl-customer", {
        rtl: true,
        loop: true,
        dots: true,
        nav: false,
        autoplay: true,
        autoplayHoverPause: true,
        smartSpeed: 1300,
        autoplayTimeout: 3000,
        responsiveClass: true,
        responsive: {
            0: {
                items: 2
            },
            480: {
                items: 3
            },
            768: {
                items: 4
            },
            992: {
                items: 5
            },
            1200: {
                items: 6
            }
        }
    });
}


/*
|--------------------------------------------------------------------------
| اسلایدر داستان‌ها
|--------------------------------------------------------------------------
*/

function owlStory() {
    initRomaOwl("#owl-Story", {
        items: 1,
        rtl: true,
        loop: true,
        dots: true,
        nav: false,
        autoplay: true,
        autoplayHoverPause: true,
        smartSpeed: 1300,
        autoplayTimeout: 5000
    });
}


/*
|--------------------------------------------------------------------------
| اسلایدر دوره‌ها
|--------------------------------------------------------------------------
*/

function owlcourse() {
    initRomaOwl(".owl-course", {
        rtl: true,
        loop: true,
        nav: true,
        dots: true,
        autoplay: true,
        autoplayHoverPause: true,
        smartSpeed: 2000,
        autoplayTimeout: 5000,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1
            },
            510: {
                items: 2
            },
            830: {
                items: 3
            },
            1200: {
                items: 3
            },
            1367: {
                items: 4
            }
        }
    });
}


/*
|--------------------------------------------------------------------------
| جدول قیمت‌گذاری
| این تابع فقط در صورت وجود جدول قیمت‌گذاری اجرا می‌شود.
|--------------------------------------------------------------------------
*/

function prcingtable() {
    var $ = jQuery;

    var $pricingContainers = $(
        ".pricing-table, .pricingtable, #pricing-table, " +
        ".pricing-table-container, .price-table"
    );

    if (!$pricingContainers.length) {
        return;
    }

    $pricingContainers.each(function () {
        var $container = $(this);

        var $table = $container.is("table")
            ? $container
            : $container.find("table").first();

        var $tabs = $container.find("ul").first();

        if (!$table.length || !$tabs.length) {
            return;
        }

        $tabs
            .off("click.romaPricing")
            .on("click.romaPricing", "li", function (event) {
                event.preventDefault();

                var position = $(this).index() + 1;

                $table.find("tr").each(function () {
                    var $cells = $(this).children("th, td");

                    $cells.hide();

                    // ستون اول همیشه نمایش داده می‌شود.
                    $cells.first().css("display", "table-cell");

                    // نمایش ستون انتخاب‌شده.
                    $cells.eq(position).css("display", "table-cell");
                });

                $tabs.find("li").removeClass("active");
                $(this).addClass("active");
            });

        // تنظیم colspan فقط برای جدول داخل همین بخش
        function updateSeparatorColspan() {
            var colspan = window.matchMedia("(min-width: 640px)").matches
                ? 6
                : 2;

            $table.find(".sep").attr("colspan", colspan);
        }

        updateSeparatorColspan();

        var mediaQuery = window.matchMedia("(min-width: 640px)");

        if (typeof mediaQuery.addEventListener === "function") {
            mediaQuery.addEventListener(
                "change",
                updateSeparatorColspan
            );
        } else if (typeof mediaQuery.addListener === "function") {
            mediaQuery.addListener(updateSeparatorColspan);
        }
    });
}


/*
|--------------------------------------------------------------------------
| متن متحرک
|--------------------------------------------------------------------------
*/

function teletype() {
    var $ = jQuery;

    if (typeof $.fn.typed !== "function") {
        return;
    }

    $(".animated-text").each(function () {
        var $element = $(this);

        if ($element.data("romaTypedInitialized")) {
            return;
        }

        $element.data("romaTypedInitialized", true);

        $element.typed({
            strings: [
                "مجله روما",
                "مطالب تازه و کاربردی",
                "خواندن، یادگیری و کشف",
                "مقالات جدید روما"
            ],
            typeSpeed: 80,
            loop: true
        });
    });
}


/*
|--------------------------------------------------------------------------
| ابزار شماره تلفن
|--------------------------------------------------------------------------
*/

function initRomaPhoneInput() {
    var $ = jQuery;
    var $telInputs = $(".phone");

    if (!$telInputs.length) {
        return;
    }

    if (typeof $.fn.intlTelInput !== "function") {
        return;
    }

    $telInputs.each(function () {
        var $telInput = $(this);

        if ($telInput.data("romaPhoneInitialized")) {
            return;
        }

        $telInput.data("romaPhoneInitialized", true);

        $telInput.intlTelInput({
            initialCountry: "ir",
            preferredCountries: [
                "ir",
                "gb",
                "br",
                "ru",
                "cn",
                "es",
                "it"
            ],
            autoPlaceholder: "aggressive",

            // مسیر صحیح فایل داخل assets/Js
            utilsScript: "assets/Js/utils.js"
        });
    });
}


/*
|--------------------------------------------------------------------------
| اسلایدر Bootstrap
|--------------------------------------------------------------------------
*/

function initRomaBootstrapCarousel() {
    var $ = jQuery;
    var $carousel = $("#myCarousel");

    if (!$carousel.length) {
        return;
    }

    if (typeof $.fn.carousel !== "function") {
        return;
    }

    $carousel.carousel({
        interval: 3000
    });
}


/*
|--------------------------------------------------------------------------
| راه‌اندازی قابلیت‌ها
|--------------------------------------------------------------------------
*/

jQuery(function ($) {
    initCollapsible();

    // اسلایدرها فقط در صورت وجود عنصر مربوطه اجرا می‌شوند.
    mainslider();
    owlNtejari();
    owlTamin();
    owlStory();
    owlevent1();
    owlevent4();
    owlevent5();
    owltoparticle();
    owlcustomer();
    owldemo5();
    owlcourse();

    // قابلیت‌های اختیاری
    teletype();
    prcingtable();
    initRomaPhoneInput();
    initRomaBootstrapCarousel();
});