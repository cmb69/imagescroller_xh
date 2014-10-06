(function($) {
    $(function() {
        $(".imagescroller").serialScroll({
            items: "li",
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
                        ".imagescroller_play, .imagescroller_stop").show();
            }).mouseleave(function() {
                $(this).find(".imagescroller_prev, .imagescroller_next," +
                        ".imagescroller_play, .imagescroller_stop").hide();
            });
            $(".imagescroller_stop").click(function() {
                $(".imagescroller").trigger("stop");
                $(".imagescroller_stop").css("visibility", "hidden");
                $(".imagescroller_play").css("visibility", "visible");
            });
            $(".imagescroller_play").click(function() {
                $(".imagescroller").trigger("start");
                $(".imagescroller_play").css("visibility", "hidden");
                $(".imagescroller_stop").css("visibility", "visible");
            })
        } else {
            $(this).find(".imagescroller_prev, img.imagescroller_next").show();
        }
    })
}(jQuery))