window.appAspro = window.appAspro || {};

if (!window.appAspro.sku) {
  window.appAspro.sku = {
    xhr: {},
    obOffers: {},
    mainItemForOffers: {},
    init: function (options) {
      const optionDefault = {
        containerClass: ".bx_catalog_item_scu.js-selected",
        depth: 0,
        strPropValue: "",
        selectedValues: "",
        showAbsent: true,
        isDetail: false,
      };
      this.options = Object.assign({}, optionDefault, options);
    },
    GetRowValues: function (arFilter, index, offersTree) {
      var i = 0,
        j,
        arValues = [],
        boolSearch = false,
        boolOneSearch = true;

      if (0 === arFilter.length) {
        for (i = 0; i < offersTree.length; i++) {
          if (!BX.util.in_array(offersTree[i].TREE[index], arValues))
            arValues[arValues.length] = offersTree[i].TREE[index].toString();
        }
        boolSearch = true;
      } else {
        for (i = 0; i < offersTree.length; i++) {
          boolOneSearch = true;
          for (j in arFilter) {
            if (arFilter[j].toString() !== offersTree[i].TREE[j].toString()) {
              boolOneSearch = false;
              break;
            }
          }

          if (boolOneSearch) {
            if (!BX.util.in_array(offersTree[i].TREE[index], arValues))
              arValues[arValues.length] = offersTree[i].TREE[index].toString();
            boolSearch = true;
          }
        }
      }
      return boolSearch ? arValues : false;
    },

    GetCanBuy: function (arFilter, offersTree) {
      var i = 0,
        j,
        boolSearch = false,
        boolOneSearch = true;

      for (i = 0; i < offersTree.length; i++) {
        boolOneSearch = true;
        for (j in arFilter) {
          if (arFilter[j].toString() !== offersTree[i].TREE[j].toString()) {
            boolOneSearch = false;
            break;
          }
        }
        if (boolOneSearch) {
          if (offersTree[i].CAN_BUY) {
            boolSearch = true;
            break;
          }
        }
      }
      return boolSearch;
    },

    decodeHtmlEntity: function (str) {
      return str.replace(/&#(\d+);/g, function (match, dec) {
        return String.fromCharCode(dec);
      });
    },

    checkPriceRange: function (quantity, obj) {
      if (typeof quantity === "undefined" || !obj.PRICE_MATRIX) return;

      var range,
        found = false,
        rangeSelected = "";
      for (var i in obj.PRICE_MATRIX.ROWS) {
        if (obj.PRICE_MATRIX.ROWS.hasOwnProperty(i)) {
          range = obj.PRICE_MATRIX.ROWS[i];
          if (
            parseInt(quantity) >= parseInt(range.QUANTITY_FROM) &&
            (range.QUANTITY_TO == "0" || parseInt(quantity) <= parseInt(range.QUANTITY_TO))
          ) {
            found = true;
            return i;
          }
        }
      }

      if (!found && (range = this.getMinPriceRange(obj))) {
        rangeSelected = range;

        return rangeSelected;
      }

      for (var k in obj.ITEM_PRICES) {
        if (obj.ITEM_PRICES.hasOwnProperty(k)) {
          if (k == rangeSelected) {
            return k;
          }
        }
      }
    },

    getMinPriceRange: function (obj) {
      var range,
        found = "";

      for (var i in obj.PRICE_MATRIX.ROWS) {
        if (obj.PRICE_MATRIX.ROWS.hasOwnProperty(i)) {
          if (!range || parseInt(obj.PRICE_MATRIX.ROWS[i].QUANTITY_FROM) < parseInt(range.QUANTITY_FROM)) {
            range = obj.PRICE_MATRIX.ROWS[i];
            found = i;
          }
        }
      }

      return i;
    },

    /*set blocks*/
    setActualDataBlock: function (th, obj) {
      /*wish|like*/
      this.setLikeBlock(th, ".like_icons .wish_item_button", obj, "DELAY");
      this.setLikeBlock(th, ".like_icons .compare_item_button", obj, "COMPARE");
      /**/

      /*buy*/
      this.setBuyBlock(th, obj);
      /**/

      if (this.options.isDetail) {
        /*wish|like*/
        this.setLikeBlock($(".product-container"), ".product-info-headnote__toolbar .wish_item_button", obj, "DELAY");
        this.setLikeBlock(
          $(".product-container"),
          ".product-info-headnote__toolbar .compare_item_button",
          obj,
          "COMPARE"
        );
        /**/
        /*store amount block*/
        if (typeof setElementStore === "function") {
          setElementStore("", obj.ID);
        }
        /**/
      }
    },
    /**/

    /*set compare/wish*/
    setLikeBlock: function (th, className, obj, type) {
      var block = th;

      block.find(className).attr("data-item", obj.ID);
      block.find(className).find("span").attr("data-item", obj.ID);

      /* if (arBasketAspro[type]) {
        block.find(className).find(".to").removeClass("added").css("display", "block");
        block.find(className).find(".in").hide();

        if (arBasketAspro[type][obj.ID] !== undefined) {
          block.find(className).find(".to").hide();
          block.find(className).find(".in").addClass("added").css("display", "block");
        }
      } */
    },
    /**/

    /*set buy*/
    setBuyBlock: function (th, obj, index) {
      var buyBlock = th.find(".offer_buy_block").first(),
        input_value = obj.CONFIG.MIN_QUANTITY_BUY,
        bList = buyBlock.find(".counter_wrapp.list").length,
        bShowCounter =
          (!this.options.isDetail && obj.CONFIG.OPTIONS.USE_PRODUCT_QUANTITY_LIST) ||
          (this.options.isDetail && obj.CONFIG.OPTIONS.USE_PRODUCT_QUANTITY_DETAIL);

      if (buyBlock.find(".counter_wrapp .counter_block").length)
        buyBlock.find(".counter_wrapp .counter_block").attr("data-item", obj.ID);

      if (typeof window["obSkuQuantys"][obj.ID] != "undefined") input_value = window["obSkuQuantys"][obj.ID];

      if (bShowCounter && obj.CONFIG.ACTION == "ADD" && obj.CAN_BUY) {
        var max = obj.CONFIG.MAX_QUANTITY_BUY > 0 ? "data-max='" + obj.CONFIG.MAX_QUANTITY_BUY + "'" : "",
          min = obj.CONFIG.SET_MIN_QUANTITY_BUY ? "data-min='" + obj.CONFIG.MIN_QUANTITY_BUY + "'" : "",
          counterHtml =
            '<span class="minus dark-color"' +
            min +
            '><i class="svg inline  svg-inline-wish ncolor colored1" aria-hidden="true"><svg width="11" height="1" viewBox="0 0 11 1"><rect width="11" height="1" rx="0.5" ry="0.5"></rect></svg></i></span>' +
            '<input type="text" class="text" name="' +
            obj.PRODUCT_QUANTITY_VARIABLE +
            '" value="' +
            input_value +
            '" />' +
            '<span class="plus dark-color" ' +
            max +
            '><i class="svg inline  svg-inline-wish ncolor colored1" aria-hidden="true"><svg width="11" height="11" viewBox="0 0 11 11"><path d="M1034.5,193H1030v4.5a0.5,0.5,0,0,1-1,0V193h-4.5a0.5,0.5,0,0,1,0-1h4.5v-4.5a0.5,0.5,0,0,1,1,0V192h4.5A0.5,0.5,0,0,1,1034.5,193Z" transform="translate(-1024 -187)"></path></svg></i></span>';

        if (arBasketAspro["BASKET"] && arBasketAspro["BASKET"][obj.ID] !== undefined) {
          if (buyBlock.find(".counter_wrapp .counter_block").length) {
            buyBlock.find(".counter_wrapp .counter_block").hide();
          } else {
            if (bList || this.options.isDetail)
              buyBlock
                .find(".counter_wrapp")
                .prepend(
                  '<div class="counter_block_inner"><div class="counter_block ' +
                    (this.options.isDetail ? "md" : "big") +
                    '" data-item="' +
                    obj.ID +
                    '"></div></div>'
                );
            else
              buyBlock
                .find(".counter_wrapp")
                .prepend('<div class="counter_block big" data-item="' + obj.ID + '"></div>');
            buyBlock.find(".counter_wrapp .counter_block").html(counterHtml).hide();
          }
        } else {
          if (buyBlock.find(".counter_wrapp .counter_block").length) {
            buyBlock.find(".counter_wrapp .counter_block_inner").show();
            buyBlock.find(".counter_wrapp .counter_block").html(counterHtml).show();
          } else {
            if (bList || this.options.isDetail)
              buyBlock
                .find(".counter_wrapp")
                .prepend(
                  '<div class="counter_block_inner"><div class="counter_block ' +
                    (this.options.isDetail ? "md" : "big") +
                    '" data-item="' +
                    obj.ID +
                    '"></div></div>'
                );
            else
              buyBlock
                .find(".counter_wrapp")
                .prepend('<div class="counter_block big" data-item="' + obj.ID + '"></div>');
            buyBlock.find(".counter_wrapp .counter_block").html(counterHtml);
          }
        }
      } else {
        if (buyBlock.find(".counter_wrapp .counter_block").length)
          buyBlock.find(".counter_wrapp .counter_block").hide();
      }

      var className =
        obj.CONFIG.ACTION == "ORDER" ||
        !obj.CAN_BUY ||
        !bShowCounter ||
        (obj.CONFIG.ACTION == "SUBSCRIBE" && obj.CATALOG_SUBSCRIBE == "Y")
          ? "wide"
          : "";

      if (obj.CAN_BUY) {
        let spanOCB = th.find(".like_icons.block .wrapp_one_click [data-item]");
        if (spanOCB) {
          spanOCB.attr("data-item", obj.ID);
          spanOCB.attr("data-iblockID", obj.IBLOCK_ID);
          spanOCB.attr("data-quantity", obj.CONFIG.MIN_QUANTITY_BUY);
          spanOCB.attr("onclick", "oneClickBuy('" + obj.ID + "', '" + obj.IBLOCK_ID_PARENT + "', this)");
        }
      }

      if (buyBlock.find(".button_block").length) {
        if (arBasketAspro["BASKET"] && arBasketAspro["BASKET"][obj.ID] !== undefined) {
          buyBlock.find(".button_block").addClass("wide").html(obj.HTML);
          markProductAddBasket(obj.ID);
        } else {
          if (className) {
            buyBlock.find(".button_block").addClass("wide").html(obj.HTML);
            if (arBasketAspro["SUBSCRIBE"] && arBasketAspro["SUBSCRIBE"][obj.ID] !== undefined)
              markProductSubscribe(obj.ID);
          } else {
            buyBlock.find(".button_block").removeClass("wide").html(obj.HTML);
          }
        }
      } else {
        buyBlock.find(".counter_wrapp").append('<div class="button_block ' + className + '">' + obj.HTML + "</div>");
        if (arBasketAspro["BASKET"] && arBasketAspro["BASKET"][obj.ID] !== undefined) markProductAddBasket(obj.ID);
        if (arBasketAspro["SUBSCRIBE"] && arBasketAspro["SUBSCRIBE"][obj.ID] !== undefined)
          markProductSubscribe(obj.ID);
      }

      if (th.closest(".list").length) {
        // is list view
        var inner = buyBlock.parent();

        if (inner.find(".wrapp-one-click").length) inner.find(".wrapp-one-click").remove();

        if (obj.CONFIG.ACTION !== "NOTHING") {
          // inner.append(obj.ONE_CLICK_BUY_HTML);
          $(obj.ONE_CLICK_BUY_HTML).insertAfter(inner.find(".offer_buy_block"));
        } else {
          if (inner.find(".wrapp-one-click").length) inner.find(".wrapp-one-click").remove();
        }
      }

      //detail ocb
      if (th.closest(".product-main").length) {
        // is detail view
        if (buyBlock.find(".wrapp-one-click").length) buyBlock.find(".wrapp-one-click").remove();

        if (obj.CONFIG.ACTION !== "NOTHING") {
          $(obj.ONE_CLICK_BUY_HTML).insertAfter(buyBlock.find(".counter_wrapp"));
        }
      }

      buyBlock.fadeIn();
      buyBlock.find(".counter_wrapp .counter_block input").data("product", "offersTree");
      buyBlock.find(".to-cart").attr("data-quantity", input_value);
      this.setPriceAction(obj, "Y", "", th);
    },
    /**/

    setPriceAction: function (obj, sku, change, wrapper) {
      if (obj == "" || typeof obj === "undefined")
        obj = this.obOffers[wrapper.find(".counter_wrapp:first").data("index")];

      var measure = obj.MEASURE && obj.SHOW_MEASURE == "Y" ? obj.MEASURE : "";
      var check_quantity = "",
        currentPriceSelected = "",
        is_sku = typeof sku !== "undefined" && sku == "Y";

      window["obSkuQuantys"][obj.ID] = obj.CONFIG.MIN_QUANTITY_BUY;
      if (wrapper.find("input[name=quantity]:first").length)
        window["obSkuQuantys"][obj.ID] = wrapper.find("input[name=quantity]:first").val();

      if (obj.PRICE_MATRIX) {
        currentPriceSelected = this.checkPriceRange(window["obSkuQuantys"][obj.ID], obj);
        if (obj.PRICE_MATRIX_HTML) {
          this.setPriceMatrix(obj.PRICE_MATRIX_HTML, obj, currentPriceSelected, wrapper);
        } else {
          if ("PRICES" in obj && obj.PRICES) {
            this.setPrice(obj.PRICES, measure, obj, wrapper);
          }
          obj.noSetPrice = true;
          this.setPriceMatrix(obj.PRICE_MATRIX_HTML, obj, currentPriceSelected, wrapper);
        }
      } else {
        if ("PRICES" in obj && obj.PRICES) {
          this.setPrice(obj.PRICES, measure, obj, wrapper);
        }
      }

      wrapper.find(".cost .with_matrix.price_matrix_wrapper").removeClass("no-popup");
      if (!wrapper.find(".cost .js_price_wrapper .more-item-info").length) {
        wrapper.find(".cost .with_matrix.price_matrix_wrapper").addClass("no-popup");
      }

      if (arMaxOptions["THEME"]["SHOW_TOTAL_SUMM"] == "Y") {
        if (obj.check_quantity) check_quantity = "Y";
        else {
          var check_quantity = typeof change !== "undefined" && change == "Y" ? change : "";
          if (check_quantity) obj.check_quantity = true;
        }

        check_quantity = is_sku = "";

        if (typeof obj.ITEM_PRICES[currentPriceSelected] !== "undefined") {
          setPriceItem(
            wrapper,
            window["obSkuQuantys"][obj.ID],
            obj.ITEM_PRICES[currentPriceSelected].DISCOUNT_PRICE,
            check_quantity,
            is_sku
          );
        } else {
          setPriceItem(wrapper, window["obSkuQuantys"][obj.ID], obj.MIN_PRICE.DISCOUNT_VALUE, check_quantity, is_sku);
        }
      }
    },

    setPriceMatrix: function (sPriceMatrix, obj, currentPriceSelected, wrapper) {
      if (wrapper.find(".cost").length) {
        var measure = obj.MEASURE && obj.SHOW_MEASURE == "Y" ? obj.MEASURE : "",
          strPrice = "";
        strPrice = getCurrentPrice(
          obj.ITEM_PRICES[currentPriceSelected].DISCOUNT_PRICE,
          obj.ITEM_PRICES[currentPriceSelected].CURRENCY,
          obj.ITEM_PRICES[currentPriceSelected].PRINT_DISCOUNT_PRICE
        );
        if (measure) strPrice += '<span class="price_measure">/' + measure + "</span>";
        wrapper.find(".not_matrix").hide();
        wrapper.find(".with_matrix .price_value_block").html(strPrice);

        if (obj.SHOW_OLD_PRICE) {
          if (
            parseFloat(obj.ITEM_PRICES[currentPriceSelected].PRICE) >
            parseFloat(obj.ITEM_PRICES[currentPriceSelected].DISCOUNT_PRICE)
          ) {
            wrapper
              .find(".with_matrix .discount")
              .html(
                getCurrentPrice(
                  obj.ITEM_PRICES[currentPriceSelected].PRICE,
                  obj.ITEM_PRICES[currentPriceSelected].CURRENCY,
                  obj.ITEM_PRICES[currentPriceSelected].PRINT_PRICE
                )
              );
            wrapper.find(".with_matrix .discount").css("display", "");
          } else {
            wrapper.find(".with_matrix .discount").html("");
            wrapper.find(".with_matrix .discount").css("display", "none");
          }
        } else {
          wrapper.find(".with_matrix .discount").html("");
          wrapper.find(".with_matrix .discount").css("display", "none");
        }

        if (obj.ITEM_PRICES[currentPriceSelected].PERCENT > 0) {
          if (obj.SHOW_DISCOUNT_PERCENT_NUMBER) {
            if (
              obj.ITEM_PRICES[currentPriceSelected].PERCENT > 0 &&
              obj.ITEM_PRICES[currentPriceSelected].PERCENT < 100
            ) {
              if (!wrapper.find(".with_matrix .sale_block .sale_wrapper .value").length)
                $('<div class="value"></div>').insertBefore(
                  wrapper.find(".with_matrix .sale_block .sale_wrapper .text")
                );

              wrapper
                .find(".with_matrix .sale_block .sale_wrapper .value")
                .html("-<span>" + obj.ITEM_PRICES[currentPriceSelected].PERCENT + "</span>%");
            } else {
              if (wrapper.find(".with_matrix .sale_block .sale_wrapper .value").length)
                wrapper.find(".with_matrix .sale_block .sale_wrapper .value").remove();
            }
          }

          wrapper
            .find(".with_matrix .sale_block .text .values_wrapper")
            .html(
              getCurrentPrice(
                obj.ITEM_PRICES[currentPriceSelected].DIFF,
                obj.ITEM_PRICES[currentPriceSelected].CURRENCY,
                obj.ITEM_PRICES[currentPriceSelected].PRINT_DIFF
              )
            );
          wrapper.find(".with_matrix .sale_block").show();
        } else {
          wrapper.find(".with_matrix .sale_block").hide();
        }

        wrapper.find(".sale_block.normal").hide();
        wrapper.find(".with_matrix").show();

        if (obj.SHOW_DISCOUNT_PERCENT) {
          wrapper.find(".cost > .price:not(.discount)").closest(".cost").find(".sale_block:not(.matrix)").hide();
          wrapper
            .find(".cost > .price:not(.discount)")
            .closest(".cost")
            .find(".sale_block:not(.matrix) .text span")
            .html("");
        }

        if (!obj.noSetPrice) {
          wrapper.find(".cost .js_price_wrapper").html(obj.PRICE_MATRIX_HTML);
          if (obj.SHOW_POPUP_PRICE)
            wrapper
              .find(".cost .js_price_wrapper")
              .append(
                '<div class="js-show-info-block more-item-info rounded3 bordered-block text-center"><i class="svg inline  svg-inline-fw" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="2" viewBox="0 0 8 2"><path data-name="Ellipse 292 copy 3" class="cls-1" d="M320,4558a1,1,0,1,1-1,1A1,1,0,0,1,320,4558Zm-3,0a1,1,0,1,1-1,1A1,1,0,0,1,317,4558Zm6,0a1,1,0,1,1-1,1A1,1,0,0,1,323,4558Z" transform="translate(-316 -4558)"></path></svg></i></div>'
              );
        } else {
          wrapper.find(".cost .js_price_wrapper > .price_matrix_wrapper ").hide();
        }

        var eventdata = {
          product: wrapper,
          measure: measure,
          config: this.config,
          offer: obj,
          obPrice: obj.ITEM_PRICES[currentPriceSelected],
        };
        BX.onCustomEvent("onAsproSkuSetPriceMatrix", [eventdata]);
      }
    },

    setPrice: function (obPrices, measure, obj, wrapper) {
      if (wrapper.find(".cost.prices").length) {
        var measure = obj.MEASURE && obj.SHOW_MEASURE == "Y" ? obj.MEASURE : "",
          product = wrapper,
          obPrices = obj.PRICES;
        if (typeof obPrices == "object") {
          wrapper.find(".with_matrix").hide();
          wrapper.find(".cost .js_price_wrapper").html(obj.PRICES_HTML);

          var eventdata = { product: product, measure: measure, config: this.config, offer: obj, obPrices: obPrices };
          BX.onCustomEvent("onAsproSkuSetPrice", [eventdata]);
        }
      }
    },

    /*set store quantity*/
    setQuantityStore: function (wrapper, ob, text, html) {
      let $wrappers = wrapper;
      if (wrapper.hasClass("product-main")) {
        // there 2 places to replace in detail page (desktop & mobile)
        $wrappers = wrapper.closest(".catalog_detail").find(".product-main");
      }

      if (parseFloat(ob.MAX_QUANTITY) > 0) $wrappers.find(".item-stock .icon").removeClass("order").addClass("stock");
      else $wrappers.find(".item-stock .icon").removeClass("stock").addClass("order");

      $wrappers.find(".item-stock .icon + span").html(text);
      // $wrappers.find('.item-stock').addClass('js-show-stores').data('id', ob.ID);
      $wrappers.find(".item-stock").data("id", ob.ID);

      $wrappers.find(".sa_block .js-info-block").remove();
    },

    UpdateImages: function (wrapper, obj) {
      if (obj.GALLERY && !wrapper.hasClass("table-view__item")) {
        let obOfferGalery = $(obj.GALLERY_HTML).children();
        wrapper.find(".image_wrapper_block .thumb").html(obOfferGalery);
      } else {
        if (!!obj.PICTURE) {
          wrapper.find(".thumb img").attr("src", obj.PICTURE);
        }
      }
    },

    UpdateName: function (wrapper, obj) {
      if (!this.options.isDetail && arMaxOptions["THEME"]["CHANGE_TITLE_ITEM_LIST"] === "Y") {
        var skuName =
          typeof obj.IPROPERTY_VALUES === "object" && obj.IPROPERTY_VALUES.ELEMENT_PAGE_TITLE
            ? obj.IPROPERTY_VALUES.ELEMENT_PAGE_TITLE
            : obj.NAME;

        var skuAlt =
          typeof obj.PREVIEW_PICTURE_FIELD === "object" && obj.PREVIEW_PICTURE_FIELD.DESCRIPTION
            ? obj.PREVIEW_PICTURE_FIELD.DESCRIPTION
            : typeof obj.IPROPERTY_VALUES === "object" && obj.IPROPERTY_VALUES.ELEMENT_PREVIEW_PICTURE_FILE_ALT
            ? obj.IPROPERTY_VALUES.ELEMENT_PREVIEW_PICTURE_FILE_ALT
            : obj.NAME;

        var skuTitle =
          typeof obj.PREVIEW_PICTURE_FIELD === "object" && obj.PREVIEW_PICTURE_FIELD.DESCRIPTION
            ? obj.PREVIEW_PICTURE_FIELD.DESCRIPTION
            : typeof obj.IPROPERTY_VALUES === "object" && obj.IPROPERTY_VALUES.ELEMENT_PREVIEW_PICTURE_FILE_TITLE
            ? obj.IPROPERTY_VALUES.ELEMENT_PREVIEW_PICTURE_FILE_TITLE
            : obj.NAME;

        wrapper.find(".item-title span").html(skuName);
        wrapper.find(".image_wrapper_block img").attr("alt", this.decodeHtmlEntity(skuAlt));
        wrapper.find(".image_wrapper_block img").attr("title", this.decodeHtmlEntity(skuTitle));
      }
    },

    UpdateLink: function (wrapper, obj) {
      if (!!obj.URL) {
        var arUrl = obj.URL.split("?");
        if (arUrl.length > 1 && wrapper.find(".item-title > a").length > 0) {
          var arUrl2 = wrapper.find(".item-title > a").attr("href").split("?");
          if (arUrl2.length > 1) {
            const fastViewBlock = wrapper.find('[data-name="fast_view"]');
            const newUrl = wrapper.find(".item-title > a").attr("href").replace(arUrl2[1], arUrl[1]);
            wrapper.find(".item-title > a").attr("href", newUrl);
            wrapper.find(".thumb").attr("href", newUrl);
            wrapper.find(".thumb").removeClass("shine");
            if (fastViewBlock.length) {
              fastViewBlock.attr("data-param-item_href", encodeURIComponent(newUrl));
              fastViewBlock.parent().html(fastViewBlock.parent().html()); //need for correct jqm url when you change offers
            }
          }
        }
      }
    },

    UpdateArticle: function (wrapper, obj) {
      if (wrapper.find(".article_block")) {
        var article_text = obj.ARTICLE ? obj.ARTICLE : "";
        if (!article_text && obj.SHOW_ARTICLE_SKU == "Y" && obj.ARTICLE_SKU) {
          article_text = obj.ARTICLE_SKU;
        }
        if (wrapper.find(".article_block > div").length) {
          wrapper.find(".article_block > div").text(article_text);
        } else {
          wrapper.find(".article_block").text(article_text);
        }
      }
    },

    UpdateProps: function (wrapper, obj) {
      if (wrapper.find(".table-view__props-wrapper").length) {
        this.UpdatePropsTable(wrapper, obj);
      } else if (wrapper.find(".properties").length) {
        var props = "";
        if (obj.DISPLAY_PROPERTIES) {
          if (wrapper.find(".properties.list").length) {
            for (var j in obj.DISPLAY_PROPERTIES) {
              props +=
                '<div class="properties__item properties__item--compact font_xs">' +
                '<div class="properties__title properties__item--inline muted">' +
                obj.DISPLAY_PROPERTIES[j]["NAME"] +
                "</div>" +
                '<div class="properties__hr properties__item--inline muted">&mdash;</div>' +
                '<div class="properties__value properties__item--inline darken">' +
                obj.DISPLAY_PROPERTIES[j]["DISPLAY_VALUE"] +
                "</div>" +
                "</div>";
            }
          } else {
            for (var j in obj.DISPLAY_PROPERTIES) {
              props +=
                '<div class="properties__item">' +
                '<div class="properties__title font_sxs muted">' +
                obj.DISPLAY_PROPERTIES[j]["NAME"] +
                "</div>" +
                '<div class="properties__value font_sm darken">' +
                obj.DISPLAY_PROPERTIES[j]["DISPLAY_VALUE"] +
                "</div>" +
                "</div>";
            }
          }
        }
        wrapper.find(".properties .properties__container_js").html(props);
      } else if (wrapper.find(".props_list.js-container").length) {
        var props = "";
        if (obj.DISPLAY_PROPERTIES) {
          for (var j in obj.DISPLAY_PROPERTIES) {
            props +=
              "<tr>" +
              "<td><span>" +
              obj.DISPLAY_PROPERTIES[j]["NAME"] +
              "</span></td>" +
              "<td><span>" +
              obj.DISPLAY_PROPERTIES[j]["DISPLAY_VALUE"] +
              "</span></td>" +
              "</tr>";
          }
        }
        wrapper.find(".props_list.js-container").html(props).show();
      }
    },

    ChangeInfo: function (wrapper, currentOfferId = 0) {
      if (currentOfferId) {
        wrapper.find(".counter_wrapp").data("index", currentOfferId); // set current sku

        this.UpdateImages(wrapper, this.obOffers[currentOfferId]);
        this.UpdateName(wrapper, this.obOffers[currentOfferId]);
        this.UpdateLink(wrapper, this.obOffers[currentOfferId]);

        this.setActualDataBlock(wrapper, this.obOffers[currentOfferId]);

        wrapper.find(".to-cart:first").data("item", this.obOffers[currentOfferId].ID);

        this.setQuantityStore(
          wrapper,
          this.obOffers[currentOfferId],
          this.obOffers[currentOfferId].AVAILIABLE.TEXT,
          this.obOffers[currentOfferId].AVAILIABLE.HTML
        );
        showItemStoresAmount();

        this.UpdateArticle(wrapper, this.obOffers[currentOfferId]);

        if (wrapper.find(".quantity_block .values").length)
          wrapper
            .find(".quantity_block .values .item span.value")
            .text(this.obOffers[currentOfferId].MAX_QUANTITY)
            .css({ opacity: "1" });

        this.UpdateProps(wrapper, this.obOffers[currentOfferId]);

        /*set discount*/
        if (this.obOffers[currentOfferId].SHOW_DISCOUNT_TIME_EACH_SKU == "Y") {
          initCountdownTime(wrapper, this.obOffers[currentOfferId].DISCOUNT_ACTIVE);
        }
        /**/
      }
    },

    GetCurrentOfferIndex: function (offersTree) {
      let i = 0,
        j,
        index = -1,
        boolOneSearch = true;

      if ($(this.options.containerClass).data("selected")) {
        this.options.selectedValues = $(this.options.containerClass).data("selected");
      }

      for (i = 0; i < offersTree.length; i++) {
        boolOneSearch = true;
        for (j in this.options.selectedValues) {
          if (this.options.selectedValues[j]) {
            if (this.options.selectedValues[j].toString() !== offersTree[i].TREE[j].toString()) {
              boolOneSearch = false;
              break;
            }
          }
        }
        if (boolOneSearch) {
          index = i;
          break;
        }
      }

      return index;
    },

    UpdatePropsTable: function (wrapper, curOffer) {
      const $props = wrapper.find(".js-offers-prop:first");
      if ($props.length) {
        wrapper.find(".js-prop").remove();
        if (curOffer["DISPLAY_PROPERTIES"]) {
          if (!Object.keys(curOffer["DISPLAY_PROPERTIES"]).length) {
            return;
          }
          if (!window["propTemplate"]) {
            let $clone = $props.clone();
            $clone.find("> *:not(:first-child)").remove();
            $clone.find(".js-prop-replace").removeClass("js-prop-replace").addClass("js-prop");
            $clone.find(".js-prop-title").text("#PROP_TITLE#");
            $clone.find(".js-prop-value").text("#PROP_VALUE#");
            $clone.find(".hint").remove();
            let cloneHtml = $clone.html();
            window["propTemplate"] = cloneHtml;
          }

          let html = "";
          for (let key in curOffer["DISPLAY_PROPERTIES"]) {
            let title = curOffer["DISPLAY_PROPERTIES"][key]["NAME"];
            let value = curOffer["DISPLAY_PROPERTIES"][key]["DISPLAY_VALUE"];

            let str = window["propTemplate"].replace("#PROP_TITLE#", title).replace("#PROP_VALUE#", value);

            html += str;
          }
          if (html) {
            $props[0].insertAdjacentHTML("beforeend", html);
          }
        }
      }
    },

    UpdateRow: function (intNumber, activeID, showID, canBuyID) {
      var i = 0,
        showI = 0,
        value = "",
        countShow = 0,
        obData = {},
        obDataCont = {},
        isCurrent = false,
        selectIndex = 0,
        RowItems = null;

      if (-1 < intNumber && intNumber < $(this.options.containerClass + " .item_wrapper").length) {
        propMode = $(this.options.containerClass + " .item_wrapper:eq(" + intNumber + ") > div").data("display_type");
        selectMode = "SELECT" === propMode;

        var tag = selectMode ? "option" : "li",
          hideClass = selectMode ? "hidden" : "missing";

        RowItems = BX.findChildren(
          $(this.options.containerClass + " .item_wrapper:eq(" + intNumber + ") .list_values_wrapper")[0],
          { tagName: tag },
          false
        );
        if (!!RowItems && 0 < RowItems.length) {
          countShow = showID.length;
          obData = {
            style: {},
            props: {
              disabled: "",
              selected: "",
            },
          };
          obDataCont = {
            style: {},
          };

          var listWrapper = $(
            this.options.containerClass + " .item_wrapper:eq(" + intNumber + ") .list_values_wrapper"
          );
          var max_scu_count = listWrapper.length ? listWrapper.data("maxCount") : 0;
          var current_count = 0;
          var more_hidden_count = 0;

          for (i = 0; i < RowItems.length; i++) {
            value = RowItems[i].getAttribute("data-onevalue");
            isCurrent = value === activeID;
            if (BX.util.in_array(value, canBuyID)) {
              var dop_class = "";
              if (max_scu_count && current_count >= max_scu_count) {
                dop_class = "scu_prop_more";
                more_hidden_count++;
              }
              obData.props.className = (isCurrent ? "active" : "") + dop_class;
              current_count++;
            } else {
              obData.props.className = isCurrent ? "active" + " " + hideClass : hideClass;
            }

            if (selectMode) {
              obData.props.disabled = "disabled";
              obData.props.selected = isCurrent ? "selected" : "";
            } else {
              obData.style.display = "none";
              obData.props.className += " item";
            }
            if (BX.util.in_array(value, showID)) {
              if (selectMode) {
                obData.props.disabled = "";
              } else {
                obData.style.display = "";
              }
              if (isCurrent) {
                selectIndex = showI;
              }
              if (value != 0) showI++;
            }
            BX.adjust(RowItems[i], obData);
          }

          if (max_scu_count) {
            var scu_item_wrapper = $(this.options.containerClass + " .item_wrapper:eq(" + intNumber + ")");
            var more_link = scu_item_wrapper.find(".show_more_link");

            if (!more_hidden_count) {
              more_link.remove();
            } else {
              if (!more_link.length) {
                var link = wrapper.find(".item-title a").attr("href");
                more_link = $(
                  '<div class="show_more_link"><a class="font_sxs colored_theme_n_hover_bg-svg-stroke" href="' +
                    link +
                    '"></a></div>'
                );
                scu_item_wrapper.append(more_link);
              }
              var titles = [
                BX.message("SHOW_MORE_SCU_1"),
                BX.message("SHOW_MORE_SCU_2"),
                BX.message("SHOW_MORE_SCU_3"),
              ];
              var more_scu_mess = BX.message("SHOW_MORE_SCU_MAIN").replace(
                "#COUNT#",
                declOfNum(more_hidden_count, titles)
              );
              var svgHTML =
                '<svg xmlns="http://www.w3.org/2000/svg" width="4" height="7" viewBox="0 0 4 7" fill="none">' +
                '<path d="M0.5 0.5L3.5 3.5L0.5 6.5" stroke="#333" stroke-linecap="round" stroke-linejoin="round"/>' +
                "</svg>";
              more_link.find("a").text(more_scu_mess).append(svgHTML);
              more_link.show();
            }
          }

          if (!showI) obDataCont.style.display = "none";
          else obDataCont.style.display = "";
          BX.adjust($(this.options.containerClass + " .item_wrapper:eq(" + intNumber + ") > div")[0], obDataCont);

          if (selectMode) {
            if (
              $(this.options.containerClass + " .item_wrapper:eq(" + intNumber + ") .list_values_wrapper")
                .parent()
                .hasClass("ik_select")
            )
              $(this.options.containerClass + " .item_wrapper:eq(" + intNumber + ") .list_values_wrapper").ikSelect(
                "reset"
              );
          }
        }
      }
    },

    UpdateSKUInfoByProps: function (offersTree) {
      let arCanBuyValues,
        arShowValues = false,
        strName = "",
        arFilter = {};

      if (typeof window["obSkuQuantys"] == "undefined") window["obSkuQuantys"] = {};

      for (i = 0; i < this.options.depth; i++) {
        strName = "PROP_" + $(this.options.containerClass + " .item_wrapper:eq(" + i + ") > div").data("id");
        if (this.options.selectedValues[strName]) {
          arFilter[strName] = this.options.selectedValues[strName].toString();
        }
      }

      strName =
        "PROP_" + $(this.options.containerClass + " .item_wrapper:eq(" + this.options.depth + ") > div").data("id");
      arShowValues = this.GetRowValues(arFilter, strName, offersTree);

      if (arShowValues && BX.util.in_array(this.options.strPropValue, arShowValues)) {
        if ($(this.options.containerClass).data("selected")) {
          this.options.selectedValues = $(this.options.containerClass).data("selected");
        }

        arFilter[strName] = this.options.strPropValue.toString();
        for (i = ++this.options.depth; i < $(this.options.containerClass + " .item_wrapper").length; i++) {
          strName = "PROP_" + $(this.options.containerClass + " .item_wrapper:eq(" + i + ") > div").data("id");
          arShowValues = this.GetRowValues(arFilter, strName, offersTree);

          if (!arShowValues) break;

          allValues = [];
          if (this.options.showAbsent) {
            arCanBuyValues = [];
            tmpFilter = [];
            tmpFilter = arFilter;

            for (j = 0; j < arShowValues.length; j++) {
              tmpFilter[strName] = arShowValues[j];
              allValues[allValues.length] = arShowValues[j];
              if (this.GetCanBuy(tmpFilter, offersTree)) {
                arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
              }
            }
          } else {
            arCanBuyValues = arShowValues;
          }

          if (
            this.options.selectedValues[strName] &&
            BX.util.in_array(this.options.selectedValues[strName], arCanBuyValues)
          ) {
            arFilter[strName] = this.options.selectedValues[strName].toString();
          } else {
            if (this.options.showAbsent) {
              arFilter[strName] = arCanBuyValues.length ? arCanBuyValues[0] : allValues[0];
            } else {
              arFilter[strName] = arCanBuyValues[0];
            }
          }

          this.UpdateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
        }

        $(this.options.containerClass).data("selected", arFilter);
      }
    },
  };
}
