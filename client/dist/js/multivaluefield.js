!function(){"use strict";var t={311:function(t){t.exports=jQuery}},e={};function n(i){var s=e[i];if(void 0!==s)return s.exports;var l=e[i]={exports:{}};return t[i](l,l.exports,n),l.exports}n(311)((t=>{function e(){const e=t(this),n=e.val(),i=t(this).closest("li").next("li");if(n){if(i.length)return;const t=e.closest("li").clone().find(".has-chzn").show().removeClass("").data("chosen",null).end().find(".chzn-container").remove().end();t.find("input, select, textarea").val("").each((function(){let t=this.id.lastIndexOf("__");if(-1!==t){t+=2;const e=parseInt(this.id.substr(t),10)+1;this.id=this.id.substr(0,t)+e}})),t.appendTo(e.parents("ul.multivaluefieldlist"))}else{const e=i.find("input.mventryfield");let n=!0;e.each((function(){t(this)&&t(this).val()&&t(this).val().length>0&&(n=!1)})),n&&i.detach()}t(this).trigger("multiValueFieldAdded")}t(document).on("keyup",".mventryfield",e),t(document).on("change",".mventryfield:not(input)",e),t.fn.sortable&&(t.entwine?t("ul.multivaluefieldlist").entwine({onmatch(){t(this).sortable()}}):t("ul.multivaluefieldlist").sortable())}))}();