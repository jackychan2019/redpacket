(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-mainPages-headline-message"],{"35cc":function(a,n,t){n=a.exports=t("2350")(!1),n.push([a.i,"uni-page-body[data-v-731a11a4]{background-color:#f3f3f3!important}.container[data-v-731a11a4]{background-color:#f3f3f3}.page-message[data-v-731a11a4]{background-color:#f3f3f3}.msg-h[data-v-731a11a4]{line-height:%?48?%;font-size:%?32?%;padding:%?30?%}.msg-c[data-v-731a11a4]{background-color:#fff}.msg-c uni-textarea[data-v-731a11a4]{width:calc(100vw - %?80?%);min-height:%?160?%;line-height:%?40?%;margin:0;padding:%?40?%;font-size:%?28?%;resize:none;border:none;display:block}.msg-b[data-v-731a11a4]{height:%?90?%;line-height:%?90?%;font-size:%?32?%;text-align:center;padding:%?30?%}.msg-b .span[data-v-731a11a4]{background-color:#ffd93f;border-radius:%?100?%;display:block}body.?%PAGE?%[data-v-731a11a4]{background-color:#f3f3f3!important}",""])},"67a4":function(a,n,t){"use strict";var e=function(){var a=this,n=a.$createElement,t=a._self._c||n;return t("v-uni-view",{staticClass:"container"},[t("v-uni-view",{staticClass:"page-message"},[t("v-uni-view",{staticClass:"msg-h"},[a._v(a._s(a.param.title))]),t("v-uni-view",{staticClass:"msg-c"},[t("v-uni-textarea",{attrs:{name:"msg",focus:"ture","auto-height":"","cursor-spacing":"80",placeholder:"请在这里输入您的留言"},model:{value:a.content,callback:function(n){a.content=n},expression:"content"}})],1),t("v-uni-view",{staticClass:"msg-b"},[t("v-uni-view",{staticClass:"span",on:{click:function(n){n=a.$handleEvent(n),a.comment()}}},[a._v("留言")])],1)],1)],1)},i=[];t.d(n,"a",function(){return e}),t.d(n,"b",function(){return i})},"90aa":function(a,n,t){var e=t("35cc");"string"===typeof e&&(e=[[a.i,e,""]]),e.locals&&(a.exports=e.locals);var i=t("4f06").default;i("9a917758",e,!0,{sourceMap:!1,shadowMode:!1})},b595:function(a,n,t){"use strict";t.r(n);var e=t("df8c"),i=t.n(e);for(var o in e)"default"!==o&&function(a){t.d(n,a,function(){return e[a]})}(o);n["default"]=i.a},b912:function(a,n,t){"use strict";var e=t("90aa"),i=t.n(e);i.a},d703:function(a,n,t){"use strict";t.r(n);var e=t("67a4"),i=t("b595");for(var o in i)"default"!==o&&function(a){t.d(n,a,function(){return i[a]})}(o);t("b912");var c=t("2877"),s=Object(c["a"])(i["default"],e["a"],e["b"],!1,null,"731a11a4",null);n["default"]=s.exports},df8c:function(a,n,t){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var e=i(t("3099"));function i(a){return a&&a.__esModule?a:{default:a}}var o={data:function(){return{loading:!1,mid:"",token:"",param:{headline_id:"",title:""},content:""}},computed:{},onLoad:function(a){this.param={headline_id:a.headline_id,title:a.title}},mounted:function(){},methods:{comment:function(){var a=this;if(0==a.content.replace(/\s/g,"").length)return uni.showToast({title:"请输入留言",icon:"none",duration:1e3,mask:!0}),!1;uni.showLoading({});var n={hid:a["param"]["headline_id"],text:a.content};e.default._post_form("&p=headline&do=HeadlineComment",n,function(a){0===a.errno&&(uni.hideLoading(),e.default.showSuccess("留言成功",function(){uni.navigateBack({delta:1})}))},!1,function(){})}}};n.default=o}}]);