(function(){ document.jivositeloaded=0;var widget_id = 'czOkF0tzWz';var d=document;var w=window;function l(){var s = d.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code-ya.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}//эта строка обычная для кода JivoSite
function zy(){
    //удаляем EventListeners
    if(w.detachEvent){//поддержка IE8
        w.detachEvent('onscroll',zy);
        w.detachEvent('onmousemove',zy);
        w.detachEvent('ontouchmove',zy);
        w.detachEvent('onresize',zy);
    }else {
        w.removeEventListener("scroll", zy, false);
        w.removeEventListener("mousemove", zy, false);
        w.removeEventListener("touchmove", zy, false);
        w.removeEventListener("resize", zy, false);
    }
    //запускаем функцию загрузки JivoSite
    if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}
    //Устанавливаем куку по которой отличаем первый и второй хит
    var cookie_date = new Date ( );
    cookie_date.setTime ( cookie_date.getTime()+60*60*28*1000); //24 часа для Москвы
    d.cookie = "JivoSiteLoaded=1;path=/;expires=" + cookie_date.toGMTString();
}
if (d.cookie.search ( 'JivoSiteLoaded' )<0){//проверяем, первый ли это визит на наш сайт, если да, то назначаем EventListeners на события прокрутки, изменения размера окна браузера и скроллинга на ПК и мобильных устройствах, для отложенной загрузке JivoSite.
    if(w.attachEvent){// поддержка IE8
        w.attachEvent('onscroll',zy);
        w.attachEvent('onmousemove',zy);
        w.attachEvent('ontouchmove',zy);
        w.attachEvent('onresize',zy);
    }else {
        w.addEventListener("scroll", zy, {capture: false, passive: true});
        w.addEventListener("mousemove", zy, {capture: false, passive: true});
        w.addEventListener("touchmove", zy, {capture: false, passive: true});
        w.addEventListener("resize", zy, {capture: false, passive: true});
    }
}else {zy();}
})();