(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-mainPages-callpge-index"],{"0679":function(a,t,e){"use strict";e.r(t);var i=e("dde3"),d=e("0a10");for(var s in d)"default"!==s&&function(a){e.d(t,a,function(){return d[a]})}(s);e("78a3");var o=e("2877"),n=Object(o["a"])(d["default"],i["a"],i["b"],!1,null,"ec257740",null);t["default"]=n.exports},"0a10":function(a,t,e){"use strict";e.r(t);var i=e("b602"),d=e.n(i);for(var s in i)"default"!==s&&function(a){e.d(t,a,function(){return i[a]})}(s);t["default"]=d.a},"6abe":function(a,t,e){var i=e("e99f");"string"===typeof i&&(i=[[a.i,i,""]]),i.locals&&(a.exports=i.locals);var d=e("4f06").default;d("40010070",i,!0,{sourceMap:!1,shadowMode:!1})},"78a3":function(a,t,e){"use strict";var i=e("6abe"),d=e.n(i);d.a},b602:function(a,t,e){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var i=e("2f62"),d=e("ebd2"),s=g(e("5f2a")),o=g(e("384d")),n=g(e("ef5a")),r=g(e("b8e4")),l=g(e("4d3d")),p=g(e("cbb2"));function g(a){return a&&a.__esModule?a:{default:a}}function c(a){for(var t=1;t<arguments.length;t++){var e=null!=arguments[t]?arguments[t]:{},i=Object.keys(e);"function"===typeof Object.getOwnPropertySymbols&&(i=i.concat(Object.getOwnPropertySymbols(e).filter(function(a){return Object.getOwnPropertyDescriptor(e,a).enumerable}))),i.forEach(function(t){f(a,t,e[t])})}return a}function f(a,t,e){return t in a?Object.defineProperty(a,t,{value:e,enumerable:!0,configurable:!0,writable:!0}):a[t]=e,a}var u={data:function(){return{loadlogo:!1,swiperI:1,loading:!1,loadAgain:!1,datas:{basic:[],adv:"",page:{}},diypagesData:[],richtext:{imageProp:{mode:"widthFix"}},btngroup:{backgroundColor:"#ffffff",btnBordeRadius:"",displayMode:"",eachLineNum:"",eachPageNum:"",list:[],indicatorDots:!0,duration:300,btngroupHandleData:[],swiperHeight:""}}},components:{Loadlogo:o.default,diypages:n.default,Advert:r.default,TabBars:l.default,wxParse:p.default},computed:c({},(0,i.mapState)(["ruzhushow","kefushow"])),onShow:function(){},mounted:function(){var a=this;a.getPagesData()},methods:c({},(0,i.mapMutations)([d.RUZHU,d.KEFU]),{ruzhuToggle:function(){this[d.RUZHU](!0)},kefuToggle:function(){this[d.KEFU](!0)},getPagesData:function(){var a=this;s.default._post_form("HomePage",{},function(t){if(console.log(t),0===t.errno){a.datas.adv=t.data.adv||"",a.datas.page=t.data.page||"",uni.setNavigationBarTitle({title:null==a.datas.page.title?"暂无标题":a.datas.page.title});var e=t.data.item,i=[];for(var d in e)i.push(e[d]);for(var s in console.log(i),a.diypagesData=i,a.diypagesData){if("menu"==a.diypagesData[s].id){a.diypagesData[s].btngroupData={};var o={list:[],indicatorDots:!0,duration:300,btngroupHandleData:[],swiperHeight:""};for(var n in o.backgroundColor=a.diypagesData[s].style.background,o.btnBordeRadius=a.diypagesData[s].style.navstyle,o.displayMode=a.diypagesData[s].style.showtype,o.eachLineNum=a.diypagesData[s].style.rownum,o.eachPageNum=a.diypagesData[s].style.pagenum,a.diypagesData[s].data)o.list.push(a.diypagesData[s].data[n]);0==a.diypagesData[s].style.showdot?o.indicatorDots=!1:o.indicatorDots=!0;var r="";if(r=o.list.length>o.eachPageNum?o.eachPageNum:o.list.length,o.swiperHeight=87*Math.ceil(r/o.eachLineNum)+15,o.list&&o.list.length>0)for(var l=Math.ceil(o.list.length/o.eachPageNum),p=0;p<l;p++){for(var g=[],c=p*o.eachPageNum,f=c;f<o.eachPageNum*(p+1)&&f<o.list.length;f++)g.push(o.list[f]);o.btngroupHandleData.push(g)}a.diypagesData[s].btngroupData=o}if("picturew4"==a.diypagesData[s].id){var u=[];for(var h in a.diypagesData[s].data)u.push(a.diypagesData[s].data[h]);a.diypagesData[s].data=u}if("picturew5"==a.diypagesData[s].id){var x=[];for(var y in a.diypagesData[s].data)x.push(a.diypagesData[s].data[y]);a.diypagesData[s].data=x}if("pictures"==a.diypagesData[s].id){var v=[];for(var b in a.diypagesData[s].data)v.push(a.diypagesData[s].data[b]);a.diypagesData[s].data=v}if("shop"==a.diypagesData[s].id){var w=[];for(var m in a.diypagesData[s].data){var D=[];for(var k in a.diypagesData[s].data[m].goods)""!==a.diypagesData[s].data[m].goods[k]&&("active"==k?(a.diypagesData[s].data[m].goods[k].css="qiang",a.diypagesData[s].data[m].goods[k].tag="抢"):"coupon"==k?(a.diypagesData[s].data[m].goods[k].css="hui",a.diypagesData[s].data[m].goods[k].tag="券"):"fightgroup"==k?(a.diypagesData[s].data[m].goods[k].css="pin",a.diypagesData[s].data[m].goods[k].tag="拼"):"groupon"==k?(a.diypagesData[s].data[m].goods[k].css="tuan",a.diypagesData[s].data[m].goods[k].tag="团"):"halfcard"==k?(a.diypagesData[s].data[m].goods[k].css="ka",a.diypagesData[s].data[m].goods[k].tag="卡"):"packages"==k&&(a.diypagesData[s].data[m].goods[k].css="li",a.diypagesData[s].data[m].goods[k].tag="礼"),D.push(a.diypagesData[s].data[m].goods[k]));a.diypagesData[s].data[m].goods=D,w.push(a.diypagesData[s].data[m])}a.diypagesData[s].shopArr=w}}a.datas.basic=a.diypagesData}},!1,function(){a.loadAgain=!0,a.loadlogo=!0})}}),onPullDownRefresh:function(){uni.stopPullDownRefresh()},onReachBottom:function(){var a=this;a.loading=!0,setTimeout(function(){a.loading=!1},4e3)}};t.default=u},dde3:function(a,t,e){"use strict";var i=function(){var a=this,t=a.$createElement,e=a._self._c||t;return e("v-uni-view",{staticClass:"container"},[a.loadlogo?a._e():e("loadlogo"),a.loadlogo?e("v-uni-view",{staticClass:"page-index",style:"background-color: "+a.datas.page.background},[""!==a.datas.adv&&null!==a.datas.adv?e("Advert",{staticClass:"advert",attrs:{advertData:a.datas.adv}}):a._e(),e("diypages",{attrs:{diypagesData:a.diypagesData,pageData:a.datas.page,btngroupData:a.btngroup}})],1):a._e()],1)},d=[];e.d(t,"a",function(){return i}),e.d(t,"b",function(){return d})},e99f:function(a,t,e){t=a.exports=e("2350")(!1),t.push([a.i,".container[data-v-ec257740]{background-color:#ffd93f}.page-index[data-v-ec257740]{width:100vw;overflow:hidden;background-color:#fff;position:relative}.index-right-bar[data-v-ec257740]{width:80px;height:80px;padding:16px;position:fixed;right:30px;bottom:60px;z-index:99}.right-bar-i[data-v-ec257740]{width:100%;height:100%;padding:6px}.right-bar-i-t[data-v-ec257740]{height:40px;text-align:center;margin-top:0;padding-top:6px}.right-bar-i-t .i[data-v-ec257740]{width:44px;height:44px;display:inline-block}.right-bar-i-t .i .image[data-v-ec257740]{width:100%;height:100%;display:block}.right-bar-i-c[data-v-ec257740]{height:32px;line-height:32px;font-size:20px;margin-top:0}.index-h[data-v-ec257740]{height:80px;line-height:80px;padding:10px 30px 20px 30px;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-webkit-justify-content:space-between;-ms-flex-pack:justify;justify-content:space-between;background-color:#ffd93f;position:relative}.index-h-l[data-v-ec257740]{color:#000;font-size:36px;font-style:italic;font-family:SimHei}.index-h-r-d[data-v-ec257740]{width:180px;height:56px;line-height:56px;color:grey;font-size:28px;text-align:center;margin-top:12px;background-color:#fff0b2;-webkit-border-radius:40px;border-radius:40px}.index-h-r-d .i[data-v-ec257740]{color:#333;font-size:32px;margin-right:10px;vertical-align:-2px}@-webkit-keyframes move_wave-data-v-ec257740{0%{-webkit-transform:translateX(0) translateZ(0) scaleY(1);transform:translateX(0) translateZ(0) scaleY(1)}to{-webkit-transform:translateX(-66.66666%) translateZ(0) scaleY(1);transform:translateX(-66.66666%) translateZ(0) scaleY(1)}}@keyframes move_wave-data-v-ec257740{0%{-webkit-transform:translateX(0) translateZ(0) scaleY(1);transform:translateX(0) translateZ(0) scaleY(1)}to{-webkit-transform:translateX(-66.66666%) translateZ(0) scaleY(1);transform:translateX(-66.66666%) translateZ(0) scaleY(1)}}.index-wave[data-v-ec257740]{width:100%;height:60px;position:absolute;top:-72px;left:0;overflow:hidden}.index-wave-d[data-v-ec257740]{width:300%;height:100%;-webkit-animation:move_wave-data-v-ec257740 4s linear infinite;animation:move_wave-data-v-ec257740 4s linear infinite;-webkit-animation-delay:0s;animation-delay:0s;display:-webkit-flex;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:start;-webkit-justify-content:flex-start;-ms-flex-pack:start;justify-content:flex-start}.index-wave-i[data-v-ec257740]{width:100%;height:100%;position:relative}.index-wave-i .image[data-v-ec257740]{width:100%;height:100%}.index-t1[data-v-ec257740]{margin-top:40px;padding:0 30px;display:-webkit-flex;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-webkit-justify-content:space-between;-ms-flex-pack:justify;justify-content:space-between;position:relative;z-index:1}.index-t1-l[data-v-ec257740]{padding-right:30px;-webkit-box-flex:1;-webkit-flex:1;-ms-flex:1;flex:1;display:-webkit-flex;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-justify-content:space-around;-ms-flex-pack:distribute;justify-content:space-around}.index-t1-l-i[data-v-ec257740]{width:140px}.index-t1-l-i-t[data-v-ec257740]{width:100px;height:100px;padding:0 20px}.index-t1-l-i-t .image[data-v-ec257740]{width:100px;height:100px}.index-t1-l-i-b[data-v-ec257740]{height:42px;line-height:42px;text-align:center;overflow:hidden;-o-text-overflow:ellipsis;text-overflow:ellipsis;white-space:nowrap}.index-t1-r[data-v-ec257740]{width:140px;border-left:1.2px solid #f6f6f6}.index-t1-r .image[data-v-ec257740]{width:110px;height:110px;margin:10px 0 0 30px}",""])}}]);