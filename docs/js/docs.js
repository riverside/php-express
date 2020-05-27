(function ($, window, document, undefined) {
    $(function () {
        var $menu = $("#menu"),
            $nav = $("#nav"),
            $a_list = $menu.find("a");

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
        }).on("click", "#menu .nav a", function (e) {
            e.preventDefault();
            var $li = $(this).closest("li");
            if ($li.hasClass("active")) {
                $li.removeClass("active");
            } else {
                $li.addClass("active").siblings("li").removeClass("active");
            }

            scrollTo(this.getAttribute("href"));
        });

        function fix_menu(event) {
            if ($(this).scrollTop() > $nav.outerHeight()) {
                $menu.css({
                    height: $(this).height() - 71
                });
            }
        }

        function scrollTo(selector) {
            $("html,body").animate({
                scrollTop: $(selector).offset().top - 71
            }, 'fast');
        }

        function scroll(event) {
            var $li,
                dt = $(document).scrollTop();
            $a_list.each(function () {
                if ($(this.getAttribute("href")).offset().top + 71 > dt) {

                    $li = $(this).closest("li").addClass("active");
                    $li.siblings("li").removeClass("active");
                    $li
                        .parent(".nav").addClass("active")
                        .parent("li").addClass("active")
                        .siblings("li").removeClass("active")
                        .children(".nav").removeClass("active");

                    window.location.hash = this.getAttribute("href");

                    return false;
                }
            });
        }

        $(window)
            .on("resize", fix_menu)
            .on("scroll", function(event) {
                fix_menu.call(this, event);
                scroll.call(this, event);
            });
    });
})(jQuery, window, document);