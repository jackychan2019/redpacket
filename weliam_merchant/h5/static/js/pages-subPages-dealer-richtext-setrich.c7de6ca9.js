(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-subPages-dealer-richtext-setrich"],{"009e":function(t,n,o){n=t.exports=o("2350")(!1),n.push([t.i,".rich-content[data-v-532f93e8]{padding:%?20?% %?30?%}",""])},"420e":function(t,n,o){"use strict";o.r(n);var e=o("baa4"),a=o("521f");for(var u in a)"default"!==u&&function(t){o.d(n,t,function(){return a[t]})}(u);o("c225");var r=o("2877"),c=Object(r["a"])(a["default"],e["a"],e["b"],!1,null,"532f93e8",null);n["default"]=c.exports},"521f":function(t,n,o){"use strict";o.r(n);var e=o("5b3e"),a=o.n(e);for(var u in e)"default"!==u&&function(t){o.d(n,t,function(){return e[t]})}(u);n["default"]=a.a},"5b3e":function(t,n,o){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var e=r(o("3099")),a=(r(o("1210")),r(o("65c3"))),u=r(o("9976"));function r(t){return t&&t.__esModule?t:{default:t}}var c={data:function(){return{content:"",loadlogo:!1}},components:{wxParse:a.default,Loadlogo:u.default},computed:{},beforeCreate:function(){},onLoad:function(t){},onUnload:function(){},mounted:function(){uni.setNavigationBarColor({frontColor:"#000000",backgroundColor:"#FFFFFF"}),this.getRich()},methods:{getRich:function(){var t=this;e.default._post_form("&p=distribution&do=getHelpNote",{},function(n){t.content=n.data},!1,function(){t.loadlogo=!0})}}};n.default=c},baa4:function(t,n,o){"use strict";var e=function(){var t=this,n=t.$createElement,o=t._self._c||n;return o("v-uni-view",[t.loadlogo?t._e():o("loadlogo"),t.loadlogo?o("v-uni-view",{staticClass:"rich-content b-f"},[o("wx-parse",{attrs:{content:t.content}})],1):t._e()],1)},a=[];o.d(n,"a",function(){return e}),o.d(n,"b",function(){return a})},c225:function(t,n,o){"use strict";var e=o("c6a4"),a=o.n(e);a.a},c6a4:function(t,n,o){var e=o("009e");"string"===typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);var a=o("4f06").default;a("50aef98e",e,!0,{sourceMap:!1,shadowMode:!1})}}]);