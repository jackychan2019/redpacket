(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-mainPages-city-selectAddress"],{"18ba":function(t,a,n){"use strict";Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0;var i=o(n("3099")),e=o(n("9e9f"));function o(t){return t&&t.__esModule?t:{default:t}}function s(t){return d(t)||r(t)||c()}function c(){throw new TypeError("Invalid attempt to spread non-iterable instance")}function r(t){if(Symbol.iterator in Object(t)||"[object Arguments]"===Object.prototype.toString.call(t))return Array.from(t)}function d(t){if(Array.isArray(t)){for(var a=0,n=new Array(t.length);a<t.length;a++)n[a]=t[a];return n}}var l={components:{},data:function(){return{city_name:"",city_id:"",city_lat:"",city_lng:"",isCurentAdres:"",skip_addressData:null,LocaTion_addressData:null,isPage:!1,searchText:"",navgateTourl:""}},computed:{addressData:function(){if(this.LocaTion_addressData||this.skip_addressData)return this.LocaTion_addressData&&this.LocaTion_addressData["ad_info"]["city_code"]===this.skip_addressData["ad_info"]["city_code"]?this.LocaTion_addressData:this.skip_addressData}},onLoad:function(t){uni.showLoading({title:"定位中..."});var a=this,n=t.lat,i=t.lng,o=t.city,s=t.id,c=t.isCurrentAddress,r=t.url,d=t.loca,l=n||"",u=i||"",f=o||t.name,v=s||"",g=c||"0",p=r||"";a.setData({city_lat:l,city_lng:u,city_name:f,city_id:v,isCurentAdres:g,navgateTourl:p}),"now"===d?e.default.wxRegister(a.againCurrentlat):a.getCurrentlat(l,u,v)},methods:{wxApiCallback:function(){var t=this;console.log(jWeixin),jWeixin.ready(function(){jWeixin.getLocation({type:"gcj02",success:function(a){t.getCurrentcity(a.latitude,a.longitude)}})})},getCurrentlat:function(t,a,n){var o=this,s={};s=t&&a?{lat:t,lng:a}:{citycode:n},i.default._post_form("&do=cityLocation",s,function(t){o.setData({skip_addressData:t.data,isPage:!0}),e.default.wxRegister(o.wxApiCallback)},!1,function(){uni.hideLoading()})},againCurrentlat:function(){var t=this;uni.showLoading({title:"定位中..."}),jWeixin.ready(function(){jWeixin.getLocation({type:"gcj02",success:function(a){t.getCurrentcity(a.latitude,a.longitude,"again")}})})},getCurrentcity:function(t,a){var n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"",e=this,o={lat:t,lng:a};i.default._post_form("&do=cityLocation",o,function(t){var a=t.data;if(e.setData({LocaTion_addressData:a}),"again"===n){var o=e.navgateTourl,s=encodeURIComponent(o),c="".concat(i.default.siteInfo.siteroot,"/app/index.php?i=").concat(i.default.siteInfo.uniacid,"&c=entry&m=weliam_merchant&p=area&ac=region&do=get_location&lat=").concat(a.location.lat,"&lng=").concat(a.location.lng,"&title=").concat(a.formatted_addresses.recommend,"&adcode=").concat(a.ad_info.adcode,"&url=").concat(s||"");location.href=c}})},citySearch:function(t){var a=this;""!==t&&i.default._post_form("&do=citySearch",{city_name:a.addressData["ad_info"]["city"],keyword:t},function(t){a.addressData["pois"]=t.data},!1,function(){})},navBack:function(){i.default.rediRectTo({url:"pages/mainPages/city/city"})},navGate:function(t){var a=this.navgateTourl,n=encodeURIComponent(a),e="".concat(i.default.siteInfo.siteroot,"/app/index.php?i=").concat(i.default.siteInfo.uniacid,"&c=entry&m=weliam_merchant&p=area&ac=region&do=get_location&lat=").concat(t.location.lat,"&lng=").concat(t.location.lng,"&title=").concat(t.title,"&name=").concat(t.ad_info.city,"&adcode=").concat(t.ad_info.adcode,"&url=").concat(n||"");location.href=e;var o=uni.getStorageSync("locationArray"),c=[];if(o){var r=0!==o.filter(function(a){return a.ad_info.city===t.ad_info.city}).length;if(r)return;c.push.apply(c,[t].concat(s(o))),uni.setStorageSync("locationArray",c)}else c.push(t),uni.setStorageSync("locationArray",c)}},watch:{searchText:function(t,a){this.citySearch(t)}}};a.default=l},"8cfb":function(t,a,n){a=t.exports=n("2350")(!1),a.push([t.i,'@charset "UTF-8";\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */.history-main[data-v-065194be]{height:%?120?%}.history-main .city[data-v-065194be]{width:%?170?%;margin-right:%?26?%}.history-main .history-input[data-v-065194be]{-webkit-box-flex:1;-webkit-flex:1;-ms-flex:1;flex:1;background:#f6f6f6;border-radius:%?38?%;height:%?80?%}.history-main .history-input .history-icon[data-v-065194be]{font-size:%?30?%;margin-right:%?20?%;color:#959595}.history-main .history-btn[data-v-065194be]{margin:0 %?12?%}.history-main .select-up-icon[data-v-065194be]{font-size:%?24?%}.current-address-main[data-v-065194be]{border-bottom:1px solid #f0f0f0;padding:%?40?% %?30?%}.current-address-main .again-lot .again-icon[data-v-065194be]{margin-right:%?8?%;color:#fe504f;font-size:%?28?%}.current-address-main .again-lot .again-lot-title[data-v-065194be]{color:#fe504f}.nearby-address[data-v-065194be]{padding:%?40?% 0;border-bottom:1px solid #f0f0f0}.nearby-address .nearby-icon[data-v-065194be]{color:#333;font-size:%?28?%;margin-right:%?14?%}.address-list .list-item[data-v-065194be]{padding:%?40?% 0;margin:0 %?68?%;border-bottom:1px solid #f0f0f0}',""])},"9fde":function(t,a,n){"use strict";var i=n("a9f9"),e=n.n(i);e.a},a9f9:function(t,a,n){var i=n("8cfb");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var e=n("4f06").default;e("ba37fcf4",i,!0,{sourceMap:!1,shadowMode:!1})},bdc7:function(t,a,n){"use strict";var i=function(){var t=this,a=t.$createElement,n=t._self._c||a;return t.isPage?n("v-uni-view",{staticClass:"content"},[n("v-uni-view",{staticClass:"history-main dis-flex flex-y-center p-left-right-30"},[n("v-uni-view",{staticClass:"city onelist-hidden dis-flex flex-y-center",on:{click:function(a){a=t.$handleEvent(a),t.navBack(a)}}},[n("v-uni-view",{staticClass:"location-icon iconfont icon-locationfill"}),t.addressData?n("v-uni-view",{staticClass:"history-btn col-3 f-28"},[t._v(t._s(t.addressData["ad_info"]["city"]))]):t._e(),n("v-uni-view",{staticClass:"select-up-icon iconfont icon-unfold"})],1),n("v-uni-view",{staticClass:"history-input dis-flex flex-y-center p-left-right-30 p-r"},[n("v-uni-view",{staticClass:"history-icon iconfont icon-search"}),n("v-uni-input",{staticClass:"f-24",attrs:{type:"text",value:"",placeholder:"请输入当前位置"},model:{value:t.searchText,callback:function(a){t.searchText=a},expression:"searchText"}})],1)],1),n("v-uni-view",{staticClass:"current-address-main dis-flex flex-x-between"},[t.addressData?n("v-uni-view",{staticClass:"current-address f-28 col-3"},[t._v(t._s(t.addressData["formatted_addresses"]["recommend"]))]):t._e(),n("v-uni-view",{staticClass:"again-lot dis-flex"},[n("v-uni-view",{staticClass:"again-icon iconfont icon-focus"}),n("v-uni-view",{staticClass:"again-lot-title f-28",on:{click:function(a){a=t.$handleEvent(a),t.againCurrentlat(a)}}},[t._v("重新定位")])],1)],1),n("v-uni-view",{staticClass:"nearby-main"},[n("v-uni-view",{staticClass:"nearby-address dis-flex flex-y-center m-left-right-30"},[n("v-uni-view",{staticClass:"nearby-icon iconfont icon-dibiao"}),n("v-uni-view",{staticClass:"f-28 col-9"},[t._v("附近地址")])],1),n("v-uni-view",{staticClass:"address-list"},t._l(t.addressData["pois"],function(a,i){return t.addressData&&t.addressData["pois"]["length"]>0?n("v-uni-view",{key:i,staticClass:"list-item",on:{click:function(n){n=t.$handleEvent(n),t.navGate(a)}}},[n("v-uni-view",{staticClass:"list-title col-3 f-30 m-btm20"},[t._v(t._s(a.title))]),n("v-uni-view",{staticClass:"list-title col-9 f-24"},[t._v(t._s(a.address))])],1):t._e()}),1)],1)],1):t._e()},e=[];n.d(a,"a",function(){return i}),n.d(a,"b",function(){return e})},c09c:function(t,a,n){"use strict";n.r(a);var i=n("bdc7"),e=n("c75c");for(var o in e)"default"!==o&&function(t){n.d(a,t,function(){return e[t]})}(o);n("9fde");var s=n("2877"),c=Object(s["a"])(e["default"],i["a"],i["b"],!1,null,"065194be",null);a["default"]=c.exports},c75c:function(t,a,n){"use strict";n.r(a);var i=n("18ba"),e=n.n(i);for(var o in i)"default"!==o&&function(t){n.d(a,t,function(){return i[t]})}(o);a["default"]=e.a}}]);