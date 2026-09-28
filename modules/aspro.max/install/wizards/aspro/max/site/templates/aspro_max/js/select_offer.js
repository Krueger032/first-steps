if (!("SelectOfferProp" in window) && typeof window.SelectOfferProp != "function") {
  SelectOfferProp = function (e) {

    let _this = $(this),
      obParams = {},
      obSelect = {},
      objUrl = parseUrlQuery(),
      add_url = "",
      selectMode = _this.hasClass("list_values_wrapper") ? true : false,
      container = _this.closest(".bx_catalog_item_scu"),
      img = _this.closest(".item-parent").find(".thumb img"),
      bDetail = _this.closest(".product-main").length,
      curCode = _this.closest(".cur").data("code"),
      depthCount = _this.closest(".item_wrapper").siblings().length,
      item = bDetail ? container.closest(".product-main") : container.closest(".item"),
      offersTreeDocFragment = item.find('.offers-template-json:first').prop('content');
      offersTree = $(offersTreeDocFragment).find('div').data('json');

    /* request params */
    let artName = _this.closest(".item-parent").find(".article_block").data("name"),
      artVal = _this.closest(".item-parent").find(".article_block").data("value"),
      itemPicture = img.data("src") ? img.data("src") : img.attr("src");
    obParams = {
      PARAMS: bDetail ? $(".js-sku-config").data("params") : _this.closest(".js_wrapper_items").data("params"),
      ID: container.data("id"),
      LINK_ID: container.data("id") + "_" + (bDetail ? "detail" : curCode ? curCode : "block"),
      PROPERTY_ID: container.data("propertyid"),
      SECTION_ID: container.data("section_id"),
      DEPTH: _this.closest(".item_wrapper").index(),
      VALUE: selectMode ? _this.find("option:selected").data("onevalue") : _this.data("onevalue"),
      PICTURE: itemPicture ? itemPicture : '',
      ARTICLE_NAME: artName ? artName : '',
      ARTICLE_VALUE: artVal ? artVal : ''
    };
    /**/



    if ("clear_cache" in objUrl && objUrl.clear_cache == "Y") {
      obParams["CLEAR_CACHE"] = "Y";
    }

    // set active
    $(".bx_catalog_item_scu").removeClass("js-selected");
    container.addClass("js-selected");

    /* save selected values */
    for (i = 0; i < depthCount + 1; i++) {
      strName = "PROP_" + container.find(".item_wrapper:eq(" + i + ") > div").data("id");

      if (container.find(".item_wrapper:eq(" + i + ") select").length) {
        obSelect[strName] = container.find(".item_wrapper:eq(" + i + ") select option[selected]").data("onevalue");
      } else {
        obSelect[strName] = container.find(".item_wrapper:eq(" + i + ") li.item.active").data("onevalue");
      }
    }
    /**/

    if (!selectMode) {
      _this.siblings().removeClass("active");
      _this.addClass("active");
    }

    appAspro.sku.init({
      selectedValues: obSelect,
      strPropValue: obParams["VALUE"],
      depth: obParams["DEPTH"],
      isDetail: !!bDetail
    });

    const prevOfferIndex = appAspro.sku.GetCurrentOfferIndex(offersTree);

    /* get sku */
    if (offersTree && typeofExt(offersTree) === "array") {
      appAspro.sku.UpdateSKUInfoByProps(offersTree);
      const currentOfferIndex = appAspro.sku.GetCurrentOfferIndex(offersTree);

      if (prevOfferIndex === currentOfferIndex) return;

      updatePropsTitle(_this);

      obParams["SELECTED_OFFER_INDEX"] = currentOfferIndex;
      obParams["SELECTED_OFFER_ID"] = offersTree[obParams["SELECTED_OFFER_INDEX"]]['ID'];

      if (appAspro.sku.obOffers[obParams["SELECTED_OFFER_ID"]] && item.is(appAspro.sku.obOffers[obParams["SELECTED_OFFER_ID"]]['item'])) {
        appAspro.sku.ChangeInfo(item, obParams["SELECTED_OFFER_ID"]);
        finalActionSKUInfo(appAspro.sku.obOffers[obParams["SELECTED_OFFER_ID"]], item);
      } else {
        if (appAspro.sku.xhr[obParams["ID"]]) {
          appAspro.sku.xhr[obParams["ID"]].abort();
        }

        BX.ajax.runAction('aspro:max.SelectOffer.select', {
          data: {
            params: obParams
          },
          onrequeststart: function (xhr) {
            appAspro.sku.xhr[obParams["ID"]] = xhr
          }
        }).then(
          response => {
            appAspro.sku.obOffers[obParams["SELECTED_OFFER_ID"]] = response.data.offer;
            appAspro.sku.mainItemForOffers = response.data.mainItemForOffers;
            appAspro.sku.obOffers[obParams["SELECTED_OFFER_ID"]]['item'] = item;
            appAspro.sku.ChangeInfo(item, obParams["SELECTED_OFFER_ID"]);
            delete appAspro.sku.xhr[obParams["ID"]];
            finalActionSKUInfo(appAspro.sku.obOffers[obParams["SELECTED_OFFER_ID"]], item);
          },
          response => {
            console.log('error');
          }
        );

      }
    }
    /**/

    function updatePropsTitle(th) {
      var sku_props = th.closest(".sku_props").find(".item_wrapper .item.active, .item_wrapper select.list_values_wrapper");

      $.each(sku_props, function (index, value) {
        value = $(value);
        var skuVal = "";
        if (value.attr("title")) {
          skuVal = value.attr("title").split(":")[1].trim();
        } else if (value.prop('tagName') === "SELECT" && typeof value.val() !== "undefined") {
          skuVal = value.val();
        } else {
          var img_row = value.find(" > i");
          if (img_row.length && img_row.attr("title")) {
            skuVal = img_row.attr("title").split(":")[1].trim();
          }
        }

        if (skuVal != "") {
          value.closest(".item_wrapper").find(".show_class .val").text(skuVal);
        }
      });
    }

    function finalActionSKUInfo(currentOffer, wrapper) {
      if ($(".counter_wrapp.list")) {
        $(".counter_wrapp.list .counter_block.big").removeClass("big");
      }

      var eventdata = {
        offer: currentOffer,
        wrapper: wrapper
      };
      BX.onCustomEvent("onFinalActionSKUInfo", [eventdata]);
    }
  };
  $(document).on("click", ".bx_catalog_item_scu li.item", SelectOfferProp);
  $(document).on("change", ".bx_catalog_item_scu select.list_values_wrapper", SelectOfferProp);
}
