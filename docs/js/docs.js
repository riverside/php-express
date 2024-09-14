(function ($, window, document, undefined) {
    $(function () {

        $(document).on("click", "#menu>li>a", function (e) {
            e.preventDefault();
            var $li = $(this).closest("li");
            if ($li.hasClass("active")) {
                $li.removeClass("active").find(".nav").removeClass("active");
            } else {
                $li.addClass("active").find(".nav").addClass("active");
            }
            $("#menu>li").not($li).removeClass("active").each(function () {
                $(this).find(".nav").removeClass("active").find("li").removeClass("active");
            });
            window.location.hash = this.getAttribute("href");
        }).on("click", "#menu .nav a", function (e) {
            e.preventDefault();
            var $li = $(this).closest("li");
            if ($li.hasClass("active")) {
                $li.removeClass("active");
            } else {
                $li.addClass("active").siblings("li").removeClass("active");
            }
            window.location.hash = this.getAttribute("href");
            scrollTo(this.getAttribute("href"));
        });

        function scrollTo(selector) {
            $("html,body").animate({
                scrollTop: $(selector).offset().top - 71
            }, 'fast');
        }
    });
})(jQuery, window, document);