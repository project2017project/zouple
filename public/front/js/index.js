$(document).ready(function () {
    $("#wait").hide(), $(function () {
        var e = $(".fixedTopMenu");
        $(window).scroll(function () {
            $(window).scrollTop() <= 50 ? e.removeClass("navbar-scroll") : e.addClass("navbar-scroll")
        })
    }), $(function () {
        var e = $(".scrSys");
        $(window).scroll(function () {
            $(window).scrollTop() <= 400 ? e.removeClass("stickbtnposition-fixed") : e.addClass("stickbtnposition-fixed")
        })
    }), $(".btn-num-product-down:not([onclick])").on("click", function (e) {
        e.preventDefault();
        var n = Number($(this).next().val());
        1 < n && $(this).next().val(n - 1)
    }), $(".btn-num-product-up:not([onclick])").on("click", function (e) {
        e.preventDefault();
        var n = Number($(this).prev().val());
        $(this).prev().val(n + 1)
    }), $(".menuBtn, .closeBtn").click(function () {
        $(".meunTab").toggleClass("active")
    }), $(".userBtn, .closeBtn2").click(function () {
        $(".userTab").toggleClass("active")
    }), $(".searchBtn, .closeBtn3").click(function () {
        $(".searchContent").toggleClass("active")
    }), $(".filterBtn").click(function () {
        $(".filterSm").toggleClass("active")
    }), $(".canFltr").click(function () {
        $(".filterSm").toggleClass("active")
    }), $(".forgatPass").click(function () {
        $(".forgot").show(), $(".log_fm").hide()
    }), $(".logIN").click(function () {
        $(".log_fm").show(), $(".forgot").hide()
    }), $(".signUP").click(function () {
        $(".signUP").addClass("active"), $(".signIn").removeClass("active")
    }), $(".signIn").click(function () {
        $(".signIn").toggleClass("active"), $(".signUP").toggleClass("active")
    })
}), $(function () {
    $("#slider-range").slider({
        range: !0,
        min: 0,
        max: 20000,
        values: [0, 20000],
        slide: function (e, n) {
            $("#amount").val("" + n.values[0] + " - " + n.values[1])
        }
    }), $("#amount").val("" + $("#slider-range").slider("values", 0) + " - " + $("#slider-range").slider("values", 1))
});
var currentTab = 0;

function showTab(e) {
    var n = document.getElementsByClassName("tab");
    if (!n.length || !n[e]) return;
    n[e].style.display = "block", 0 == e ? (document.getElementById("prevBtn").style.display = "none", document.getElementById("nextBtn").style.display = "inline") : (document.getElementById("prevBtn").style.display = "inline", document.getElementById("nextBtn").style.display = "none"), e == n.length - 1 ? (document.getElementById("nextBtn").innerHTML = "Pay now", document.getElementById("nextBtn").style.display = "none") : document.getElementById("nextBtn").innerHTML = "Continue", fixStepIndicator(e)
}

function nextPrev(e) {
    var n = document.getElementsByClassName("tab");
    if (!n.length || !n[currentTab]) return false;
    return !(1 == e && !validateForm()) && (n[currentTab].style.display = "none", (currentTab += e) >= n.length ? (document.getElementById("regForm").submit(), !1) : void showTab(currentTab))
}

function validateForm() {
    var e, n, t = !0;
    if (!document.getElementsByClassName("tab")[currentTab]) return true;
    for (e = document.getElementsByClassName("tab")[currentTab].getElementsByTagName("input"), n = 0; n < e.length; n++) "" == e[n].value && (e[n].className += " invalid", t = !1);
    return t && document.getElementsByClassName("step")[currentTab] && (document.getElementsByClassName("step")[currentTab].className += " finish"), t
}

function fixStepIndicator(e) {
    var n, t = document.getElementsByClassName("step");
    if (!t.length || !t[e]) return;
    for (n = 0; n < t.length; n++) t[n].className = t[n].className.replace(" active", "");
    t[e].className += " active"
}
if (document.getElementsByClassName("tab").length) showTab(currentTab);

/* Zouple premium polish: visual-only frontend behavior */
(function () {
    "use strict";

    var reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    function ready(callback) {
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", callback);
            return;
        }

        callback();
    }

    function initReveal() {
        if (reduceMotion || typeof window.AOS === "undefined") {
            return;
        }

        var selectors = [
            ".product-card",
            ".zouple-product-card",
            ".thumbnail",
            ".card",
            ".card-box",
            ".blog-card",
            ".testimonial-card",
            ".zouple-cart-item",
            ".zouple-checkout-panel",
            "section > .container",
            "section > .container-fluid"
        ];

        selectors.forEach(function (selector) {
            Array.prototype.forEach.call(document.querySelectorAll(selector), function (element) {
                if (element.closest(".modal") || element.hasAttribute("data-aos")) {
                    return;
                }

                element.setAttribute("data-aos", "fade-up");
                element.setAttribute("data-aos-duration", "520");
                element.setAttribute("data-aos-offset", "60");
                element.setAttribute("data-aos-once", "true");
                element.classList.add("zouple-reveal");
            });
        });

        window.AOS.init({
            once: true,
            duration: 520,
            easing: "ease-out-cubic",
            offset: 60,
            disable: function () {
                return window.innerWidth < 576 || reduceMotion;
            }
        });
    }

    function initBackToTop() {
        var button = document.createElement("button");
        button.type = "button";
        button.className = "zouple-back-to-top";
        button.setAttribute("aria-label", "Back to top");
        button.innerHTML = '<i class="fa fa-angle-up" aria-hidden="true"></i>';
        document.body.appendChild(button);

        function toggle() {
            if (window.pageYOffset > 420) {
                button.classList.add("is-visible");
            } else {
                button.classList.remove("is-visible");
            }
        }

        button.addEventListener("click", function () {
            window.scrollTo({
                top: 0,
                behavior: reduceMotion ? "auto" : "smooth"
            });
        });

        window.addEventListener("scroll", toggle, { passive: true });
        toggle();
    }

    ready(function () {
        initReveal();
        initBackToTop();
    });
})();
