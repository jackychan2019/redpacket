(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-subPages-dealer-apply-apply"],{"0e0a":function(t,a,i){"use strict";Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0;var e=r(i("5f2a")),n=r(i("cbb2"));function r(t){return t&&t.__esModule?t:{default:t}}var o={data:function(){return{}},components:{wxParse:n.default},computed:{},onShow:function(){},mounted:function(){},methods:{navgateTo:function(){e.default.navigationTo({url:"pages/subPages/dealer/index/index"})}}};a.default=o},"0fda":function(t,a,i){"use strict";i.r(a);var e=i("0e0a"),n=i.n(e);for(var r in e)"default"!==r&&function(t){i.d(a,t,function(){return e[t]})}(r);a["default"]=n.a},"4ec3":function(t,a,i){"use strict";var e=i("7361"),n=i.n(e);n.a},7361:function(t,a,i){var e=i("9385");"string"===typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);var n=i("4f06").default;n("3ecd845c",e,!0,{sourceMap:!1,shadowMode:!1})},"847c":function(t,a,i){"use strict";var e=function(){var t=this,a=t.$createElement,i=t._self._c||a;return i("v-uni-view",{staticClass:"container"},[i("v-uni-view",{staticClass:"header-bg",style:{"background-image":" url("+t.imageRoot+"hezuo.jpg)"}}),i("v-uni-view",{staticClass:"apply-content"},[i("v-uni-view",{staticClass:"rich-part m-btm40"},[i("v-uni-view",{staticClass:"parse-rich"},[i("v-uni-view",{staticClass:"title-content dis-flex flex-y-center flex-x-center m-btm40"},[i("v-uni-view",{staticClass:"title-iconLeft title-iconStyle",style:{"background-image":" url("+t.imageRoot+"titleRight.png)"}}),i("v-uni-view",{staticClass:"f-30 col-3 m-left-right-20"},[t._v("申请分销商说明")]),i("v-uni-view",{staticClass:"title-iconRight title-iconStyle",style:{"background-image":" url("+t.imageRoot+"titleLeft.png)"}})],1),i("wx-parse",{attrs:{content:"申请分销商详情"}})],1),i("v-uni-view",{staticClass:"box-btm-shade"})],1),i("v-uni-view",{staticClass:"withdraw-msg"},[i("v-uni-view",{staticClass:"withdrawTop-style p-r"},[i("v-uni-view",{staticClass:"withdrawTop-style-with"})],1),i("v-uni-view",{staticClass:"withdraw-part"},[i("v-uni-view",{staticClass:"withdraw-content"},[i("v-uni-view",{staticClass:"title-content dis-flex flex-y-center flex-x-center m-btm50"},[i("v-uni-view",{staticClass:"title-iconLeft title-iconStyle",style:{"background-image":" url("+t.imageRoot+"titleRight.png)"}}),i("v-uni-view",{staticClass:"f-30 col-3 m-left-right-20"},[t._v("最近提现")]),i("v-uni-view",{staticClass:"title-iconRight title-iconStyle",style:{"background-image":" url("+t.imageRoot+"titleLeft.png)"}})],1),i("v-uni-view",{staticClass:"scroll"},[i("v-uni-scroll-view",{staticClass:"scroll-view",attrs:{"scroll-y":!0}},[i("v-uni-view",{staticClass:"withdraw-list"},t._l(5,function(a,e){return i("v-uni-view",{key:e,staticClass:"withdraw-item dis-flex flex-y-center flex-x-between border-line border-bottom"},[i("v-uni-view",{staticClass:"withdrawerName col-3 f-24"},[t._v("漆**大")]),i("v-uni-view",{staticClass:"withdrawerPrice col-9 f-24"},[t._v("申请提现￥66.66")])],1)}),1)],1)],1)],1)],1)],1)],1),i("v-uni-view",{staticClass:"confirmApply",on:{click:function(a){a=t.$handleEvent(a),t.navgateTo(a)}}},[i("v-uni-view",{staticClass:"Apply-button f-28 col-3 t-c f-w"},[t._v("成为分销商")]),i("v-uni-view",{staticClass:"Apply-buttom-shade"})],1)],1)},n=[];i.d(a,"a",function(){return e}),i.d(a,"b",function(){return n})},9385:function(t,a,i){a=t.exports=i("2350")(!1),a.push([t.i,'@charset "UTF-8";\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */uni-page-body[data-v-0ae70d81]{background-color:#f44}.container .header-bg[data-v-0ae70d81]{width:100%;height:%?360?%;background-size:100% %?360?%;background-repeat:no-repeat}.container .rich-part[data-v-0ae70d81]{padding:0 %?44?%}.container .rich-part .parse-rich[data-v-0ae70d81]{position:relative;padding:%?48?% 0;background-color:#fff;border-radius:%?20?%;z-index:2}.container .rich-part .box-btm-shade[data-v-0ae70d81]{position:relative;border-radius:%?20?%;background-color:#d10000;height:%?40?%;margin-top:%?-30?%;z-index:1}.container .withdraw-msg .withdrawTop-style[data-v-0ae70d81]{height:%?30?%;background-color:#d10000;margin:0 %?30?%;border-radius:%?16?%;position:relative;padding:0 %?10?%}.container .withdraw-msg .withdrawTop-style .withdrawTop-style-with[data-v-0ae70d81]{position:absolute;background-color:#ab0000;width:96%;height:%?10?%;border-radius:%?6?%;left:50%;top:50%;-webkit-transform:translate(-50%,-50%);-ms-transform:translate(-50%,-50%);transform:translate(-50%,-50%)}.container .withdraw-msg .withdraw-part[data-v-0ae70d81]{padding:0 %?50?%}.container .withdraw-msg .withdraw-part .withdraw-content[data-v-0ae70d81]{padding:%?50?% %?30?% %?30?%;position:relative;background-color:#fff;margin-top:%?-12?%;border-radius:0 0 %?10?% %?10?%}.container .withdraw-msg .withdraw-part .withdraw-content .scroll-view[data-v-0ae70d81]{height:%?400?%;background-color:#f6f6f6;border-radius:%?10?%}.container .withdraw-msg .withdraw-part .withdraw-content .scroll-view .withdraw-list[data-v-0ae70d81]{margin:0 %?30?%}.container .withdraw-msg .withdraw-part .withdraw-content .scroll-view .withdraw-list .withdraw-item[data-v-0ae70d81]{padding:%?26?% 0}.container .title-content .title-iconStyle[data-v-0ae70d81]{width:%?64?%;height:%?44?%;background-size:%?64?% %?44?%;background-repeat:no-repeat}.container .confirmApply[data-v-0ae70d81]{padding:%?50?% %?30?%}.container .confirmApply .Apply-button[data-v-0ae70d81]{position:relative;background-color:#ffd74b;padding:%?30?% 0;border-radius:%?44?%;z-index:2}.container .confirmApply .Apply-buttom-shade[data-v-0ae70d81]{position:relative;z-index:1;height:%?80?%;background-color:#ffae00;border-radius:%?44?%;margin-top:%?-70?%}body.?%PAGE?%[data-v-0ae70d81]{background-color:#f44}',""])},efb7:function(t,a,i){"use strict";i.r(a);var e=i("847c"),n=i("0fda");for(var r in n)"default"!==r&&function(t){i.d(a,t,function(){return n[t]})}(r);i("4ec3");var o=i("2877"),s=Object(o["a"])(n["default"],e["a"],e["b"],!1,null,"0ae70d81",null);a["default"]=s.exports}}]);