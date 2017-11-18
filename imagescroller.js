(function($) {
    $(function() {
        $(".imagescroller").serialScroll({
            items: ".imagescroller_item",
            prev: ".imagescroller_container .imagescroller_prev",
            next: ".imagescroller_container .imagescroller_next",
            force: true,
            axis: "xy",
            duration: IMAGESCROLLER.duration,
            interval: IMAGESCROLLER.interval,
            constant: IMAGESCROLLER.constant
        });
        if (IMAGESCROLLER.dynamicControls) {
            $(".imagescroller_container").mouseenter(function() {
                $(this).find(".imagescroller_prev, .imagescroller_next," +
                        ".imagescroller_play, .imagescroller_stop").css("visibility", "visible");
            }).mouseleave(function() {
                $(this).find(".imagescroller_prev, .imagescroller_next," +
                        ".imagescroller_play, .imagescroller_stop").css("visibility", "hidden");
            });
            $(".imagescroller_stop").click(function() {
                $(".imagescroller").trigger("stop");
                $(".imagescroller_stop").css("display", "none");
                $(".imagescroller_play").css("display", "inline");
            });
            $(".imagescroller_play").click(function() {
                $(".imagescroller").trigger("start");
                $(".imagescroller_play").css("display", "none");
                $(".imagescroller_stop").css("display", "inline");
            })
        } else {
            $(this).find(".imagescroller_prev, .imagescroller_next").css("visibility", "visible");
        }
    })
}(jQuery))
