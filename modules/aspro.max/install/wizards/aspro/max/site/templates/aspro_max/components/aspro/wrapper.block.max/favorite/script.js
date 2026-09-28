/* BX.addCustomEvent("onCounterGoals", function (eventdata) {
  try {
    if (
      eventdata.goal &&
      eventdata.params &&
      eventdata.params.id &&
      (eventdata.goal === "goal_wish_add" || eventdata.goal === "goal_wish_remove")
    ) {
      requestFavorit();
    }
  } catch (e) {
    console.error(e);
  }
}); */

document.addEventListener("click", function (e) {
  const label = e.target.closest(".js_clear_favorits");

  if (label) {
    const action = JItemActionFavorite.prototype.action.toUpperCase();

    if (
      typeofExt(arAsproCounters) === "object" &&
      typeofExt(arAsproCounters[action]) === "object" &&
      typeofExt(arAsproCounters[action].ITEMS) === "object"
    ) {
      const data = {
        action: action,
        type: "multiple",
        items: Object.keys(arAsproCounters[action].ITEMS),
      };

      BX.ajax
        .runAction(JItemActionFavorite.prototype.requestUrl, { data: { fields: data } })
        .then(response => {
          label.remove();
          arAsproCounters[action].COUNT = response.data.count;

          requestFavorit();
        })
        .catch(e => {
          // show error notice
          if (typeof JNoticeSurface === "function") {
            let surface = JNoticeSurface.get();
            e.statusText = JItemActionFavorite.prototype.getMessageFromErrors(e.errors);
            surface.onRequestError && surface.onRequestError(e);
          }
        });
    }
  }
});

function requestFavorit(selector) {
  const selectorNode = selector || ".personal__block--favorite-products .js_append.ajax_load";

  BX.ajax({
    url: location.href,
    data: {
      action: "reload",
      ajax: "y",
      sessid: BX.bitrix_sessid(),
    },
    method: "POST",
    dataType: "html",
    async: true,
    onsuccess: function (html) {
      var ob = BX.processHTML(html);

      const action = JItemActionFavorite.prototype.action.toUpperCase();
      if (arAsproCounters[action].COUNT) {
        $(selectorNode).html(ob.HTML);
      } else {
        $(".js_clear_favorits").remove();
        $(selectorNode).closest(".personal__block--favorite-products").html(ob.HTML);
      }
      JItemAction.actual();
    },
  });
}
