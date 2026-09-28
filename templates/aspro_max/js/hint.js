$(document).on("click", ".hint .hint__icon", function (e) {
    var tooltipWrapp = $(this).closest(".hint");

    if (tooltipWrapp.hasClass("active")) {
        tooltipWrapp.removeClass("active").find(".tooltip").slideUp(200);
    } else {
        tooltipWrapp.addClass("active");
        tooltipWrapp.find(".tooltip").slideDown(200);
        tooltipWrapp.find(".tooltip_close").click(function (e) {
            e.stopPropagation();
            tooltipWrapp.removeClass("active").find(".tooltip").slideUp(100);
        });
    }
    e.stopPropagation();
});
$(document).ready(function () {
    $("html, body").on("mousedown", function (e) {
        if (typeof e.target.className == "string") {
            if (!$(e.target).closest(".hint.active").length) {
                $(".hint.active .hint__icon").trigger("click");
            }
        }
    });
});