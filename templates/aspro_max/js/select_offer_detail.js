(function (window) {
  window.appAspro = window.appAspro || {};

  window.appAspro.skuDetailJSLoad = true;

  function setDeliverySKU(th, obj) {
    var buyBlock = th.find(".offer_buy_block"),
      input_value = obj.CONFIG.MIN_QUANTITY_BUY;

    if (buyBlock.find("input[name=quantity]").length) {
      input_value = buyBlock.find("input[name=quantity]").val();
    }

    var $calculate = buyBlock.closest(".catalog_detail").find(".calculate-delivery");
    if ($calculate.length) {
      $calculate.each(function () {
        var $calculateSpan = $(this).find("span[data-event=jqm]").first();

        if ($calculateSpan.length) {
          var $clone = $calculateSpan.clone();
          $clone.attr("data-param-product_id", obj.ID).attr("data-param-quantity", input_value).removeClass("clicked");
          $clone.insertAfter($calculateSpan).on("click", function () {
            $(this).parent().addClass("loadings");
          });
          $calculateSpan.remove();
        }

        if ($(this).hasClass("with_preview")) {
          $(this).removeClass("inited");

          if (this.timerInitCalculateDelivery) {
            clearTimeout(this.timerInitCalculateDelivery);
          }

          var that = this;
          this.timerInitCalculateDelivery = setTimeout(function () {
            initCalculatePreview();
            that.timerInitCalculateDelivery = false;
          }, 1000);
        }
      });

      if (obj.CONFIG.ACTION === "ADD" && obj.CONFIG.CAN_BUY === true) {
        $calculate.show();
      } else {
        $calculate.hide();
      }
    }

    if (th.find(".cheaper_form").length) {
      var cheaper_form = th.find(".cheaper_form span");
      cheaper_form.data("autoload-product_name", obj.NAME);
      cheaper_form.data("autoload-product_id", obj.ID);
    }

    InitFancyBoxVideo();

    if (typeof obMaxPredictions === "object") {
      obMaxPredictions.showAll();
    }
  }

  function UpdatePropsSKU(container, obj) {
    if (!container.find(".js-offers-prop").length) {
      container = container.closest(".product-container");
    }
    const $props = container.find(".js-offers-prop:first");
    if ($props.length) {
      container.find(".js-prop").remove();
      if (obj["DISPLAY_PROPERTIES"]) {
        const maxVisibleProps = $props.data("visible_prop_count") || Infinity;
        let propIterator = container.find(".js-prop-replace").length;

        if (!Object.keys(obj["DISPLAY_PROPERTIES"]).length) {
          checkPropsBlockVisibility(container[0], propIterator, maxVisibleProps);
          return;
        }

        if (!window["propTemplate"]) {
          window["propTemplate"] = container
            .closest(".catalog-items, .container, #fast_view_item")
            .find(".props-template")
            .html();
          if (!window["propTemplate"]) {
            console.error("Unable to find property <template> tag");
            return;
          }
        }

        let html = "";
        for (let key in obj["DISPLAY_PROPERTIES"]) {
          let title = obj["DISPLAY_PROPERTIES"][key]["NAME"];
          let value = obj["DISPLAY_PROPERTIES"][key]["DISPLAY_VALUE"];

          if (obj["DISPLAY_PROPERTIES"][key]["HINT"]) {
            title +=
              ' <div class="hint hint--down">' +
              '<span class="icon colored_theme_hover_bg"><i>?</i></span>' +
              '<div class="tooltip">' +
              obj["DISPLAY_PROPERTIES"][key]["HINT"] +
              "</div>" +
              "</div>";
          }

          let str = window["propTemplate"].replace("#PROP_TITLE#", title).replace("#PROP_VALUE#", value);

          html += str;

          propIterator++;
        }

        checkPropsBlockVisibility(container[0], propIterator, maxVisibleProps);

        if (html) {
          $props[0].insertAdjacentHTML("beforeend", html);
        }
      }
    }
  }

  function checkPropsBlockVisibility(wrapper, propIterator, maxVisibleProps) {
    const nodePropsContainer = wrapper.querySelectorAll(".char-toggle-visible");
    if (nodePropsContainer.length > 0) {
      nodePropsContainer.forEach((node) => node.classList.toggle("hidden", !propIterator));
    }
  }

  function UpdateGroupPropsSKU(wrapper, obj) {
    let container = wrapper.closest(".product-container");
    const $props = container.find(".js-offers-group-wrap:first");
    if ($props.length) {
      container.find(".js-offers-group").remove();
      container.find(".js-offers-group__item").remove();
      if (obj["DISPLAY_PROPERTIES"] && obj["PROPS_GROUP_HTML"]) {
        if (!Object.keys(obj["DISPLAY_PROPERTIES"]).length) {
          return;
        }
        let tmpDiv = document.createElement("div");
        tmpDiv.innerHTML = obj["PROPS_GROUP_HTML"];
        let offerPropGroups = tmpDiv.querySelectorAll(".js-offers-group");
        let noGroupContainer = container.find("[data-group-code='no-group']");
        if (offerPropGroups.length) {
          let groupCode, elementPropGroup;
          for (let keyGroup = 0; keyGroup < offerPropGroups.length; keyGroup++) {
            groupCode = offerPropGroups[keyGroup].getAttribute("data-group-code");
            elementPropGroup = container.find("[data-group-code='" + groupCode + "'] .js-offers-group__items-wrap");
            if (elementPropGroup.length) {
              elementPropGroup.append(offerPropGroups[keyGroup].querySelectorAll(".js-offers-group__item"));
            } else {
              if (noGroupContainer.length) {
                noGroupContainer.before(offerPropGroups[keyGroup]);
              } else {
                $props.append(offerPropGroups[keyGroup]);
              }
            }
          }
        }
      }
    }
  }

  function SetSliderPictSKU(wrapper, obj) {
    let detailContainer = wrapper.closest(".catalog_detail"),
      mainGalleryContainer = detailContainer.find(".detail-gallery-big");
    ($gallery = detailContainer.find(".detail-gallery-big-slider:not(.detail-gallery-big-slider--magnifier)")),
      ($galleryThumbWrap = detailContainer.find(".detail-gallery-big-slider-thumbs__inner")),
      ($galleryThumb = detailContainer.find(".gallery-slider-thumb"));

    if ($gallery.length && obj["GALLERY"]) {
      const mainSlider = $gallery.data("swiper");
      const countPhoto = obj["GALLERY"].length;
      const mainSlideClassList = $gallery.data("slideClassList");
      const slideHtml = [];
      const thumbsSlider = $galleryThumb.data("swiper");
      const thumbsSlideClassList = $galleryThumb.data("slideClassList");
      const slideThmbHtml = [];
      const bFastview = !!wrapper.closest(".fast_view_frame").length;

      if (countPhoto > 0) {
        for (let i in obj["GALLERY"]) {
          const image = obj["GALLERY"][i];
          const title = image["TITLE"] ? image["TITLE"] : obj["NAME"];
          const alt = image["ALT"] ? image["ALT"] : obj["NAME"];
          if (typeof image === "object") {
            slideHtml.push(
              '<div id="photo-' +
                i +
                '" class="' +
                mainSlideClassList +
                '">' +
                '<a href="' +
                obj.GALLERY[i].BIG.src +
                '" data-fancybox="gallery' +
                (bFastview ? "_fast" : "") +
                '" class="detail-gallery-big__link popup_link fancy" title="' +
                title +
                '">' +
                '<img class="detail-gallery-big__picture rounded3" src="' +
                obj.GALLERY[i].SMALL.src +
                '" alt="' +
                alt +
                '" title="' +
                title +
                '" />' +
                "</a>" +
                "</div>"
            );
          }
        }

        if ($galleryThumb.length) {
          if (countPhoto > 1) {
            for (let i in obj["GALLERY"]) {
              const image = obj["GALLERY"][i];
              const title = image["TITLE"] ? image["TITLE"] : obj["NAME"];
              const alt = image["ALT"] ? image["ALT"] : obj["NAME"];

              if (typeof image === "object") {
                slideThmbHtml.push(
                  '<div id="thumb-photo-' +
                    i +
                    '" class="' +
                    (i == 0 ? "swiper-slide-thumb-active " : "") +
                    thumbsSlideClassList +
                    '"' +
                    'data-big="' +
                    obj.GALLERY[i].BIG.src +
                    '">' +
                    '<img class="gallery__picture" src="' +
                    image.SMALL.src +
                    '" alt="' +
                    alt +
                    '" title="' +
                    title +
                    '" data-xoriginalwidth="' +
                    image.BIG.width +
                    '" data-xoriginalheight="' +
                    image.BIG.height +
                    '" /> ' +
                    "</div>"
                );
              }
            }
          }
          $galleryThumb.attr("data-size", countPhoto);
        }
      } else {
        slideHtml.push(
          '<div class="detail-gallery-big__item detail-gallery-big__item--big detail-gallery-big__item--no-image swiper-slide">' +
            "<span " +
            ' class="detail-gallery-big__link" >' +
            '<img class="detail-gallery-big__picture one rounded3" src="' +
            appAspro.sku.mainItemForOffers.NO_PHOTO +
            '" alt="' +
            obj.NAME +
            '" title="' +
            obj.NAME +
            '" />' +
            "</span>" +
            "</div>"
        );
      }

      //set image for send_gift
      if (countPhoto) {
        let newOfferImage = obj.GALLERY[0]["BIG"]["src"] ? obj.GALLERY[0]["BIG"]["src"] : obj.GALLERY[0]["SRC"];
        detailContainer.find('link[itemprop="image"]').attr("href", newOfferImage);
      }

      if (arAsproOptions["THEME"]["DETAIL_PICTURE_MODE"] == "MAGNIFIER") {
        var pict = "";

        if (obj.GALLERY && obj.GALLERY[0]) {
          pict =
            '<a href="javascript:void(0)" class="detail-gallery-big__link fancy_zoom line_link" title="' +
            obj.GALLERY[0].TITLE +
            '">' +
            '<img class="detail-gallery-big__picture rounded3 zoom_picture" border="0" src="' +
            obj.GALLERY[0].SMALL.src +
            '" alt="' +
            obj.GALLERY[0].ALT +
            '" title="' +
            obj.GALLERY[0].TITLE +
            '" data-xoriginal="' +
            obj.GALLERY[0].BIG.src +
            '" data-xoriginalwidth="' +
            obj.GALLERY[0].BIG.width +
            '" data-xoriginalheight="' +
            obj.GALLERY[0].BIG.height +
            '"/>' +
            "</a>";
        } else {
          pict =
            '<span class="detail-gallery-big__link line_link" >' +
            '<img class="detail-gallery-big__picture one" border="0" src="' +
            appAspro.sku.mainItemForOffers.NO_PHOTO +
            '" alt="' +
            obj.NAME +
            '" title="' +
            obj.NAME +
            '" data-xoriginal2="' +
            appAspro.sku.mainItemForOffers.NO_PHOTO +
            '"/>' +
            "</span>";
        }

        let xzoomPict = detailContainer.find(".line_link");
        if (xzoomPict.length) {
          xzoomPict.parent().html(pict);
        }
        InitZoomPict();
      }

      if (mainSlider !== undefined) {
        mainSlider.removeAllSlides();
        mainSlider.appendSlide(slideHtml);
        mainSlider.update();
      }

      if (thumbsSlider !== undefined) {
        thumbsSlider.removeAllSlides();
        thumbsSlider.appendSlide(slideThmbHtml);
        thumbsSlider.update();
      }

      detailContainer.find(".popup_video").remove();
      let popUpVideo = obj.POPUP_VIDEO ? obj.POPUP_VIDEO : appAspro.sku.mainItemForOffers.POPUP_VIDEO,
        bWithVideo = false;

      if (popUpVideo) {
        let popupHtml =
          '<div class="video-block popup_video ' +
          ' sm"><a class="various video_link image dark_link" href="' +
          popUpVideo +
          '" title="' +
          BX.message("POPUP_VIDEO") +
          '"><span class="play text-upper font_xs">' +
          /*BX.message("POPUP_VIDEO") +*/
          "</span></a></div>";
        const $stickersContainer = detailContainer.find(".detail-stickers-wrap");
        if ($stickersContainer.length) {
          $(popupHtml).appendTo($stickersContainer);
          bWithVideo = true;
        }
      }

      if (bWithVideo) {
        mainGalleryContainer.addClass("detail-gallery-big--with-video");
      } else {
        mainGalleryContainer.removeClass("detail-gallery-big--with-video");
      }

      InitFancyBox();
      InitFancyBoxVideo();
    }
  }

  function SetArticleSKU(wrapper, obj) {
    wrapper = wrapper.closest(".catalog_detail");
    let articleBlock = wrapper.find(".product-info-headnote__article .article");
    if (articleBlock) {
      var article_text = obj.ARTICLE ? obj.ARTICLE : "";
      if (!article_text && obj.SHOW_ARTICLE_SKU == "Y" && appAspro.sku.mainItemForOffers.ARTICLE) {
        article_text = appAspro.sku.mainItemForOffers.ARTICLE;
      }
      if (articleBlock.find(".article__value").length) {
        articleBlock.find(".article__value").text(article_text);
      }
      if (article_text) {
        articleBlock.show();
      } else {
        articleBlock.hide();
      }
      let article_name = obj.ARTICLE_NAME ? obj.ARTICLE_NAME : "";
      if (article_name && articleBlock.find(".article__title").length) {
        articleBlock.find(".article__title").text(article_name + ":");
      }
    }
  }

  function SetHrefSKU(wrapper, obj) {
    let titleHref = wrapper.find(".fast-view-title"),
      bottomHref = wrapper.find(".bottom-href-fast-view");
    if (titleHref.length) {
      titleHref.attr("href", obj.URL);
    }
    if (bottomHref.length) {
      bottomHref.attr("href", obj.URL);
    }
  }

  function SetServicesSKU(wrapper, obj) {
    var servWrap = wrapper.closest(".product-container").find(".buy_services_wrap");
    if (servWrap.length) {
      servWrap.attr("data-parent_product", obj["ID"]);
      servWrap.find(".services-item").each(function () {
        var serviceItem = $(this);
        var basketItem = arBasketAspro.SERVICES[serviceItem.attr("data-item_id") + "_" + obj["ID"]];

        if (typeof basketItem != "undefined" && basketItem["basket_id"]) {
          serviceItem.find('input[name="buy_switch_services"]').prop("checked", true);
          serviceItem.find('.counter_block input[name="quantity"]').val(basketItem.quantity);
          serviceItem.addClass("services_on");
        } else {
          serviceItem.removeClass("services_on");
          serviceItem.find('input[name="buy_switch_services"]').prop("checked", false);
        }
      });
    }
  }

  function SetTitleSKU(wrapper, obj) {
    if (arMaxOptions["THEME"]["CHANGE_TITLE_ITEM_DETAIL"] == "Y") {
      var skuName =
        typeof obj.IPROPERTY_VALUES === "object" && obj.IPROPERTY_VALUES.ELEMENT_PAGE_TITLE
          ? obj.IPROPERTY_VALUES.ELEMENT_PAGE_TITLE
          : obj.NAME;
      var skuWindowTitle =
        typeof obj.IPROPERTY_VALUES === "object" && obj.IPROPERTY_VALUES.ELEMENT_META_TITLE
          ? obj.IPROPERTY_VALUES.ELEMENT_META_TITLE
          : obj.NAME;
      var bFastView = wrapper.find(".fastview-product").length;
      if (bFastView) {
        wrapper.find(".fast-view-title").html(skuName);
      } else {
        $("h1").html(skuName);
        document.title = skuWindowTitle + "" + appAspro.sku.mainItemForOffers.POSTFIX;
        if (typeof ItemObj == "object") {
          ItemObj.TITLE = skuName;
          ItemObj.WINDOW_TITLE = skuWindowTitle;
        }
      }
    }
    $('.catalog_detail input[data-sid="PRODUCT_NAME"]').attr("value", $("h1").text());
  }

  function setDescriptionSKU(wrapper, obj) {
    var block = wrapper,
      detailTextBlockWrapper = block.closest(".product-container").find(".detail-text-wrap .ordered-block__text-wrap"),
      detailTextBlock = detailTextBlockWrapper.find(".ordered-block__text"),
      spoilerButton = detailTextBlockWrapper.find(".ordered-block__spoiler-btn"),
      previewTextBlock = block.find(".preview-text-replace");
    if (detailTextBlock.length && appAspro.sku.mainItemForOffers.SHOW_SKU_DESCRIPTION) {
      if (obj.DETAIL_TEXT.length) {
        detailTextBlock.html(obj.DETAIL_TEXT);
      } else if (appAspro.sku.mainItemForOffers.DETAIL_TEXT.length) {
        detailTextBlock.html(appAspro.sku.mainItemForOffers.DETAIL_TEXT);
      }

      if (typeof initCollapsedBlock === "function") {
        if (detailTextBlock.hasClass("opened")) {
          detailTextBlock.addClass("lineclamp-3");
          detailTextBlock.removeClass("opened");
          spoilerButton.removeClass("show");
        }

        showCollapsedBlock(detailTextBlock, spoilerButton);
      }
    }
    if (previewTextBlock.length && appAspro.sku.mainItemForOffers.SHOW_SKU_DESCRIPTION) {
      if (obj.PREVIEW_TEXT.length) {
        previewTextBlock.html(obj.PREVIEW_TEXT);
      } else if (appAspro.sku.mainItemForOffers.PREVIEW_TEXT.length) {
        previewTextBlock.html(appAspro.sku.mainItemForOffers.PREVIEW_TEXT);
      }
    }
  }

  function setOfferSetSKU(wrapper, obj) {
    var mainWrap = wrapper.closest(".product-container"),
      offerSet = mainWrap.find("[data-offerSetId]"),
      currentOfferSet = mainWrap.find("[data-offerSetId=" + obj["ID"] + "]");
    if (offerSet.length) {
      offerSet.hide();
    }
    if (offerSet.length) {
      currentOfferSet.show();
    }
  }

  function SetViewedSKU(obj) {
    var arPriceItem = obj.MIN_PRICE;

    setViewedProduct(obj.ID, {
      PRODUCT_ID: appAspro.sku.mainItemForOffers.PRODUCT_ID,
      IBLOCK_ID: obj.IBLOCK_ID,
      NAME: obj.NAME,
      DETAIL_PAGE_URL: obj.URL,
      PICTURE_ID: obj.PREVIEW_PICTURE_FIELD
        ? obj.PREVIEW_PICTURE_FIELD.ID
        : obj.PARENT_PICTURE
        ? obj.PARENT_PICTURE.ID
        : obj.GALLERY.length
        ? obj.GALLERY[0].ID
        : false,
      CATALOG_MEASURE_NAME: obj.MEASURE,
      MIN_PRICE: arPriceItem,
      CAN_BUY: obj.CONFIG.CAN_BUY ? "Y" : "N",
      IS_OFFER: "Y",
      WITH_OFFERS: "N",
    });
  }

  function SetAdditionalGallerySKU(wrapper, obj) {
    var $gallery = wrapper.closest(".product-container").find(".additional-gallery");

    if (window.scrollTabsTimeout !== undefined) {
      clearTimeout(window.scrollTabsTimeout);
    }
    if (typeof ResizeScrollTabs === "function") {
      window.scrollTabsTimeout = setTimeout(ResizeScrollTabs, 20);
    }

    if ($gallery.length) {
      var bHidden = $gallery.hasClass("hidden"),
        bigGallery = $gallery.find(".big-gallery-block .owl-carousel"),
        smallGallery = $gallery.find(".small-gallery-block .row"),
        slideBigHtml = (slideSmallHtml = ""),
        descTab = $('.nav.nav-tabs [href="#desc"]').closest("li");

      var galleryItems = obj.ADDITIONAL_GALLERY.concat(appAspro.sku.mainItemForOffers.ADDITIONAL_GALLERY);

      if (galleryItems.length) {
        if (bHidden) {
          $gallery.removeClass("hidden");
          bigGallery.removeClass("owl-hidden");
          descTab.removeClass("hidden");
        }

        $gallery
          .find(".switch-item-block .switch-item-block__count-wrapper--small .switch-item-block__count-value")
          .text(galleryItems.length);
        $gallery
          .find(".switch-item-block .switch-item-block__count-wrapper--big .switch-item-block__count-value")
          .text(1 + "/" + galleryItems.length);

        for (var i in galleryItems) {
          if (typeof galleryItems[i] == "object") {
            slideBigHtml +=
              '<div class="item">' +
              '<a href="' +
              galleryItems[i].DETAIL.SRC +
              '" data-fancybox="big-gallery" class="fancy"><img class="picture" border="0" src="' +
              galleryItems[i].PREVIEW.src +
              '" alt="' +
              galleryItems[i].ALT +
              '" title="' +
              galleryItems[i].TITLE +
              '" /></a>' +
              "</div>";

            slideSmallHtml +=
              '<div class="col-md-3"><div class="item">' +
              '<a href="' +
              galleryItems[i].DETAIL.SRC +
              '" data-fancybox="small-gallery" class="fancy"><img class="picture" border="0" src="' +
              galleryItems[i].PREVIEW.src +
              '" alt="' +
              galleryItems[i].ALT +
              '" title="' +
              galleryItems[i].TITLE +
              '" /></a>' +
              "</div></div>";
          }
        }

        bigGallery.html(slideBigHtml);
        smallGallery.html(slideSmallHtml);

        if (bigGallery.data("owl.carousel") !== undefined) bigGallery.data("owl.carousel").destroy();

        InitOwlSlider();
        InitFancyBox();
        typeof ResizeScrollTabs === "function" && ResizeScrollTabs();
      } else {
        $gallery.addClass("hidden");
        if (
          $gallery.closest(".tab-pane").find(".ordered-block").length <= 1 &&
          $gallery.closest(".tab-pane").find(".detail-text-wrap").length === 0
        ) {
          descTab.addClass("hidden");
          if (typeof SetActiveTab === "function") {
            SetActiveTab($(wrapper.closest(".product-container").find(".tabs > .nav-tabs > li")));
          }
          typeof ResizeScrollTabs === "function" && ResizeScrollTabs();
        }
      }
    }
  }

  BX.addCustomEvent("onFinalActionSKUInfo", function (eventdata) {
    if (eventdata) {
      let wrapper = eventdata.wrapper;
      let objOffer = eventdata.offer;
      let isDetail = wrapper.hasClass("product-main");
      let isFastView = wrapper.find(".fastview-product").length;

      if (isDetail) {
        if (appAspro.sku.mainItemForOffers.hasOwnProperty("OID") && appAspro.sku.mainItemForOffers.OID && !isFastView) {
          setLocationSKU(objOffer.ID, appAspro.sku.mainItemForOffers.OID);
        }
        setDescriptionSKU(wrapper, objOffer);
        setDeliverySKU(wrapper, objOffer);
        UpdatePropsSKU(wrapper, objOffer);
        UpdateGroupPropsSKU(wrapper, objOffer);
        SetSliderPictSKU(wrapper, objOffer);
        SetArticleSKU(wrapper, objOffer);
        SetServicesSKU(wrapper, objOffer);
        SetTitleSKU(wrapper, objOffer);
        SetViewedSKU(objOffer);
        SetAdditionalGallerySKU(wrapper, objOffer);
        setOfferSetSKU(wrapper, objOffer);
        if (typeof setNewHeader === "function" && arMaxOptions["THEME"]["SHOW_HEADER_GOODS"] === "Y") {
          setNewHeader(objOffer);
        }
        if (isFastView) {
          SetHrefSKU(wrapper, objOffer);
        }
      }
    }
  });
})(window);
