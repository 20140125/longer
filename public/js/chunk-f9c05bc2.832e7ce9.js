(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-f9c05bc2"],{"0cb2":function(e,t,n){var r=n("7b0b"),o=Math.floor,a="".replace,c=/\$([$&'`]|\d{1,2}|<[^>]*>)/g,i=/\$([$&'`]|\d{1,2})/g;e.exports=function(e,t,n,s,u,l){var d=n+e.length,f=s.length,p=i;return void 0!==u&&(u=r(u),p=c),a.call(l,p,(function(r,a){var c;switch(a.charAt(0)){case"$":return"$";case"&":return e;case"`":return t.slice(0,n);case"'":return t.slice(d);case"<":c=u[a.slice(1,-1)];break;default:var i=+a;if(0===i)return r;if(i>f){var l=o(i/10);return 0===l?r:l<=f?void 0===s[l-1]?a.charAt(1):s[l-1]+a.charAt(1):r}c=s[i-1]}return void 0===c?"":c}))}},"23c4":function(e,t,n){"use strict";n("81a6")},5319:function(e,t,n){"use strict";var r=n("d784"),o=n("d039"),a=n("825a"),c=n("50c4"),i=n("a691"),s=n("1d80"),u=n("8aa5"),l=n("0cb2"),d=n("14c3"),f=n("b622"),p=f("replace"),g=Math.max,h=Math.min,m=function(e){return void 0===e?e:String(e)},b=function(){return"$0"==="a".replace(/./,"$0")}(),v=function(){return!!/./[p]&&""===/./[p]("a","$0")}(),w=!o((function(){var e=/./;return e.exec=function(){var e=[];return e.groups={a:"7"},e},"7"!=="".replace(e,"$<a>")}));r("replace",(function(e,t,n){var r=v?"$":"$0";return[function(e,n){var r=s(this),o=void 0==e?void 0:e[p];return void 0!==o?o.call(e,r,n):t.call(String(r),e,n)},function(e,o){if("string"===typeof o&&-1===o.indexOf(r)&&-1===o.indexOf("$<")){var s=n(t,this,e,o);if(s.done)return s.value}var f=a(this),p=String(e),b="function"===typeof o;b||(o=String(o));var v=f.global;if(v){var w=f.unicode;f.lastIndex=0}var O=[];while(1){var y=d(f,p);if(null===y)break;if(O.push(y),!v)break;var j=String(y[0]);""===j&&(f.lastIndex=u(p,c(f.lastIndex),w))}for(var k="",x=0,S=0;S<O.length;S++){y=O[S];for(var $=String(y[0]),C=g(h(i(y.index),p.length),0),N=[],L=1;L<y.length;L++)N.push(m(y[L]));var V=y.groups;if(b){var K=[$].concat(N,C,p);void 0!==V&&K.push(V);var M=String(o.apply(void 0,K))}else M=l($,p,C,N,V,o);C>=x&&(k+=p.slice(x,C)+M,x=C+$.length)}return k+p.slice(x)}]}),!w||!b||v)},"81a6":function(e,t,n){},"99af":function(e,t,n){"use strict";var r=n("23e7"),o=n("d039"),a=n("e8b5"),c=n("861d"),i=n("7b0b"),s=n("50c4"),u=n("8418"),l=n("65f0"),d=n("1dde"),f=n("b622"),p=n("2d00"),g=f("isConcatSpreadable"),h=9007199254740991,m="Maximum allowed index exceeded",b=p>=51||!o((function(){var e=[];return e[g]=!1,e.concat()[0]!==e})),v=d("concat"),w=function(e){if(!c(e))return!1;var t=e[g];return void 0!==t?!!t:a(e)},O=!b||!v;r({target:"Array",proto:!0,forced:O},{concat:function(e){var t,n,r,o,a,c=i(this),d=l(c,0),f=0;for(t=-1,r=arguments.length;t<r;t++)if(a=-1===t?c:arguments[t],w(a)){if(o=s(a.length),f+o>h)throw TypeError(m);for(n=0;n<o;n++,f++)n in a&&u(d,f,a[n])}else{if(f>=h)throw TypeError(m);u(d,f++,a)}return d.length=f,d}})},dda8:function(e,t,n){"use strict";n.r(t);n("b0c0");var r=n("7a23"),o={class:"grid",style:{padding:"0 5px !important"}},a={class:"input-group"},c=Object(r["createTextVNode"])("搜索"),i={key:0,class:"keywords"},s={class:"history-keywords"},u={class:"icon"},l=Object(r["createVNode"])("span",null,"搜索历史",-1),d={class:"hot-keywords"},f={class:"icon"},p=Object(r["createVNode"])("span",null,"热门搜索",-1);function g(e,t,n,g,h,m){var b=Object(r["resolveComponent"])("el-button"),v=Object(r["resolveComponent"])("el-input"),w=Object(r["resolveComponent"])("el-tag"),O=Object(r["resolveComponent"])("Lists"),y=Object(r["resolveComponent"])("HomeLayout");return Object(r["openBlock"])(),Object(r["createBlock"])(y,{"header-title":"魔盒逗图 -- 热搜"},{body:Object(r["withCtx"])((function(){return[Object(r["createVNode"])("div",o,[Object(r["createVNode"])("div",a,[Object(r["createVNode"])(v,{modelValue:h.pagination.name,"onUpdate:modelValue":t[2]||(t[2]=function(e){return h.pagination.name=e}),placeholder:"请输入"},{append:Object(r["withCtx"])((function(){return[Object(r["createVNode"])(b,{type:"primary",onClick:t[1]||(t[1]=function(e){return m.doSearch(h.pagination.name)})},{default:Object(r["withCtx"])((function(){return[c]})),_:1})]})),_:1},8,["modelValue"])]),0===h.loadMore.length?(Object(r["openBlock"])(),Object(r["createBlock"])("div",i,[Object(r["createVNode"])("div",s,[Object(r["createVNode"])("div",u,[l,Object(r["createVNode"])("i",{class:"el-icon-delete",onClick:t[3]||(t[3]=function(){return m.oldDelete&&m.oldDelete.apply(m,arguments)})})]),(Object(r["openBlock"])(!0),Object(r["createBlock"])(r["Fragment"],null,Object(r["renderList"])(h.oldKeywords,(function(e,t){return Object(r["openBlock"])(),Object(r["createBlock"])(w,{key:t,effect:"dark",type:"info",onClick:function(t){return m.doSearch(e)}},{default:Object(r["withCtx"])((function(){return[Object(r["createTextVNode"])(Object(r["toDisplayString"])(e),1)]})),_:2},1032,["onClick"])})),128))]),Object(r["createVNode"])("div",d,[Object(r["createVNode"])("div",f,[p,Object(r["createVNode"])("i",{class:h.showHotWords?"el-icon-open":"el-icon-turn-off",onClick:t[4]||(t[4]=function(e){return h.showHotWords=!h.showHotWords})},null,2)]),h.showHotWords?(Object(r["openBlock"])(!0),Object(r["createBlock"])(r["Fragment"],{key:0},Object(r["renderList"])(m.hotKeyWord,(function(e,t){return Object(r["openBlock"])(),Object(r["createBlock"])(w,{key:t,effect:"dark",onClick:function(t){return m.doSearch(e)}},{default:Object(r["withCtx"])((function(){return[Object(r["createTextVNode"])(Object(r["toDisplayString"])(e),1)]})),_:2},1032,["onClick"])})),128)):Object(r["createCommentVNode"])("",!0)])])):Object(r["createCommentVNode"])("",!0),h.loadMore.length>0?(Object(r["openBlock"])(),Object(r["createBlock"])(O,{key:1,"load-more":h.loadMore,style:{padding:"50px 0 80px 0"}},null,8,["load-more"])):Object(r["createCommentVNode"])("",!0)])]})),_:1})}var h=n("1da1"),m=(n("96cf"),n("ac1f"),n("841c"),n("159b"),n("5319"),n("a434"),n("99af"),n("eb00")),b=n("0d9f"),v={name:"Search",components:{Lists:b["a"],HomeLayout:m["a"]},data:function(){return{showHotWords:!0,oldKeywords:[],loadMore:this.$store.state.index.imageLists.search.lists,pagination:{page:1,limit:20,source:"h5",name:this.$store.state.index.imageLists.search.searchKeys,type:"search"}}},computed:{hotKeyWord:function(){return this.$store.state.index.configuration.hotKeyWord},imageLists:function(){var e=this,t=JSON.parse(JSON.stringify(this.$store.state.index.imageLists.search.lists));return t.forEach((function(t){t.name=t.name.replace(e.pagination.name,'<span style="color:#409EFF">'+e.pagination.name+"</span>")})),t},total:function(){return this.$store.state.index.imageLists.search.total}},beforeUnmount:function(){window.removeEventListener("scroll",this.handleScroll)},created:function(){window.addEventListener("scroll",this.handleScroll)},mounted:function(){var e=this;this.$nextTick(Object(h["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e.oldKeywords=JSON.parse(window.localStorage.getItem("OldKeys")||"[]"),t.next=3,e.getConfiguration();case 3:case"end":return t.stop()}}),t)}))))},methods:{getConfiguration:function(){var e=this;return Object(h["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,e.$store.dispatch("index/getConfiguration",{keywords:"HotKeyWord",type:"hotKeyWord"});case 2:case"end":return t.stop()}}),t)})))()},oldDelete:function(){var e=this;return Object(h["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,e.$confirm("确定清除历史搜索记录？","删除记录",{type:"success",showClose:!1}).then((function(){window.localStorage.removeItem("OldKeys"),e.oldKeywords=[],e.setMessage("删除历史记录成功","success",!1)})).catch((function(){e.setMessage("已取消删除","success",!1)}));case 2:case"end":return t.stop()}}),t)})))()},setMessage:function(e,t){var n=this,r=!(arguments.length>2&&void 0!==arguments[2])||arguments[2],o=this.$loading({lock:!0,spinner:"el-icon-loading",background:"rgba(225, 225, 224, 0.8)"});setTimeout(Object(h["a"])(regeneratorRuntime.mark((function r(){return regeneratorRuntime.wrap((function(r){while(1)switch(r.prev=r.next){case 0:return o.close(),r.next=3,n.$message({message:e,type:t,offset:350,dangerouslyUseHTMLString:!0,duration:1e3});case 3:case"end":return r.stop()}}),r)}))),r?500:0)},doSearch:function(e){var t=this;return Object(h["a"])(regeneratorRuntime.mark((function n(){var r;return regeneratorRuntime.wrap((function(n){while(1)switch(n.prev=n.next){case 0:if(e){n.next=4;break}return t.setMessage("请输入搜索词","success"),setTimeout((function(){t.pagination.name=t.$store.state.index.imageLists.search.searchKeys}),500),n.abrupt("return",!1);case 4:return r=t.oldKeywords.indexOf(e),-1===r||t.oldKeywords.splice(r,1),t.oldKeywords.unshift(e),t.oldKeywords.length>20&&t.oldKeywords.pop(),window.localStorage.setItem("OldKeys",JSON.stringify(t.oldKeywords)),t.pagination={page:1,limit:20,source:"h5",name:e,type:"search"},n.next=11,t.getImageLists(t.pagination);case 11:case"end":return n.stop()}}),n)})))()},getImageLists:function(e){var t=this;return Object(h["a"])(regeneratorRuntime.mark((function n(){var r;return regeneratorRuntime.wrap((function(n){while(1)switch(n.prev=n.next){case 0:return r=t.$loading({lock:!0,text:"玩命加载中。。。",spinner:"el-icon-loading",background:"rgba(225, 225, 224, 0.8)"}),n.next=3,t.$store.dispatch("index/getImageLists",e);case 3:t.loadMore=t.loadMore.concat(t.imageLists),r.close();case 5:case"end":return n.stop()}}),n)})))()},handleScroll:function(){var e=this;return Object(h["a"])(regeneratorRuntime.mark((function t(){var n,r;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(n=document.documentElement.scrollTop||document.body.scrollTop,r=document.documentElement.scrollHeight||document.body.scrollHeight,!(n>=r-1e3-10*e.pagination.page&&e.imageLists.length<e.total)){t.next=6;break}return e.pagination.page=e.pagination.page+1,t.next=6,e.getImageLists(e.pagination);case 6:case"end":return t.stop()}}),t)})))()}}},w=(n("23c4"),n("d959")),O=n.n(w);const y=O()(v,[["render",g]]);t["default"]=y}}]);
//# sourceMappingURL=chunk-f9c05bc2.832e7ce9.js.map