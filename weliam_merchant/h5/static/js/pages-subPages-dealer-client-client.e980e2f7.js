(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-subPages-dealer-client-client"],{"0110":function(t,e,i){"use strict";i.r(e);var a=i("467f"),n=i("f86e");for(var c in n)"default"!==c&&function(t){i.d(e,t,function(){return n[t]})}(c);i("b86b");var s=i("2877"),l=Object(s["a"])(n["default"],a["a"],a["b"],!1,null,"b9c5e7e6",null);e["default"]=l.exports},"0c68":function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a={data:function(){return{currentTab:"all",tabBar:[{title:"全部",typeTab:"all"},{title:"已下单",typeTab:"alerea"},{title:"未下单",typeTab:"not"}]}},components:{},computed:{},onShow:function(){},mounted:function(){},methods:{curtabBar:function(t){var e=this;e.currentTab=t}}};e.default=a},"467f":function(t,e,i){"use strict";var a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"container"},[i("v-uni-view",{staticClass:"header"},[i("v-uni-view",{staticClass:"tabBar b-f dis-flex"},t._l(t.tabBar,function(e,a){return i("v-uni-view",{staticClass:"tabBar-item p-r f-24 col-9 t-c",class:{"tabBar-active-text":t.currentTab===e.typeTab},on:{click:function(i){i=t.$handleEvent(i),t.curtabBar(e.typeTab)}}},[t._v(t._s(e.title)),i("v-uni-view",{class:{active:t.currentTab===e.typeTab}})],1)}),1)],1),i("v-uni-view",{staticClass:"content"},[i("v-uni-view",{staticClass:"search-main m-top-btm40"},[i("v-uni-view",{staticClass:"client-total dis-flex flex-y-center m-btm30"},[i("v-uni-view",{staticClass:"f-24 col-9 m-right20"},[t._v("今日新增客户:3人")]),i("v-uni-view",{staticClass:"f-24 col-9"},[t._v("合计:666666人")])],1),i("v-uni-view",{staticClass:"search-box dis-flex flex-y-center"},[i("v-uni-view",{staticClass:"search-input p-left-right-30 "},[i("v-uni-input",{staticClass:"f-24",attrs:{type:"text",value:"",placeholder:"输入客户昵称查询","placeholder-style":"color:#999999;"}})],1),i("v-uni-view",{staticClass:"search-btn col-f f-24 t-c"},[t._v("搜索")])],1)],1),i("v-uni-view",{staticClass:"invite-client b-f bor-radius-10upx"},[i("v-uni-view",{staticClass:"client-list-box"},[i("v-uni-view",{staticClass:"invite-title dis-flex flex-y-center flex-x-between padding-box-all border-line border-bottom"},[i("v-uni-view",{staticClass:"f-28 col-3"},[t._v("累计邀请")]),i("v-uni-view",{staticClass:"f-24 col-9"},[t._v("全部类型"),i("v-uni-text",{staticClass:"iconfont icon-unfold title-icon m-left10"})],1)],1),i("v-uni-scroll-view",{staticClass:"scroll-view",attrs:{"scroll-y":!0}},[i("v-uni-view",{staticClass:"client-list"},t._l(5,function(e,a){return i("v-uni-view",{staticClass:"client-item p-top-bom-30 m-left-right-30 dis-flex flex-y-center border-line border-bottom"},[i("v-uni-view",{staticClass:"user-avatar m-right30"}),i("v-uni-view",{staticClass:"user-detail"},[i("v-uni-view",{staticClass:"dis-flex flex-y-center flex-x-between"},[i("v-uni-view",{staticClass:"user-nickname f-28 col-3"},[t._v("哈尼哈尼")]),i("v-uni-view",{staticClass:"dealer-class f-24 dealer-class_3"},[t._v("初级分销员")])],1),i("v-uni-view",{staticClass:"binding-time f-24 col-9 m-top-btm10"},[t._v("推荐绑定时间：2030-06-22 18:16:06")]),i("v-uni-view",{staticClass:"client-price dis-flex flex-y-center flex-x-between"},[i("v-uni-view",{staticClass:"f-24 col-9"},[t._v("累计赏金：￥2.8")]),i("v-uni-view",{staticClass:"f-24 col-9"},[t._v("已购买3单")])],1)],1)],1)}),1)],1)],1)],1)],1)],1)},n=[];i.d(e,"a",function(){return a}),i.d(e,"b",function(){return n})},b86b:function(t,e,i){"use strict";var a=i("e594"),n=i.n(a);n.a},e594:function(t,e,i){var a=i("edcf");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("f8b30b96",a,!0,{sourceMap:!1,shadowMode:!1})},edcf:function(t,e,i){e=t.exports=i("2350")(!1),e.push([t.i,'@charset "UTF-8";\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */uni-page-body[data-v-b9c5e7e6]{background-color:#f7f7f7}.header .tabBar[data-v-b9c5e7e6]{width:100%}.header .tabBar .tabBar-active-text[data-v-b9c5e7e6]{font-size:%?30?%;color:#333}.header .tabBar .tabBar-item[data-v-b9c5e7e6]{width:33.33%;height:%?96?%;line-height:%?96?%}.header .tabBar .tabBar-item .active[data-v-b9c5e7e6]{position:absolute;bottom:0;left:50%;-webkit-transform:translateX(-50%);-ms-transform:translateX(-50%);transform:translateX(-50%);background-color:#f44;width:%?50?%;height:%?8?%;border-radius:%?20?%}.content[data-v-b9c5e7e6]{padding:0 %?30?%}.content .search-main .search-box .search-input[data-v-b9c5e7e6]{margin-right:%?40?%;background-color:#eee;height:%?60?%;border-radius:%?30?%}.content .search-main .search-box .search-input uni-input[data-v-b9c5e7e6]{height:100%}.content .search-main .search-box .search-btn[data-v-b9c5e7e6]{width:%?90?%;line-height:%?60?%;height:%?60?%;background:#f44;border-radius:%?30?%}.content .invite-client .scroll-view[data-v-b9c5e7e6]{height:60vh}.content .invite-client .client-list-box .invite-title .title-icon[data-v-b9c5e7e6]{font-size:%?24?%;color:#333}.content .invite-client .client-list-box .client-list .client-item .user-avatar[data-v-b9c5e7e6]{width:%?90?%;height:%?90?%;border-radius:50%;background-size:%?90?% %?90?%;background-repeat:no-repeat;background-image:url(http://img4.imgtn.bdimg.com/it/u=3024387196,1621670548&fm=26&gp=0.jpg);-webkit-flex-shrink:0;-ms-flex-negative:0;flex-shrink:0}.content .invite-client .client-list-box .client-list .client-item .user-detail[data-v-b9c5e7e6]{-webkit-box-flex:1;-webkit-flex:1;-ms-flex:1;flex:1}.content .invite-client .client-list-box .client-list .client-item .user-detail .dealer-class[data-v-b9c5e7e6]{width:%?150?%;height:%?40?%;line-height:%?40?%;text-align:center;border-radius:%?18?%}.content .invite-client .client-list-box .client-list .client-item .user-detail .dealer-class_1[data-v-b9c5e7e6]{color:#999;background-color:#eee}.content .invite-client .client-list-box .client-list .client-item .user-detail .dealer-class_2[data-v-b9c5e7e6]{color:#f44;background-color:#ffd3d3}.content .invite-client .client-list-box .client-list .client-item .user-detail .dealer-class_3[data-v-b9c5e7e6]{color:#ff730d;background-color:#ffdfc5}body.?%PAGE?%[data-v-b9c5e7e6]{background-color:#f7f7f7}',""])},f86e:function(t,e,i){"use strict";i.r(e);var a=i("0c68"),n=i.n(a);for(var c in a)"default"!==c&&function(t){i.d(e,t,function(){return a[t]})}(c);e["default"]=n.a}}]);