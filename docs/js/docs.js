(function ($, window, document, undefined) {
    $(function () {
        var $main = $("#main"),
            $menu = $("#menu"),
            $nav = $("#nav");

        $(document).on("click", "#menu>li>a", function (e) {
            var $li = $(this).closest("li");
            if ($li.hasClass("active")) {
                $li.removeClass("active").find(".nav").removeClass("active");
            } else {
                $li.addClass("active").find(".nav").addClass("active");
            }
            $("#menu>li").not($li).removeClass("active").each(function () {
                $(this).find(".nav").removeClass("active");
            });
        }).on("click", "#menu .nav a", function (e) {
            var $li = $(this).closest("li");
            if ($li.hasClass("active")) {
                $li.removeClass("active");
            } else {
                $li.addClass("active").siblings("li").removeClass("active");
            }
        });

        function fix_menu(event) {
            if ($(this).scrollTop() > $nav.outerHeight()) {
                $menu.css({
                    height: $(this).height()-71
                });
            }
        }

        $(window)
            .on("resize", fix_menu)
            .on("scroll", fix_menu);
    });
})(jQuery, window, document);