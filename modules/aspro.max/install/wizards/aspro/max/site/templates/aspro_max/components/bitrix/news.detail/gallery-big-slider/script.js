BX.addCustomEvent("onSlide", function (eventdata) {  
  try {
    ignoreResize.push(true);

    if (eventdata) {
      let slider = eventdata.slider;
      let data = eventdata.data;
      let $infoCount = $(slider).siblings(".gallery-count-info");
      
      if ($infoCount.length) { 
        $infoCount.find(".gallery-count-info__js-text").text(data.item.index + 1);
      }
    }
  } catch (e) {
  } finally {
    ignoreResize.pop();
  }
});