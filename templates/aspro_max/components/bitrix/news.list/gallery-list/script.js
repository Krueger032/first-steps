$(document).on("click", ".gallery-list__item-text-cross-part", function () {
  var bSlider = $(this).closest(".owl-carousel").length;
  var index = $(this)
    .closest(bSlider ? ".owl-item" : ".item-wrapper")
    .index();
  var arItems = [];

  $(this)
    .closest(bSlider ? ".owl-stage" : ".row")
    .find(".gallery-list__item-link[data-big]")
    .each(function () {
      var that = $(this);
      arItems.push({
        src: that.data("big"),
        opts: {
          caption: that.attr("title"),
          thumb: that.data("big"),
        },
      });
    });

  if (arItems.length) {
    $.fancybox.open(
      arItems,
      fancyBoxOpts,
      index
    );
  }
});