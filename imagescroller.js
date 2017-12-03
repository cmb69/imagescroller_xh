jQuery(function($) {
    $(".imagescroller_container").each(function () {
        var container = $(this);
        var config = container.data("config");
        var scroller = container.find(".imagescroller");
        var prevButton = container.find(".imagescroller_prev");
        var nextButton = container.find(".imagescroller_next");
        var stopButton = container.find(".imagescroller_stop");
        var playButton = container.find(".imagescroller_play");

        scroller.serialScroll({
            items: ".imagescroller_item",
            force: true,
            axis: "xy",
            duration: config.duration,
            interval: config.interval,
            constant: config.constant
        });
        prevButton.click(function () {
            scroller.trigger("prev");
        });
        nextButton.click(function () {
            scroller.trigger("next");
        });
        if (config.dynamicControls) {
            container
                .mouseenter(function () {
                    container.find(".imagescroller_controls img").css("visibility", "visible");
                })
                .mouseleave(function () {
                    container.find(".imagescroller_controls img").css("visibility", "hidden");
                });
            stopButton.click(function () {
                scroller.trigger("stop");
                stopButton.css("display", "none");
                playButton.css("display", "inline");
            });
            playButton.click(function () {
                scroller.trigger("start");
                playButton.css("display", "none");
                stopButton.css("display", "inline");
            })
        } else {
            container.find(".imagescroller_prev_next").css("visibility", "visible");
        }
    });
});
