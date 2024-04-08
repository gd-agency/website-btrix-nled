var basketTimeoutSlide;
var resizeEventTimer;

var funcDefined = function (func) {
  try {
    if (typeof func == "function") {
      return true;
    } else {
      return typeof window[func] === "function";
    }
  } catch (e) {
    return false;
  }
};

if (!funcDefined("setLocationSKU")) {
  function setLocationSKU(ID, offerID) {
    if (offerID === undefined) offerID = "oid";
    if (offerID) {
      var objUrl = parseUrlQuery(),
        j = 0,
        prefix = "",
        query_string = "",
        url = "";
      objUrl[offerID] = ID;
      for (var i in objUrl) {
        if (parseInt(j) > 0) {
          prefix = "&";
        }
        query_string += prefix + i + "=" + objUrl[i];
        j++;
      }
      if (query_string) {
        url = location.pathname + "?" + query_string;
      }
      try {
        history.replaceState(null, null, url);
        return;
      } catch (e) {}
      location.hash = "#" + url.substr(1);
    }
  }
}

/*sku change props*/
if (!("SelectOfferProp" in window) && typeof window.SelectOfferProp != "function") {
  SelectOfferProp = function () {
    // return false;
    var _this = $(this),
      obParams = {},
      obSelect = {},
      objUrl = parseUrlQuery(),
      add_url = "",
      selectMode = !_this.hasClass("item") ? true : false,
      container = _this.closest(".bx_catalog_item_scu"),
      img = _this.closest(".item-parent").find(".thumb img");

    /* request params */
    obParams = {
      PARAMS: _this.closest(".js-wrapper-block").data("params"),
      ID: container.data("offer_id"),
      SITE_ID: container.data("site_id"),
      LINK_ID: container.data("id") + "_" + _this.closest(".cur").data("code"),
      IBLOCK_ID: container.data("offer_iblockid"),
      PROPERTY_ID: container.data("propertyid"),
      DEPTH: _this.closest(".item_wrapper").index(),
      VALUE: selectMode ? _this.find("option:selected").data("onevalue") : _this.data("onevalue"),
      CLASS: "inner_content",
      PICTURE: img.data("src") ? img.data("src") : img.attr("src"),
      ARTICLE_NAME: _this.closest(".item_wrapper").find(".article_block").data("name"),
      ARTICLE_VALUE: _this.closest(".item_wrapper").find(".article_block").data("value"),
    };
    /**/

    if ("clear_cache" in objUrl) {
      if (objUrl.clear_cache == "Y") add_url += "?clear_cache=Y";
    }

    /* save selected values */
    for (i = 0; i < obParams.DEPTH + 1; i++) {
      strName = "PROP_" + container.find(".item_wrapper:eq(" + i + ") > div").data("id");
      if (container.find(".item_wrapper:eq(" + i + ") select").length) {
        obSelect[strName] = container.find(".item_wrapper:eq(" + i + ") select option:selected").data("onevalue");
        obParams[strName] = container.find(".item_wrapper:eq(" + i + ") select option:selected").data("onevalue");

        if (container.find(".item_wrapper:eq(" + i + ") select option:selected").attr("title")) {
          _this
            .closest(".item_wrapper")
            .find(".show_class")
            .text(container.find(".item_wrapper:eq(" + i + ") select option:selected").attr("title"));
        }
      } else {
        obSelect[strName] = container.find(".item_wrapper:eq(" + i + ") li.item.active").data("onevalue");
        obParams[strName] = container.find(".item_wrapper:eq(" + i + ") li.item.active").data("onevalue");
      }
    }
    // obParams.SELECTED = JSON.stringify(obSelect);
    /**/

    if (!selectMode) {
      _this.siblings().removeClass("active");
      _this.addClass("active");
    }

    setPropertyTitle(_this);

    /* get sku */
    $.ajax({
      url: arOptimusOptions["SITE_DIR"] + "ajax/js_item_detail.php" + add_url,
      type: "POST",
      data: obParams,
    }).success(function (html) {
      var ob = BX.processHTML(html);
      BX.ajax.processScripts(ob.SCRIPT);
      // if (funcDefined("sliceItemBlock")) {
      //   sliceItemBlock({ resize: false });
      // }

      if ($(".counter_wrapp.list")) {
        $(".counter_wrapp.list .counter_block.big").removeClass("big");
      }
    });
  };
  $(document).on("click", ".ajax_load .bx_catalog_item_scu li.item:not(.active)", SelectOfferProp);
  $(document).on("change", ".ajax_load .bx_catalog_item_scu select", SelectOfferProp);
}

if (!funcDefined("setPropertyTitle")) {
  var setPropertyTitle = function(ctx) {
    if (ctx.attr("title")) {
      const skuVal = ctx.attr("title");
      ctx.closest(".item_wrapper").find(".show_class").text(skuVal);
    } else {
      const img_row = ctx.find(" > i");

      if (img_row.length && img_row.attr("title")) {
        const skuVal = img_row.attr("title");
        ctx.closest(".item_wrapper").find(".show_class").text(skuVal);
      }
    }
  }
}

if (!funcDefined("trimPrice")) {
  var trimPrice = function trimPrice(s) {
    s = s.split(" ").join("");
    s = s.split("&nbsp;").join("");
    return s;
  };
}

if (!funcDefined("markProductRemoveBasket")) {
  var markProductRemoveBasket = function markProductRemoveBasket(id) {
    $(".in-cart[data-item=" + id + "]").hide();
    $(".to-cart[data-item=" + id + "]").show();
    $(".to-cart[data-item=" + id + "]")
      .closest(".button_block")
      .removeClass("wide");
    $(".to-cart[data-item=" + id + "]")
      .closest(".counter_wrapp")
      .find(".counter_block")
      .show();
    $(".counter_block[data-item=" + id + "]").show();
    $(".in-subscribe[data-item=" + id + "]").hide();
    $(".to-subscribe[data-item=" + id + "]").show();
    $(".wish_item[data-item=" + id + "]").removeClass("added");
    $(".wish_item[data-item=" + id + "] .value:not(.added)").show();
    $(".wish_item[data-item=" + id + "] .value.added").hide();
  };
}

if (!funcDefined("markProductAddBasket")) {
  var markProductAddBasket = function markProductAddBasket(id) {
    $(".to-cart[data-item=" + id + "]").hide();
    $(".to-cart[data-item=" + id + "]")
      .closest(".counter_wrapp")
      .find(".counter_block")
      .hide();
    $(".to-cart[data-item=" + id + "]")
      .closest(".button_block")
      .addClass("wide");
    $(".in-cart[data-item=" + id + "]").show();
    $(".wish_item[data-item=" + id + "]").removeClass("added");
    $(".wish_item[data-item=" + id + "] .value:not(.added)").show();
    $(".wish_item[data-item=" + id + "] .value.added").hide();
  };
}

if (!funcDefined("markProductDelay")) {
  var markProductDelay = function markProductDelay(id) {
    $(".in-cart[data-item=" + id + "]").hide();
    $(".to-cart[data-item=" + id + "]").show();
    $(".to-cart[data-item=" + id + "]")
      .closest(".counter_wrapp")
      .find(".counter_block")
      .show();
    $(".to-cart[data-item=" + id + "]")
      .closest(".button_block")
      .removeClass("wide");
    $(".wish_item[data-item=" + id + "]").addClass("added");
    $(".wish_item[data-item=" + id + "] .value:not(.added)").hide();
    // $('.wish_item[data-item='+id+'] .value.added').show();
    $(".wish_item[data-item=" + id + "] .value.added").css("display", "block");
  };
}

if (!funcDefined("markProductSubscribe")) {
  var markProductSubscribe = function markProductSubscribe(id) {
    $(".to-subscribe[data-item=" + id + "]").hide();
    $(".in-subscribe[data-item=" + id + "]").css("display", "block");
  };
}

if (!funcDefined("basketFly")) {
  var basketFly = function basketFly(action) {
    if (typeof obOptimusPredictions === "object") {
      obOptimusPredictions.updateAll();
    }

    $.post(
      arOptimusOptions["SITE_DIR"] + "ajax/basket_fly.php",
      "PARAMS=" + $("#basket_form").find("input#fly_basket_params").val(),
      $.proxy(function (data) {
        var small = $(".opener .basket_count").hasClass("small"),
          basket_count = $(data).find(".basket_count").find(".items div").text();
        $("#basket_line .basket_fly").addClass("loaded").html(data);

        if (action == "refresh") {
          $("li[data-type=AnDelCanBuy]").trigger("click");
        }

        if (window.matchMedia("(min-width: 769px)").matches) {
          if (action == "open") {
            if (small) {
              if (arOptimusOptions["THEME"]["SHOW_BASKET_ONADDTOCART"] !== "N") {
                if (!$('.basket_fly').hasClass('opened-once')) {
                  $(".opener .basket_count").click();
                  $('.basket_fly').addClass('opened-once');
                }
              }
            } else {
              $(".opener .basket_count").removeClass("small");
              $('.tabs_content.basket li[item-section="AnDelCanBuy"]').addClass("cur");
              $('#basket_line ul.tabs li[item-section="AnDelCanBuy"]').addClass("cur");
            }
          } else if (action == "wish") {
            if (small) {
              if (arOptimusOptions["THEME"]["SHOW_BASKET_ONADDTOCART"] !== "N") {
                $(".opener .wish_count").click();
              }
            } else {
              $(".opener .wish_count").removeClass("small");
              $('.tabs_content.basket li[item-section="DelDelCanBuy"]').addClass("cur");
              $('#basket_line ul.tabs li[item-section="DelDelCanBuy"]').addClass("cur");
            }
          } else {
            if (arOptimusOptions["THEME"]["SHOW_BASKET_ONADDTOCART"] !== "N") {
              $(".opener .basket_count").click();
            }
          }
        }
      })
    );
  };
}

if (!funcDefined("clearViewedProduct")) {
  function clearViewedProduct() {
    try {
      var siteID = arOptimusOptions.SITE_ID;
      var localKey = "OPTIMUS_VIEWED_ITEMS_" + siteID;
      var cookieParams = { path: "/", expires: 30 };
      if (typeof BX.localStorage !== "undefined") {
        // remove local storage
        BX.localStorage.set(localKey, {}, 0);
      }
      // remove cookie
      $.removeCookie(localKey, cookieParams);
    } catch (e) {
      console.error(e);
    }
  }
}

if (!funcDefined("setViewedProduct")) {
  function setViewedProduct(id, arData) {
    try {
      // save $.cookie option
      var bCookieJson = $.cookie.json;
      $.cookie.json = true;

      var siteID = arOptimusOptions.SITE_ID;
      var localKey = "OPTIMUS_VIEWED_ITEMS_" + siteID;
      var cookieParams = { path: "/", expires: 30 };

      if (typeof BX.localStorage !== "undefined" && typeof id !== "undefined" && typeof arData !== "undefined") {
        var PRODUCT_ID = typeof arData.PRODUCT_ID !== "undefined" ? arData.PRODUCT_ID : id;
        var arViewedLocal = BX.localStorage.get(localKey) ? BX.localStorage.get(localKey) : {};
        var arViewedCookie = $.cookie(localKey) ? $.cookie(localKey) : {};
        var count = 0;

        // delete some items (sync cookie & local storage)
        for (var _id in arViewedLocal) {
          arViewedLocal[_id].IS_LAST = false;
          if (typeof arViewedCookie[_id] === "undefined") {
            delete arViewedLocal[_id];
          }
        }
        for (var _id in arViewedCookie) {
          if (typeof arViewedLocal[_id] === "undefined") {
            delete arViewedCookie[_id];
          }
        }

        for (var _id in arViewedCookie) {
          count++;
        }

        // delete item if other item (offer) of that PRODUCT_ID is exists
        if (typeof arViewedLocal[PRODUCT_ID] !== "undefined") {
          if (arViewedLocal[PRODUCT_ID].ID != id) {
            delete arViewedLocal[PRODUCT_ID];
            delete arViewedCookie[PRODUCT_ID];
          }
        }

        delete arViewedLocal[2243];
        delete arViewedCookie[2243];

        var time = new Date().getTime();
        arData.ID = id;
        arData.ACTIVE_FROM = time;
        arData.IS_LAST = true;
        arViewedLocal[PRODUCT_ID] = arData;
        arViewedCookie[PRODUCT_ID] = [time.toString(), arData.PICTURE_ID];

        $.cookie(localKey, arViewedCookie, cookieParams);
        BX.localStorage.set(localKey, arViewedLocal, 2592000); // 30 days
      }
    } catch (e) {
      console.error(e);
    } finally {
      // restore $.cookie option
      $.cookie.json = bCookieJson;
    }
  }
}

if (!funcDefined("initSelects")) {
  function initSelects(target) {
    var iOS = navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false;
    if (iOS) return;
    if ($("#bx-soa-order").length) return;
    // SELECT STYLING
    $(target)
      .find(".wrapper select:visible")
      .ikSelect({
        syntax:
          '<div class="ik_select_link"> \
						<span class="ik_select_link_text"></span> \
						<div class="trigger"></div> \
					</div> \
					<div class="ik_select_dropdown"> \
						<div class="ik_select_list"> \
						</div> \
					</div>',
        dynamicWidth: true,
        ddMaxHeight: 112,
        customClass: "common_select",
        //equalWidths: true,
        onShow: function (inst) {
          inst.$dropdown.css("top", parseFloat(inst.$dropdown.css("top")) - 5 + "px");
          if (inst.$dropdown.outerWidth() < inst.$link.outerWidth()) {
            inst.$dropdown.css("width", inst.$link.outerWidth());
          }
          if (inst.$dropdown.outerWidth() > inst.$link.outerWidth()) {
            inst.$dropdown.css("width", inst.$link.outerWidth());
          }
          var count = 0,
            client_height = 0;
          inst.$dropdown.css("left", inst.$link.offset().left);
          $(inst.$listInnerUl)
            .find("li")
            .each(function () {
              if (!$(this).hasClass("ik_select_option_disabled")) {
                ++count;
                client_height += $(this).outerHeight();
              }
            });
          if (client_height < 112) {
            inst.$listInner.css("height", "auto");
          } else {
            inst.$listInner.css("height", "112px");
          }
          inst.$link.addClass("opened");
          inst.$listInner.addClass("scroller");
        },
        onHide: function (inst) {
          inst.$link.removeClass("opened");
        },
      });
    // END OF SELECT STYLING

    var timeout;
    $(window).on("resize", function () {
      clearTimeout(timeout);
      timeout = setTimeout(function () {
        //$('select:visible').ikSelect('redraw');
        var inst = "";
        if ((inst = $(".common_select-link.opened + select").ikSelect().data("plugin_ikSelect"))) {
          inst.$dropdown.css("left", inst.$link.offset().left + "px");
        }
      }, 20);
    });
  }
}

if (!funcDefined("CheckTopMenuFullCatalogSubmenu")) {
  CheckTopMenuFullCatalogSubmenu = function () {
    if (arOptimusOptions["THEME"]["MENU_TYPE_VIEW"] != "hover") return;
    var $menu = $(".menu_top_block");
    var $wrapmenu = $menu.parents(".wrap_menu");
    var wrapMenuWidth = $wrapmenu.actual("outerWidth");
    var wrapMenuLeft = $wrapmenu.offset().left;
    var wrapMenuRight = wrapMenuLeft + wrapMenuWidth;
    var bCatalogFirst = $menu.hasClass("catalogfirst");
    var findMenuLi = $(".menu_top_block:visible li.full");
    var parentSubmenuWidth = $menu.actual("outerWidth");

    if ($(".catalog_block.menu_top_block").length) {
      if ($(".catalog_block.menu_top_block").is(":visible")) findMenuLi = $(".menu_top_block.catalog_block li.full");
    }

    findMenuLi.each(function () {
      var $this = $(this);
      var $submenu = $this.find(">.dropdown");

      if ($submenu.length) {
        if (bCatalogFirst) {
          $submenu.css({
            left: parentSubmenuWidth + "px",
            width: wrapMenuWidth - parentSubmenuWidth + "px",
            "padding-left": "0px",
            "padding-right": "0px",
          });
        } else {
          $submenu.css({
            left: $this.offset().left * -1 + "px",
            width: $(window).width() - 1 + "px",
            "padding-left": wrapMenuLeft + "px",
            "padding-right": $(window).width() - wrapMenuRight + "px",
          });
        }
        if (!isOnceInited && bCatalogFirst && arOptimusOptions["THEME"]["MENU_POSITION"] == "top") {
          $this.on("mouseenter", function () {
            $submenu.css("min-height", $this.closest(".dropdown").actual("outerHeight") + "px");
          });
        }
      }
    });
  };
}

$.fn.getMaxHeights = function (outer, classNull, minHeight) {
  var maxHeight = this.map(function (i, e) {
    var calc_height = 0;

    $(e).css("height", "");

    if (outer == true) {
      calc_height = $(e).actual("outerHeight");
    } else {
      calc_height = $(e).actual("height");
    }
    return calc_height;
  }).get();
  for (var i = 0, c = maxHeight.length; i < c; ++i) {
    if (maxHeight[i] % 2) {
      --maxHeight[i];
    }
  }
  return Math.max.apply(this, maxHeight);
};

$.fn.equalizeHeights = function (outer, classNull, minHeight) {
  var maxHeight = this.map(function (i, e) {
    var minus_height = 0,
      calc_height = 0;
    if (classNull !== false) {
      if (!isMobile) {
        minus_height = parseInt($(e).find(classNull).actual("outerHeight"));
      }
    }
    if (minus_height) minus_height += 12;
    $(e).css("height", "");
    if (outer == true) {
      calc_height = $(e).actual("outerHeight") - minus_height;
    } else {
      calc_height = $(e).actual("height") - minus_height;
    }
    if (minHeight !== false) {
      if (calc_height < minHeight) {
        calc_height += minHeight - calc_height;
      }
      if (window.matchMedia("(max-width: 520px)").matches) {
        calc_height = 300;
      }
      if (window.matchMedia("(max-width: 400px)").matches) {
        calc_height = 200;
      }
    }
    return calc_height;
  }).get();

  for (var i = 0, c = maxHeight.length; i < c; ++i) {
    if (maxHeight[i] % 2) {
      --maxHeight[i];
    }
  }
  return this.height(Math.max.apply(this, maxHeight));
};

$.fn.getFloatWidth = function () {
  var width = 0;

  if ($(this).length) {
    var rect = $(this)[0].getBoundingClientRect();
    if (!(width = rect.width)) {
      width = rect.right - rect.left;
    }
  }

  return width;
};

$.fn.sliceHeight = function (options) {
  function _slice(el) {
    el.each(function () {
      $(this).css("line-height", "");
      $(this).css("height", "");
    });

    if (options.mobile == true) {
      if (window.matchMedia("(max-width: 520px)").matches) {
        return;
      }
    }

    if (typeof options.autoslicecount == "undefined" || options.autoslicecount !== false) {
      var row =
          typeof options.row !== "undefined" && options.row.length
            ? el.first().parents(options.row).getFloatWidth()
            : el.first().parents(".items").getFloatWidth(),
        elw =
          typeof options.item !== "undefined" && options.item.length
            ? $(options.item).first().getFloatWidth()
            : el.first().hasClass("item")
            ? el.first().getFloatWidth()
            : el.first().parents(".item").getFloatWidth();
      if (!row) {
        row = el.first().parents(".row").getFloatWidth();
      }
      if (row && elw) {
        options.slice = Math.floor(row / elw);
      }
    }
    if (options.slice) {
      for (var i = 0; i < el.length; i += options.slice) {
        $(el.slice(i, i + options.slice)).equalizeHeights(options.outer, options.classNull, options.minHeight);
      }
    }
    if (options.lineheight) {
      var lineheightAdd = parseInt(options.lineheight);
      if (isNaN(lineheightAdd)) {
        lineheightAdd = 0;
      }
      el.each(function () {
        $(this).css("line-height", $(this).actual("height") + lineheightAdd + "px");
      });
    }
  }

  var options = $.extend(
    {
      slice: null,
      outer: false,
      lineheight: false,
      autoslicecount: true,
      classNull: false,
      minHeight: false,
      options: false,
      resize: true,
      row: false,
      item: false,
      mobile: false,
    },
    options
  );

  var el = $(this);

  if (options.mobile == true) {
    if (
      typeof arOptimusOptions === "object" &&
      typeof arOptimusOptions.THEME.MOBILE_CATALOG_BLOCK_COMPACT === "string" &&
      arOptimusOptions.THEME.MOBILE_CATALOG_BLOCK_COMPACT.toUpperCase() === "Y" &&
      el.first().parents(".catalog_block.items").length
    ) {
      options.mobile = false;
    }
  }
  _slice(el);

  if (options.resize) {
    if (!window.sliceResizeElements) {
      window.sliceResizeElements = [];
      BX.addCustomEvent("onWindowResize", function () {
        ignoreResize.push(true);
        window.sliceResizeElements.forEach(function (el) {
          _slice(el);
        });
        ignoreResize.pop();
      });
    }

    window.sliceResizeElements.push(el);
  } else {
    if (!ignoreResize.length) {
      // ignoreResize.push(true);
      _slice(el);
      // ignoreResize.pop();
    }
  }
};

$.fn.sliceHeightNoResize = function (options) {
  function _slice(el) {
    el.each(function () {
      $(this).css("line-height", "");
      $(this).css("height", "");
    });
    if (typeof options.autoslicecount == "undefined" || options.autoslicecount !== false) {
      var elw = el.first().hasClass("item") ? el.first().getFloatWidth() : el.first().parents(".item").getFloatWidth();
      var elsw = el.first().parents(".items").getFloatWidth();
      if (!elsw) {
        elsw = el.first().parents(".row").getFloatWidth();
      }
      if (elsw && elw) {
        options.slice = Math.floor(elsw / elw);
      }
    }

    if (options.slice) {
      for (var i = 0; i < el.length; i += options.slice) {
        $(el.slice(i, i + options.slice)).equalizeHeights(options.outer, options.classNull, options.minHeight);
      }
    }
    if (options.lineheight) {
      var lineheightAdd = parseInt(options.lineheight);
      if (isNaN(lineheightAdd)) {
        lineheightAdd = 0;
      }
      el.each(function () {
        $(this).css("line-height", $(this).actual("height") + lineheightAdd + "px");
      });
    }
  }

  var options = $.extend(
    {
      slice: null,
      outer: false,
      lineheight: false,
      autoslicecount: true,
      classNull: false,
      minHeight: false,
      options: false,
      resize: true,
    },
    options
  );

  var el = $(this);
  _slice(el);
};

if (!funcDefined("initHoverBlock")) {
  function initHoverBlock(target) {
    /*$(target).find('.catalog_item.item_wrap').on('mouseenter', function(){
			$(this).addClass('hover');
		})
		$(target).find('.catalog_item.item_wrap').on('mouseleave', function(){
			$(this).removeClass('hover');
		})*/
  }
}

if (!funcDefined("setStatusButton")) {
  function setStatusButton() {
    $.ajax({
      url: arOptimusOptions["SITE_DIR"] + "ajax/getAjaxBasket.php",
      type: "POST",
      success: function (data) {
        if (data.BASKET) {
          for (var i in data.BASKET) {
            $(".to-cart[data-item=" + data.BASKET[i] + "]").hide();
            $(".counter_block[data-item=" + data.BASKET[i] + "]").hide();
            $(".in-cart[data-item=" + data.BASKET[i] + "]").show();
            $(".in-cart[data-item=" + data.BASKET[i] + "]")
              .closest(".button_block")
              .addClass("wide");
          }
        }
        if (data.DELAY) {
          for (var i in data.DELAY) {
            $(".wish_item.to[data-item=" + data.DELAY[i] + "]").hide();
            $(".wish_item.in[data-item=" + data.DELAY[i] + "]").show();
            if ($(".wish_item[data-item=" + data.DELAY[i] + "]").find(".value.added").length) {
              $(".wish_item[data-item=" + data.DELAY[i] + "]").addClass("added");
              $(".wish_item[data-item=" + data.DELAY[i] + "]")
                .find(".value")
                .hide();
              $(".wish_item[data-item=" + data.DELAY[i] + "]")
                .find(".value.added")
                .show();
            }
          }
        }
        if (data.SUBSCRIBE) {
          for (var i in data.SUBSCRIBE) {
            $(".to-subscribe[data-item=" + data.SUBSCRIBE[i] + "]").hide();
            $(".in-subscribe[data-item=" + data.SUBSCRIBE[i] + "]").show();
          }
        }
        if (data.COMPARE) {
          for (var i in data.COMPARE) {
            $(".compare_item.to[data-item=" + data.COMPARE[i] + "]").hide();
            $(".compare_item.in[data-item=" + data.COMPARE[i] + "]").show();
            if ($(".compare_item[data-item=" + data.COMPARE[i] + "]").find(".value.added").length) {
              $(".compare_item[data-item=" + data.COMPARE[i] + "]")
                .find(".value")
                .hide();
              $(".compare_item[data-item=" + data.COMPARE[i] + "]")
                .find(".value.added")
                .show();
            }
          }
        }
      },
    });
  }
}

window.onload = function () {
  window.dataLayer = window.dataLayer || [];
};

if (!funcDefined("onLoadjqm")) {
  var onLoadjqm = function (name, hash, requestData, selector, requestTitle, isButton, thButton) {

    if ( hash.c.noOverlay === undefined || (hash.c.noOverlay !== undefined && !hash.c.noOverlay) ) {
      let diffWidth;
      $("body").addClass("jqm-initied");

      if ((diffWidth = window.innerWidth - document.documentElement.clientWidth)) {
        $("body").css({ "padding-right": diffWidth + "px", "box-sizing": "border-box"});
      }

      hash.w
        .closest("#popup_iframe_wrapper")
        .css({ "z-index": 3000, display: "flex" });
    }

    hash.w.addClass("show").css({
      opacity: 1,
    });

    hash.w.addClass("scrollbar scrollbar--overflow"); 

    $("body").css({ overflow: "hidden", /*height: "100vh"*/});

    if(!$("body").hasClass("fast_view") && name === "fast_view") {
      $("body").addClass(name);
    }  

    if (typeof requestData == "undefined") {
      requestData = "";
    }
    if (typeof selector == "undefined") {
      selector = false;
    }
    //var width = $("." + name + "_frame").width();
    //$("." + name + "_frame").css("margin-left", "-" + width / 2 + "px");

    if (name == "order-popup-call") {
    } else if (name == "order-button") {
      $(".order-button_frame")
        .find("div[product_name]")
        .find("input")
        .val(hash.t.title)
        .attr("readonly", "readonly")
        .css({ overflow: "hidden", "text-overflow": "ellipsis" });
    } else if ((name == "to-order" || name == "cheaper") && selector) {
      $(".to-order_frame")
        .find('[data-sid="PRODUCT_NAME"]')
        .val($(selector).data("name"))
        .attr("readonly", "readonly")
        .css({ overflow: "hidden", "text-overflow": "ellipsis" });
      $(".to-order_frame").find('[data-sid="PRODUCT_ID"]').val($(selector).attr("data-item"));
      $(".cheaper_frame")
        .find('[data-sid="PRODUCT_NAME"]')
        .val($(selector).data("name"))
        .attr("readonly", "readonly")
        .css({ overflow: "hidden", "text-overflow": "ellipsis" });
      $(".cheaper_frame").find('[data-sid="PRODUCT_ID"]').val($(selector).attr("data-item"));
    } else if (name == "services" && selector) {
      $(".services_frame").find('[data-sid="SERVICE"]').val($(selector).attr("data-title"));
    } else if (name == "resume" && selector) {
      if ($(selector).attr("data-jobs")) {
        $(".resume_frame").find('[data-sid="POST"]').attr("readonly", "readonly").val($(selector).attr("data-jobs"));
      }
    } else if (name == "basket_error") {
      /*else if(name == "subscribe" && selector) {
			if($(selector).attr('data-item')){
				$(".subscribe_frame").find('[data-sid="POST"]').attr("readonly", "readonly").val($(selector).attr('data-jobs'));
			}
		}*/
      $(".basket_error_frame .pop-up-title").text(requestTitle);
      $(".basket_error_frame .ajax_text").html(requestData);
      // $('.basket_error_frame .ajax_text select').ikSelect('redraw');
      initSelects(document);
      if (isButton == "Y" && thButton) {
        $(
          "<div class='popup_button_basket_wr'><span class='popup_button_basket big_btn button' data-item=" +
            thButton.data("item") +
            "><span>" +
            BX.message("ERROR_BASKET_BUTTON") +
            "</span></span></div>"
        ).insertAfter($(".basket_error_frame .ajax_text"));
      }
    } else if (name == "one_click_buy") {
      $("#one_click_buy_form").submit(function () {
        if ($("#one_click_buy_form").valid()) {
          if ($("." + name + "_frame form input.error").length || $("." + name + "_frame form textarea.error").length) {
            return false;
          } else if (!$(this).find("#one_click_buy_form_button").hasClass("clicked")) {
            if (!$(this).find("#one_click_buy_form_button").hasClass("clicked"))
              $(this).find("#one_click_buy_form_button").addClass("clicked");
            var bSend = true;
            if (window.renderRecaptchaById && window.asproRecaptcha && window.asproRecaptcha.key) {
              if (
                window.asproRecaptcha.params.recaptchaSize == "invisible" &&
                typeof grecaptcha != "undefined" &&
                arOptimusOptions.THEME.ONE_CLICK_BUY_CAPTCHA === "Y"
              ) {
                if ($("#one_click_buy_form").find(".g-recaptcha-response").val()) {
                  // eventdata.form.submit();
                  bSend = true;
                } else {
                  grecaptcha.execute($("#one_click_buy_form").find(".g-recaptcha").data("widgetid"));
                  $(this).find("#one_click_buy_form_button").removeClass("clicked");
                  bSend = false;
                }
              }
            }

            if (bSend) {
              var form_url = $(this).attr("action");
              $.ajax({
                url: form_url,
                data: $(this).serialize(),
                type: "POST",
                dataType: "json",
                error: function (data) {
                  alert("Error connecting server");
                },
                success: function (data) {
                  if (data.result == "Y") {
                    if (arOptimusOptions["COUNTERS"]["USE_1CLICK_GOALS"] !== "N") {
                      var eventdata = { goal: "goal_1click_success" };
                      BX.onCustomEvent("onCounterGoals", [eventdata]);
                    }

                    if (ocb_files.length) {
                      var obData = new FormData();
                      $.each(ocb_files, function (key, value) {
                        if (value) obData.append(key + "_" + value.code, value[0]);
                      });
                      if (obData) {
                        $.ajax({
                          url: form_url + "?uploadfiles&orderID=" + data.message,
                          type: "POST",
                          data: obData,
                          cache: false,
                          dataType: "json",
                          processData: false, // Don't process the files
                          contentType: false, // this is string query
                          error: function (data) {
                            alert("Error with files");
                          },
                          success: function (respond, textStatus, jqXHR) {
                            $(".one_click_buy_result").show();
                            $(".one_click_buy_result_success").show();
                            purchaseCounter(data.message, arOptimusOptions["COUNTERS"]["TYPE"]["ONE_CLICK"]);
                          },
                        });
                      }
                    } else {
                      $(".one_click_buy_result").show();
                      $(".one_click_buy_result_success").show();
                      purchaseCounter(data.message, arOptimusOptions["COUNTERS"]["TYPE"]["ONE_CLICK"]);
                    }
                  } else {
                    $(".one_click_buy_result").show();
                    $(".one_click_buy_result_fail").show();
                    if ("err" in data && data.err) data.message = data.message + " \n" + data.err;
                    $(".one_click_buy_result_text").html(data.message);
                  }
                  $(".one_click_buy_modules_button", self).removeClass("disabled");
                  $("#one_click_buy_form").hide();
                  $("#one_click_buy_form_result").show();
                },
              });
            }
          }
        }
        return false;
      });
    } else if (name == "one_click_buy_basket") {
      $("#one_click_buy_form").on("submit", function () {
        if ($("#one_click_buy_form").valid()) {
          if ($("." + name + "_frame form input.error").length || $("." + name + "_frame form textarea.error").length) {
            return false;
          } else if (!$(this).find("#one_click_buy_form_button").hasClass("clicked")) {
            if (!$(this).find("#one_click_buy_form_button").hasClass("clicked"))
              $(this).find("#one_click_buy_form_button").addClass("clicked");
            var bSend = true;
            if (window.renderRecaptchaById && window.asproRecaptcha && window.asproRecaptcha.key) {
              if (
                window.asproRecaptcha.params.recaptchaSize == "invisible" &&
                typeof grecaptcha != "undefined" &&
                arOptimusOptions.THEME.ONE_CLICK_BUY_CAPTCHA === "Y"
              ) {
                if ($("#one_click_buy_form").find(".g-recaptcha-response").val()) {
                  // eventdata.form.submit();
                  bSend = true;
                } else {
                  grecaptcha.execute($("#one_click_buy_form").find(".g-recaptcha").data("widgetid"));
                  $(this).find("#one_click_buy_form_button").removeClass("clicked");
                  bSend = false;
                }
              }
            }
            if (bSend) {
              var form_url = $(this).attr("action");
              $.ajax({
                url: $(this).attr("action"),
                data: $(this).serialize(),
                type: "POST",
                dataType: "json",
                error: function (data) {
                  window.console && console.log(data);
                },
                success: function (data) {
                  if (data.result == "Y") {
                    if (arOptimusOptions["COUNTERS"]["USE_FASTORDER_GOALS"] !== "N") {
                      var eventdata = { goal: "goal_fastorder_success" };
                      BX.onCustomEvent("onCounterGoals", [eventdata]);
                    }

                    if (ocb_files.length) {
                      var obData = new FormData();
                      $.each(ocb_files, function (key, value) {
                        if (value) obData.append(key + "_" + value.code, value[0]);
                      });
                      if (obData) {
                        $.ajax({
                          url: form_url + "?uploadfiles&orderID=" + data.message,
                          type: "POST",
                          data: obData,
                          cache: false,
                          dataType: "json",
                          processData: false, // Don't process the files
                          contentType: false, // this is string query
                          error: function (data) {
                            alert("Error with files");
                          },
                          success: function (respond, textStatus, jqXHR) {
                            $(".one_click_buy_result").show();
                            $(".one_click_buy_result_success").show();
                            purchaseCounter(data.message, arOptimusOptions["COUNTERS"]["TYPE"]["ONE_CLICK"]);
                          },
                        });
                      }
                    } else {
                      $(".one_click_buy_result").show();
                      $(".one_click_buy_result_success").show();
                      purchaseCounter(data.message, arOptimusOptions["COUNTERS"]["TYPE"]["ONE_CLICK"]);
                    }
                  } else {
                    $(".one_click_buy_result").show();
                    $(".one_click_buy_result_fail").show();
                    if ("err" in data && data.err) data.message = data.message + " \n" + data.err;
                    $(".one_click_buy_result_text").text(data.message);
                  }
                  $(".one_click_buy_modules_button", self).removeClass("disabled");
                  $("#one_click_buy_form").hide();
                  $("#one_click_buy_form_result").show();
                },
              });
            }
          }
        }
        return false;
      });
    }

    var eventdata = { action: "loadForm" };

		if(typeof selector !== 'undefined' && selector !== false ){
			BX.onCustomEvent("onCompleteAction", [eventdata, $(selector)[0]]);
		} else {
			BX.onCustomEvent("onCompleteAction", [eventdata, $(hash.t)[0]]);
		}

    $("." + name + "_frame").show();
  };
}

if (!funcDefined("onHidejqm")) {
  var onHidejqm = function (name, hash) {

    if (hash.w.find(".one_click_buy_result_success").is(":visible") && name == "one_click_buy_basket") {
      window.location.href = window.location.href;
    }

    hash.w.animate({ opacity: 0 }, 200, function () {
      hash.w.hide();
      hash.w.empty();
      hash.o.remove();
      hash.w.removeClass("show");
      hash.w.removeClass("scrollbar");
      hash.w.removeClass("scrollbar--overflow");
      $("html").removeClass("overflow_hidden_popup_forms");

      if (!hash.w.closest("#popup_iframe_wrapper").find(".jqmOverlay").length) {
        hash.w
          .closest("#popup_iframe_wrapper")
          .css({ "z-index": "", display: "" });
      }

      if (
        !$(".jqmOverlay:not(.mobp)").length ||
        $(".jqmOverlay.waiting").length
      ) {
        $("body").removeClass("jqm-initied");
        $("body").removeClass("fast_view");
      }

      if (!$("body").hasClass("fast_view")) {
        $("body").css({ overflow: "", height: "", "padding-right": "", "box-sizing": "content-box" });
      }
    });
  };
}

if (!funcDefined("oneClickBuy")) {
  var oneClickBuy = function (elementID, iblockID, that) {
    var name = "one_click_buy";
    var elementQuantity = 1;
    var offerProps = false;
    var buy_btn = $(that).closest(".buy_block").find(".to-cart");
    var buy_btn2 = $(that).closest("tr").find(".to-cart");

    if (typeof that !== "undefined") {
      elementQuantity = $(that).attr("data-quantity");
      offerProps = $(that).attr("data-props");
    }

    if (elementQuantity < 0) {
      elementQuantity = 1;
    }

    var tmp_props = buy_btn.data("props"),
      tmp_props2 = buy_btn2.data("props"),
      props = "",
      part_props = "",
      add_props = "N",
      fill_prop = {},
      iblockid = buy_btn.data("iblockid"),
      item = buy_btn.attr("data-item");

    if (tmp_props) {
      props = tmp_props.split(";");
    } else if (tmp_props2) {
      props = tmp_props2.split(";");
    }
    if (buy_btn.data("part_props")) {
      part_props = buy_btn.data("part_props");
    }
    if (buy_btn.data("add_props")) {
      add_props = buy_btn.data("add_props");
    }

    fill_prop = fillBasketPropsExt(buy_btn, "prop", buy_btn.data("bakset_div"));
    fill_prop.iblockID = iblockid;
    fill_prop.part_props = part_props;
    fill_prop.add_props = add_props;
    fill_prop.props = JSON.stringify(props);
    fill_prop.item = item;
    fill_prop.ocb_item = "Y";    

    if (!$(that).hasClass("clicked")) {
      $(that).addClass("clicked");
      $("body")
        .find("." + name + "_frame")
        .remove();
      $("body")
        .find("." + name + "_trigger")
        .remove();
      $("body #popup_iframe_wrapper").append(
        '<div class="' + name + '_frame popup"></div>'
      );
      $("body #popup_iframe_wrapper").append(
        '<div class="' + name + '_trigger"></div>'
      );
      $("." + name + "_frame").jqm({
        trigger: "." + name + "_trigger",
        onHide: function (hash) {
          onHidejqm(name, hash);
        },
        toTop: false,
        onLoad: function (hash) {
          onLoadjqm(name, hash);
        },
        ajax:
        arOptimusOptions["SITE_DIR"] +
          "ajax/one_click_buy.php?ELEMENT_ID=" +
          elementID +
          "&IBLOCK_ID=" +
          iblockID +
          "&ELEMENT_QUANTITY=" +
          elementQuantity +
          "&OFFER_PROPS=" +
          fill_prop.props,
      });
      $("." + name + "_trigger").click();
    }
  };
}

if (!funcDefined("oneClickBuyBasket")) {
  var oneClickBuyBasket = function () {
    name = "one_click_buy_basket";
    $("body")
      .find("." + name + "_frame")
      .remove();
    $("body")
      .find("." + name + "_trigger")
      .remove();
    // $("body").append('<div class="' + name + '_frame popup"></div>');
    // $("body").append('<div class="' + name + '_trigger"></div>');

    $("body #popup_iframe_wrapper").append(
      '<div class="' + name + '_frame popup"></div>'
    );
    $("body #popup_iframe_wrapper").append(
      '<div class="' + name + '_trigger"></div>'
    );

    $("." + name + "_frame").jqm({
      trigger: "." + name + "_trigger",
      onHide: function (hash) {
        onHidejqm(name, hash);
      },
      onLoad: function (hash) {
        onLoadjqm(name, hash);
      },
      ajax: arOptimusOptions["SITE_DIR"] + "ajax/one_click_buy_basket.php",
    });
    $("." + name + "_trigger").click();
  };
}

if (!funcDefined("scroll_block")) {
  function scroll_block(block) {
    const topPos = block.offset().top;
    // const headerH = $('header').outerHeight(true,true);

    if ($(".stores_tab").length) {
      $("li.stores_tab").trigger("click");
    } else if ($(".prices_tab").length) {
      $("li.prices_tab").trigger("click");
      const pricesTab = $(".prices_tab.current .opener");

      if (pricesTab[0] && !pricesTab[0].querySelector(".opener_icon.opened")) {
        pricesTab[0].click();

        $(pricesTab[0]).parents("tr").next(".offer_stores").find(".stores_block_wrap").slideDown(200);
      }
    }
    $("html,body").animate({ scrollTop: topPos - 30 }, 150);
  }
}

if (!funcDefined("jqmEd")) {
  var jqmEd = function (
    name,
    form_id,
    open_trigger,
    requestData,
    selector,
    requestTitle,
    isButton,
    thButton
  ) {
    if (typeof requestData == "undefined") {
      requestData = "";
    }

    if (typeof selector == "undefined") {
      selector = false;
    }

    // $("body")
    //   .find("." + name + "_frame")
    //   .remove();
    // $("body").append('<div class="' + name + '_frame popup"></div>');

    $("body #popup_iframe_wrapper")
      .find("." + name + "_frame")
      .remove();
    $("body #popup_iframe_wrapper").append(
      '<div class="' + name + '_frame jqmWindow popup"></div>'
    );

    if (typeof open_trigger == "undefined") {
      $("." + name + "_frame").jqm({
        trigger: "." + name + "_frame.popup",
        onHide: function (hash) {
          onHidejqm(name, hash);
        },
        onLoad: function (hash) {
          onLoadjqm(name, hash, requestData, selector);
        },
        ajax:
          arOptimusOptions["SITE_DIR"] +
          "ajax/form.php?form_id=" +
          form_id +
          (requestData.length ? "&" + requestData : ""),
      });
    } else {
      if (name == "enter") {
        $("." + name + "_frame").jqm({
          trigger: open_trigger,
          onHide: function (hash) {
            onHidejqm(name, hash);
          },
          onLoad: function (hash) {
            onLoadjqm(name, hash, requestData, selector);
          },
          ajax: arOptimusOptions["SITE_DIR"] + "ajax/auth.php",
        });
      } else if (name == "basket_error") {
        $("." + name + "_frame").jqm({
          trigger: open_trigger,
          onHide: function (hash) {
            onHidejqm(name, hash);
          },
          onLoad: function (hash) {
            onLoadjqm(name, hash, requestData, selector, requestTitle, isButton, thButton);
          },
          ajax: arOptimusOptions["SITE_DIR"] + "ajax/basket_error.php",
        });
      } else {
        $("." + name + "_frame").jqm({
          trigger: open_trigger,
          onHide: function (hash) {
            onHidejqm(name, hash);
          },
          onLoad: function (hash) {
            onLoadjqm(name, hash, requestData, selector);
          },
          ajax:
            arOptimusOptions["SITE_DIR"] +
            "ajax/form.php?form_id=" +
            form_id +
            (requestData.length ? "&" + requestData : ""),
        });

      }

      $(open_trigger).dblclick(function () {
        return false;
      });
    }
    return true;
  };
}

initCalculatePreview = function () {
	$(".calculate-delivery.with_preview:not(.inited)").each(function () {
	  var $this = $(this);
	  var $calculateSpan = $this.find("span[data-event=jqm]");
	  var $preview = $this.find(".calculate-delivery-preview");
  
	  $this.addClass("inited");
	  $this.appear(
		function () {
		  if ($calculateSpan.length) {
			if (
			  typeof window["calculate-delivery-preview-index"] === "undefined"
			) {
			  window["calculate-delivery-preview-index"] = 1001;
			} else {
			  ++window["calculate-delivery-preview-index"];
			}
  
			var productId = $calculateSpan.attr("data-param-product_id") * 1;
			var quantity = $calculateSpan.attr("data-param-quantity") * 1;
  
			if (productId > 0) {
			  var areaIndexSended = window["calculate-delivery-preview-index"];
			  $calculateSpan.data({ areaIndex: areaIndexSended });
  
			  $.ajax({
				url: arOptimusOptions["SITE_DIR"] + "ajax/delivery.php",
				type: "POST",
				data: {
				  is_preview: "Y",
				  index: areaIndexSended,
				  product_id: productId,
				  quantity: quantity,
				},
				beforeSend: function () {
				  $this.addClass("loadings");
				},
				success: function (response) {
				  var areaIndex = $calculateSpan.data("areaIndex");
				  if (
					typeof areaIndex !== "undefined" &&
					areaIndex == areaIndexSended
				  ) {
					$calculateSpan.hide();
					$preview.html(response);
					if (!$preview.find(".catalog-delivery-preview").length) {
					  $preview.empty();
					  $calculateSpan.show();
					}
				  }
				},
				error: function (xhr, ajaxOptions, thrownError) {},
				complete: function () {
				  var areaIndex = $calculateSpan.data("areaIndex");
				  if (
					typeof areaIndex !== "undefined" &&
					areaIndex == areaIndexSended
				  ) {
					$this.removeClass("loadings");
				  }
				},
			  });
			}
		  }
		},
		{ accX: 0, accY: 0 }
	  );
	});
};

if (!funcDefined("replaceBasketPopup")) {
  function replaceBasketPopup(hash) {
    if (typeof hash != "undefined") {
      hash.w.hide();
      hash.o.hide();
    }
  }
}

if (!funcDefined("waitLayer")) {
  function waitLayer(delay, callback) {
    if (typeof dataLayer != "undefined") {
      if (typeof callback == "function") {
        callback();
      }
    } else {
      setTimeout(function () {
        waitLayer(delay, callback);
      }, delay);
    }
  }
}

if (!funcDefined("waitCounter")) {
  waitCounter = function (idCounter, delay, callback) {
    var obCounter = window["yaCounter" + idCounter];
    if (typeof obCounter == "object") {
      if (typeof callback == "function") {
        callback();
      }
    } else {
      setTimeout(function () {
        waitCounter(idCounter, delay, callback);
      }, delay);
    }
  };
}

if (!funcDefined("InitTopestMenuGummi")) {
  InitTopestMenuGummi = function () {
    if (!isOnceInited) {
      function _init() {
        var arItems = $menuTopest.find(">li:not(.more)");
        var cntItems = arItems.length;
        if (cntItems) {
          var itemsWidth = 0;
          for (var i = 0; i < cntItems; ++i) {
            var item = arItems.eq(i);
            var itemWidth = item.actual("outerWidth", { includeMargin: true });
            arItemsHideWidth[i] = (itemsWidth += itemWidth) + (i !== cntItems - 1 ? moreWidth : 0);
          }
        }
      }

      function _gummi() {
        var rowWidth = $menuTopest.actual("innerWidth");
        var arItems = $menuTopest.find(">li:not(.more),li.more>.dropdown>li");
        var cntItems = arItems.length;
        if (cntItems) {
          var bMore = false;
          for (var i = cntItems - 1; i >= 0; --i) {
            var item = arItems.eq(i);
            var bInMore = item.parents(".more").length > 0;
            if (!bInMore) {
              if (arItemsHideWidth[i] > rowWidth) {
                if (!bMore) {
                  bMore = true;
                  more.removeClass("hidden");
                }
                var clone = item.clone();
                clone.find(">a").addClass("dark_font");
                clone.prependTo(moreDropdown);
                item.addClass("cloned");
              }
            }
          }
          for (var i = 0; i < cntItems; ++i) {
            var item = arItems.eq(i);
            var bInMore = item.parents(".more").length > 0;
            if (bInMore) {
              if (arItemsHideWidth[i] <= rowWidth) {
                if (i === cntItems - 1) {
                  bMore = false;
                  more.addClass("hidden");
                }
                var clone = item.clone();
                clone.find(">a").removeClass("dark_font");
                clone.insertBefore(more);
                item.addClass("cloned");
              }
            }
          }
          $menuTopest.find("li.cloned").remove();
        }
      }

      var $menuTopest = $(".menu.topest");
      var more = $menuTopest.find(">.more");
      var moreDropdown = more.find(">.dropdown");
      var moreWidth = more.actual("outerWidth", { includeMargin: true });
      var arItemsHideWidth = [];
      ignoreResize.push(true);
      _init();
      _gummi();
      ignoreResize.pop();

      $(window).resize(function () {
        ignoreResize.push(true);
        _gummi();
        ignoreResize.pop();
      });
    }
  };
}

if (!funcDefined("InitTopMenuGummi")) {
  InitTopMenuGummi = function () {
    function _init() {
      var arItems = $topMenu.closest(".wrap_menu").find(".inc_menu .menu_top_block >li:not(.more)");
      var cntItems = arItems.length;
      if (cntItems) {
        var itemsWidth = 0;
        for (var i = 0; i < cntItems; ++i) {
          var item = arItems.eq(i);
          var itemWidth = item.actual("outerWidth");
          arItemsHideWidth[i] = (itemsWidth += itemWidth) + (i !== cntItems - 1 ? moreWidth : 0);
        }
      }
    }

    function _gummi() {
      var rowWidth = $wrapMenu.actual("innerWidth") - $wrapMenuLeft.actual("innerWidth");
      var arItems = $topMenu.find(">li:not(.more):not(.catalog),li.more>.dropdown>li");
      var cntItems = arItems.length;

      if (cntItems) {
        var bMore = false;
        for (var i = cntItems - 1; i >= 0; --i) {
          var item = arItems.eq(i);
          var bInMore = item.parents(".more").length > 0;
          if (!bInMore) {
            if (arItemsHideWidth[i] > rowWidth) {
              if (!bMore) {
                bMore = true;
                more.removeClass("hidden");
              }
              var clone = item.clone();
              clone.find(">.dropdown").removeAttr("style").removeClass("toleft");
              clone.find(">a").addClass("dark_font").removeAttr("style");
              clone.prependTo(moreDropdown);
              item.addClass("cloned");
            }
          }
        }
        for (var i = 0; i < cntItems; ++i) {
          var item = arItems.eq(i);
          var bInMore = item.parents(".more").length > 0;
          if (bInMore) {
            if (arItemsHideWidth[i] <= rowWidth) {
              if (i === cntItems - 1) {
                bMore = false;
                more.addClass("hidden");
              }
              var clone = item.clone();
              clone.find(">a").removeClass("dark_font");
              clone.insertBefore(more);
              item.addClass("cloned");
            }
          }
        }
        $topMenu.find("li.cloned").remove();

        var cntItemsVisible = $topMenu.find(">li:not(.more):not(.catalog)").length;
        var o = rowWidth - arItemsHideWidth[cntItemsVisible - 1];
        var itemsPaddingAdd = Math.floor(o / (cntItemsVisible + (more.hasClass("hidden") ? 0 : 1)));
        var itemsPadding_new = itemsPadding_min + itemsPaddingAdd;
        var itemsPadding_new_l = Math.floor(itemsPadding_new / 2);
        var itemsPadding_new_r = itemsPadding_new - itemsPadding_new_l;

        $topMenu.find(">li:not(.catalog):visible>a").each(function () {
          $(this).css({ "padding-left": itemsPadding_new_l + "px" });
          $(this).css({ "padding-right": itemsPadding_new_r + "px" });
        });

        var lastItemPadding_new =
          itemsPadding_new + o - (cntItemsVisible + (more.is(":visible") ? 1 : 0)) * itemsPaddingAdd;
        var lastItemPadding_new_l = Math.floor(lastItemPadding_new / 2);
        var lastItemPadding_new_r = lastItemPadding_new - lastItemPadding_new_l;
        $topMenu
          .find(">li:visible")
          .last()
          .find(">a")
          .css({ "padding-left": lastItemPadding_new_l + "px" });
        $topMenu
          .find(">li:visible")
          .last()
          .find(">a")
          .css({ "padding-right": lastItemPadding_new_r + "px" });
        CheckTopMenuFullCatalogSubmenu();
      }
    }

    var $topMenu = $(".menu_top_block");
    var $wrapMenu = $topMenu.parents(".wrap_menu");
    var $wrapMenuLeft = $wrapMenu.find(".catalog_menu_ext");
    var more = $topMenu.find(">.more");
    var moreWidth = more.actual("outerWidth", { includeMargin: true });
    more.addClass("hidden");
    var arItemsHideWidth = [];
    var moreDropdown = more.find(">.dropdown");
    var itemsPadding = parseInt(more.find(">a").css("padding-left")) * 2;
    var itemsPadding_min = itemsPadding;

    // setTimeout(function(){
    ignoreResize.push(true);
    _init();
    _gummi();
    ignoreResize.pop();
    // }, 100)

    $(window).resize(function () {
      ignoreResize.push(true);
      _gummi();
      ignoreResize.pop();
    });

    /*BX.addCustomEvent('onTopPanelFixUnfix', function(eventdata) {
			ignoreResize.push(true);
			_gummi();
			ignoreResize.pop();
		});*/
  };
}

if (!funcDefined("checkCounters")) {
  function checkCounters(name) {
    if (typeof name !== "undefined") {
      if (
        name == "google" &&
        arOptimusOptions["COUNTERS"]["GOOGLE_ECOMERCE"] == "Y" &&
        arOptimusOptions["COUNTERS"]["GOOGLE_COUNTER"] > 0
      ) {
        return true;
      } else if (
        name == "yandex" &&
        arOptimusOptions["COUNTERS"]["USE_YA_COUNTER"] == "Y" &&
        arOptimusOptions["COUNTERS"]["YANDEX_ECOMERCE"] == "Y" &&
        arOptimusOptions["COUNTERS"]["YANDEX_COUNTER"] > 0
      ) {
        return true;
      } else {
        return false;
      }
    } else if (
      (arOptimusOptions["COUNTERS"]["USE_YA_COUNTER"] == "Y" &&
        arOptimusOptions["COUNTERS"]["YANDEX_ECOMERCE"] == "Y" &&
        arOptimusOptions["COUNTERS"]["YANDEX_COUNTER"] > 0) ||
      (arOptimusOptions["COUNTERS"]["GOOGLE_ECOMERCE"] == "Y" && arOptimusOptions["COUNTERS"]["GOOGLE_COUNTER"] > 0)
    ) {
      return true;
    } else {
      return false;
    }
  }
}

if (!funcDefined("addBasketCounter")) {
  function addBasketCounter(id) {
    if (arOptimusOptions["COUNTERS"]["USE_BASKET_GOALS"] !== "N") {
      var eventdata = { goal: "goal_basket_add", params: { id: id } };
      BX.onCustomEvent("onCounterGoals", [eventdata]);
    }
    if (checkCounters()) {
      $.ajax({
        url: arOptimusOptions["SITE_DIR"] + "ajax/goals.php",
        dataType: "json",
        type: "POST",
        data: { ID: id },
        success: function (item) {
          if (!!item && !!item.ID) {
            waitLayer(100, function () {
              dataLayer.push({
                event: arOptimusOptions["COUNTERS"]["GOOGLE_EVENTS"]["ADD2BASKET"],
                ecommerce: {
                  currencyCode: item.CURRENCY,
                  add: {
                    products: [
                      {
                        id: item.ID,
                        name: item.NAME,
                        price: item.PRICE,
                        brand: item.BRAND,
                        category: item.CATEGORY,
                        quantity: item.QUANTITY,
                      },
                    ],
                  },
                },
              });
            });
          }
        },
      });
    }
  }
}

if (!funcDefined("purchaseCounter")) {
  function purchaseCounter(order_id, type, callback) {
    if (checkCounters()) {
      $.ajax({
        url: arOptimusOptions["SITE_DIR"] + "ajax/goals.php",
        dataType: "json",
        type: "POST",
        data: { ORDER_ID: order_id, TYPE: type },
        success: function (order) {
          var products = [];
          if (order.ITEMS) {
            for (var i in order.ITEMS) {
              products.push({
                id: order.ITEMS[i].ID,
                sku: order.ITEMS[i].ID,
                name: order.ITEMS[i].NAME,
                price: order.ITEMS[i].PRICE,
                brand: order.ITEMS[i].BRAND,
                category: order.ITEMS[i].CATEGORY,
                quantity: order.ITEMS[i].QUANTITY,
              });
            }
          }
          if (order.ID) {
            waitLayer(100, function () {
              dataLayer.push({
                event: arOptimusOptions["COUNTERS"]["GOOGLE_EVENTS"]["PURCHASE"],
                ecommerce: (d = {
                  purchase: {
                    actionField: {
                      id: order.ACCOUNT_NUMBER,
                      shipping: order.PRICE_DELIVERY,
                      tax: order.TAX_VALUE,
                      list: type,
                      revenue: order.PRICE,
                    },
                    products: products,
                  },
                }),
              });

              if (typeof callback !== "undefined") {
                callback(d);
              }
            });
          } else {
            if (typeof callback !== "undefined") {
              callback(false);
            }
          }
        },
        error: function () {
          if (typeof callback !== "undefined") {
            callback(false);
          }
        },
      });
    } else {
      if (typeof callback !== "undefined") {
        callback(false);
      }
    }
  }
}

if (!funcDefined("viewItemCounter")) {
  function viewItemCounter(id, price_id) {
    if (checkCounters()) {
      $.ajax({
        url: arOptimusOptions["SITE_DIR"] + "ajax/goals.php",
        dataType: "json",
        type: "POST",
        data: { PRODUCT_ID: id, PRICE_ID: price_id },
        success: function (item) {
          if (item.ID) {
            waitLayer(100, function () {
              dataLayer.push({
                //"event": "",
                ecommerce: {
                  detail: {
                    products: [
                      {
                        id: item.ID,
                        name: item.NAME,
                        price: item.PRICE,
                        brand: item.BRAND,
                        category: item.CATEGORY,
                      },
                    ],
                  },
                },
              });
            });
          }
        },
      });
    }
  }
}

if (!funcDefined("checkoutCounter")) {
  function checkoutCounter(step, option, callback) {
    if (checkCounters("google")) {
      $.ajax({
        url: arOptimusOptions["SITE_DIR"] + "ajax/goals.php",
        dataType: "json",
        type: "POST",
        data: { BASKET: "Y" },
        success: function (basket) {
          var products = [];
          if (basket.ITEMS) {
            for (var i in basket.ITEMS) {
              products.push({
                id: basket.ITEMS[i].ID,
                name: basket.ITEMS[i].NAME,
                price: basket.ITEMS[i].PRICE,
                brand: basket.ITEMS[i].BRAND,
                category: basket.ITEMS[i].CATEGORY,
                quantity: basket.ITEMS[i].QUANTITY,
              });
            }
          }
          if (products) {
            waitLayer(100, function () {
              dataLayer.push({
                event: arOptimusOptions["COUNTERS"]["GOOGLE_EVENTS"]["CHECKOUT_ORDER"],
                ecommerce: {
                  checkout: {
                    actionField: {
                      step: step,
                      option: option,
                    },
                    products: products,
                  },
                },
                /*"eventCallback": function() {
							    	if((typeof callback !== 'undefined') && (typeof callback === 'function')){
							    		callback();
							    	}
							   }*/
              });
            });
          }
        },
      });
    }
  }
}

if (!funcDefined("delFromBasketCounter")) {
  function delFromBasketCounter(id, callback) {
    if (checkCounters()) {
      $.ajax({
        url: arOptimusOptions["SITE_DIR"] + "ajax/goals.php",
        dataType: "json",
        type: "POST",
        data: { ID: id },
        success: function (item) {
          if (item.ID) {
            waitLayer(100, function () {
              dataLayer.push({
                event: arOptimusOptions["COUNTERS"]["GOOGLE_EVENTS"]["REMOVE_BASKET"],
                ecommerce: {
                  remove: {
                    products: [
                      {
                        id: item.ID,
                        name: item.NAME,
                        category: item.CATEGORY,
                      },
                    ],
                  },
                },
              });
              if (typeof callback == "function") {
                callback();
              }
            });
          }
        },
      });
    }
  }
}

if (!funcDefined("setHeightCompany")) {
  function setHeightCompany() {
    $(".md-50.img").height($(".md-50.big").outerHeight() - 35);
  }
}

if (!funcDefined("initSly")) {
  function initSly() {
    var $frame = $(document).find(".frame");
    var $slidee = $frame.children("ul").eq(0);
    var $wrap = $frame.parent();

    if (arOptimusOptions["PAGES"]["CATALOG_PAGE"]) {
      $frame.sly({
        horizontal: 1,
        itemNav: "basic",
        smart: 1,
        mouseDragging: 0,
        touchDragging: 0,
        releaseSwing: 0,
        startAt: 0,
        scrollBar: $wrap.find(".scrollbar"),
        scrollBy: 1,
        speed: 300,
        elasticBounds: 0,
        easing: "swing",
        dragHandle: 1,
        dynamicHandle: 1,
        clickBar: 1,

        // Buttons
        forward: $wrap.find(".forward"),
        backward: $wrap.find(".backward"),
      });
      $frame.sly("reload");
    }
  }
}

if (!funcDefined("createTableCompare")) {
  function createTableCompare(originalTable, appendDiv, cloneTable) {
    try {
      var clone = originalTable.clone().removeAttr("id").addClass("clone");
      if (cloneTable.length) {
        cloneTable.remove();
        appendDiv.html("");
        appendDiv.html(clone);
      } else {
        appendDiv.append(clone);
      }
    } catch (e) {
    } finally {
    }
  }
}

if (!funcDefined("fillBasketPropsExt")) {
  fillBasketPropsExt = function (that, prop_code, basket_prop_div) {
    var i = 0,
      propCollection = null,
      foundValues = false,
      basketParams = {},
      obBasketProps = null;

    // obBasketProps = that.closest('.catalog_detail').find('.basket_props_block');
    if (that.closest("#fast_view_item").length)
      obBasketProps = that.closest("#fast_view_item").find("#" + basket_prop_div)[0];
    else obBasketProps = BX(basket_prop_div);

    if (!!obBasketProps) {
      propCollection = obBasketProps.getElementsByTagName("select");
      if (!!propCollection && !!propCollection.length) {
        for (i = 0; i < propCollection.length; i++) {
          if (!propCollection[i].disabled) {
            switch (propCollection[i].type.toLowerCase()) {
              case "select-one":
                basketParams[propCollection[i].name] = propCollection[i].value;
                foundValues = true;
                break;
              default:
                break;
            }
          }
        }
      }
      propCollection = obBasketProps.getElementsByTagName("input");
      if (!!propCollection && !!propCollection.length) {
        for (i = 0; i < propCollection.length; i++) {
          if (!propCollection[i].disabled) {
            switch (propCollection[i].type.toLowerCase()) {
              case "hidden":
                basketParams[propCollection[i].name] = propCollection[i].value;
                foundValues = true;
                break;
              case "radio":
                if (propCollection[i].checked) {
                  basketParams[propCollection[i].name] = propCollection[i].value;
                  foundValues = true;
                }
                break;
              default:
                break;
            }
          }
        }
      }
    }
    if (!foundValues) {
      basketParams[prop_code] = [];
      basketParams[prop_code][0] = 0;
    }
    return basketParams;
  };
}
if (!funcDefined("showBasketError")) {
  showBasketError = function (mess, title, addButton, th) {
    var title_set = title ? title : BX.message("ERROR_BASKET_TITLE"),
      isButton = "N",
      thButton = "";
    if (typeof addButton !== undefined) {
      isButton = "Y";
    }
    if (typeof th !== undefined) {
      thButton = th;
    }
    $("body").append("<span class='add-error-bakset' style='display:none;'></span>");
    jqmEd("basket_error", "error-bakset", ".add-error-bakset", mess, this, title_set, isButton, thButton);
    $("body .add-error-bakset").click();
    $("body .add-error-bakset").remove();
  };
}

if (!funcDefined("isRealValue")) {
  function isRealValue(obj) {
    return obj && obj !== "null" && obj !== "undefined";
  }
}

if (!funcDefined("rightScroll")) {
  function rightScroll(prop, id) {
    var el = BX("prop_" + prop + "_" + id);
    if (el) {
      var curVal = parseInt(el.style.marginLeft);
      if (curVal >= 0) el.style.marginLeft = curVal - 20 + "%";
    }
  }
}

if (!funcDefined("leftScroll")) {
  function leftScroll(prop, id) {
    var el = BX("prop_" + prop + "_" + id);
    if (el) {
      var curVal = parseInt(el.style.marginLeft);
      if (curVal < 0) el.style.marginLeft = curVal + 20 + "%";
    }
  }
}

if (!funcDefined("InitOrderCustom")) {
  InitOrderCustom = function () {
    $(".ps_logo img").wrap('<div class="image"></div>');

    $("#bx-soa-order .radio-inline").each(function () {
      if ($(this).find("input").attr("checked") == "checked") {
        $(this).addClass("checked");
      }
    });

    $("#bx-soa-order .checkbox input[type=checkbox]").each(function () {
      if ($(this).attr("checked") == "checked") $(this).parent().addClass("checked");
    });

    // $('#bx-soa-order .bx-authform-starrequired').each(function() {
    // 	var html = $(this).html();
    // 	$(this).closest('label').append('<span class="bx-authform-starrequired"> '+ html + '</span>');
    // 	$(this).detach();
    // });
    $(".bx_ordercart_coupon").each(function () {
      if ($(this).find(".bad").length) $(this).addClass("bad");
      else if ($(this).find(".good").length) $(this).addClass("good");
    });
    /*if (typeof(propsMap) !== 'undefined') {
			$(propsMap).on('click', function () {
				var value = $('#orderDescription').val();
				if ($('#orderDescription')) {
					if (value != '') {
						$('#orderDescription').closest('.form-group').addClass('value_y');
					}
				}
			});
		}*/
  };
}

if (!funcDefined("InitLabelAnimation")) {
  InitLabelAnimation = function (className) {
    // Fix order labels
    if (!$(className).length) {
      return;
    }
    $(className)
      .find(".form-group")
      .each(function () {
        if (
          $(this).find("input[type=text], textarea").length &&
          !$(this).find(".dropdown-block").length &&
          $(this).find("input[type=text], textarea").val() != ""
        ) {
          $(this).addClass("value_y");
        }
      });

    $(document).on("click", className + " .form-group:not(.bx-soa-pp-field) label", function () {
      $(this).parent().find("input, textarea").focus();
    });

    $(document).on(
      "focusout",
      className +
        " .form-group:not(.bx-soa-pp-field) input, " +
        className +
        " .form-group:not(.bx-soa-pp-field) textarea",
      function () {
        var value = $(this).val();
        if (
          value != "" &&
          !$(this).closest(".form-group").find(".dropdown-block").length &&
          !$(this).closest(".form-group").find("#profile_change").length
        ) {
          $(this).closest(".form-group").addClass("value_y");
        } else {
          $(this).closest(".form-group").removeClass("value_y");
        }
      }
    );

    $(document).on(
      "focus",
      className +
        " .form-group:not(.bx-soa-pp-field) input, " +
        className +
        " .form-group:not(.bx-soa-pp-field) textarea",
      function () {
        if (
          !$(this).closest(".form-group").find(".dropdown-block").length &&
          !$(this).closest(".form-group").find("#profile_change").length &&
          !$(this).closest(".form-group").find("[name=PERSON_TYPE_OLD]").length
        ) {
          $(this).closest(".form-group").addClass("value_y");
        }
      }
    );
  };
}

checkPopupWidth = function () {
  $(".popup.show").each(function () {
    var width_form = $(this).actual("width");
    $(this).css({
      "margin-left": $(window).width() > width_form ? "-" + width_form / 2 + "px" : "-" + $(window).width() / 2 + "px",
    });
  });
};

checkCaptchaWidth = function () {
  $(".captcha-row").each(function () {
    var width = $(this).actual("width");
    if ($(this).hasClass("b")) {
      if (width > 320) {
        $(this).removeClass("b");
      }
    } else {
      if (width <= 320) {
        $(this).addClass("b");
      }
    }
  });
};

checkFormWidth = function () {
  $(".form .form_left").each(function () {
    var form = $(this).parents(".form");
    var width = form.actual("width");
    if (form.hasClass("b")) {
      if (width > 417) {
        form.removeClass("b");
      }
    } else {
      if (width <= 417) {
        form.addClass("b");
      }
    }
  });
};

checkFormControlWidth = function () {
  $(".form-control").each(function () {
    var width = $(this).actual("width");
    var labelWidth = $(this).find("label:not(.error) > span").actual("width");
    var errorWidth = $(this).find("label.error").actual("width");
    if (errorWidth > 0) {
      if ($(this).hasClass("h")) {
        if (width > labelWidth + errorWidth + 5) {
          $(this).removeClass("h");
        }
      } else {
        if (width <= labelWidth + errorWidth + 5) {
          $(this).addClass("h");
        }
      }
    } else {
      $(this).removeClass("h");
    }
  });
};

scrollToTop = function () {
  if (arOptimusOptions["THEME"]["SCROLLTOTOP_TYPE"] !== "NONE") {
    var _isScrolling = false;
    // Append Button
    $("body").append(
      $("<a />")
        .addClass(
          "scroll-to-top " +
            arOptimusOptions["THEME"]["SCROLLTOTOP_TYPE"] +
            " " +
            arOptimusOptions["THEME"]["SCROLLTOTOP_POSITION"]
        )
        .attr({ href: "#", id: "scrollToTop" })
    );
    $("#scrollToTop").click(function (e) {
      e.preventDefault();
      $("body, html").animate({ scrollTop: 0 }, 500);
      return false;
    });
    // Show/Hide Button on Window Scroll event.
    $(window).scroll(function () {
      if (!_isScrolling) {
        _isScrolling = true;
        if ($(window).scrollTop() > 150) {
          $("#scrollToTop").stop(true, true).addClass("visible");
          _isScrolling = false;
        } else {
          $("#scrollToTop").stop(true, true).removeClass("visible");
          _isScrolling = false;
        }
        checkScrollToTop();
      }
    });
  }
};

checkScrollToTop = function () {
  var bottom = 55,
    scrollVal = $(window).scrollTop(),
    windowHeight = $(window).height(),
    footerOffset = 70;
  if ($("footer").length) footerOffset = $("footer").offset().top + 70;

  if (arOptimusOptions["THEME"]["SCROLLTOTOP_POSITION"] == "CONTENT") {
    warpperWidth = $("body > .wrapper > .wrapper_inner").width();
    $("#scrollToTop").css("margin-left", Math.ceil(warpperWidth / 2) + 23);
  }

  if (scrollVal + windowHeight > footerOffset) {
    $("#scrollToTop").css("bottom", bottom + scrollVal + windowHeight - footerOffset - 0);
  } else if (parseInt($("#scrollToTop").css("bottom")) > bottom) {
    $("#scrollToTop").css("bottom", bottom);
  }
};

CheckObjectsSizes = function () {
  $(".container iframe,.container object,.container video").each(function () {
    var height_attr = $(this).attr("height");
    var width_attr = $(this).attr("width");
    if (height_attr && width_attr) {
      $(this).css("height", ($(this).outerWidth() * height_attr) / width_attr);
    }
  });
};

if (!funcDefined("reloadTopBasket")) {
  var reloadTopBasket = function reloadTopBasket(action, basketWindow, speed, delay, slideDown, item) {
    var obj = {
      PARAMS: $("#top_basket_params").val(),
      ACTION: action,
    };
    if (typeof item !== "undefined") {
      obj.delete_top_item = "Y";
      obj.delete_top_item_id = item.data("id");
    }
    $.post(
      arOptimusOptions["SITE_DIR"] + "ajax/show_basket_popup.php",
      obj,
      $.proxy(function (data) {
        $(basketWindow).html(data);

        getActualBasket();

        if (arOptimusOptions["THEME"]["SHOW_BASKET_ONADDTOCART"] !== "N") {
          if ($(window).outerWidth() > 520) {
            if (slideDown == "Y") $(basketWindow).find(".basket_popup_wrapp").stop(true, true).slideDown(speed);
            clearTimeout(basketTimeoutSlide);
            basketTimeoutSlide = setTimeout(function () {
              var _this = $("#basket_line").find(".basket_popup_wrapp");
              if (_this.is(":hover")) {
                _this.show();
              } else {
                $("#basket_line").find(".basket_popup_wrapp").slideUp(speed);
              }
            }, delay);
          }
        }
      })
    );
  };
}

/*countdown start*/

if (!funcDefined("initCountdown")) {
  var initCountdown = function initCountdown() {
    if ($(".view_sale_block").size()) {
      $(".view_sale_block").each(function () {
        var activeTo = $(this).find(".active_to").text(),
          dateTo = new Date(activeTo.replace(/(\d+)\.(\d+)\.(\d+)/, "$3/$2/$1"));
        $(this).find(".countdown").countdown(
          {
            until: dateTo,
            format: "dHMS",
            padZeroes: true,
            layout:
              '{d<}<span class="days item">{dnn}<div class="text">{dl}</div></span>{d>} <span class="hours item">{hnn}<div class="text">{hl}</div></span> <span class="minutes item">{mnn}<div class="text">{ml}</div></span> <span class="sec item">{snn}<div class="text">{sl}</div></span>',
          },
          $.countdown.regionalOptions["ru"]
        );
      });
    }
  };
}

if (!funcDefined("initCountdownTime")) {
  var initCountdownTime = function initCountdownTime(block, time) {
    if (time) {
      var dateTo = new Date(time.replace(/(\d+)\.(\d+)\.(\d+)/, "$3/$2/$1"));
      block.find(".countdown").countdown("destroy");
      block.find(".countdown").countdown(
        {
          until: dateTo,
          format: "dHMS",
          padZeroes: true,
          layout:
            '{d<}<span class="days item">{dnn}<div class="text">{dl}</div></span>{d>} <span class="hours item">{hnn}<div class="text">{hl}</div></span> <span class="minutes item">{mnn}<div class="text">{ml}</div></span> <span class="sec item">{snn}<div class="text">{sl}</div></span>',
        },
        $.countdown.regionalOptions["ru"]
      );
      block.find(".view_sale_block").show();
    } else {
      block.find(".view_sale_block").hide();
    }
  };
}

/*countdown end*/

var isOnceInited = (insertFilter = false);
var animationTime = 200;
var delayTime = 200;
var topMenuEnterTimer = false;
var isMobile = jQuery.browser.mobile;

if (isMobile) {
  document.documentElement.className += " mobile";
}

/*filter start*/
if (!funcDefined("checkVerticalMobileFilter")) {
  var checkVerticalMobileFilter = function checkVerticalMobileFilter() {
    if ($(".right_block1.catalog.vertical").length && !$(".left_block.filter_ajax").length) {
      if (typeof window["trackBarOptions"] !== "undefined") {
        window["trackBarValues"] = {};
        for (key in window["trackBarOptions"]) {
          window["trackBarValues"][key] = {
            leftPercent: window["trackBar" + key].leftPercent,
            leftValue: window["trackBar" + key].minInput.value,
            rightPercent: window["trackBar" + key].rightPercent,
            rightValue: window["trackBar" + key].maxInput.value,
          };
        }
      }

      if (window.matchMedia("(max-width: 950px)").matches) {
        if (!insertFilter) {
          $(".js_filter .bx_filter.bx_filter_vertical").html($(".left_block .bx_filter.bx_filter_vertical").html());
          $(".left_block .bx_filter.bx_filter_vertical .bx_filter_section").remove();
          insertFilter = true;
        }
      } else {
        if (insertFilter) {
          $(".left_block .bx_filter.bx_filter_vertical").html($(".js_filter .bx_filter.bx_filter_vertical").html());
          $(".js_filter .bx_filter.bx_filter_vertical .bx_filter_section").remove();
          insertFilter = false;
        }
      }

      if (typeof window["trackBarOptions"] !== "undefined") {
        for (key in window["trackBarOptions"]) {
          window["trackBarOptions"][key].leftPercent = window["trackBarValues"][key].leftPercent;
          window["trackBarOptions"][key].rightPercent = window["trackBarValues"][key].rightPercent;
          window["trackBarOptions"][key].curMinPrice = window["trackBarValues"][key].leftValue;
          window["trackBarOptions"][key].curMaxPrice = window["trackBarValues"][key].rightValue;
          window["trackBar" + key] = new BX.Iblock.SmartFilter(window["trackBarOptions"][key]);
          window["trackBar" + key].minInput.value = window["trackBarValues"][key].leftValue;
          window["trackBar" + key].maxInput.value = window["trackBarValues"][key].rightValue;
        }
      }
    } else if ($(".left_block.filter_ajax").length) {
      var posBlock = $(".ajax_load").position();

      $(".left_block.filter_ajax .bx_filter").css("top", posBlock.top - 13);
      if (
        $(".left_block.filter_ajax .bx_filter").is(":visible") &&
        !$(".adaptive_filter .filter_opener").hasClass("opened") &&
        window.matchMedia("(max-width: 950px)").matches
      ) {
        $(".adaptive_filter .filter_opener").addClass("opened");
      }
    }
  };
}
/*filter end*/

// TOP MENU ANIMATION
$(document).on("click", ".menu_top_block>li .more a", function () {
  $this = $(this);
  $this.parents(".dropdown").first().find(">.hidden").removeClass("hidden");
  $this.parent().addClass("hidden");
  setTimeout(function () {
    $this.parent().remove();
  }, 500);
});

$(document).on("mouseenter", ".menu_top_block.catalogfirst>li>.dropdown>li.full", function () {
  var $submenu = $(this).find(">.dropdown");

  if ($submenu.length) {
    if (topMenuEnterTimer) {
      clearTimeout(topMenuEnterTimer);
      topMenuEnterTimer = false;
    }
  }
});

$(document).on("mouseenter", ".menu_top_block>li:not(.full)", function () {
  var $submenu = $(this).find(">.dropdown");

  if ($submenu.length && !$submenu.hasClass("visible")) {
    var $menu = $(this).parents(".menu");
    var $wrapmenu = $menu.parents(".wrap_menu");
    var wrapMenuWidth = $wrapmenu.actual("outerWidth");
    var wrapMenuLeft = $wrapmenu.offset().left;
    var wrapMenuRight = wrapMenuLeft + wrapMenuWidth;
    var left = wrapMenuRight - ($(this).offset().left + $submenu.actual("outerWidth"));
    if (
      window.matchMedia("(min-width: 951px)").matches &&
      $(this).hasClass("catalog") &&
      ($(".banner_auto").hasClass("catalog_page") || $(".banner_auto").hasClass("front_page"))
    ) {
      return;
    }
    if (left < 0) {
      $submenu.css({ left: left + "px" });
    }
    $submenu.stop().slideDown(animationTime, function () {
      $submenu.css({ height: "", overflow: "visible" });
    });

    $(this).on("mouseleave", function () {
      var leaveTimer = setTimeout(function () {
        $submenu.stop().slideUp(animationTime, function () {
          $submenu.css({ left: "" });
        });
      }, delayTime);

      $(this).on("mouseenter", function () {
        if (leaveTimer) {
          clearTimeout(leaveTimer);
          leaveTimer = false;
        }
      });
    });
  }
});

$(document).on("mouseenter", ".menu_top_block>li:not(.full)", function () {
  if (
    window.matchMedia("(min-width: 951px)").matches &&
    $(this).hasClass("catalog") &&
    ($(".banner_auto").hasClass("catalog_page") || $(".banner_auto").hasClass("front_page"))
  ) {
    return;
  }

  var _this = $(this).find(">.dropdown");
  var $catalogNlo = _this.find("[data-nlo]");
  var isNloLoaded = !$catalogNlo.length;

  if (!isNloLoaded) {
    if (!$catalogNlo.hasClass("nlo-loadings")) {
      $catalogNlo.addClass("nlo-loadings");
      setTimeout(function () {
        $.ajax({
          data: { nlo: $catalogNlo.attr("data-nlo") },
          success: function (response) {
            // stop ya metrika webvisor DOM indexer
            pauseYmObserver();

            isNloLoaded = true;
            $catalogNlo[0].insertAdjacentHTML("beforebegin", $.trim(response));
            $catalogNlo.remove();

            // resume ya metrika webvisor
            // (300ms transition) + (100ms scroll handler)
            setTimeout(resumeYmObserver, 400);
          },
          error: function () {
            $catalogNlo.removeClass("nlo-loadings");
          },
        });
      }, 300);
    }
  }
});

$(document).on("mouseenter", ".menu_top_block>li .dropdown>li", function () {
  var $this = $(this);
  var $submenu = $this.find(">.dropdown");

  if (
    $submenu.length &&
    ((!$this.parents(".full").length && !$this.hasClass("full")) || $this.parents(".more").length)
  ) {
    var $menu = $this.parents(".menu");
    var $wrapmenu = $menu.parents(".wrap_menu");
    var arParentSubmenuForOpacity = [];
    topMenuEnterTimer = setTimeout(function () {
      var wrapMenuWidth = $wrapmenu.actual("outerWidth");
      var wrapMenuLeft = $wrapmenu.offset().left;
      var wrapMenuRight = wrapMenuLeft + wrapMenuWidth;
      var $parentSubmenu = $this.parent();
      var bToLeft = $parentSubmenu.hasClass("toleft") ? true : false;
      if (!bToLeft) {
        bToLeft = $this.offset().left + $this.actual("outerWidth") + $submenu.actual("outerWidth") > wrapMenuRight;
      } else {
        bToLeft = $this.offset().left + $this.actual("outerWidth") - $submenu.actual("outerWidth") < wrapMenuLeft;
      }

      if (bToLeft) {
        $this.find(">.dropdown").addClass("toleft").show();
      } else {
        $this.find(">.dropdown").removeClass("toleft").show();
      }
      var submenuLeft = $submenu.offset().left;
      var submenuRight = submenuLeft + $submenu.actual("outerWidth");

      $this.parents(".dropdown").each(function () {
        var $this = $(this);
        var leftOffset = $this.offset().left;
        var rightOffset = leftOffset + $this.actual("outerWidth");
        if (
          (leftOffset >= submenuLeft && leftOffset < submenuRight - 1) ||
          (rightOffset > submenuLeft + 1 && rightOffset <= submenuRight)
        ) {
          arParentSubmenuForOpacity.push($this);
          $this.find(">li>a").css({ opacity: "0.1" });
        }
      });
    }, delayTime);

    $this.unbind("mouseleave");
    $this.on("mouseleave", function () {
      var leaveTimer = setTimeout(function () {
        $this.find(".dropdown").removeClass("toleft").hide();
        if (arParentSubmenuForOpacity.length) {
          for (i in arParentSubmenuForOpacity) {
            arParentSubmenuForOpacity[i].find(">li>a").css({ opacity: "" });
          }
        }
      }, delayTime);

      $this.unbind("mouseenter");
      $this.on("mouseenter", function () {
        if (leaveTimer) {
          clearTimeout(leaveTimer);
          leaveTimer = false;
        }
      });
    });
  }
});

getGridSize = function (counts) {
  var counts_item = 1;
  //wide
  if (window.matchMedia("(min-width: 1200px)").matches) {
    counts_item = counts[0];
  }

  //large
  if (window.matchMedia("(max-width: 1200px)").matches) {
    counts_item = counts[1];
  }

  //middle
  if (window.matchMedia("(max-width: 992px)").matches) {
    counts_item = counts[2];
  }

  //small
  if (counts[3]) {
    if (window.matchMedia("(max-width: 600px)").matches) {
      counts_item = counts[3];
    }
  }

  //exsmall
  if (counts[4]) {
    if (window.matchMedia("(max-width: 400px)").matches) {
      counts_item = counts[4];
    }
  }

  return counts_item;
};

CheckFlexSlider = function () {
  $(".flexslider:not(.thmb)").each(function () {
    var slider = $(this);
    slider.resize();
    var counts = slider.data("flexslider").vars.counts;
    if (typeof counts != "undefined") {
      var cnt = getGridSize(counts);
      var to0 =
        cnt != slider.data("flexslider").vars.minItems ||
        cnt != slider.data("flexslider").vars.maxItems ||
        cnt != slider.data("flexslider").vars.move;
      if (to0) {
        slider.data("flexslider").vars.minItems = cnt;
        slider.data("flexslider").vars.maxItems = cnt;
        slider.data("flexslider").vars.move = cnt;
        slider.flexslider(0);
        slider.resize();
        slider.resize(); // twise!
      }
    }
  });
};

InitFlexSlider = function () {
  $(".flexslider:not(.thmb):not(.flexslider-init)").each(function () {
    var slider = $(this);
    var options;
    var defaults = {
      animationLoop: false,
      controlNav: false,
      directionNav: true,
      animation: "slide",
    };
    var config = $.extend({}, defaults, options, slider.data("plugin-options"));
    if (!slider.parent().hasClass("top_slider_wrapp")) {
      if (typeof config.counts != "undefined" && config.direction !== "vertical") {
        config.maxItems = getGridSize(config.counts);
        config.minItems = getGridSize(config.counts);
        config.move = getGridSize(config.counts);
        config.itemWidth = 200;
      }

      config.after = function (slider) {
        var eventdata = { slider: slider };
        BX.onCustomEvent("onSlide", [eventdata]);
      };
      config.start = function (slider) {
        var eventdata = { slider: slider };
        BX.onCustomEvent("onSlideInit", [eventdata]);
      };

      config.end = function (slider) {
        var eventdata = { slider: slider };
        BX.onCustomEvent("onSlideEnd", [eventdata]);
      };

      slider.flexslider(config).addClass("flexslider-init");
      if (config.controlNav) slider.addClass("flexslider-control-nav");
      if (config.directionNav) slider.addClass("flexslider-direction-nav");
    }
  });
};

InitZoomPict = function (el) {
  var block = $(".zoom_picture");
  if (typeof el !== "undefined") block = el;
  if (block.length) {
    var slide = block.closest(".slides");
    var zoomer = block,
      options,
      defaults = {
        zoomWidth: 200,
        zoomHeight: 200,
        adaptive: false,
        title: true,
        Xoffset: 15,
      };
    var config = $.extend({}, defaults, options, zoomer.data("plugin-options"));
    zoomer.xzoom(config);

    block.on("mouseleave", function () {
      block.data("xzoom").movezoom(event);
      /*
			if($('.xzoom-lens').length)
				block.data('xzoom').closezoom();
			*/
    });
  }
};

if (!funcDefined("pauseYmObserver")) {
  // pause ya metrika webvisor MutationObserver callback (DOM indexer)
  // use before insert html with some animation
  pauseYmObserver = function () {
    if (
      typeof MutationObserver === "function" &&
      typeof MutationObserver.observers === "object" &&
      typeof MutationObserver.observers.ym === "object"
    ) {
      if (typeof pauseYmObserver.cnt === "undefined") {
        pauseYmObserver.cnt = 0;
      }

      ++pauseYmObserver.cnt;

      if (!MutationObserver.observers.ym.paused) {
        MutationObserver.observers.ym.pause();
      }
    }
  };
}

if (!funcDefined("resumeYmObserver")) {
  // resume ya metrika webvisor MutationObserver callback
  // use when animation finished
  resumeYmObserver = function () {
    if (
      typeof MutationObserver === "function" &&
      typeof MutationObserver.observers === "object" &&
      typeof MutationObserver.observers.ym === "object"
    ) {
      if (typeof pauseYmObserver.cnt === "undefined") {
        pauseYmObserver.cnt = 1;
      }

      pauseYmObserver.cnt -= pauseYmObserver.cnt > 0 ? 1 : 0;

      if (!pauseYmObserver.cnt && MutationObserver.observers.ym.paused) {
        MutationObserver.observers.ym.resume();
      }
    }
  };
}

var arBasketAsproCounters = {};
SetActualBasketFlyCounters = function () {
  if (arBasketAsproCounters.DEFAULT == true) {
    $.ajax({
      url: arOptimusOptions["SITE_DIR"] + "ajax/basket_fly.php",
      type: "post",
      success: function (html) {
        $("#basket_line .basket_fly").addClass("loaded").html(html);
      },
    });
  } else {
    $(".basket_fly .opener .basket_count .count")
      .attr("class", "count" + (arBasketAsproCounters.READY.COUNT > 0 ? "" : " empty_items"))
      .find(".items span")
      .text(arBasketAsproCounters.READY.COUNT);
    $(".basket_fly .opener .basket_count + a").attr("href", arBasketAsproCounters["READY"]["HREF"]);
    $(".basket_fly .opener .basket_count")
      .attr("title", arBasketAsproCounters.READY.TITLE)
      .attr("class", "basket_count small clicked" + (arBasketAsproCounters.READY.COUNT > 0 ? "" : " empty"));

    $(".basket_fly .opener .wish_count .count")
      .attr("class", "count" + (arBasketAsproCounters.DELAY.COUNT > 0 ? "" : " empty_items"))
      .find(".items span")
      .text(arBasketAsproCounters.DELAY.COUNT);
    $(".basket_fly .opener .wish_count + a").attr("href", arBasketAsproCounters.DELAY.HREF);
    $(".basket_fly .opener .wish_count")
      .attr("title", arBasketAsproCounters.DELAY.TITLE)
      .attr("class", "wish_count small clicked" + (arBasketAsproCounters.DELAY.COUNT > 0 ? "" : " empty"));

    $(".basket_fly .opener .compare_count .count")
      .attr("class", "count" + (arBasketAsproCounters.COMPARE.COUNT > 0 ? "" : " empty_items"))
      .find(".items span")
      .text(arBasketAsproCounters.COMPARE.COUNT);
    $(".basket_fly .opener .compare_count + a").attr("href", arBasketAsproCounters.COMPARE.HREF);

    $(".basket_fly .opener .user_block")
      .attr("title", arBasketAsproCounters.PERSONAL.TITLE)
      .find("+ a")
      .attr("href", arBasketAsproCounters.PERSONAL.HREF);
    $(".basket_fly .opener .user_block .wraps_icon_block")
      .attr(
        "class",
        "wraps_icon_block" +
          (arBasketAsproCounters.PERSONAL.ID > 0 ? " user_auth" : " user_reg") +
          (arBasketAsproCounters.PERSONAL.SRC ? " w_img" : " no_img")
      )
      .attr(
        "style",
        arBasketAsproCounters.PERSONAL.SRC
          ? 'background:url("' + arBasketAsproCounters.PERSONAL.SRC + '") center center no-repeat;'
          : ""
      );

    const eventdata = { action: "loadActualBasket" };
    BX.onCustomEvent("onCompleteAction", [eventdata]);
  }
};

/*set price item*/
if (!funcDefined("setPriceItem")) {
  var setPriceItem = function setPriceItem(main_block, quantity, rewrite_price, check_quantity, is_sku) {
    var old_quantity = main_block.find(".to-cart").attr("data-ratio"),
      value =
        typeof rewrite_price !== "undefined" && rewrite_price
          ? rewrite_price
          : main_block.find(".to-cart").attr("data-value"),
      currency = main_block.find(".to-cart").attr("data-currency"),
      total_block = '<div class="total_summ"><div>' + BX.message("TOTAL_SUMM_ITEM") + "<span></span></div></div>",
      price_block = main_block.find(".cost.prices"),
      check = typeof check_quantity !== "undefined" && check_quantity;

    if (main_block.find(".buy_block").length) {
      if (!main_block.find(".buy_block .total_summ").length) $(total_block).appendTo(main_block.find(".buy_block"));
    } else if (main_block.find(".counter_wrapp").length) {
      if (!main_block.find(".counter_wrapp .total_summ").length)
        $(total_block).appendTo(main_block.find(".counter_wrapp:first"));
    }
    if (main_block.find(".total_summ").length) {
      if (value && currency) {
        if (
          ((1 == quantity && old_quantity == quantity) || (typeof is_sku !== "undefined" && is_sku && !check)) &&
          arOptimusOptions.THEME.SHOW_TOTAL_SUMM !== "ALWAYS"
        ) {
          main_block.find(".total_summ").slideUp(200);
        } else {
          main_block.find(".total_summ span").html(BX.Currency.currencyFormat(value * quantity, currency, true));
          if (main_block.find(".total_summ").is(":hidden")) main_block.find(".total_summ").slideDown(200);
        }
      } else main_block.find(".total_summ").slideUp(200);
    }
  };
}

if (!funcDefined("getCurrentPrice")) {
  var getCurrentPrice = function getCurrentPrice(price, currency, print_price) {
    var val = "";
    var format_value = BX.Currency.currencyFormat(price, currency);
    if (print_price.indexOf(format_value) >= 0) {
      val = print_price.replace(
        format_value,
        '<span class="price_value">' + format_value + '</span><span class="price_currency">'
      );
      val += "</span>";
    } else {
      val = print_price;
    }

    return val;
  };
}

$(document).ready(function () {
  //ecommerce order
  if (arOptimusOptions["PAGES"]["ORDER_PAGE"]) {
    var arUrl = parseUrlQuery();
    if ("ORDER_ID" in arUrl) {
      var _id = arUrl["ORDER_ID"];
      if (arOptimusOptions["COUNTERS"]["USE_FULLORDER_GOALS"] !== "N") {
        var eventdata = { goal: "goal_order_success", result: _id };
        BX.onCustomEvent("onCounterGoals", [eventdata]);
      }

      if (checkCounters()) {
        if (typeof BX.localStorage !== "undefined") {
          var d = BX.localStorage.get("gtm_e_" + _id);
          if (typeof d === "object") {
            waitLayer(100, function () {
              dataLayer.push({ event: arOptimusOptions["COUNTERS"]["GOOGLE_EVENTS"]["PURCHASE"], ecommerce: d });
            });
          }

          if (typeof localStorage !== "undefined") {
            localStorage.removeItem("gtm_e_" + _id);
          }
        }
      }
    }
  }

  // ya.metrika debug
  if (arOptimusOptions["COUNTERS"]["USE_DEBUG_GOALS"] === "Y") {
    $.cookie("_ym_debug", 1, { path: "/" });
  } else {
    $.cookie("_ym_debug", null, { path: "/" });
  }

  scrollToTop();
  checkVerticalMobileFilter();
  checkFormWidth();

  if (!jQuery.browser.safari) {
    setTimeout(function () {
      InitTopestMenuGummi();
      InitTopMenuGummi();
      isOnceInited = true;

      InitFlexSlider();

      // setTimeout(function() {$(window).resize();}, 150); // need to check resize flexslider & menu

      try {
        // SHOW TOP MENU ON READY AFTER GUMMI
        $("header .wrap_menu").css({ overflow: "visible" });
        $(".visible_on_ready").removeClass("visible_on_ready");
      } catch (e) {
        console.error(e);
      }
    }, 100);
  } else {
    setTimeout(function () {
      $(window).resize(); // need to check resize flexslider & menu
      setTimeout(function () {
        InitTopestMenuGummi();
        InitTopMenuGummi();
        isOnceInited = true;

        InitFlexSlider();

        try {
          // SHOW TOP MENU ON READY AFTER GUMMI
          $("header .wrap_menu").css({ overflow: "visible" });
          $(".visible_on_ready").removeClass("visible_on_ready");
        } catch (e) {
          console.error(e);
        }

        setTimeout(function () {
          $(window).scroll();
        }, 50);
      }, 50);
    }, 350);
  }

  InitZoomPict();

  $("body").on("click", ".captcha_reload", function (e) {
    var captcha = $(this).parents(".captcha-row");
    e.preventDefault();
    $.ajax({
      url: arOptimusOptions["SITE_DIR"] + "ajax/captcha.php",
    }).done(function (text) {
      captcha.find("input[name=captcha_sid]").val(text);
      captcha.find("img").attr("src", "/bitrix/tools/captcha.php?captcha_sid=" + text);
      captcha.find("input[name=captcha_word]").val("").removeClass("error");
      captcha.find(".captcha_input").removeClass("error").find(".error").remove();
    });
  });

  setTimeout(function () {
    $(".bg_image_site").css({
      opacity: 1,
    });
  }, 200);

  if (window.matchMedia("(min-width: 768px)").matches) {
    $(".wrapper_middle_menu.wrap_menu").removeClass("mobile");
  }

  if (window.matchMedia("(max-width: 767px)").matches) {
    $(".wrapper_middle_menu.wrap_menu").addClass("mobile");
  }

  setTimeout(function () {
    $(window).scroll();
  }, 400);

  $(".menu_top_block .v_bottom > a").on("click", function (e) {
    if ($(e.target).hasClass("toggle_block")) e.preventDefault();
  });
  $(".menu_top_block .v_bottom > a .toggle_block").on("click", function (e) {
    $(this).closest(".v_bottom").toggleClass("opened");
    $(this).closest(".v_bottom").find(">.dropdown").slideToggle();
  });

  $(document).on("click", ".show_props",  function() {
    $(this).prev(".props_list_wrapp").stop().slideToggle(333);
    $(this).find(".char_title").toggleClass("opened");
  });

  $(document).on(
    "mouseenter",
    ".menu_top_block.catalog_block >.dropdown>li.m_line, .menu_top_block.catalogfirst > li >.dropdown>li.m_line",
    function () {
      var _this = $(this);
      if (window.dropdownTimeout && window.dropdownTimeout[_this.index()]) {
        clearTimeout(window.dropdownTimeout[_this.index()]);
      }

      var dropdown = _this.find("> ul");
      if (dropdown.length) {
        var offset = dropdown[0].getBoundingClientRect();
        var offsetLi = _this[0].getBoundingClientRect();
        var offsetUl = _this.closest("ul")[0].getBoundingClientRect();
        var bottomOuter = document.documentElement.clientHeight - offset.y - offset.height;
        if (bottomOuter < 0) {
          if (offset.height > document.documentElement.clientHeight && offsetUl.y < 0) {
            var dropdownTopMax = -offsetLi.y;
          } else {
            var dropdownTopMax = offsetUl.y - offsetLi.y;
          }
          var dropdownBottom = document.documentElement.clientHeight - offset.y - offsetLi.height;
          var dropdownTop = dropdownBottom + offsetLi.height - offset.height;
          if (dropdownTop < dropdownTopMax) {
            dropdownTop = dropdownTopMax;
          }
          dropdown.css({
            top: dropdownTop + "px",
            bottom: -dropdownBottom + "px",
            overflow: "auto",
          });
        }
      }
    }
  );

  $(document).on(
    "mouseleave",
    ".menu_top_block.catalog_block >.dropdown>li.m_line, .menu_top_block.catalogfirst > li >.dropdown>li.m_line",
    function () {
      var _this = $(this);
      var dropdown = _this.find("> ul");
      if (dropdown.length) {
        if (!window.dropdownTimeout) {
          window.dropdownTimeout = {};
        }
        window.dropdownTimeout[_this.index()] = setTimeout(function () {
          dropdown.css({
            top: "",
            bottom: "",
          });
        }, 300);
      }
    }
  );

  $(document).on("click", ".see_more",  function(e) {
    e.preventDefault();
    var see_more = $(this).is(".see_more") ? $(this) : $(this).parents(".see_more").first();
    var see_moreText = see_more.find("> a").length ? see_more.find("> a") : see_more;
    var see_moreHiden = see_more.parent().find("> .d");
    if (see_more.hasClass("open")) {
      see_moreText.text(BX.message("CATALOG_VIEW_MORE"));
      see_more.removeClass("open");
      see_moreHiden.hide();
    } else {
      see_moreText.text(BX.message("CATALOG_VIEW_LESS"));
      see_more.addClass("open");
      see_moreHiden.show();
    }
    return false;
  });

  $(".button.faq_button").click(function (e) {
    e.preventDefault();
    $(this).toggleClass("opened");
    $(".faq_ask .form").slideToggle();
  });

  $(".staff.list .staff_section .staff_section_title a").click(function (e) {
    e.preventDefault();
    $(this).parents(".staff_section").toggleClass("opened");
    $(this).parents(".staff_section").find(".staff_section_items").stop().slideToggle(600);
    $(this).parents(".staff_section_title").find(".opener_icon").toggleClass("opened");
  });

  $(".jobs_wrapp .item .name").click(function (e) {
    $(this).closest(".item").toggleClass("opened");
    $(this).closest(".item").find(".description_wrapp").stop().slideToggle(600);
    $(this).closest(".item").find(".opener_icon").toggleClass("opened");
  });

  $(document).on("click", ".faq.list .item .q a",  function(e) {
    e.preventDefault();
    $(this).parents(".item").toggleClass("opened");
    $(this).parents(".item").find(".a").stop().slideToggle();
    $(this).parents(".item").find(".q .opener_icon").toggleClass("opened");
  });

  $(".opener_icon").click(function (e) {
    e.preventDefault();
    $(this).parent().find("a").trigger("click");
  });

  $(document).on("click", ".to-order",  function(e) {
    e.preventDefault();
    $("body").append("<span class='evb-toorder' style='display:none;'></span>");
    jqmEd("to-order", arOptimusOptions["FORM"]["TOORDER_FORM_ID"], ".evb-toorder", "", this);
    $("body .evb-toorder").click();
    $("body .evb-toorder").remove();
  });

  $(document).on("click", ".cheaper",  function(e) {
    e.preventDefault();
    $("body").append("<span class='evb-cheaper' style='display:none;'></span>");
    jqmEd("cheaper", arOptimusOptions["FORM"]["CHEAPER_FORM_ID"], ".evb-cheaper", "", this);
    $("body .evb-cheaper").click();
    $("body .evb-cheaper").remove();
  });

  $(document).on('click', '.delivery', function(e){
		e.preventDefault();
		var dataProductId = $(this).data('param-product_id');
		var dataQuantity = $(this).data('param-quantity');
		$("body").append("<span class='evb-delivery' style='display:none;'></span>");
		jqmEd('delivery', arOptimusOptions['FORM']['DELIVERY_FORM_ID'].toLowerCase(), '.evb-delivery', 'product_id='+dataProductId+'&quantity='+dataQuantity, this);
		$("body .evb-delivery").click();
		$("body .evb-delivery").remove();
	
	});
	$(".animate-load").on("click", function () {
		if (!jQuery.browser.mobile) {
			$(this).parent().addClass("loadings");
		}
  });
	 
	// init calculate delivery with preview
	initCalculatePreview(); 

  //$(".dotdot").dotdotdot();

  $(document).on("click", ".more_block", function () {
    const content_offset = $(".catalog_detail .tabs_section").offset();
    const $descriptionTab = document.querySelector('.tabs_section .nav-tabs #description_tab');

    if (window.matchMedia("(min-width: 768px)").matches) {
      if ($("#headerfixed").length) {
        content_offset.top -= $("#headerfixed").height();
      }
      if ($(".product-item-detail-tabs-container-fixed").length) {
        content_offset.top -= $(".product-item-detail-tabs-container-fixed").height();
      }
    }

    if ($descriptionTab) {
      $descriptionTab.click();
    }

    $("html, body").animate({ scrollTop: content_offset.top - 43 }, 400);
  });

  $(document).on("click", ".counter_block:not(.basket) .plus", function () {
    if (!$(this).parents(".basket_wrapp").length) {
      if ($(this).parent().data("offers") != "Y") {
        var isDetailSKU2 = $(this).parents(".counter_block_wr").length,
          input = $(this).parents(".counter_block").find("input[type=text]"),
          tmp_ratio = !isDetailSKU2
            ? $(this).parents(".counter_wrapp").find(".to-cart").data("ratio")
            : $(this).parents(".counter_block_wr").find(".button_block .to-cart").data("ratio"),
          isDblQuantity = !isDetailSKU2
            ? $(this).parents(".counter_wrapp").find(".to-cart").data("float_ratio")
            : $(this).parents(".counter_block_wr").find(".button_block .to-cart").data("float_ratio"),
          ratio = isDblQuantity ? parseFloat(tmp_ratio) : parseInt(tmp_ratio, 10),
          max_value = "";
        currentValue = input.val();

        if (isDblQuantity)
          ratio =
            Math.round(ratio * arOptimusOptions.JS_ITEM_CLICK.precisionFactor) /
            arOptimusOptions.JS_ITEM_CLICK.precisionFactor;

        curValue = isDblQuantity ? parseFloat(currentValue) : parseInt(currentValue, 10);

        curValue += ratio;
        if (isDblQuantity) {
          curValue =
            Math.round(curValue * arOptimusOptions.JS_ITEM_CLICK.precisionFactor) /
            arOptimusOptions.JS_ITEM_CLICK.precisionFactor;
        }
        if (parseFloat($(this).data("max")) > 0) {
          if (input.val() <= $(this).data("max")) {
            if (curValue <= $(this).data("max")) input.val(curValue);

            input.change();
          }
        } else {
          input.val(curValue);
          input.change();
        }
      }
    }
  });

  $(document).on("click", ".counter_block:not(.basket) .minus", function () {
    if (!$(this).parents(".basket_wrapp").length) {
      if ($(this).parent().data("offers") != "Y") {
        var isDetailSKU2 = $(this).parents(".counter_block_wr").length;
        input = $(this).parents(".counter_block").find("input[type=text]");
        (tmp_ratio = !isDetailSKU2
          ? $(this).parents(".counter_wrapp").find(".to-cart").data("ratio")
          : $(this).parents(".counter_block_wr").find(".button_block .to-cart").data("ratio")),
          (isDblQuantity = !isDetailSKU2
            ? $(this).parents(".counter_wrapp").find(".to-cart").data("float_ratio")
            : $(this).parents(".counter_block_wr").find(".button_block .to-cart").data("float_ratio")),
          (ratio = isDblQuantity ? parseFloat(tmp_ratio) : parseInt(tmp_ratio, 10)),
          (max_value = "");
        currentValue = input.val();

        if (isDblQuantity)
          ratio =
            Math.round(ratio * arOptimusOptions.JS_ITEM_CLICK.precisionFactor) /
            arOptimusOptions.JS_ITEM_CLICK.precisionFactor;

        curValue = isDblQuantity ? parseFloat(currentValue) : parseInt(currentValue, 10);

        curValue -= ratio;
        if (isDblQuantity) {
          curValue =
            Math.round(curValue * arOptimusOptions.JS_ITEM_CLICK.precisionFactor) /
            arOptimusOptions.JS_ITEM_CLICK.precisionFactor;
        }

        if (parseFloat($(this).parents(".counter_block").find(".plus").data("max")) > 0) {
          if (currentValue > ratio) {
            if (curValue < ratio) {
              input.val(ratio);
            } else {
              input.val(curValue);
            }
            input.change();
          }
        } else {
          if (curValue > ratio) {
            input.val(curValue);
          } else {
            if (ratio) {
              input.val(ratio);
            } else if (currentValue > 1) {
              input.val(curValue);
            }
          }
          input.change();
        }
      }
    }
  });

  $(".counter_block input[type=text]").numeric({ allow: "." });
  $(".counter_block input[type=text]").on("focus", function () {
    $(this).addClass("focus");
  });
  $(".counter_block input[type=text]").on("blur", function () {
    $(this).removeClass("focus");
  });

  var timerInitCalculateDelivery = false;

  $(document).on("change", ".counter_block input[type=text]", function (e) {
    if (!$(this).parents(".basket_wrapp").length) {
      var val = $(this).val(),
        tmp_ratio = $(this).parents(".counter_wrapp").find(".to-cart").data("ratio")
          ? $(this).parents(".counter_wrapp").find(".to-cart").data("ratio")
          : $(this).parents(".counter_block_wr").find(".button_block .to-cart").data("ratio"),
        isDblQuantity = $(this).parents(".counter_wrapp").find(".to-cart").data("float_ratio")
          ? $(this).parents(".counter_wrapp").find(".to-cart").data("float_ratio")
          : $(this).parents(".counter_block_wr").find(".button_block .to-cart").data("float_ratio"),
        ratio = isDblQuantity ? parseFloat(tmp_ratio) : parseInt(tmp_ratio, 10),
        diff = val % ratio;
      if (isDblQuantity) {
        ratio =
          Math.round(ratio * arOptimusOptions.JS_ITEM_CLICK.precisionFactor) /
          arOptimusOptions.JS_ITEM_CLICK.precisionFactor;
        if (
          Math.round(diff * arOptimusOptions.JS_ITEM_CLICK.precisionFactor) /
            arOptimusOptions.JS_ITEM_CLICK.precisionFactor ==
          ratio
        )
          diff = 0;
      }

      if ($(this).hasClass("focus")) {
        const intCount =
          Math.round(
            Math.round((val * arOptimusOptions.JS_ITEM_CLICK.precisionFactor) / ratio) /
              arOptimusOptions.JS_ITEM_CLICK.precisionFactor
          ) || 1;
        val = intCount <= 1 ? ratio : intCount * ratio;
        val =
          Math.round(val * arOptimusOptions.JS_ITEM_CLICK.precisionFactor) /
          arOptimusOptions.JS_ITEM_CLICK.precisionFactor;
      }

      if (parseFloat($(this).parents(".counter_block").find(".plus").data("max")) > 0) {
        if (val > parseFloat($(this).parents(".counter_block").find(".plus").data("max"))) {
          val = parseFloat($(this).parents(".counter_block").find(".plus").data("max"));
        }
      }

      if (val < ratio) {
        val = ratio;
      } else if (!parseFloat(val)) {
        val = 1;
      }

      $(this).parents(".counter_block").parent().parent().find(".to-cart").attr("data-quantity", val);
      $(this).parents(".counter_block").parent().parent().find(".one_click").attr("data-quantity", val);
      $(this).parents(".counter_block_wr").find(".one_click").attr("data-quantity", val);
      $(this).val(val);

      var $calculate = $(this).closest(".item").length
			? $(this).closest(".item").find(".calculate-delivery")
			: $(this).closest(".catalog_detail").find(".calculate-delivery");
			if ($calculate.length) {
				$calculate.each(function () {
				var $calculateSpan = $(this).find("span[data-event=jqm]").first();
		
				if ($calculateSpan.length) {
					var $clone = $calculateSpan.clone();
					$clone.attr("data-param-quantity", val).removeClass("clicked");
					$clone.insertAfter($calculateSpan).on("click", function () {
					if (!jQuery.browser.mobile) {
						$(this).parent().addClass("loadings");
					}
					});
					$calculateSpan.remove();
				}
		
				if ($(this).hasClass("with_preview")) {
					$(this).removeClass("inited");
		
					if (timerInitCalculateDelivery) {
					clearTimeout(timerInitCalculateDelivery);
					}
		
					timerInitCalculateDelivery = setTimeout(function () {
					initCalculatePreview();
					timerInitCalculateDelivery = false;
					}, 1000);
				}
				});
			}

      var eventdata = { type: "change", params: { id: $(this), value: val } };
      BX.onCustomEvent("onCounterProductAction", [eventdata]);
    }
  });

  BX.addCustomEvent("onCounterProductAction", function (eventdata) {
    if (typeof eventdata != "object") {
      eventdata = { type: "undefined" };
    }
    try {
      if (typeof eventdata.type != "undefined") {
        if (!eventdata.params.id.closest(".gifts").length) {
          // no gift
          var obProduct = eventdata.params.id.data("product");
          if (eventdata.params.id.closest(".item_info").find(".sku_props").length) {
            if (typeof window.obSkuQuantys === "undefined") window.obSkuQuantys = {};

            const offerID = eventdata.params.id.closest(".offer_buy_block").find(".to-cart").data("item");
            window.obSkuQuantys[offerID] = eventdata.params.value;
          }

          if (typeof window[obProduct] === "object") {
            if (obProduct === "obOffers") {
              setPriceAction("", "", "Y");
            } else {
              window[obProduct].setPriceAction("Y");
            }
          } else if (eventdata.params.id.length) {
            if (
              eventdata.params.id.closest(".main_item_wrapper").length &&
              arOptimusOptions["THEME"]["SHOW_TOTAL_SUMM"] != "N"
            ) {
              setPriceItem(eventdata.params.id.closest(".main_item_wrapper"), eventdata.params.value);
            }
          }
          BX.onCustomEvent("onCounterProductActionResize");
        }
      }
    } catch (e) {
      console.error(e);
    }
  });

  /*slide cart*/
  $(document).on("mouseenter", "#basket_line .basket_normal:not(.empty_cart):not(.bcart) .basket_block ", function () {
    $(this).closest(".basket_normal").find(".popup").addClass("block");
    $(this).closest(".basket_normal").find(".basket_popup_wrapp").stop(true, true).slideDown(150);
  });
  $(document).on("mouseleave", "#basket_line .basket_normal .basket_block ", function () {
    var th = $(this);
    $(this)
      .closest(".basket_normal")
      .find(".basket_popup_wrapp")
      .stop(true, true)
      .slideUp(150, function () {
        th.closest(".basket_normal").find(".popup").removeClass("block");
      });
  });

  $(document).on("click", ".fast_view_block",  function() {
    var _th = $(this),
      iblockid = _th.data("param-iblock_id"),
      href = _th.data("param-item_href"),
      name = _th.data("param-form_id");

    $("body #popup_iframe_wrapper")
      .find("." + name + "_frame")
      .remove();
    $("body #popup_iframe_wrapper")
      .find("." + name + "_trigger")
      .remove();
    $("body #popup_iframe_wrapper").append('<div class="' + name + '_frame popup"></div>');
    $("body #popup_iframe_wrapper").append('<div class="' + name + '_trigger"></div>');
    $("." + name + "_frame").jqm({
      trigger: "." + name + "_trigger",
      onHide: function (hash) {
        onHidejqm(name, hash);
      },
      onLoad: function (hash) {
        onLoadjqm(name, hash);
      },
      ajax:
        arOptimusOptions["SITE_DIR"] + "ajax/form.php?form_id=fast_view&iblock_id=" + iblockid + "&item_href=" + href,
    });
    $("." + name + "_trigger").click();
  });

  $(document).on("click", ".popup_button_basket", function () {
    var th = $(".to-cart[data-item=" + $(this).data("item") + "]");

    var val = th.attr("data-quantity");

    if (!val) $val = 1;

    var tmp_props = th.data("props"),
      props = "",
      part_props = "",
      add_props = "N",
      fill_prop = {},
      iblockid = th.data("iblockid"),
      offer = th.data("offers"),
      rid = "",
      basket_props = "",
      item = th.attr("data-item");

    if (offer != "Y") {
      offer = "N";
    } else {
      basket_props = th.closest(".prices_tab").find(".bx_sku_props input").val();
    }
    if (tmp_props) {
      props = tmp_props.split(";");
    }
    if (th.data("part_props")) {
      part_props = th.data("part_props");
    }
    if (th.data("add_props")) {
      add_props = th.data("add_props");
    }
    if ($(".rid_item").length) {
      rid = $(".rid_item").data("rid");
    } else if (th.data("rid")) {
      rid = th.data("rid");
    }

    fill_prop = fillBasketPropsExt(th, "prop", "bx_ajax_text");

    fill_prop.quantity = val;
    fill_prop.add_item = "Y";
    fill_prop.rid = rid;
    fill_prop.offers = offer;
    fill_prop.iblockID = iblockid;
    fill_prop.part_props = part_props;
    fill_prop.add_props = add_props;
    fill_prop.props = JSON.stringify(props);
    fill_prop.item = item;
    fill_prop.basket_props = basket_props;

    $.ajax({
      type: "POST",
      url: arOptimusOptions["SITE_DIR"] + "ajax/item.php",
      data: fill_prop,
      dataType: "json",
      success: function (data) {
        $(".basket_error_frame").jqmHide();
        if ("STATUS" in data) {
          getActualBasket(fill_prop.iblockID);
          if (data.STATUS === "OK") {
            th.hide();
            th.closest(".counter_wrapp").find(".counter_block").hide();
            th.parents("tr").find(".counter_block_wr .counter_block").hide();
            th.closest(".button_block").addClass("wide");
            th.parent().find(".in-cart").show();

            addBasketCounter(item);
            $(".wish_item[data-item=" + item + "]").removeClass("added");
            $(".wish_item[data-item=" + item + "]")
              .find(".value")
              .show();
            $(".wish_item[data-item=" + item + "]")
              .find(".value.added")
              .hide();
            if ($("#basket_line .cart").length) {
              if ($("#basket_line .cart").is(".empty_cart")) {
                $("#basket_line .cart")
                  .removeClass("empty_cart")
                  .find(".cart_wrapp a.basket_link")
                  .removeAttr("href")
                  .addClass("cart-call");
                $("#basket_line .cart").removeClass("ecart");
                touchBasket(".cart:not(.empty_cart) .basket_block .link");
              }

              reloadTopBasket("add", $("#basket_line"), 200, 5000, "Y");
            } else if ($("#basket_line .basket_fly").length) {
              if ($(window).outerWidth() > 768) {
                basketFly("open");
              } else {
                basketFly("refresh");
              }
            }
          } else {
            showBasketError(BX.message(data.MESSAGE));
          }
        } else {
          showBasketError(BX.message("CATALOG_PARTIAL_BASKET_PROPERTIES_ERROR"));
        }
      },
    });
  });

  $(document).on("click", ".to-cart:not(.read_more)", function (e) {
    e.preventDefault();
    var th = $(this);

    var val = $(this).attr("data-quantity");

    if (!val) $val = 1;

    var tmp_props = $(this).data("props"),
      props = "",
      part_props = "",
      add_props = "N",
      fill_prop = {},
      iblockid = $(this).data("iblockid"),
      offer = $(this).data("offers"),
      rid = "",
      basket_props = "",
      item = $(this).attr("data-item");

    if (offer != "Y") {
      offer = "N";
    } else {
      basket_props = $(this).closest(".prices_tab").find(".bx_sku_props input").val();
    }
    if (tmp_props) {
      props = tmp_props.split(";");
    }
    if ($(this).data("part_props")) {
      part_props = $(this).data("part_props");
    }
    if ($(this).data("add_props")) {
      add_props = $(this).data("add_props");
    }
    if ($(".rid_item").length) {
      rid = $(".rid_item").data("rid");
    } else if ($(this).data("rid")) {
      rid = $(this).data("rid");
    }

    fill_prop = fillBasketPropsExt(th, "prop", th.data("bakset_div"));

    fill_prop.quantity = val;
    fill_prop.add_item = "Y";
    fill_prop.rid = rid;
    fill_prop.offers = offer;
    fill_prop.iblockID = iblockid;
    fill_prop.part_props = part_props;
    fill_prop.add_props = add_props;
    fill_prop.props = JSON.stringify(props);
    fill_prop.item = item;
    fill_prop.basket_props = basket_props;

    if (th.data("empty_props") == "N") {
      showBasketError($("#" + th.data("bakset_div")).html(), BX.message("ERROR_BASKET_PROP_TITLE"), "Y", th);
    } else {
      $.ajax({
        type: "POST",
        url: arOptimusOptions["SITE_DIR"] + "ajax/item.php",
        data: fill_prop,
        dataType: "json",
        success: function (data) {
          getActualBasket(fill_prop.iblockID);
          if (data !== null) {
            if ("STATUS" in data) {
              if (data.MESSAGE_EXT === null) data.MESSAGE_EXT = "";
              if (data.STATUS === "OK") {
                /*th.hide();
								th.closest('.counter_wrapp').find('.counter_block').hide();
								th.parents('tr').find('.counter_block_wr .counter_block').hide();
								th.closest('.button_block').addClass('wide');
								th.parent().find('.in-cart').show();*/

                $(".to-cart[data-item=" + item + "]").hide();
                $(".to-cart[data-item=" + item + "]")
                  .closest(".counter_wrapp")
                  .find(".counter_block")
                  .hide();
                $(".to-cart[data-item=" + item + "]")
                  .parents("tr")
                  .find(".counter_block_wr .counter_block")
                  .hide();
                $(".to-cart[data-item=" + item + "]")
                  .closest(".button_block")
                  .addClass("wide");
                $(".to-cart[data-item=" + item + "]")
                  .parent()
                  .find(".in-cart")
                  .show();

                addBasketCounter(item);
                $(".wish_item[data-item=" + item + "]").removeClass("added");
                $(".wish_item[data-item=" + item + "]")
                  .find(".value")
                  .show();
                $(".wish_item[data-item=" + item + "]")
                  .find(".value.added")
                  .hide();
                if ($("#basket_line .cart").length) {
                  if ($("#basket_line .cart").is(".empty_cart")) {
                    $("#basket_line .cart")
                      .removeClass("empty_cart")
                      .find(".cart_wrapp a.basket_link")
                      .removeAttr("href")
                      .addClass("cart-call");
                    $("#basket_line .cart").removeClass("ecart");
                    touchBasket(".cart:not(.empty_cart) .basket_block .link");
                  }

                  reloadTopBasket("add", $("#basket_line"), 200, 5000, "Y");
                } else if ($("#basket_line .basket_fly").length) {
                  if ($(window).outerWidth() > 768) {
                    basketFly("open");
                  } else {
                    basketFly("refresh");
                  }
                }
              } else {
                showBasketError(BX.message(data.MESSAGE) + " <br/>" + data.MESSAGE_EXT);
              }
            } else {
              showBasketError(BX.message("CATALOG_PARTIAL_BASKET_PROPERTIES_ERROR"));
            }
          } else {
            /*th.hide();
						th.closest('.counter_wrapp').find('.counter_block').hide();
						th.parents('tr').find('.counter_block_wr .counter_block').hide();
						th.closest('.button_block').addClass('wide');
						th.parent().find('.in-cart').show();*/

            $(".to-cart[data-item=" + item + "]").hide();
            $(".to-cart[data-item=" + item + "]")
              .closest(".counter_wrapp")
              .find(".counter_block")
              .hide();
            $(".to-cart[data-item=" + item + "]")
              .parents("tr")
              .find(".counter_block_wr .counter_block")
              .hide();
            $(".to-cart[data-item=" + item + "]")
              .closest(".button_block")
              .addClass("wide");
            $(".to-cart[data-item=" + item + "]")
              .parent()
              .find(".in-cart")
              .show();

            addBasketCounter(item);
            $(".wish_item[data-item=" + item + "]").removeClass("added");
            $(".wish_item[data-item=" + item + "]")
              .find(".value")
              .show();
            $(".wish_item[data-item=" + item + "]")
              .find(".value.added")
              .hide();
            if ($("#basket_line .cart").length) {
              if ($("#basket_line .cart").is(".empty_cart")) {
                $("#basket_line .cart")
                  .removeClass("empty_cart")
                  .find(".cart_wrapp a.basket_link")
                  .removeAttr("href")
                  .addClass("cart-call");
                $("#basket_line .cart").removeClass("ecart");
                touchBasket(".cart:not(.empty_cart) .basket_block .link");
              }

              reloadTopBasket("add", $("#basket_line"), 200, 5000, "Y");
            } else if ($("#basket_line .basket_fly").length && $(window).outerWidth() > 768) {
              basketFly("open");
            }
          }
        },
      });
    }
  });

  $(document).on("click", ".to-subscribe", function (e) {
    e.preventDefault();
    if ($(this).is(".auth")) {
      if ($(this).hasClass("nsubsc")) {
        $("body").append("<span class='evb-subs' style='display:none;'></span>");
        jqmEd("subscribe", "subscribe", ".evb-subs", "id=" + $(this).data("item"), this);
        $("body .evb-subs").click();
        $("body .evb-subs").remove();
      } else {
        location.href = arOptimusOptions["SITE_DIR"] + "auth/?backurl=" + location.pathname;
      }
    } else {
      var item = $(this).attr("data-item"),
        iblockid = $(this).attr("data-iblockid");
      $(this).hide();
      $(this).parent().find(".in-subscribe").show();
      $.get(
        arOptimusOptions["SITE_DIR"] + "ajax/item.php?item=" + item + "&subscribe_item=Y",
        $.proxy(function (data) {
          $(".wish_item[data-item=" + item + "]").removeClass("added");
          getActualBasket(iblockid);
        })
      );
    }
  });

  $(document).on("click", ".in-subscribe", function (e) {
    e.preventDefault();
    var item = $(this).attr("data-item"),
      iblockid = $(this).attr("data-iblockid");
    $(this).hide();
    $(this).parent().find(".to-subscribe").show();
    $.get(
      arOptimusOptions["SITE_DIR"] + "ajax/item.php?item=" + item + "&subscribe_item=Y",
      $.proxy(function (data) {
        getActualBasket(iblockid);
      })
    );
  });

  $(document).on("keyup", function (e) {
    if (e.keyCode == 27) {
      if ($(".jqmWindow").length) {
        $(".jqmWindow").jqmHide();
      }
    }
  });

  $(document).on("click", ".wish_item", function (e) {
    e.preventDefault();
    var val = $(this).attr("data-quantity"),
      offer = $(this).data("offers"),
      iblockid = $(this).data("iblock"),
      tmp_props = $(this).data("props"),
      props = "",
      item = $(this).attr("data-item");
    item2 = $(this).attr("data-item");
    if (!val) $val = 1;
    if (offer != "Y") offer = "N";
    if (tmp_props) {
      props = tmp_props.split(";");
    }
    if (!$(this).hasClass("text")) {
      if ($(this).hasClass("added")) {
        $(this).hide();
        $(this).closest(".wish_item_button").find(".to").show();

        $(".like_icons").each(function () {
          if ($(this).find('.wish_item.text[data-item="' + item + '"]').length) {
            $(this)
              .find('.wish_item.text[data-item="' + item + '"]')
              .removeClass("added");
            $(this)
              .find('.wish_item.text[data-item="' + item + '"]')
              .find(".value")
              .show();
            $(this)
              .find('.wish_item.text[data-item="' + item + '"]')
              .find(".value.added")
              .hide();
          }
          if ($(this).find(".wish_item_button").length) {
            $(this)
              .find(".wish_item_button")
              .find('.wish_item[data-item="' + item + '"]')
              .removeClass("added");
            $(this)
              .find(".wish_item_button")
              .find('.wish_item[data-item="' + item + '"]')
              .find(".value")
              .show();
            $(this)
              .find(".wish_item_button")
              .find('.wish_item[data-item="' + item + '"]')
              .find(".value.added")
              .hide();
          }
        });
      } else {
        $(this).hide();
        $(this).closest(".wish_item_button").find(".in").addClass("added").show();

        $(".like_icons").each(function () {
          if ($(this).find('.wish_item.text[data-item="' + item + '"]').length) {
            $(this)
              .find('.wish_item.text[data-item="' + item + '"]')
              .addClass("added");
            $(this)
              .find('.wish_item.text[data-item="' + item + '"]')
              .find(".value")
              .hide();
            $(this)
              .find('.wish_item.text[data-item="' + item + '"]')
              .find(".value.added")
              .css({ display: "block" });
          }
          if ($(this).find(".wish_item_button").length) {
            $(this)
              .find(".wish_item_button")
              .find('.wish_item[data-item="' + item + '"]')
              .addClass("added");
            $(this)
              .find(".wish_item_button")
              .find('.wish_item[data-item="' + item + '"]')
              .find(".value")
              .hide();
            $(this)
              .find(".wish_item_button")
              .find('.wish_item[data-item="' + item + '"]')
              .find(".value.added")
              .show();
          }
        });
      }
    } else {
      if (!$(this).hasClass("added")) {
        $(".wish_item[data-item=" + item + "]").addClass("added");
        $(".wish_item[data-item=" + item + "]")
          .find(".value")
          .hide();
        $(".wish_item[data-item=" + item + "]")
          .find(".value.added")
          .css("display", "block");

        $(".like_icons").each(function () {
          if ($(this).find(".wish_item_button").length) {
            $(this)
              .find(".wish_item_button")
              .find('.wish_item[data-item="' + item + '"]')
              .addClass("added");
            $(this)
              .find(".wish_item_button")
              .find('.wish_item[data-item="' + item + '"]')
              .find(".value")
              .hide();
            $(this)
              .find(".wish_item_button")
              .find('.wish_item[data-item="' + item + '"]')
              .find(".value.added")
              .show();
          }
        });
      } else {
        $(".wish_item[data-item=" + item + "]").removeClass("added");
        $(".wish_item[data-item=" + item + "]")
          .find(".value")
          .show();
        $(".wish_item[data-item=" + item + "]")
          .find(".value.added")
          .hide();

        $(".like_icons").each(function () {
          if ($(this).find(".wish_item_button").length) {
            $(this)
              .find(".wish_item_button")
              .find('.wish_item[data-item="' + item + '"]')
              .removeClass("added");
            $(this)
              .find(".wish_item_button")
              .find('.wish_item[data-item="' + item + '"]')
              .find(".value")
              .show();
            $(this)
              .find(".wish_item_button")
              .find('.wish_item[data-item="' + item + '"]')
              .find(".value.added")
              .hide();
          }
        });
      }
    }

    $(".in-cart[data-item=" + item + "]").hide();
    $(".to-cart[data-item=" + item + "]")
      .parent()
      .removeClass("wide");
    $(".to-cart[data-item=" + item + "]").show();
    $(".counter_block[data-item=" + item + "]").show();
    if (
      !$(".counter_block[data-item=" + item + "]")
        .closest(".counter_wrapp")
        .find(".to-order").length
    ) {
      $(".to-cart[data-item=" + item + "]").show();
      $(".counter_block[data-item=" + item + "]").show();
    }
    if (!$(this).closest(".module-cart").size()) {
      $.ajax({
        type: "GET",
        url: arOptimusOptions["SITE_DIR"] + "ajax/item.php",
        data:
          "item=" +
          item2 +
          "&quantity=" +
          val +
          "&wish_item=Y" +
          "&offers=" +
          offer +
          "&iblockID=" +
          iblockid +
          "&props=" +
          JSON.stringify(props),
        dataType: "json",
        success: function (data) {
          getActualBasket(iblockid);
          if (data !== null) {
            if (data.MESSAGE_EXT === null) data.MESSAGE_EXT = "";
            if ("STATUS" in data) {
              if (data.STATUS === "OK") {
                if (arOptimusOptions["COUNTERS"]["USE_BASKET_GOALS"] !== "N") {
                  var eventdata = { goal: "goal_wish_add", params: { id: item2 } };
                  BX.onCustomEvent("onCounterGoals", [eventdata]);
                }
                if ($("#basket_line .cart").size()) {
                  reloadTopBasket("wish", $("#basket_line"), 200, 5000, "N");
                } else {
                  basketFly("wish");
                }
              } else {
                showBasketError(
                  BX.message(data.MESSAGE) + " <br/>" + data.MESSAGE_EXT,
                  BX.message("ERROR_ADD_DELAY_ITEM")
                );
              }
            } else {
              showBasketError(
                BX.message(data.MESSAGE) + " <br/>" + data.MESSAGE_EXT,
                BX.message("ERROR_ADD_DELAY_ITEM")
              );
            }
          } else {
            if ($("#basket_line .cart").size()) {
              reloadTopBasket("wish", $("#basket_line"), 200, 5000, "N");
            } else {
              basketFly("wish");
            }
          }
        },
      });
    }
  });

  $(document).on("click", ".compare_item", function (e) {
    e.preventDefault();
    var item = $(this).attr("data-item");
    var iblockID = $(this).attr("data-iblock");
    if (!$(this).hasClass("text")) {
      if ($(this).hasClass("added")) {
        $(this).hide();
        $(this).closest(".compare_item_button").find(".to").show();

        /*sync other button*/
        $(".like_icons").each(function () {
          if ($(this).find('.compare_item.text[data-item="' + item + '"]').length) {
            $(this)
              .find('.compare_item.text[data-item="' + item + '"]')
              .removeClass("added");
            $(this)
              .find('.compare_item.text[data-item="' + item + '"]')
              .find(".value")
              .show();
            $(this)
              .find('.compare_item.text[data-item="' + item + '"]')
              .find(".value.added")
              .hide();
          }
          if ($(this).find(".compare_item_button").length) {
            $(this)
              .find(".compare_item_button")
              .find('.compare_item[data-item="' + item + '"]')
              .removeClass("added");
            $(this)
              .find(".compare_item_button")
              .find('.compare_item[data-item="' + item + '"]')
              .find(".value")
              .show();
            $(this)
              .find(".compare_item_button")
              .find('.compare_item[data-item="' + item + '"]')
              .find(".value.added")
              .hide();
          }
        });
      } else {
        $(this).hide();
        $(this).closest(".compare_item_button").find(".in").show();

        /*sync other button*/
        $(".like_icons").each(function () {
          if ($(this).find('.compare_item.text[data-item="' + item + '"]').length) {
            $(this)
              .find('.compare_item.text[data-item="' + item + '"]')
              .addClass("added");
            $(this)
              .find('.compare_item.text[data-item="' + item + '"]')
              .find(".value")
              .hide();
            $(this)
              .find('.compare_item.text[data-item="' + item + '"]')
              .find(".value.added")
              .css({ display: "block" });
          }
          if ($(this).find(".compare_item_button").length) {
            $(this)
              .find(".compare_item_button")
              .find('.compare_item[data-item="' + item + '"]')
              .addClass("added");
            $(this)
              .find(".compare_item_button")
              .find('.compare_item[data-item="' + item + '"]')
              .find(".value.added")
              .show();
            $(this)
              .find(".compare_item_button")
              .find('.compare_item[data-item="' + item + '"]')
              .find(".value")
              .hide();
          }
        });
      }
    } else {
      if (!$(this).hasClass("added")) {
        $(".compare_item[data-item=" + item + "]").addClass("added");
        $(".compare_item[data-item=" + item + "]")
          .find(".value")
          .hide();
        $(".compare_item[data-item=" + item + "]")
          .find(".value.added")
          .css("display", "block");

        /*sync other button*/
        $(".like_icons").each(function () {
          if ($(this).find(".compare_item_button").length) {
            $(this)
              .find(".compare_item_button")
              .find('.compare_item[data-item="' + item + '"]')
              .addClass("added");
            $(this)
              .find(".compare_item_button")
              .find('.compare_item[data-item="' + item + '"]')
              .find(".value.added")
              .show();
            $(this)
              .find(".compare_item_button")
              .find('.compare_item[data-item="' + item + '"]')
              .find(".value")
              .hide();
          }
        });
      } else {
        $(".compare_item[data-item=" + item + "]").removeClass("added");
        $(".compare_item[data-item=" + item + "]")
          .find(".value")
          .show();
        $(".compare_item[data-item=" + item + "]")
          .find(".value.added")
          .hide();

        /*sync other button*/
        $(".like_icons").each(function () {
          if ($(this).find(".compare_item_button").length) {
            $(this)
              .find(".compare_item_button")
              .find('.compare_item[data-item="' + item + '"]')
              .removeClass("added");
            $(this)
              .find(".compare_item_button")
              .find('.compare_item[data-item="' + item + '"]')
              .find(".value")
              .show();
            $(this)
              .find(".compare_item_button")
              .find('.compare_item[data-item="' + item + '"]')
              .find(".value.added")
              .hide();
          }
        });
      }
    }

    $.get(
      arOptimusOptions["SITE_DIR"] + "ajax/item.php?item=" + item + "&compare_item=Y&iblock_id=" + iblockID,
      $.proxy(function (data) {
        getActualBasket(iblockID, "Comapre");
        jsAjaxUtil.InsertDataToNode(
          arOptimusOptions["SITE_DIR"] + "ajax/show_compare_preview_top.php",
          "compare_line",
          false
        );
        if ($("#compare_fly").length) {
          jsAjaxUtil.InsertDataToNode(
            arOptimusOptions["SITE_DIR"] + "ajax/show_compare_preview_fly.php",
            "compare_fly",
            false
          );
        }
      })
    );
  });

  const handlePinchZoomiOS = function(e) {
    e.preventDefault();
  }

  const $viewport = document.querySelector('meta[name=viewport]');
  var metaViewport;

  const handlePinchZoom = function(add) {
    const $html = document.querySelector('html');

    if (!!~$html.className.indexOf('bx-core')) {
      if (!metaViewport) {
        metaViewport = Object.fromEntries($viewport.getAttribute('content').split(',').map(function(el) {
          return el.trim().split('=');
        }));
      }

      const handleMetaViewport = function(add) {
        let viewportData = Object.assign({}, metaViewport);
    
        if (add) {
          viewportData['maximum-scale'] = 1;
          viewportData['user-scalable'] = 'no';
        }
    
        viewportData = Object.keys(viewportData).map(function(el) {
          return el + '=' + viewportData[el];
        }).join(', ');
      
        $viewport.setAttribute('content', viewportData);
      }

      if (add) {
        if (!!~$html.className.indexOf('bx-ios')) {
          document.addEventListener('gesturestart', handlePinchZoomiOS);
        } else {
          handleMetaViewport(true);
        }
      } else {
        if (!!~$html.className.indexOf('bx-ios')) {
          document.removeEventListener('gesturestart', handlePinchZoomiOS);
        } else {
          handleMetaViewport();
        }
      }
    }
  }

  $(".fancy").fancybox({
    openEffect: "fade",
    closeEffect: "fade",
    nextEffect: "fade",
    prevEffect: "fade",
    tpl: {
      closeBtn:
        '<a title="' + BX.message("FANCY_CLOSE") + '" class="fancybox-item fancybox-close" href="javascript:;"></a>',
      next:
        '<a title="' +
        BX.message("FANCY_NEXT") +
        '" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
      prev:
        '<a title="' +
        BX.message("FANCY_PREV") +
        '" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>',
    },
    beforeLoad: function() {
      if (window.visualViewport.scale > 1) {
        return false;
      }
    },
    beforeShow: function() {
      handlePinchZoom(true);
    },
    beforeClose: function() {
      handlePinchZoom();
    }
  });

  $(".tabs>li").on("click", function () {
    if (!$(this).hasClass("active")) {
      var sliderIndex = $(this).index(),
        curLiNav = $(this)
          .closest(".top_blocks")
          .find(".slider_navigation")
          .find(">li:eq(" + sliderIndex + ")"),
        curLi = $(this)
          .closest(".top_blocks")
          .siblings(".tabs_content")
          .find(">li:eq(" + sliderIndex + ")");
      $(this).addClass("active").addClass("cur").siblings().removeClass("active").removeClass("cur");
      curLi.addClass("cur").siblings().removeClass("cur");
      curLiNav.addClass("cur").siblings().removeClass("cur");

      // //equal height
      // curLi
      //   .find(".catalog_block .catalog_item_wrapp .catalog_item .item_info:visible .item-title")
      //   .sliceHeight({ item: ".catalog_item:visible", resize: false, mobile: true });
      // curLi
      //   .find(".catalog_block .catalog_item_wrapp .catalog_item .item_info:visible")
      //   .sliceHeight({ classNull: ".footer_button", item: ".catalog_item:visible", resize: false, mobile: true });
      // curLi
      //   .find(".catalog_block .catalog_item_wrapp .catalog_item:visible")
      //   .sliceHeight({ classNull: ".footer_button", item: ".catalog_item:visible", resize: false, mobile: true });
    }
  });

  /*search click*/
  $(".search_block .icon").on("click", function () {
    var th = $(this);
    if ($(this).hasClass("open")) {
      $(this).closest(".center_block").find(".search_middle_block").removeClass("active");
      $(this).removeClass("open");
      $(this).closest(".center_block").find(".search_middle_block").find(".noborder").hide();
    } else {
      setTimeout(function () {
        th.closest(".center_block").find(".search_middle_block").find(".noborder").show();
      }, 100);
      $(this).closest(".center_block").find(".search_middle_block").addClass("active");
      $(this).addClass("open");
    }
  });
  $(document).on("mouseenter", ".display_list .item_wrap", function () {
    $(this).prev().addClass("prev");
  });
  $(document).on("mouseleave", ".display_list .item_wrap", function () {
    $(this).prev().removeClass("prev");
  });
  $(document).on("mouseenter", ".catalog_block .item_wrap", function () {
    $(this).addClass("shadow_delay");
  });
  $(document).on("mouseleave", ".catalog_block .item_wrap", function () {
    $(this).removeClass("shadow_delay");
  });
  $(document).on("click", ".no_goods .button", function () {
    $(".bx_filter .smartfilter .bx_filter_search_reset").trigger("click");
  });

  $(document).on("click", ".ajax_load_btn", function () {
    var url = $(this).closest(".container").find(".module-pagination .flex-direction-nav .flex-next").attr("href"),
      th = $(this).find(".more_text_ajax");

    var objUrl = parseUrlQuery(),
      add_url = "",
      bTabsBlock = false,
      obGetData = { ajax_get: "Y", AJAX_REQUEST: "Y" };

    th.addClass("loading");

    /*hit on front*/
    if (th.closest(".tab_slider_wrapp.specials").length) {
      var curLi = $(this).closest(".tab"),
        url = th.closest(".tabs_content").data("url");

      bTabsBlock = true;
      obGetData.AJAX_POST = "Y";
      obGetData.FILTER_HIT_PROP = curLi.data("code");
      obGetData.AJAX_PARAMS = $(this).closest(".tab_slider_wrapp").find(".request-data").data("value");
      obGetData.GLOBAL_FILTER = curLi.data("filter");

      add_url = "?PAGEN_1=" + curLi.find(".nav-inner-wrapper").data("page");
    }
    /**/

    if ("clear_cache" in objUrl) {
      if (objUrl.clear_cache == "Y") {
        add_url += (add_url.length ? "&" : "?") + "clear_cache=Y";
      }
    }

    $.ajax({
      url: url + add_url,
      // data: {"ajax_get": "Y"},
      data: obGetData,
      type: bTabsBlock ? "POST" : "GET",
      success: function (html) {
        var new_html = $.parseHTML(html);
        th.removeClass("loading");

        /*hit on front*/
        if (th.closest(".tab_slider_wrapp.specials").length) {
          curLi.find(".catalog_block").append(html);
          curLi.find(".catalog_block .wrap_nav").remove();

          // curLi
          //   .find(".catalog_block .catalog_item_wrapp .catalog_item .item_info:visible .item-title")
          //   .sliceHeight({ item: ".catalog_item:visible", mobile: true });
          // curLi
          //   .find(".catalog_block .catalog_item_wrapp .catalog_item .item_info:visible")
          //   .sliceHeight({ item: ".catalog_item:visible", mobile: true });
          // curLi
          //   .find(".catalog_block .catalog_item_wrapp .catalog_item:visible")
          //   .sliceHeight({ classNull: ".footer_button", item: ".catalog_item:visible", mobile: true });

          curLi.find(".bottom_nav").html($(html).find(".bottom_nav").html());
        } else {
          if ($(".display_list").length) {
            $(".display_list").append(html);
          } else if ($(".block_list").length) {
            $(".block_list").append(html);
            touchItemBlock(".catalog_item a");
          } else if ($(".module_products_list").length) {
            $(".module_products_list > tbody").append(html);
          }
          $(".bottom_nav").html($(html).find(".bottom_nav").html());
        }
        setStatusButton();
        initCountdown();
        showTotalSummItem();
        BX.onCustomEvent("onAjaxSuccess");
      },
    });
  });

  $(document).on("click", ".bx_compare .tabs-head li", function () {
    var url = $(this).find(".sortbutton").data("href");
    BX.showWait(BX("bx_catalog_compare_block"));
    $.ajax({
      url: url,
      data: { ajax_action: "Y" },
      success: function (html) {
        history.pushState(null, null, url);
        $("#bx_catalog_compare_block").html(html);
        BX.closeWait();
      },
    });
  });
  var hoveredTrs;
  $(document).on(
    {
      mouseover: function (e) {
        var _ = $(this);
        var tbodyIndex = _.closest("tbody").index() + 1; //+1 for nth-child
        var trIndex = _.index() + 1; // +1 for nth-child
        hoveredTrs = $(e.delegateTarget)
          .find(".data_table_props")
          .children(":nth-child(" + tbodyIndex + ")")
          .children(":nth-child(" + trIndex + ")")
          .addClass("hovered");
      },
      mouseleave: function (e) {
        if (hoveredTrs) hoveredTrs.removeClass("hovered");
      },
    },
    ".bx_compare .data_table_props tbody>tr"
  );
  $(document).on("click", ".fancy_offer", function (e) {
    e.preventDefault();
    var arPict = [];
    $(".sliders .slides_block li").each(function () {
      var obImg = {};
      obImg = {
        title: $(this).find("img").attr("alt"),
        href: $(this).data("big"),
      };
      if ($(this).hasClass("current")) {
        arPict.unshift(obImg);
      } else {
        arPict.push(obImg);
      }
    });
    $.fancybox(arPict, {
      openEffect: "fade",
      closeEffect: "fade",
      nextEffect: "fade",
      prevEffect: "fade",
      type: "image",
      tpl: {
        closeBtn:
          '<a title="' + BX.message("FANCY_CLOSE") + '" class="fancybox-item fancybox-close" href="javascript:;"></a>',
        next:
          '<a title="' +
          BX.message("FANCY_NEXT") +
          '" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
        prev:
          '<a title="' +
          BX.message("FANCY_PREV") +
          '" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>',
      },
      beforeLoad: function() {
        if (window.visualViewport.scale > 1) {
          return false;
        }
      },
      beforeShow: function() {
        handlePinchZoom(true);
      },
      beforeClose: function() {
        handlePinchZoom();
      }
    });
  });

  /*tabs*/
  $(document).on("click", ".tabs_section .tabs-head li",  function(e, s) {
    if (!$(this).is(".current")) {
      $(".tabs_section .tabs-head li").removeClass("current");
      $(this).addClass("current");
      $(".tabs_section ul.tabs_content li").removeClass("current");
      if ($(this).attr("id") == "product_reviews_tab") {
        $(".shadow.common").hide();
        if (s === "slide") {
          $("#reviews_content").slideDown(200, function () {});
          $(".tabs_section ul.tabs_content > li")
            .not(":eq(" + $(this).index() + ")")
            .find("> div + div")
            .slideUp(200, function () {});
          var container = $(".tabs_section ul.tabs_content > li:eq(" + $(this).index() + ")");
          container.find("> div + div").slideDown(200, function () {
            container.addClass("current");
          });
        } else {
          $(".tabs_section ul.tabs_content > li:eq(" + $(this).index() + ")").addClass("current");
          $(".tabs_section ul.tabs_content > li")
            .not(":eq(" + $(this).index() + ")")
            .find("> div + div")
            .hide();
          $("#for_product_reviews_tab").show();
          $("#reviews_content").show();
        }
      } else {
        $(".shadow.common").show();
        if (s === "slide") {
          $(".tabs_section ul.tabs_content > li")
            .not(":eq(" + $(this).index() + ")")
            .find("> div + div")
            .slideUp(200, function () {});
          var container = $(".tabs_section ul.tabs_content > li:eq(" + $(this).index() + ")");
          container.find("> div + div").slideDown(200, function () {
            container.addClass("current");
          });
        } else {
          $("#reviews_content").hide();
          $(".tabs_section ul.tabs_content > li > div + div").hide();
          var container = $(".tabs_section ul.tabs_content > li:eq(" + $(this).index() + ")");
          container.addClass("current");
          container.find("> div + div").show();
        }
      }
    } else if (s === "slide") {
      var container = $(".tabs_section ul.tabs_content > li:eq(" + $(this).index() + ")");
      container.find("> div + div").slideUp(200, function () {
        $(".tabs_section .tabs-head li").removeClass("current");
        $(".tabs_section ul.tabs_content li").removeClass("current");
      });
    }
  });
  $(document).on("click", ".tabs_section .tabs-body li .title-tab-heading",  function () {
    $(".tabs_section .tabs-head li:eq(" + $(this).parent("li").index() + ")").trigger("click", "slide");
  });
  /*open first section slide*/
  setTimeout(function () {
    $(".jobs_wrapp .item:first .name tr").trigger("click");
  }, 300);

  $(document).on("click", ".buy_block .slide_offer", function () {
    scroll_block($(".tabs_section"));
  });
  $(".share_wrapp .text").on("click", function () {
    $(this).parent().find(".shares").fadeToggle();
  });
  $(document).on("mousedown", "html, body",  function (e) {
    e.stopPropagation();
    $(".shares").fadeOut();
    $(".search_middle_block").removeClass("active_wide");
  });
  // $(".share_wrapp")
  //   .find("*")
  //   .on("mousedown", function (e) {
  //     e.stopPropagation();
  //   });
  
  $(document).on("mousedown", ".share_wrapp *", function (e) { 
    e.stopPropagation();
  });

  $(document).on("click", ".reviews-collapse-link", function () {
    $(".reviews-reply-form").slideToggle();
  });

  initCountdown();

  /*adaptive menu start*/
  $(".menu.adaptive").on("click", function () {
    $(this).toggleClass("opened");
    if ($(this).hasClass("opened")) {
      $(".mobile_menu").toggleClass("opened").slideDown();
    } else {
      $(".mobile_menu").toggleClass("opened").slideUp();
    }
  });
  /*$('.mobile_menu .has-child >a').on('click', function(e){
		var parentLi=$(this).parent();
		e.preventDefault();
		parentLi.toggleClass('opened');
		parentLi.find('.dropdown').slideToggle();
	})*/

  $(".mobile_menu .has-child >div.toggle_mobile_menu").on("click", function (e) {
    var parentLi = $(this).parent();
    e.preventDefault();
    parentLi.toggleClass("opened");
    parentLi.find(".dropdown").slideToggle();
  });

  $(document).on("keyup", ".mobile_menu .search-input-div input",  function (e) {
    var inputValue = $(this).val();
    $(".center_block .stitle_form input").val(inputValue);
    if (e.keyCode == 13) {
      $(".center_block .stitle_form form").submit();
    }
  });

  $(document).on("keyup", ".center_block .stitle_form input",  function (e) {
    var inputValue = $(this).val();
    $(".mobile_menu .search-input-div input").val(inputValue);
    if (e.keyCode == 13) {
      $(".center_block .stitle_form form").submit();
    }
  });

  $(document).on("click", ".mobile_menu .search-button-div button",  function (e) {
    e.preventDefault();
    var inputValue = $(this).parents().find("input").val();
    $(".center_block .stitle_form input").val(inputValue);
    $(".center_block .stitle_form form").submit();
  });
  /*adaptive menu end*/

  $(".btn.btn-add").on("click", function () {
    $.ajax({
      type: "GET",
      url: arOptimusOptions["SITE_DIR"] + "ajax/clearBasket.php",
      success: function (data) {},
    });
  });

  //set cookie for basket link click
  $(document).on(
    "click",
    ".bx_ordercart_order_table_container .control > a, .basket-item-actions-remove, a[data-entity=basket-item-remove-delayed]",
    function (e) {
      $.removeCookie("click_basket", { path: "/" });
      $.cookie("click_basket", "Y", { path: "/" });
    }
  );

  /*detail order show payments*/
  $(".sale-order-detail-payment-options-methods-info-change-link").on("click", function () {
    $(this).closest(".sale-order-detail-payment-options-methods-info").addClass("opened").siblings().addClass("opened");
  });

  /*expand/hide filter values*/
  $(document).on("click", ".expand_block", function () {
    togglePropBlock($(this));
  });

  /*touch event*/
  document.addEventListener(
    "touchend",
    function (event) {
      if (!$(event.target).closest(".menu_item_l1").length) {
        $(".menu .menu_item_l1 .child").css({ display: "none" });
        $(".menu_item_l1").removeClass("hover");
      }
      if (!$(event.target).closest(".basket_block").length) {
        $(".basket_block .link").removeClass("hover");
        $(".basket_block .basket_popup_wrapp").slideUp();
      }
      if (!$(event.target).closest(".catalog_item").length) {
        var tabsContentUnhoverHover = $(".tab:visible").attr("data-unhover") * 1;
        if (tabsContentUnhoverHover) $(".tab:visible").stop().animate({ height: tabsContentUnhoverHover }, 100);
        $(".tab:visible").find(".catalog_item").removeClass("hover");
        $(".tab:visible").find(".catalog_item .buttons_block").stop().fadeOut(233);
        if ($(".catalog_block").length) {
          $(".catalog_block").find(".catalog_item").removeClass("hover");
          //$('.catalog_block').find('.catalog_item').blur();
        }
      }
      //togglePropBlock($(event.target));
    },
    false
  );

  //touchItemBlock('.catalog_item a');

  $(document).on("keyup", ".coupon .input_coupon input", function () {
    if ($(this).val().length) {
      $(this).removeClass("error");
      $(this).closest(".input_coupon").find(".error").remove();
    } else {
      $(this).addClass("error");
      $("<label class='error'>" + BX.message("INPUT_COUPON") + "</label>").insertBefore($(this));
    }
  });
  showPhoneMask("input[autocomplete=tel]");
  BX.addCustomEvent(window, "onAjaxSuccess", function (e) {
    if (e != "OK") {
      initSelects(document);
      // InitLabelAnimation('#bx-soa-order-form');
      InitOrderCustom();
      showPhoneMask("input[autocomplete=tel]");
      if ($("#content > .catalog_detail").length) {
        $(".bx_filter").remove();
      }

      InitFlexSlider();

      if (arOptimusOptions["PAGES"]["CATALOG_PAGE"]) {
        setStatusButton();
        initCountdown();
      }

      if (arOptimusOptions["PAGES"]["ORDER_PAGE"]) {
        orderActions(e);
      }
    }
  });
  BX.addCustomEvent(window, "onFrameDataRequestFail", function (response) {
    console.log(response);
  });
});

if (!funcDefined("togglePropBlock")) {
  togglePropBlock = function (className) {
    var all_props_block = className.closest(".bx_filter_parameters_box_container").find(".hidden_values");
    if (all_props_block.length && (className.hasClass("inner_text") || className.hasClass("expand_block"))) {
      if (all_props_block.is(":visible")) {
        className.text(BX.message("FILTER_EXPAND_VALUES"));
        all_props_block.hide();
      } else {
        className.text(BX.message("FILTER_HIDE_VALUES"));
        all_props_block.show();
      }
    }
  };
}

if (!funcDefined("showPhoneMask")) {
  showPhoneMask = function (className) {
    $(className).inputmask("mask", { mask: arOptimusOptions["THEME"]["PHONE_MASK"], showMaskOnHover: false });
  };
}

if (!funcDefined("parseUrlQuery")) {
  parseUrlQuery = function () {
    var data = {};
    if (location.search) {
      var pair = location.search.substr(1).split("&");
      for (var i = 0; i < pair.length; i++) {
        var param = pair[i].split("=");
        data[param[0]] = param[1];
      }
    }
    return data;
  };
}

if (!funcDefined("getActualBasket")) {
  getActualBasket = function (iblockID, type) {
    var data = "";
    if (typeof iblockID !== "undefined") {
      data = { iblockID: iblockID };
    }
    $.ajax({
      type: "GET",
      url: arOptimusOptions["SITE_DIR"] + "ajax/actualBasket.php",
      data: data,
      success: function (data) {
        if (!$(".js_ajax").length) $("body").append('<div class="js_ajax"></div>');
        $(".js_ajax").html(data);

        const eventdata = { action: "loadActualBasket" };
        BX.onCustomEvent("onCompleteAction", [eventdata]);
      },
    });
  };
}

function touchMenu(selector) {
  if ($(window).outerWidth() > 600) {
    $(selector).each(function () {
      var th = $(this);
      th.on("touchend", function (e) {
        if (th.find(".child").length && !th.hasClass("hover")) {
          e.preventDefault();
          e.stopPropagation();
          th.siblings().removeClass("hover");
          th.addClass("hover");
          $(".menu .child").css({ display: "none" });
          th.find(".child").css({ display: "block" });
        }
      });
    });
  } else {
    $(selector).off();
  }
}
function touchItemBlock(selector) {
  $(selector).each(function () {
    var th = $(this),
      item = th.closest(".catalog_item");
    if (!th.closest(".best_block").length) {
      th.on("touchend", function (e) {
        if (!item.hasClass("hover")) {
          e.preventDefault();
          // e.stopPropagation();
          item.siblings().removeClass("hover");
          item.siblings().blur();
          item.closest(".catalog_block").find(".catalog_item").removeClass("hover");
          item.addClass("hover");
          item.addClass("touch");

          var tabsContentHover = th.closest(".tab").attr("data-hover") * 1,
            tabsContentUnhoverHover = th.closest(".tab").attr("data-unhover") * 1;

          th.closest(".tab").stop().animate({ height: tabsContentUnhoverHover }, 100);
          th.closest(".catalog_item").siblings().find(".buttons_block").stop().fadeOut(233);

          th.closest(".tab").fadeTo(100, 1);
          th.closest(".tab").stop().css({ height: tabsContentHover });
          th.closest(".catalog_item").find(".buttons_block").fadeIn(450, "easeOutCirc");
        }
      });
    }
  });
}
function touchBasket(selector) {
  if (arOptimusOptions["THEME"]["SHOW_BASKET_ONADDTOCART"] !== "N") {
    if ($(window).outerWidth() > 600) {
      $(document)
        .find(selector)
        .on("touchend", function (e) {
          if ($(this).parent().find(".basket_popup_wrapp").length && !$(this).hasClass("hover")) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass("hover");
            $(this).parent().find(".basket_popup_wrapp").slideDown();
          }
        });
    } else {
      $(selector).off();
    }
  }
}

function showTotalSummItem(popup) {
  //show total summ
  if (arOptimusOptions["THEME"]["SHOW_TOTAL_SUMM"] == "ALWAYS") {
    var parent = "body ";
    if (typeof popup === "string" && popup == "Y") parent = ".popup ";
    $(parent + ".counter_wrapp .counter_block input.text").each(function () {
      var _th = $(this);

      if (_th.data("product")) {
        var obProduct = _th.data("product");
        if (typeof window[obProduct] == "object") window[obProduct].setPriceAction("Y");
        else setPriceItem(_th.closest(".main_item_wrapper"), _th.val());
      } else setPriceItem(_th.closest(".main_item_wrapper"), _th.val());
    });
  }
}

function initFull() {
  initSelects(document);
  initHoverBlock(document);
  // touchItemBlock('.catalog_item a');
  // InitLabelAnimation('#bx-soa-order-form');
  InitOrderCustom();
  if (!$("html.print").length) checkStickyFooter();
  else window.print();

  showTotalSummItem();
  basketActions();
  orderActions();
}
if (!funcDefined("orderActions")) {
  orderActions = function (e) {
    if (arOptimusOptions["PAGES"]["ORDER_PAGE"]) {
      //phone
      if ($("#bx-soa-order input[autocomplete=tel]").length) {
        // get property phone
        for (var i = 0; i < BX.Sale.OrderAjaxComponent.result.ORDER_PROP.properties.length; ++i) {
          if (BX.Sale.OrderAjaxComponent.result.ORDER_PROP.properties[i].IS_PHONE == "Y") {
            var arPropertyPhone = BX.Sale.OrderAjaxComponent.result.ORDER_PROP.properties[i];
          }
        }

        // validate input type=tel
        if (
          typeof BX.Sale.OrderAjaxComponent !== "undefined" &&
          typeof BX.Sale.OrderAjaxComponent === "object" &&
          typeof arPropertyPhone == "object" &&
          arPropertyPhone
        ) {
          BX.Sale.OrderAjaxComponent.validatePhone = function (input, arProperty, fieldName) {
            if (!input || !arProperty) return [];

            var value = input.value,
              errors = [],
              name = BX.util.htmlspecialchars(arProperty.NAME),
              field = BX.message("SOA_FIELD") + ' "' + name + '"',
              re;

            if (arProperty.REQUIRED == "Y" && value.length == 0) {
              errors.push(field + " " + BX.message("SOA_REQUIRED"));
            }

            if (arProperty.IS_PHONE == "Y" && value.length > 0) {
              function regexpPhone(value, element, regexp) {
                var re = new RegExp(regexp);
                return re.test(value);
              }

              var validPhone = regexpPhone($(input).val(), $(input), arOptimusOptions["THEME"]["VALIDATE_PHONE_MASK"]);

              if (!validPhone) {
                errors.push(field + " " + BX.message("JS_FORMAT_ORDER"));
              }
            }

            return errors;
          };

          BX.Sale.OrderAjaxComponent.getValidationDataPhone = function (arProperty, propContainer) {
            var data = {},
              inputs;
            switch (arProperty.TYPE) {
              case "STRING":
                data.action = "blur";
                data.func = BX.delegate(function (input, fieldName) {
                  return this.validatePhone(input, arProperty, fieldName);
                }, this);

                inputs = propContainer.querySelectorAll("input[type=tel]");
                if ($(inputs).length) {
                  data.inputs = inputs;
                  break;
                }
            }

            return data;
          };

          BX.Sale.OrderAjaxComponent.bindValidationPhone = function (id, propContainer) {
            if (!this.validation.properties || !this.validation.properties[id]) return;

            var arProperty = this.validation.properties[id],
              data = this.getValidationDataPhone(arProperty, propContainer),
              i,
              k;

            if (data && data.inputs && data.action) {
              for (i = 0; i < $(data.inputs).length; i++) {
                if (BX.type.isElementNode(data.inputs[i])) {
                  BX.bind(
                    data.inputs[i],
                    data.action,
                    BX.delegate(function () {
                      this.isValidProperty(data);
                    }, this)
                  );
                } else {
                  for (k = 0; k < $(data.inputs[i]).length; k++)
                    BX.bind(
                      data.inputs[i][k],
                      data.action,
                      BX.delegate(function () {
                        this.isValidProperty(data);
                      }, this)
                    );
                }
              }
            }
          };

          BX.Sale.OrderAjaxComponent.isValidPropertiesBlock = function (excludeLocation) {
            if (!this.options.propertyValidation) return [];

            var props = this.orderBlockNode.querySelectorAll(".bx-soa-customer-field[data-property-id-row]"),
              propsErrors = [],
              id,
              propContainer,
              arProperty,
              data,
              i;

            for (i = 0; i < props.length; i++) {
              id = props[i].getAttribute("data-property-id-row");

              if (!!excludeLocation && this.locations[id]) continue;

              propContainer = props[i].querySelector(".soa-property-container");
              if (propContainer) {
                arProperty = this.validation.properties[id];
                data = this.getValidationData(arProperty, propContainer);
                dataPhone = this.getValidationDataPhone(arProperty, propContainer);
                data = $.extend({}, data, dataPhone);

                propsErrors = propsErrors.concat(this.isValidProperty(data, true));
              }
            }

            return propsErrors;
          };

          // create input type=tel
          var input = $("input[autocomplete=tel]"),
            inputHTML = input[0].outerHTML,
            value = input.val(),
            newInput = input[0].outerHTML.replace('type="text"', 'type="tel" value="' + value + '"');

          if ($(input).length < 2) {
            input.hide();
            $(newInput).insertAfter(input);
          }
          showPhoneMask("input[autocomplete=tel][type=tel]");

          // change value input type=text when change input type=tel
          $("input[autocomplete=tel][type=tel]").on("blur", function () {
            var $this = $(this);

            var value = $this.val();
            $this.parent().find("input[autocomplete=tel][type=text]").val(value);
          });

          BX.Sale.OrderAjaxComponent.bindValidationPhone(arPropertyPhone.ID, $("input[autocomplete=tel]").parent()[0]);
        }
      }

      if ($(".bx-soa-cart-total").length) {
        if (!$(".change_basket").length)
          $(".bx-soa-cart-total").prepend(
            '<div class="change_basket">' +
              BX.message("BASKET_CHANGE_TITLE") +
              '<a href="' +
              arOptimusOptions["SITE_DIR"] +
              'basket/" class="change_link">' +
              BX.message("BASKET_CHANGE_LINK") +
              "</a></div>"
          );
        if (typeof BX.Sale.OrderAjaxComponent == "object") {
          if (arOptimusOptions["COUNTERS"]["USE_FULLORDER_GOALS"] !== "N") {
            if (typeof BX.Sale.OrderAjaxComponent.reachgoalbegin === "undefined") {
              BX.Sale.OrderAjaxComponent.reachgoalbegin = true;
              var eventdata = { goal: "goal_order_begin" };
              // BX.onCustomEvent('onCounterGoals', [eventdata])
            }
          }

          // update oreder register form
          if ($(".bx-soa-section-content.reg").length && !$(".bx-soa-section-content.reg .redisigned").length) {
            var bRebindRegSubmit = false;

            if (arOptimusOptions.THEME.LOGIN_EQUAL_EMAIL === "Y") {
              bRebindRegSubmit = true;

              // update input NEW_LOGIN
              if ($('input[name="NEW_LOGIN"]').length) {
                $('input[name="NEW_LOGIN"]').closest(".bx-authform-formgroup-container").hide();
              }
            }

            if (bRebindRegSubmit) {
              // bind new handler for submit button
              var $regSubmit = $("#do_register~input[type=submit]");
              if ($regSubmit.length) {
                BX.unbindAll($regSubmit[0]);
                $(document).on("click", "#do_register~input[type=submit]", function (e) {
                  e.preventDefault();
                  e.stopImmediatePropagation();

                  if (arOptimusOptions.THEME.LOGIN_EQUAL_EMAIL === "Y") {
                    var email = BX.findChild(BX("bx-soa-auth"), { attribute: { name: "NEW_EMAIL" } }, true, false);
                    var login = BX.findChild(BX("bx-soa-auth"), { attribute: { name: "NEW_LOGIN" } }, true, false);

                    if (login && email) {
                      login.value = email.value;
                    }
                  }

                  BX("do_register").value = "Y";
                  BX.Sale.OrderAjaxComponent.sendRequest("showAuthForm");
                });
              }
            }

            // update captcha
            var $captcha = $(".bx-soa-section-content.reg").find(".bx-captcha");
            if ($captcha.length) {
              $captcha.addClass("captcha_image");
              $captcha.append('<div class="captcha_reload"></div>');
              $captcha
                .closest(".bx-authform-formgroup-container")
                .addClass("captcha-row")
                .find("input[name=captcha_word]")
                .closest(".bx-authform-input-container")
                .addClass("captcha_input");
            }

            // update input NEW_NAME && NEW_LAST_NAME
            if ($("input[name=NEW_NAME]").length && $("input[name=NEW_LAST_NAME]").length) {
              if (!$("input[name=NEW_NAME]").closest(".bx-authform-formgroup-container.col-md-6").length) {
                $("input[name=NEW_NAME],input[name=NEW_LAST_NAME]")
                  .closest(".bx-authform-formgroup-container")
                  .addClass("col-md-6");
                var html = $("input[name=NEW_LAST_NAME]")
                  .closest(".bx-authform-formgroup-container")
                  .wrap('<div id="trem_"></div>')
                  .parent()
                  .html();
                $("#trem_").remove();
                $(html).insertAfter(
                  $("input[name=NEW_NAME]").closest(".bx-authform-formgroup-container").wrap('<div class="row"></div>')
                );
              }
            }

            // update input NEW_EMAIL && PHONE_NUMBER
            if ($("input[name=NEW_EMAIL]").length && $("input[name=PHONE_NUMBER]").length) {
              if (!$("input[name=PHONE_NUMBER]").closest(".bx-authform-formgroup-container.col-md-6").length) {
                $("input[name=NEW_EMAIL],input[name=PHONE_NUMBER]")
                  .closest(".bx-authform-formgroup-container")
                  .addClass("col-md-6");
                var html = $("input[name=PHONE_NUMBER]")
                  .closest(".bx-authform-formgroup-container")
                  .wrap('<div id="trem_"></div>')
                  .parent()
                  .html();
                $("#trem_").remove();
                $(html).insertAfter(
                  $("input[name=NEW_EMAIL]").closest(".bx-authform-formgroup-container").wrap('<div class="row"></div>')
                );
              }
            }

            // update input NEW_PASSWORD && NEW_PASSWORD_CONFIRM
            if ($("input[name=NEW_PASSWORD]").length && $("input[name=NEW_PASSWORD_CONFIRM]").length) {
              if (!$("input[name=NEW_PASSWORD]").closest(".bx-authform-formgroup-container.col-md-6").length) {
                $("input[name=NEW_PASSWORD],input[name=NEW_PASSWORD_CONFIRM]")
                  .closest(".bx-authform-formgroup-container")
                  .addClass("col-md-6");
                var html = $("input[name=NEW_PASSWORD_CONFIRM]")
                  .closest(".bx-authform-formgroup-container")
                  .wrap('<div id="trem_"></div>')
                  .parent()
                  .html();
                $("#trem_").remove();
                $(html).insertAfter(
                  $("input[name=NEW_PASSWORD]")
                    .closest(".bx-authform-formgroup-container")
                    .wrap('<div class="row"></div>')
                );
              }
            }

            // update input PHONE_NUMBER
            if ($("input[name=PHONE_NUMBER]").length) {
              var input = $("input[name=PHONE_NUMBER]"),
                inputHTML = input[0].outerHTML,
                value = input.val(),
                newInput = input[0].outerHTML.replace('type="text"', 'type="tel" value="' + value + '"');

              if ($(input).length < 2) {
                input.hide();
                $(newInput).insertAfter(input);

                showPhoneMask("input[name=PHONE_NUMBER][type=tel]");

                $("input[name=PHONE_NUMBER][type=tel]").on("blur", function () {
                  var $this = $(this);
                  var value = $this.val();
                  $this.parent().find("input[name=PHONE_NUMBER][type=text]").val(value);
                });

                var $label = $("input[name=PHONE_NUMBER][type=tel]")
                  .closest(".bx-authform-formgroup-container")
                  .find(".bx-authform-label-container");
                $label.html(
                  BX.message("ORDER_PHONE_LABEL") +
                    ($label.find(".bx-authform-starrequired").length
                      ? '<span class="bx-authform-starrequired"> *</span>'
                      : "")
                );
              }
            }

            $(".bx-soa-section-content.reg").append('<div class="redisigned hidden></div>');
          }

          const asproLicenceShow = arOptimusOptions["THEME"]["SHOW_LICENCE"] === "Y",
            asproLicenceChecked = arOptimusOptions["THEME"]["LICENCE_CHECKED"] === "Y";

          const asproOfferShow = arOptimusOptions["THEME"]["SHOW_OFFER"] === "Y",
            asproOfferChecked = arOptimusOptions["THEME"]["OFFER_CHECKED"] === "Y";

          if ($(".bx-soa-cart-total-line-total").length && (asproLicenceShow || asproOfferShow)) {
            if (typeof e === "undefined") {
              BX.Sale.OrderAjaxComponent.state_licence = asproLicenceChecked ? "checked" : "";
              BX.Sale.OrderAjaxComponent.state_offer = asproOfferChecked ? "checked" : "";
            }

            if (
              (!$(".licence_block.filter").length && asproLicenceShow) ||
              (!$(".offer_block.filter").length && asproOfferShow)
            ) {
              $('<div class="form"><div class="license_order_wrap"></div></div>').insertBefore($("#bx-soa-orderSave"));

              if (!$(".licence_block.filter").length && asproLicenceShow)
                $(
                  '<div class="licence_block filter label_block"><label data-for="licenses_order" class="hidden error">' +
                    BX.message("JS_REQUIRED_LICENSES") +
                    '</label><input type="checkbox" name="licenses_order" required ' +
                    BX.Sale.OrderAjaxComponent.state_licence +
                    ' value="Y"><label data-for="licenses_order" class="license">' +
                    BX.message("LICENSES_TEXT") +
                    "</label></div>"
                ).appendTo($(".license_order_wrap"));

              if (!$(".offer_block.filter").length && asproOfferShow)
                $(
                  '<div class="offer_block filter label_block"><label data-for="offer_order" class="hidden error">' +
                    BX.message("JS_REQUIRED_OFFER") +
                    '</label><input type="checkbox" name="offer_order" required ' +
                    BX.Sale.OrderAjaxComponent.state_offer +
                    ' value="Y"><label data-for="offer_order" class="offer_pub">' +
                    BX.message("OFFER_TEXT") +
                    "</label></div>"
                ).appendTo($(".license_order_wrap"));

              if (asproLicenceShow) {
                $(document).on("click", ".bx-soa .licence_block label.license", function () {
                  const id = $(this).data("for");
                  $(".bx-soa .licence_block label.error").addClass("hidden");
                  if (!$("input[name=" + id + "]").prop("checked")) {
                    $("input[name=" + id + "]").prop("checked", "checked");
                    BX.Sale.OrderAjaxComponent.state_licence = "checked";
                  } else {
                    $("input[name=" + id + "]").prop("checked", "");
                    BX.Sale.OrderAjaxComponent.state_licence = "";
                  }
                });
              }

              if (asproOfferShow) {
                $(document).on("click", ".bx-soa .offer_block label.offer_pub", function () {
                  const id = $(this).data("for");

                  $(".bx-soa .offer_block label.error").addClass("hidden");

                  if (!$("input[name=" + id + "]").prop("checked")) {
                    $("input[name=" + id + "]").prop("checked", "checked");
                    BX.Sale.OrderAjaxComponent.state_licence = "checked";
                  } else {
                    $("input[name=" + id + "]").prop("checked", "");
                    BX.Sale.OrderAjaxComponent.state_licence = "";
                  }
                });
              }

              $(document).on("click", ".lic_condition a", function () {
                if (BX.hasClass(BX("bx-soa-order"), "orderform--v1")) {
                  if (BX.Sale.OrderAjaxComponent.isValidForm()) {
                    BX.Sale.OrderAjaxComponent.animateScrollTo($(".licence_block, .offer_block")[0], 800, 50);
                  }
                } else {
                  const iCountErrors = BX.Sale.OrderAjaxComponent.isValidPropertiesBlock().length;

                  if (!BX.Sale.OrderAjaxComponent.activeSectionId || !iCountErrors) {
                    BX.Sale.OrderAjaxComponent.animateScrollTo($(".licence_block, .offer_block")[0], 800, 50);
                  }
                }
              });
            }
            $("#bx-soa-orderSave, .bx-soa-cart-total-button-container").addClass("lic_condition");

            if (
              typeof BX.Sale.OrderAjaxComponent.oldClickOrderSaveAction === "undefined" &&
              typeof BX.Sale.OrderAjaxComponent.clickOrderSaveAction !== "undefined"
            ) {
              BX.Sale.OrderAjaxComponent.oldClickOrderSaveAction = BX.Sale.OrderAjaxComponent.clickOrderSaveAction;
              BX.Sale.OrderAjaxComponent.clickOrderSaveAction = function (event) {
                if (
                  ($('input[name="licenses_order"]').prop("checked") || asproLicenceShow === false) &&
                  ($('input[name="offer_order"]').prop("checked") || asproOfferShow === false)
                ) {
                  $(".bx-soa .licence_block label.error").addClass("hidden");
                  $(".bx-soa .offer_block label.error").addClass("hidden");

                  if (BX.Sale.OrderAjaxComponent.isValidForm()) {
                    if (typeof BX.Sale.OrderAjaxComponent.allowOrderSave == "function")
                      BX.Sale.OrderAjaxComponent.allowOrderSave();
                    if (typeof BX.Sale.OrderAjaxComponent.doSaveAction == "function")
                      BX.Sale.OrderAjaxComponent.doSaveAction();
                    else BX.Sale.OrderAjaxComponent.oldClickOrderSaveAction(event);
                  }
                } else {
                  if (!$('input[name="licenses_order"]').prop("checked"))
                    $(".bx-soa .licence_block label.error").removeClass("hidden");

                  if (!$('input[name="offer_order"]').prop("checked"))
                    $(".bx-soa .offer_block label.error").removeClass("hidden");
                }
              };
              if (BX.Sale.OrderAjaxComponent.orderSaveBlockNode.querySelector(".checkbox")) {
                if (typeof browser == "object") {
                  if ("msie" in browser && browser.msie)
                    $(BX.Sale.OrderAjaxComponent.orderSaveBlockNode.querySelector(".checkbox")).remove();
                  else BX.Sale.OrderAjaxComponent.orderSaveBlockNode.querySelector(".checkbox").remove();
                }
              }
              BX.unbindAll(BX.Sale.OrderAjaxComponent.totalInfoBlockNode.querySelector("a.btn-order-save"));
              BX.unbindAll(BX.Sale.OrderAjaxComponent.mobileTotalBlockNode.querySelector("a.btn-order-save"));
              BX.unbindAll(BX.Sale.OrderAjaxComponent.orderSaveBlockNode.querySelector("a"));
              BX.bind(
                BX.Sale.OrderAjaxComponent.totalInfoBlockNode.querySelector("a.btn-order-save"),
                "click",
                BX.proxy(BX.Sale.OrderAjaxComponent.clickOrderSaveAction, BX.Sale.OrderAjaxComponent)
              );
              BX.bind(
                BX.Sale.OrderAjaxComponent.mobileTotalBlockNode.querySelector("a.btn-order-save"),
                "click",
                BX.proxy(BX.Sale.OrderAjaxComponent.clickOrderSaveAction, BX.Sale.OrderAjaxComponent)
              );
              BX.bind(
                BX.Sale.OrderAjaxComponent.orderSaveBlockNode.querySelector("a"),
                "click",
                BX.proxy(BX.Sale.OrderAjaxComponent.clickOrderSaveAction, BX.Sale.OrderAjaxComponent)
              );
            }

            /*$('.bx-soa-cart-total .licence_block label.license').on('click', function(){
							var id = $(this).data('for');
							$('.bx-soa-cart-total .licence_block label.error').addClass('hidden');
							if(!$('input[name='+id+']').prop('checked')){
								$('input[name='+id+']').prop('checked', 'checked');
								BX.Sale.OrderAjaxComponent.state_licence = 'checked';
							}
							else{
								$('input[name='+id+']').prop('checked', '');
								BX.Sale.OrderAjaxComponent.state_licence = '';
							}
						})*/
          }
          if (BX.Sale.OrderAjaxComponent.hasOwnProperty("params")) {
            $(".bx-soa-cart-total .change_link").attr("href", BX.Sale.OrderAjaxComponent.params.PATH_TO_BASKET);
            if (arOptimusOptions["PRICES"]["MIN_PRICE"]) {
              if (
                arOptimusOptions["PRICES"]["MIN_PRICE"] > Number(BX.Sale.OrderAjaxComponent.result.TOTAL.ORDER_PRICE)
              ) {
                $('<div class="fademask_ext"></div>').appendTo($("body"));
                location.href = BX.Sale.OrderAjaxComponent.params.PATH_TO_BASKET;
              }
            }
          }

          // fix hide total block
          //BX.removeClass(BX.Sale.OrderAjaxComponent.totalInfoBlockNode, 'bx-soa-cart-total-fixed');
          $(window).scroll();

          if (checkCounters() && typeof BX.Sale.OrderAjaxComponent.oldSaveOrder === "undefined") {
            var saveFunc =
              typeof BX.Sale.OrderAjaxComponent.saveOrder !== "undefined" ? "saveOrder" : "saveOrderWithJson";
            if (typeof BX.Sale.OrderAjaxComponent[saveFunc] !== "undefined") {
              BX.Sale.OrderAjaxComponent.oldSaveOrder = BX.Sale.OrderAjaxComponent[saveFunc];
              BX.Sale.OrderAjaxComponent[saveFunc] = function (result) {
                var res = BX.parseJSON(result);
                if (res && res.order) {
                  if (!res.order.SHOW_AUTH) {
                    if (
                      res.order.REDIRECT_URL &&
                      res.order.REDIRECT_URL.length &&
                      (!res.order.ERROR || BX.util.object_keys(res.order.ERROR).length < 1)
                    ) {
                      if (
                        (arMatch = res.order.REDIRECT_URL.match(/ORDER_ID\=[^&=]*/g)) &&
                        arMatch.length &&
                        (_id = arMatch[0].replace(/ORDER_ID\=/g, "", arMatch[0]))
                      ) {
                        $.ajax({
                          url: arOptimusOptions["SITE_DIR"] + "ajax/check_order.php",
                          dataType: "json",
                          type: "POST",
                          data: { ID: _id },
                          success: function (id) {
                            if (parseInt(id)) {
                              purchaseCounter(parseInt(id), BX.message("FULL_ORDER"), function (d) {
                                if (typeof d == "object") {
                                  if (typeof BX.localStorage !== "undefined") {
                                    BX.localStorage.set("gtm_e_" + _id, d, 60);
                                  }
                                }
                                BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
                              });
                            } else {
                              BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
                            }
                          },
                          error: function () {
                            BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
                          },
                        });
                      } else {
                        BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
                      }
                    } else {
                      BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
                    }
                  } else {
                    BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
                  }
                } else {
                  BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
                }
              };
            }
          }
        }
        $(".bx-ui-sls-quick-locations.quick-locations").on("click", function () {
          $(this).siblings().removeClass("active");
          $(this).addClass("active");
        });
      }
    }
  };
}

if (!funcDefined("basketActions")) {
  basketActions = function () {
    if (arOptimusOptions["PAGES"]["BASKET_PAGE"]) {
      checkMinPrice();

      //remove4Cart
      if (typeof BX.Sale !== "undefined" && typeof BX.Sale === "object") {
        if (typeof BX.Sale.BasketComponent !== "undefined" && typeof BX.Sale.BasketComponent === "object") {
          $(document).on("click", ".basket-item-actions-remove", function () {
            var basketID = $(this).closest(".basket-items-list-item-container").data("id");
            delFromBasketCounter(BX.Sale.BasketComponent.items[basketID].PRODUCT_ID);
          });
        }
      }

      if (location.hash) {
        var hash = location.hash.substring(1);
        if ($("#basket_toolbar_button_" + hash).length) {
          $("#basket_toolbar_button_" + hash).trigger("click");
        }
      }

      $(".bx_sort_container").append(
        '<div class="top_control basket_sort"><span style="display:none;" class="delete_all button grey_br transparent remove_all_basket">' +
          BX.message("BASKET_CLEAR_ALL_BUTTON") +
          "</span></div>"
      );
      if ($(".basket-items-list-header-filter").length) {
        $(".basket-items-list-header-filter").append(
          '<div class="top_control basket_sort"><span style="opacity:1;" class="delete_all button grey_br transparent remove_all_basket">' +
            BX.message("BASKET_CLEAR_ALL_BUTTON") +
            "</span></div>"
        );

        var cur_index = $(".basket-items-list-header-filter > a.active").index();
        //fix delayed
        if (cur_index == 3) cur_index = 2;

        if ($(".basket-items-list-header-filter > a.active").data("filter") == "all") cur_index = "all";

        $(".basket-items-list-header-filter .top_control .delete_all").data("type", cur_index);

        $(".basket-items-list-header-filter > a").on("click", function () {
          var index = $(this).index();

          //fix delayed
          if (index == 3) index = 2;

          if ($(this).data("filter") == "all") index = "all";

          $(".basket-items-list-header-filter .top_control .delete_all").data("type", index);
        });

        if (arOptimusOptions["THEME"]["SHOW_BASKET_PRINT"] == "Y") {
          $(
            '<span class="basket_sort"><span class="basket_print button grey_br transparent">' +
              BX.message("BASKET_PRINT_BUTTON") +
              "</span></span>"
          ).insertAfter($("#pagetitle"));
        }
      } else {
        if (arOptimusOptions["THEME"]["SHOW_BASKET_PRINT"] == "Y") {
          $(".bx_sort_container .top_control").prepend(
            '<span class="basket_print button grey_br transparent">' + BX.message("BASKET_PRINT_BUTTON") + "</span>"
          );
        }

        var cur_index = $(".bx_sort_container a.current").index();
        $(".bx_sort_container .top_control .delete_all").data("type", cur_index);
        if ($(".bx_ordercart > div:eq(" + cur_index + ") table tbody tr td.item").length)
          $(".bx_sort_container .top_control .delete_all").css("display", "block");

        $(".bx_ordercart .bx_ordercart_coupon #coupon").wrap('<div class="input"></div>');

        $(".bx_sort_container > a").on("click", function () {
          var index = $(this).index();
          $(".bx_sort_container .top_control .delete_all").data("type", index);

          if ($(".bx_ordercart > div:eq(" + index + ") table tbody tr td.item").length)
            $(".bx_sort_container .top_control .delete_all").css("display", "block");
          else $(".bx_sort_container .top_control .delete_all").css("display", "none");
        });
      }

      $(".basket_print").on("click", function () {
        // window.open(location.pathname+"?print=Y",'_blank');
        window.print();
      });

      $(".delete_all").on("click", function () {
        if (arOptimusOptions["COUNTERS"]["USE_BASKET_GOALS"] !== "N") {
          var eventdata = { goal: "goal_basket_clear", params: { type: $(this).data("type") } };
          BX.onCustomEvent("onCounterGoals", [eventdata]);
        }
        $.post(
          arOptimusOptions["SITE_DIR"] + "ajax/action_basket.php",
          "TYPE=" + $(this).data("type") + "&CLEAR_ALL=Y",
          $.proxy(function (data) {
            location.reload();
          })
        );
      });

      $(".bx_item_list_section .bx_catalog_item").sliceHeight({ row: ".bx_item_list_slide", item: ".bx_catalog_item" });

      $(document).on(
        "click",
        ".bx_ordercart_order_pay_center .checkout, .basket-checkout-section-inner .basket-btn-checkout",
        function () {
          if (checkCounters("google")) {
            checkoutCounter(1, "start order");
          }
        }
      );

      BX.addCustomEvent("onAjaxSuccess", function () {
        checkMinPrice();

        var errorText = $.trim($("#warning_message").text());
        $("#basket_items_list .error_text").detach();
        if (errorText != "") {
          $("#warning_message").hide().text("");
          $("#basket_items_list").prepend('<div class="error_text">' + errorText + "</div>");
        }
      });
    }
  };
}

if (!funcDefined("checkMinPrice")) {
  checkMinPrice = function () {
    if (arOptimusOptions["PAGES"]["BASKET_PAGE"]) {
      var summ_raw = 0,
        summ = 0;
      if ($("#allSum_FORMATED").length) {
        summ_raw = $("#allSum_FORMATED")
          .text()
          .replace(/[^0-9\.,]/g, "");
        summ = parseFloat(summ_raw);
        if ($("#basket_items").length) {
          var summ = 0;
          $("#basket_items tr").each(function () {
            if (typeof $(this).data("item-price") !== "undefined" && $(this).data("item-price"))
              summ +=
                $(this).data("item-price") *
                $(this)
                  .find("#QUANTITY_INPUT_" + $(this).attr("id"))
                  .val();
          });
        }
        if (!$(".catalog_back").length)
          $(".bx_ordercart_order_pay_center").prepend(
            '<a href="' +
              arOptimusOptions["PAGES"]["CATALOG_PAGE_URL"] +
              '" class="catalog_back button transparent big_btn grey_br">' +
              BX.message("BASKET_CONTINUE_BUTTON") +
              "</a>"
          );
      }

      if (arOptimusOptions["THEME"]["SHOW_ONECLICKBUY_ON_BASKET_PAGE"] == "Y")
        $(".basket-coupon-section").addClass("smallest");

      if (typeof BX.Sale !== "undefined") {
        if (typeof BX.Sale.BasketComponent !== "undefined" && "result" in BX.Sale.BasketComponent)
          summ = BX.Sale.BasketComponent.result.allSum;
      }

      if (arOptimusOptions["PRICES"]["MIN_PRICE"]) {
        if (arOptimusOptions["PRICES"]["MIN_PRICE"] > summ) {
          if ($(".oneclickbuy.fast_order").length) $(".oneclickbuy.fast_order").remove();

          if ($(".basket-checkout-container").length) {
            if (!$(".icon_error_wrapper").length) {
              $(".basket-checkout-block.basket-checkout-block-btn").html(
                '<div class="icon_error_wrapper"><div class="icon_error_block">' +
                  BX.message("MIN_ORDER_PRICE_TEXT").replace(
                    "#PRICE#",
                    jsPriceFormat(arOptimusOptions["PRICES"]["MIN_PRICE"])
                  ) +
                  "</div></div>"
              );
            }
          } else {
            if (!$(".icon_error_wrapper").length) {
              $(".bx_ordercart_order_pay_center").prepend(
                '<div class="icon_error_wrapper"><div class="icon_error_block">' +
                  BX.message("MIN_ORDER_PRICE_TEXT").replace(
                    "#PRICE#",
                    jsPriceFormat(arOptimusOptions["PRICES"]["MIN_PRICE"])
                  ) +
                  "</div></div>"
              );
            }
            if ($(".bx_ordercart_order_pay .checkout").length) $(".bx_ordercart_order_pay .checkout").remove();
          }
        } else {
          if ($(".icon_error_wrapper").length) $(".icon_error_wrapper").remove();

          if ($(".basket-checkout-container").length) {
            if (
              !$(".oneclickbuy.fast_order").length &&
              arOptimusOptions["THEME"]["SHOW_ONECLICKBUY_ON_BASKET_PAGE"] == "Y" &&
              !$(".basket-btn-checkout.disabled").length
            )
              $(".basket-checkout-section-inner").append(
                '<div class="fastorder"><span class="oneclickbuy button big_btn fast_order" onclick="oneClickBuyBasket()">' +
                  BX.message("BASKET_QUICK_ORDER_BUTTON") +
                  "</span></div>"
              );
          } else {
            if ($(".bx_ordercart_order_pay .checkout").length)
              $(".bx_ordercart .bx_ordercart_order_pay .checkout").css("opacity", "1");
            else
              $(".bx_ordercart_order_pay_center").append(
                '<a href="javascript:void(0)" onclick="checkOut();" class="checkout" style="opacity: 1;">' +
                  BX.message("BASKET_ORDER_BUTTON") +
                  "</a>"
              );
            if (
              !$(".oneclickbuy.fast_order").length &&
              arOptimusOptions["THEME"]["SHOW_ONECLICKBUY_ON_BASKET_PAGE"] == "Y"
            )
              $(".bx_ordercart_order_pay_center").append(
                '<span class="oneclickbuy button big_btn fast_order" onclick="oneClickBuyBasket()">' +
                  BX.message("BASKET_QUICK_ORDER_BUTTON") +
                  "</span>"
              );
          }
        }
      } else {
        if ($(".basket-checkout-container").length) {
          if (
            !$(".oneclickbuy.fast_order").length &&
            arOptimusOptions["THEME"]["SHOW_ONECLICKBUY_ON_BASKET_PAGE"] == "Y" &&
            !$(".basket-btn-checkout.disabled").length
          )
            $(".basket-checkout-section-inner").append(
              '<div class="fastorder"><span class="oneclickbuy button big_btn fast_order" onclick="oneClickBuyBasket()">' +
                BX.message("BASKET_QUICK_ORDER_BUTTON") +
                "</span></div>"
            );
        } else {
          $(".bx_ordercart .bx_ordercart_order_pay .checkout").css("opacity", "1");
          if (
            !$(".oneclickbuy.fast_order").length &&
            arOptimusOptions["THEME"]["SHOW_ONECLICKBUY_ON_BASKET_PAGE"] == "Y"
          )
            $(".bx_ordercart_order_pay_center").append(
              '<span class="oneclickbuy button big_btn fast_order" onclick="oneClickBuyBasket()">' +
                BX.message("BASKET_QUICK_ORDER_BUTTON") +
                "</span>"
            );
        }
      }
      $("#basket-root .basket-checkout-container").addClass("visible");
    }
  };
}

var isFrameDataReceived = false;
if (typeof window.frameCacheVars !== "undefined") {
  BX.addCustomEvent("onFrameDataReceived", function (json) {
    initFull();
    isFrameDataReceived = true;
  });
} else {
  $(document).ready(initFull);
}

if (!funcDefined("setHeightBlockSlider")) {
  setHeightBlockSlider = function () {
    var sliderWidth = $(document).find(".specials.tab_slider_wrapp").outerWidth();

    $(document).find(".specials.tab_slider_wrapp .tabs_content > li.cur").css("height", "");
    $(document).find(".specials.tab_slider_wrapp .tabs_content .tab.cur .tabs_slider .buttons_block").hide();
    $(document).find(".specials.tab_slider_wrapp .tabs_content > li.cur").equalize({ children: ".item-title" });
    $(document).find(".specials.tab_slider_wrapp .tabs_content > li.cur").equalize({ children: ".item_info" });
    $(document).find(".specials.tab_slider_wrapp .tabs_content > li.cur").equalize({ children: ".catalog_item" });
    var itemsButtonsHeight = $(document)
      .find(".specials.tab_slider_wrapp .tabs_content .tab.cur .tabs_slider li .buttons_block")
      .height();
    var tabsContentUnhover = $(document).find(".specials.tab_slider_wrapp .tabs_content .tab.cur").height() * 1;
    var tabsContentHover = tabsContentUnhover + itemsButtonsHeight + 50;
    $(document).find(".specials.tab_slider_wrapp .tabs_content .tab.cur").attr("data-unhover", tabsContentUnhover);
    $(document).find(".specials.tab_slider_wrapp .tabs_content .tab.cur").attr("data-hover", tabsContentHover);
    $(document).find(".specials.tab_slider_wrapp .tabs_content").height(tabsContentUnhover);

    $(document)
      .find(".specials.tab_slider_wrapp .tabs_content .tabs_slider")
      .each(function (index, el) {
        const height = $(this).find(".catalog_item").height();
        $(el).attr("data-height", height);
      });
  };
}

if (!funcDefined("checkStickyFooter")) {
  checkStickyFooter = function () {
    try {
      ignoreResize.push(true);
      $("#content").css("min-height", "");
      var contentTop = 0;
      if ($("#content").length) contentTop = $("#content").offset().top;

      var contentBottom = contentTop + $("#content").outerHeight();

      var footerTop = 0;
      if ($("footer").length) footerTop = $("footer").offset().top;

      $("#content").css(
        "min-height",
        $(window).height() - contentTop - (footerTop - contentBottom) - $("footer").outerHeight() + "px"
      );
      ignoreResize.pop();
    } catch (e) {
      console.error(e);
    }
  };
}

/* EVENTS */
var timerResize = false,
  ignoreResize = [];
$(window).resize(function () {
  if (!$("html.print").length) checkStickyFooter();

  // here immediate functions
  if (!ignoreResize.length) {
    if (timerResize) {
      clearTimeout(timerResize);
      timerResize = false;
    }
    timerResize = setTimeout(function () {
      // here delayed functions in event
      BX.onCustomEvent("onWindowResize", false);
    }, 50);
  }
});

var timerScroll = false,
  ignoreScroll = [],
  documentScrollTopLast = $(document).scrollTop();
$(window).scroll(function () {
  // here immediate functions
  documentScrollTopLast = $(document).scrollTop();
  if (!ignoreScroll.length) {
    if (timerScroll) {
      clearTimeout(timerScroll);
      timerScroll = false;
    }
    timerScroll = setTimeout(function () {
      // here delayed functions in event
      BX.onCustomEvent("onWindowScroll", false);
    }, 50);
  }
});

BX.addCustomEvent("onWindowResize", function (eventdata) {
  try {
    ignoreResize.push(true);

    // CheckTopMenuFullCatalogSubmenu();

    checkScrollToTop();
    //checkPopupWidth();
    checkCaptchaWidth();
    checkFormWidth();
    checkFormControlWidth();
    touchMenu("ul.menu:not(.opened) > li.menu_item_l1");
    touchBasket(".cart:not(.empty_cart) .basket_block .link");
    CheckObjectsSizes();

    if (funcDefined("checkMobileFilter")) {
      checkMobileFilter();
    }

    CheckFlexSlider();

    // InitZoomPict();
    initSly();

    if (window.matchMedia("(min-width: 767px)").matches) {
      $(".wrapper_middle_menu.wrap_menu").removeClass("mobile");
    }
    if (window.matchMedia("(max-width: 767px)").matches) {
      $(".wrapper_middle_menu.wrap_menu").addClass("mobile");
    }

    if ($(window).outerWidth() > 600) {
      $("#header ul.menu").removeClass("opened").css("display", "");

      if ($(".authorization-cols").length) {
        $(".authorization-cols").equalize({ children: ".col .auth-title", reset: true });
        $(".authorization-cols").equalize({ children: ".col .form-block", reset: true });
      }
    } else {
      $(".authorization-cols .auth-title").css("height", "");
      $(".authorization-cols .form-block").css("height", "");
    }

    if ($("#basket_form").length && $(window).outerWidth() <= 600) {
      $("#basket_form .tabs_content.basket > li.cur td").each(function () {
        $(this).css("width", "");
      });
    }

    if ($(".front_slider_wrapp").length) {
      $(".extended_pagination li i").each(function () {
        $(this).css({
          borderBottomWidth: $(this).parent("li").outerHeight() / 2,
          borderTopWidth: $(this).parent("li").outerHeight() / 2,
        });
      });
    }

    setHeightCompany();
    $(".bx_filter_section .bx_filter_select_container").each(function () {
      var prop_id = $(this).closest(".bx_filter_parameters_box").attr("property_id");
      if ($("#smartFilterDropDown" + prop_id).length) {
        $("#smartFilterDropDown" + prop_id).css("max-width", $(this).width());
      }
    });
    setTimeout(function () {
      checkVerticalMobileFilter();
    }, 100);
  } catch (e) {
  } finally {
    ignoreResize.pop();
  }
});

BX.addCustomEvent("onWindowScroll", function (eventdata) {
  try {
    ignoreScroll.push(true);
  } catch (e) {
  } finally {
    ignoreScroll.pop();
  }
});

BX.addCustomEvent("onSlideInit", function (eventdata) {
  try {
    ignoreResize.push(true);
    if (eventdata) {
      var slider = eventdata.slider;
      if (slider) {
        if (slider.hasClass("viewed")) {
          $(".viewed_block .rows_block .item .item-title").sliceHeight({
            outer: true,
            slice: 8,
            autoslicecount: false,
          });
          $(".viewed_block .rows_block .item").sliceHeight({ slice: 8, autoslicecount: false });
        }
      }
    }
  } catch (e) {
  } finally {
    ignoreResize.pop();
  }
});

BX.addCustomEvent("onCounterGoals", function (eventdata) {
  if (arOptimusOptions["COUNTERS"]["USE_YA_COUNTER"] === "Y") {
    var idCounter = arOptimusOptions["COUNTERS"]["YA_COUNTER_ID"];
    idCounter = parseInt(idCounter);

    if (typeof eventdata != "object") {
      eventdata = { goal: "undefined" };
    }
    if (typeof eventdata.goal != "string") {
      eventdata.goal = "undefined";
    }

    if (idCounter) {
      try {
        waitCounter(idCounter, 50, function () {
          var obCounter = window["yaCounter" + idCounter];
          if (typeof obCounter == "object") {
            obCounter.reachGoal(eventdata.goal);
          }
        });
      } catch (e) {
        console.error(e);
      }
    } else {
      console.info("Bad counter id!", idCounter);
    }
  }
});

var onCaptchaVerifyinvisible = function (response) {
  $(".g-recaptcha:last").each(function () {
    var id = $(this).attr("data-widgetid");
    if (typeof id !== "undefined" && response) {
      if (!$(this).closest("form").find(".g-recaptcha-response").val())
        $(this).closest("form").find(".g-recaptcha-response").val(response);
      if ($("iframe[src*=recaptcha]").length) {
        $("iframe[src*=recaptcha]").each(function () {
          var block = $(this).parent().parent();
          if (!block.hasClass("grecaptcha-badge")) block.remove();
        });
      }
      $(this).closest("form").submit();
    }
  });
};

var onCaptchaVerifynormal = function (response) {
  $(".g-recaptcha").each(function () {
    var id = $(this).attr("data-widgetid");
    if (typeof id !== "undefined") {
      if (grecaptcha.getResponse(id) != "") {
        $(this).closest("form").find(".recaptcha").valid();
      }
    }
  });
};

BX.addCustomEvent("onSubmitForm", function (eventdata) {
  try {
    if (!window.renderRecaptchaById || !window.asproRecaptcha || !window.asproRecaptcha.key) {
      eventdata.form.submit();
      $(eventdata.form).closest(".form").addClass("sending");
      return true;
    }
    if (window.asproRecaptcha.params.recaptchaSize == "invisible" && typeof grecaptcha != "undefined") {
      if ($(eventdata.form).find(".g-recaptcha-response").val()) {
        eventdata.form.submit();
        $(eventdata.form).closest(".form").addClass("sending");
      } else {
        grecaptcha.execute($(eventdata.form).find(".g-recaptcha").data("widgetid"));
      }
    } else {
      eventdata.form.submit();
      $(eventdata.form).closest(".form").addClass("sending");
    }

    return true;
  } catch (e) {
    console.error(e);
    return true;
  }
});

$(document).ready(function () {
  /*  --- Bind mobile filter  --- */
  var $mobilefilter = $("#mobilefilter");
  var $mobileMenu = $("#mobilemenu, #mobileheadersimple");
  if ($mobilefilter.length && window.matchMedia("(max-width: 768px)").matches) {
    $mobilefilter.isOpen = $mobileMenu.hasClass("show");
    $mobilefilter.isAppendLeft = false;
    $mobilefilter.isWrapFilter = false;
    $mobilefilter.isHorizontalOrCompact = $(".filter_horizontal").length || $(".bx_filter_vertical.compact").length;
    $mobilefilter.close = '<i class="svg svg-close close-icons"></i>';

    $(document).on("click", ".filter_opener", function () {
      OpenMobileFilter();
    });

    $(document).on("click", "#mobilefilter .svg-close.close-icons", function () {
      CloseMobileFilter();
    });

    $(document).on("click", ".bx_filter_select_block", function (e) {
      var bx_filter_select_container = $(e.target).parents(".bx_filter_select_container");
      if (bx_filter_select_container.length) {
        var prop_id = bx_filter_select_container.closest(".bx_filter_parameters_box").attr("data-property_id");
        if ($("#smartFilterDropDown" + prop_id).length) {
          $("#smartFilterDropDown" + prop_id).css({
            "max-width": bx_filter_select_container.width(),
            "z-index": "3020",
          });
        }
      }
    });

    $(document).on("mouseup", ".bx_filter_section", function (e) {
      if ($(e.target).hasClass("bx_filter_search_button")) {
        CloseMobileFilter();
      }
    });

    $(document).on("mouseup", ".bx_filter_parameters_box_title", function (e) {
      $("[id^='smartFilterDropDown']").hide();
      if ($(e.target).hasClass("close-icons")) {
        CloseMobileFilter();
      }
    });

    $mobilefilter.parent().append('<div id="mobilefilter-overlay"></div>');
    var $mobilefilterOverlay = $("#mobilefilter-overlay");

    $mobilefilterOverlay.click(function () {
      if ($mobilefilter.isOpen) {
        CloseMobileFilter();
        //e.stopPropagation();
      }
    });

    mobileFilterNum = function (num, def) {
      if (def) {
        $(".bx_filter_search_button").val(num.data("f"));
      } else {
        var str = "";
        var $prosLeng = $(".bx_filter_parameters_box > span");

        str +=
          $prosLeng.data("f") +
          " " +
          num +
          " " +
          declOfNumFilter(num, [$prosLeng.data("fi"), $prosLeng.data("fr"), $prosLeng.data("frm")]);
        $(".bx_filter_search_button").val(str);
      }
    };

    declOfNumFilter = function (number, titles) {
      cases = [2, 0, 1, 1, 1, 2];
      return titles[number % 100 > 4 && number % 100 < 20 ? 2 : cases[number % 10 < 5 ? number % 10 : 5]];
    };

    OpenMobileFilter = function () {
      if (!$mobilefilter.isOpen) {
        if (!$mobilefilter.isAppendLeft) {
          if (!$mobilefilter.isWrapFilter) {
            $(".bx_filter").wrap("<div id='wrapInlineFilter'></div>");
            $mobilefilter.isWrapFilter = true;
          }
          $(".bx_filter").css("top", "");
          $(".bx_filter")
            .appendTo($("#mobilefilter"))
            .find(".title .bx_filter_parameters_box_title")
            .append($mobilefilter.close);
          $mobilefilter.isAppendLeft = true;
        }

        // show overlay
        setTimeout(function () {
          $mobilefilterOverlay.fadeIn("fast");
        }, 100);

        // fix body
        $("body").css({ "overflow-y": "hidden" });

        // show mobile filter
        $mobilefilter.addClass("show");
        $mobilefilter.find(".bx_filter").css({ display: "block" });
        $mobilefilter.isOpen = true;

        var init = $mobilefilter.data("init");
        if (typeof init === "undefined") {
          $mobilefilter.scroll(function () {
            $(".bx_filter_section .bx_filter_select_container").each(function () {
              var prop_id = $(this).closest(".bx_filter_parameters_box").attr("data-property_id");
              if ($("#smartFilterDropDown" + prop_id).length) {
                $("#smartFilterDropDown" + prop_id).hide();
              }
            });
          });

          $mobilefilter.data("init", "Y");
        }
      }
    };

    CloseMobileFilter = function (append) {
      $(".bx_filter").css("display", "");
      if ($mobilefilter.isOpen) {
        // scroll to top
        $mobilefilter.find(".bx_filter_parameters").scrollTop(0);

        // unfix body
        $("body").css({ "overflow-y": "auto" });

        // hide overlay
        setTimeout(function () {
          $mobilefilterOverlay.fadeOut("fast");
        }, 100);

        // hide mobile filter
        $mobilefilter.removeClass("show");
        $mobilefilter.isOpen = false;
      }

      if (append && $mobilefilter.isAppendLeft) {
        $(".bx_filter").appendTo($("#wrapInlineFilter")).show().find(".svg-close").remove();
        $mobilefilter.isAppendLeft = false;
        $mobilefilter.removeData("init");
        mobileFilterNum($("#modef_num_mobile"), true);
      }
    };

    checkMobileFilter = function () {
      if (
        (!window.matchMedia("(max-width: 991px)").matches && !$mobilefilter.isHorizontalOrCompact) ||
        (!window.matchMedia("(max-width: 767px)").matches && $mobilefilter.isHorizontalOrCompact)
      ) {
        CloseMobileFilter(true);
      }
    };
  } else {
    checkVerticalMobileFilter();
    $(document).on("click", ".filter_opener", function () {
      $(this).toggleClass("opened");
      if ($(".visible_mobile_filter").length) {
        $(".visible_mobile_filter").show();
        $(".bx_filter_vertical, .bx_filter").slideToggle(333);
      } else {
        $(".bx_filter_vertical").closest("div[id^=bx_incl]").show();
        $(".bx_filter_vertical, .bx_filter").slideToggle(333);
      }
    });
  }
  /*  --- END Bind mobile filter  --- */
});

/*custom event for sku prices*/

/*BX.addCustomEvent('onAsproSkuSetPrice', function(eventdata){
	console.log(eventdata);
})*/

/*BX.addCustomEvent('onAsproSkuSetPriceMatrix', function(eventdata){
	console.log(eventdata);
})*/

InitScrollBar = function (el, initOptions) {
  var block = $(".scrollbar:not(.mobile-scroll)");
  if (typeof el !== "undefined") block = el;
  if (block.length) {
    var options,
      defaults = {
        mouseWheel: {
          scrollAmount: 150,
          preventDefault: true,
        },
      };
    var config = $.extend({}, defaults, options, block.data("plugin-options"), initOptions);

    config.callbacks = {
      onScroll: function () {
        if ($(this).find(".mCSB_buttonLeft").hasClass("disabled"))
          $(this).find(".mCSB_buttonLeft").removeClass("disabled");
        if ($(this).find(".mCSB_buttonRight").hasClass("disabled"))
          $(this).find(".mCSB_buttonRight").removeClass("disabled");
      },
      onTotalScrollBack: function () {
        $(this).find(".mCSB_buttonLeft").addClass("disabled");
      },
      onTotalScroll: function () {
        $(this).find(".mCSB_buttonRight").addClass("disabled");
      },
      onInit: function () {
        $(this).find(".mCSB_buttonLeft").addClass("disabled");
      },
    };
    block.mCustomScrollbar(config);
  }
};

AjaxClickLink = function (e) {
  var _this = "",
    container = $("#right_block_ajax .inner_wrapper"),
    container_inner = container.find(".ajax_load"),
    dopAction = "";
  url = "";
  if ("preventDefault" in e) {
    e.preventDefault();
    _this = $(e.target).hasClass("js-load-link") ? $(e.target) : $(e.target).closest(".js-load-link");
  } else {
    _this = $(e);
    dopAction = "Y";
  }

  if (container.length) {
    var objUrl = parseUrlQuery(),
      obGetData = { ajax_get_filter: "Y", control_ajax: "Y" };

    obGetData.bitrix_include_areas = "N";

    if ("clear_cache" in objUrl) {
      if (objUrl.clear_cache == "Y") {
        obGetData.clear_cache = "Y";
      }
    }

    if (container_inner.length) container_inner.addClass("loading-state");
    else container.addClass("loading-state");

    if (_this.data("url")) url = _this.data("url");

    if (dopAction) {
      BX.PopupWindowManager.getCurrentPopup().close();
      $(".bx_filter_select_popup ul li .sort_btn").removeClass("current");
      _this.addClass("current");
      _this.closest(".bx_filter_block").find(".bx_filter_select_text").text(_this.text());
    }

    $(".bx_filter .bx_sort_filter .bx_filter_select_text").text(_this.text());
    $(".bx_filter .bx_sort_filter .bx_filter_select_popup ul li span.current").removeClass("current");
    $(".bx_filter .bx_sort_filter .bx_filter_select_popup ul li")
      .eq(_this.parent().index())
      .find("span")
      .addClass("current");

    $.ajax({
      url: url,
      data: obGetData,
      success: function (html) {
        container.html(html);

        if (container_inner.length) container_inner.removeClass("loading-state");
        else container.removeClass("loading-state");

        var eventdata = { action: "jsLoadBlock" };
        BX.onCustomEvent("onCompleteAction", [eventdata, _this]);

        initCountdown();
        InitScrollBar();
      },
    });
  }
};

if (!funcDefined("throttle")) {
  function throttle(func, wait, debounce) {
    var timeout;
    return function () {
      var context = this,
        args = arguments;
      var throttler = function () {
        timeout = null;
        func.apply(context, args);
      };
      if (debounce) clearTimeout(timeout);
      if (debounce || !timeout) timeout = setTimeout(throttler, wait);
    };
  }
}

// document ready
$(function () {
  BX.addCustomEvent("onCompleteAction", function (eventdata, _this) {
    try {
      if (eventdata.action === "loadForm") {
        $(_this).parent().removeClass("loadings");
        $(_this).removeClass("clicked");
        
        if ($(_this).hasClass("one_click_buy_trigger")) {
          $(".wrapp_one_click > span").removeClass("clicked");
          $(".one_click").removeClass("clicked");
        } else if ($(_this).hasClass("one_click_buy_basket_trigger")) {
          $(".fast_order").removeClass("clicked");
        }
      } else if (eventdata.action === "loadActualBasket" && typeof arBasketAspro !== 'undefined') {
        const basketCount = Object.keys(arBasketAspro.BASKET).length;
        const wishCount = Object.keys(arBasketAspro.DELAY).length;
        const compareCount = Object.keys(arBasketAspro.COMPARE).length;

        if (wishCount > 0) {
          $(".basket-link.delay .count").removeClass("hidden");
          $(".basket-link.delay").removeClass("no-items");
        } else {
          $(".basket-link.delay .count").addClass("hidden");
          $(".basket-link.delay").addClass("no-items");
        }

        if (basketCount > 0) {
          $(".basket-link.basket .count").removeClass("hidden");
          $(".basket-link.basket").removeClass("no-items");
        } else {
          $(".basket-link.basket .count").addClass("hidden");
          $(".basket-link.basket").addClass("no-items");
        }

        if (compareCount > 0) {
          if (!$(".basket-link.compare .count").length) {
            $(".basket-link.compare .wraps_icon_block").append(
              $(
                '<div class="count"><span><div class="items"><a href="' +
                  $(".basket-link.compare .link").attr("href") +
                  '">' +
                  compareCount +
                  "</a></div></span></div>"
              )
            );
          }
          $(".basket-link.compare .count").removeClass("hidden");
          $(".basket-link.compare").removeClass("no-items");
        } else {
          $(".basket-link.compare .count").addClass("hidden");
          $(".basket-link.compare").addClass("no-items");
        }

        $(".basket-link.basket .count .text").text(basketCount);
        $(".basket-link.delay .count .text").text(wishCount);
        $(".basket-link.compare .count a, .basket-link.comapre > .link").text(compareCount);
      }
    } catch (e) {
      console.error(e);
    }
  });

  checkHeaderFixed();

  $(".menu-block.mobile .title").on("click", function () {
    const $menuWrapper = $(".menu-block.mobile");
    const $menuBlock = $(".menu-block.mobile .navs");

    $menuWrapper.toggleClass("opened");
    $menuBlock.toggleClass("opened");

    if ($menuWrapper.hasClass("opened")) $menuBlock.slideDown();
    else $menuBlock.slideUp();
  });
});

const checkHeaderFixed = function () {
  const isMobile = jQuery.browser.mobile || window.matchMedia("(max-width: 991px)").matches;
  const mfixed = $("#headerfixed_mobile"),
    menu = $("header .main-nav");

  var startScroll = 0;

  var $mobileHeaderFixedNlo = mfixed.find("[data-nlo]"),
    isMobileNloLoaded = !$mobileHeaderFixedNlo.length,
    OnMobileHeaderFixedScrollHandler;

  var header_fixed = $("#headerfixed");
  header = $("header").first();

  if (header_fixed.length) {
    if (header.length) {
      var isHeaderFixed = false,
        isTabsFixed = false,
        headerCanFix = true,
        headerFixedHeight = header_fixed.actual("outerHeight"),
        headerNormalHeight = header.actual("outerHeight"),
        headerDiffHeight = headerNormalHeight - headerFixedHeight,
        mobileBtnMenu = $(".btn.btn-responsive-nav"),
        headerTop = $("#panel:visible").actual("outerHeight");
      topBlock = $(".TOP_HEADER").first();

      var $headerFixedNlo = header_fixed.find("[data-nlo]"),
        isNloLoaded = !$headerFixedNlo.length,
        OnHeaderFixedScrollHandler;

      if (headerDiffHeight <= 0) headerDiffHeight = 0;
      if (topBlock.length) headerTop += topBlock.actual("outerHeight");

      $(window).scroll(
        (OnHeaderFixedScrollHandler = function () {
          var tabs_fixed = $(".product-item-detail-tabs-container-fixed");

          if (window.matchMedia("(min-width:992px)").matches) {
            var scrollTop = $(window).scrollTop();
            headerCanFix = !mobileBtnMenu.is(":visible") /* && !$('.dropdown-menu:visible').length*/;

            var tabs_offset = $(".tabs_section .tabs1").offset();

            var current_is = $(".search-wrapper .search-input:visible"),
              title_search_result = $(".title-search-result." + current_is.attr("id")),
              pos,
              pos_input;

            if (!isHeaderFixed) {
              if (scrollTop > headerNormalHeight + headerTop && headerCanFix) {
                if (!isNloLoaded) {
                  if (!$headerFixedNlo.hasClass("nlo-loadings")) {
                    $headerFixedNlo.addClass("nlo-loadings");
                    setTimeout(function () {
                      $.ajax({
                        data: { nlo: $headerFixedNlo.attr("data-nlo") },
                        success: function (response) {
                          // stop ya metrika webvisor DOM indexer
                          pauseYmObserver();

                          isNloLoaded = true;
                          $headerFixedNlo[0].insertAdjacentHTML("beforebegin", $.trim(response));
                          $headerFixedNlo.remove();

                          OnHeaderFixedScrollHandler();

                          // resume ya metrika webvisor
                          // (300ms transition) + (100ms scroll handler)
                          setTimeout(resumeYmObserver, 400);
                        },
                        error: function () {
                          $headerFixedNlo.removeClass("nlo-loadings");
                        },
                      });
                    }, 300);
                  }
                } else {
                  isHeaderFixed = true;
                  // header_fixed.css('top', '-' + headerNormalHeight + 'px');
                  header_fixed.addClass("fixed");
                  // header_fixed.stop(0).animate({top: '0'}, 300);

                  /*header_fixed.animate({top: '0'}, {duration:300, complete:
                                    function(){}
                                });*/

                  if ($("nav.mega-menu.sliced").length) $("nav.mega-menu.sliced").removeClass("initied");
                  //CheckTopMenuDotted();
                }
              }
            } else if (isHeaderFixed || !headerCanFix) {
              if (scrollTop <= headerDiffHeight + headerTop || !headerCanFix) {
                isHeaderFixed = false;
                header_fixed.removeClass("fixed");
                $(".title-search-result.title-search-input2_fixed").css("display", "none");
              }
            }
            //fixed tabs
            if (tabs_fixed.length) {
              if (scrollTop + headerFixedHeight > tabs_offset.top) {
                tabs_fixed.css({ top: header_fixed.actual("outerHeight") });
                tabs_fixed.addClass("fixed");
              } else if (tabs_fixed.hasClass("fixed")) {
                tabs_fixed.removeAttr("style");
                tabs_fixed.removeClass("fixed");
              }
            }
          }
        })
      );
    }
  }

  //mobile fixed
  if (mfixed.length && isMobile) {
    var isMHeaderFixed = false,
      mheaderCanFix = true,
      mheaderFixedHeight = mfixed.actual("outerHeight"),
      mheaderNormalHeight = header.actual("outerHeight") + menu.actual("outerHeight"),
      mheaderDiffHeight = mheaderNormalHeight - mheaderFixedHeight,
      mheaderTop = $("#panel:visible").actual("outerHeight");

    if (headerDiffHeight <= 0) headerDiffHeight = 0;

    $(window).scroll(
      (OnMobileHeaderFixedScrollHandler = function () {
        var scrollTop = $(window).scrollTop();

        if (window.matchMedia("(max-width:991px)").matches) {
          if (!isMHeaderFixed) {
            if (scrollTop > mheaderDiffHeight + mheaderTop) {
              if (!isMobileNloLoaded) {
                if (!$mobileHeaderFixedNlo.hasClass("nlo-loadings")) {
                  $mobileHeaderFixedNlo.addClass("nlo-loadings");
                  setTimeout(function () {
                    $.ajax({
                      data: { nlo: $mobileHeaderFixedNlo.attr("data-nlo") },
                      success: function (response) {
                        // stop ya metrika webvisor DOM indexer
                        pauseYmObserver();

                        isMobileNloLoaded = true;
                        $mobileHeaderFixedNlo[0].insertAdjacentHTML("beforebegin", $.trim(response));
                        $mobileHeaderFixedNlo.remove();

                        OnMobileHeaderFixedScrollHandler();

                        // resume ya metrika webvisor
                        // (300ms transition) + (100ms scroll handler)
                        setTimeout(resumeYmObserver, 400);
                      },
                      error: function () {
                        $mobileHeaderFixedNlo.removeClass("nlo-loadings");
                      },
                    });
                  }, 300);
                }
              } else {
                //console.log(scrollTop, mheaderFixedHeight);
                isMHeaderFixed = true;
                mfixed.addClass("fixed");
              }
            }
          } else if (isMHeaderFixed) {
            if (scrollTop <= mheaderDiffHeight + mheaderTop) {
              isMHeaderFixed = false;
              mfixed.removeClass("fixed");
              $("#headerfixed_mobile .menu.adaptive, #headerfixed_mobile .menu.full").removeClass("opened");
              $("#headerfixed_mobile .menu.full").css("display", "none");
            }
          }
        } else mfixed.removeClass("fixed");
      })
    );
  }
};
