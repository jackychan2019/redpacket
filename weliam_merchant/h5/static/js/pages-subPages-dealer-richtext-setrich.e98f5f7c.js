(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-subPages-dealer-richtext-setrich"],{"042d":function(t,n,o){"use strict";o.r(n);var e=o("3c0e"),a=o("2cdb");for(var c in a)"default"!==c&&function(t){o.d(n,t,function(){return a[t]})}(c);o("a5c6");var u=o("2877"),r=Object(u["a"])(a["default"],e["a"],e["b"],!1,null,"d1cefc74",null);n["default"]=r.exports},"2cdb":function(t,n,o){"use strict";o.r(n);var e=o("552f"),a=o.n(e);for(var c in e)"default"!==c&&function(t){o.d(n,t,function(){return e[t]})}(c);n["default"]=a.a},"3c0e":function(t,n,o){"use strict";var e=function(){var t=this,n=t.$createElement,o=t._self._c||n;return o("v-uni-view",[t.loadlogo?t._e():o("loadlogo"),t.loadlogo?o("v-uni-view",{staticClass:"rich-content b-f"},[o("wx-parse",{attrs:{content:t.content}})],1):t._e()],1)},a=[];o.d(n,"a",function(){return e}),o.d(n,"b",function(){return a})},"552f":function(t,n,o){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var e=u(o("5f2a")),a=(u(o("d1a8")),u(o("cbb2"))),c=u(o("384d"));function u(t){return t&&t.__esModule?t:{default:t}}var r={data:function(){return{content:"",loadlogo:!1}},components:{wxParse:a.default,Loadlogo:c.default},computed:{},beforeCreate:function(){},onLoad:function(t){},onUnload:function(){},mounted:function(){uni.setNavigationBarColor({frontColor:"#000000",backgroundColor:"#FFFFFF"}),this.getRich()},methods:{getRich:function(){var t=this;e.default._post_form("&p=distribution&do=getHelpNote",{},function(n){t.content=n.data},!1,function(){t.loadlogo=!0})}}};n.default=r},"6ca5":function(t,n,o){var e=o("9389");"string"===typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);var a=o("4f06").default;a("75e7c6dc",e,!0,{sourceMap:!1,shadowMode:!1})},9389:function(t,n,o){n=t.exports=o("2350")(!1),n.push([t.i,".rich-content[data-v-d1cefc74]{padding:%?20?% %?30?%}",""])},a5c6:function(t,n,o){"use strict";var e=o("6ca5"),a=o.n(e);a.a}}]);