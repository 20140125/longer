(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-07426f5a"],{"0cb2":function(e,t,n){var r=n("7b0b"),o=Math.floor,a="".replace,c=/\$([$&'`]|\d{1,2}|<[^>]*>)/g,i=/\$([$&'`]|\d{1,2})/g;e.exports=function(e,t,n,s,l,d){var u=n+e.length,p=s.length,g=i;return void 0!==l&&(l=r(l),g=c),a.call(d,g,(function(r,a){var c;switch(a.charAt(0)){case"$":return"$";case"&":return e;case"`":return t.slice(0,n);case"'":return t.slice(u);case"<":c=l[a.slice(1,-1)];break;default:var i=+a;if(0===i)return r;if(i>p){var d=o(i/10);return 0===d?r:d<=p?void 0===s[d-1]?a.charAt(1):s[d-1]+a.charAt(1):r}c=s[i-1]}return void 0===c?"":c}))}},"0d9f":function(e,t,n){"use strict";n("b0c0");var r=n("7a23"),o={class:"grid"},a={class:"panel"},c={class:"card-img"},i={class:"card-num-view"},s={class:"card-bottom"},l={class:"card-title-view row"},d={class:"card-title"};function u(e,t,n,u,p,g){var f=Object(r["resolveComponent"])("el-image");return Object(r["openBlock"])(),Object(r["createBlock"])("div",o,[(Object(r["openBlock"])(!0),Object(r["createBlock"])(r["Fragment"],null,Object(r["renderList"])(n.loadMore,(function(e,t){return Object(r["openBlock"])(),Object(r["createBlock"])("div",{key:t,class:"grid-list"},[Object(r["createVNode"])("div",a,[Object(r["createVNode"])("div",c,[Object(r["createVNode"])(f,{"preview-src-list":[e.href],src:e.href,fit:"unset",style:{width:"100%",height:"100%"}},null,8,["preview-src-list","src"])]),Object(r["createVNode"])("div",i,Object(r["toDisplayString"])(e.width?"".concat(e.width,"p"):""),1),Object(r["createVNode"])("div",s,[Object(r["createVNode"])("div",l,[Object(r["createVNode"])("div",d,[Object(r["createVNode"])("span",{innerHTML:e.name},null,8,["innerHTML"])])])])])])})),128))])}var p={name:"Lists",props:["loadMore"]},g=n("d959"),f=n.n(g);const h=f()(p,[["render",u]]);t["a"]=h},"23c4":function(e,t,n){"use strict";n("81a6")},5319:function(e,t,n){"use strict";var r=n("d784"),o=n("d039"),a=n("825a"),c=n("50c4"),i=n("a691"),s=n("1d80"),l=n("8aa5"),d=n("0cb2"),u=n("14c3"),p=n("b622"),g=p("replace"),f=Math.max,h=Math.min,m=function(e){return void 0===e?e:String(e)},b=function(){return"$0"==="a".replace(/./,"$0")}(),v=function(){return!!/./[g]&&""===/./[g]("a","$0")}(),O=!o((function(){var e=/./;return e.exec=function(){var e=[];return e.groups={a:"7"},e},"7"!=="".replace(e,"$<a>")}));r("replace",(function(e,t,n){var r=v?"$":"$0";return[function(e,n){var r=s(this),o=void 0==e?void 0:e[g];return void 0!==o?o.call(e,r,n):t.call(String(r),e,n)},function(e,o){if("string"===typeof o&&-1===o.indexOf(r)&&-1===o.indexOf("$<")){var s=n(t,this,e,o);if(s.done)return s.value}var p=a(this),g=String(e),b="function"===typeof o;b||(o=String(o));var v=p.global;if(v){var O=p.unicode;p.lastIndex=0}var w=[];while(1){var j=u(p,g);if(null===j)break;if(w.push(j),!v)break;var k=String(j[0]);""===k&&(p.lastIndex=l(g,c(p.lastIndex),O))}for(var y="",x=0,N=0;N<w.length;N++){j=w[N];for(var V=String(j[0]),S=f(h(i(j.index),g.length),0),$=[],C=1;C<j.length;C++)$.push(m(j[C]));var L=j.groups;if(b){var B=[V].concat($,S,g);void 0!==L&&B.push(L);var K=String(o.apply(void 0,B))}else K=d(V,g,S,$,L,o);S>=x&&(y+=g.slice(x,S)+K,x=S+V.length)}return y+g.slice(x)}]}),!O||!b||v)},"81a6":function(e,t,n){},dda8:function(e,t,n){"use strict";n.r(t);n("b0c0");var r=n("7a23"),o={class:"grid",style:{padding:"0 5px !important"}},a={class:"input-group"},c=Object(r["createTextVNode"])("搜索"),i={key:0,class:"keywords"},s={class:"history-keywords"},l={class:"icon"},d=Object(r["createVNode"])("span",null,"搜索历史",-1),u={class:"hot-keywords"},p={class:"icon"},g=Object(r["createVNode"])("span",null,"热门搜索",-1);function f(e,t,n,f,h,m){var b=Object(r["resolveComponent"])("el-button"),v=Object(r["resolveComponent"])("el-input"),O=Object(r["resolveComponent"])("el-tag"),w=Object(r["resolveComponent"])("Lists"),j=Object(r["resolveComponent"])("HomeLayout");return Object(r["openBlock"])(),Object(r["createBlock"])(j,{"header-title":"魔盒逗图 -- 热搜"},{body:Object(r["withCtx"])((function(){return[Object(r["createVNode"])("div",o,[Object(r["createVNode"])("div",a,[Object(r["createVNode"])(v,{modelValue:h.pagination.name,"onUpdate:modelValue":t[2]||(t[2]=function(e){return h.pagination.name=e}),placeholder:"请输入"},{append:Object(r["withCtx"])((function(){return[Object(r["createVNode"])(b,{type:"primary",onClick:t[1]||(t[1]=function(e){return m.doSearch(h.pagination.name)})},{default:Object(r["withCtx"])((function(){return[c]})),_:1})]})),_:1},8,["modelValue"])]),0===h.loadMore.length?(Object(r["openBlock"])(),Object(r["createBlock"])("div",i,[Object(r["createVNode"])("div",s,[Object(r["createVNode"])("div",l,[d,Object(r["createVNode"])("i",{class:"el-icon-delete",onClick:t[3]||(t[3]=function(){return m.oldDelete&&m.oldDelete.apply(m,arguments)})})]),(Object(r["openBlock"])(!0),Object(r["createBlock"])(r["Fragment"],null,Object(r["renderList"])(h.oldKeywords,(function(e,t){return Object(r["openBlock"])(),Object(r["createBlock"])(O,{key:t,effect:"dark",type:"info",onClick:function(t){return m.doSearch(e)}},{default:Object(r["withCtx"])((function(){return[Object(r["createTextVNode"])(Object(r["toDisplayString"])(e),1)]})),_:2},1032,["onClick"])})),128))]),Object(r["createVNode"])("div",u,[Object(r["createVNode"])("div",p,[g,Object(r["createVNode"])("i",{class:h.showHotWords?"el-icon-open":"el-icon-turn-off",onClick:t[4]||(t[4]=function(e){return h.showHotWords=!h.showHotWords})},null,2)]),h.showHotWords?(Object(r["openBlock"])(!0),Object(r["createBlock"])(r["Fragment"],{key:0},Object(r["renderList"])(m.hotKeyWord,(function(e,t){return Object(r["openBlock"])(),Object(r["createBlock"])(O,{key:t,effect:"dark",onClick:function(t){return m.doSearch(e)}},{default:Object(r["withCtx"])((function(){return[Object(r["createTextVNode"])(Object(r["toDisplayString"])(e),1)]})),_:2},1032,["onClick"])})),128)):Object(r["createCommentVNode"])("",!0)])])):Object(r["createCommentVNode"])("",!0),h.loadMore.length>0?(Object(r["openBlock"])(),Object(r["createBlock"])(w,{key:1,"load-more":h.loadMore,style:{padding:"50px 0 80px 0"}},null,8,["load-more"])):Object(r["createCommentVNode"])("",!0)])]})),_:1})}var h=n("1da1"),m=(n("96cf"),n("ac1f"),n("841c"),n("159b"),n("5319"),n("a434"),n("99af"),n("eb00")),b=n("0d9f"),v={name:"Search",components:{Lists:b["a"],HomeLayout:m["a"]},data:function(){return{showHotWords:!0,oldKeywords:[],loadMore:this.$store.state.index.imageLists.search.lists,pagination:{page:1,limit:20,source:"h5",name:this.$store.state.index.imageLists.search.searchKeys,type:"search"}}},computed:{hotKeyWord:function(){return this.$store.state.index.configuration.hotKeyWord},imageLists:function(){var e=this,t=JSON.parse(JSON.stringify(this.$store.state.index.imageLists.search.lists));return t.forEach((function(t){t.name=t.name.replace(e.pagination.name,'<span style="color:#409EFF">'+e.pagination.name+"</span>")})),t},total:function(){return this.$store.state.index.imageLists.search.total}},beforeUnmount:function(){window.removeEventListener("scroll",this.handleScroll)},created:function(){window.addEventListener("scroll",this.handleScroll)},mounted:function(){var e=this;this.$nextTick(Object(h["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e.oldKeywords=JSON.parse(window.localStorage.getItem("OldKeys")||"[]"),t.next=3,e.getConfiguration();case 3:case"end":return t.stop()}}),t)}))))},methods:{getConfiguration:function(){var e=this;return Object(h["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,e.$store.dispatch("index/getConfiguration",{keywords:"HotKeyWord",type:"hotKeyWord"});case 2:case"end":return t.stop()}}),t)})))()},oldDelete:function(){var e=this;return Object(h["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,e.$confirm("确定清除历史搜索记录？","删除记录",{type:"success",showClose:!1}).then((function(){window.localStorage.removeItem("OldKeys"),e.oldKeywords=[],e.setMessage("删除历史记录成功","success",!1)})).catch((function(){e.setMessage("已取消删除","success",!1)}));case 2:case"end":return t.stop()}}),t)})))()},setMessage:function(e,t){var n=this,r=!(arguments.length>2&&void 0!==arguments[2])||arguments[2],o=this.$loading({lock:!0,spinner:"el-icon-loading",background:"rgba(225, 225, 224, 0.8)"});setTimeout(Object(h["a"])(regeneratorRuntime.mark((function r(){return regeneratorRuntime.wrap((function(r){while(1)switch(r.prev=r.next){case 0:return o.close(),r.next=3,n.$message({message:e,type:t,offset:350,dangerouslyUseHTMLString:!0,duration:1e3});case 3:case"end":return r.stop()}}),r)}))),r?500:0)},doSearch:function(e){var t=this;return Object(h["a"])(regeneratorRuntime.mark((function n(){var r;return regeneratorRuntime.wrap((function(n){while(1)switch(n.prev=n.next){case 0:if(e){n.next=4;break}return t.setMessage("请输入搜索词","success"),setTimeout((function(){t.pagination.name=t.$store.state.index.imageLists.search.searchKeys}),500),n.abrupt("return",!1);case 4:return r=t.oldKeywords.indexOf(e),-1===r||t.oldKeywords.splice(r,1),t.oldKeywords.unshift(e),t.oldKeywords.length>20&&t.oldKeywords.pop(),window.localStorage.setItem("OldKeys",JSON.stringify(t.oldKeywords)),t.pagination={page:1,limit:20,source:"h5",name:e,type:"search"},n.next=11,t.getImageLists(t.pagination);case 11:case"end":return n.stop()}}),n)})))()},getImageLists:function(e){var t=this;return Object(h["a"])(regeneratorRuntime.mark((function n(){var r;return regeneratorRuntime.wrap((function(n){while(1)switch(n.prev=n.next){case 0:return r=t.$loading({lock:!0,text:"玩命加载中。。。",spinner:"el-icon-loading",background:"rgba(225, 225, 224, 0.8)"}),n.next=3,t.$store.dispatch("index/getImageLists",e);case 3:t.loadMore=t.loadMore.concat(t.imageLists),r.close();case 5:case"end":return n.stop()}}),n)})))()},handleScroll:function(){var e=this;return Object(h["a"])(regeneratorRuntime.mark((function t(){var n,r;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(n=document.documentElement.scrollTop||document.body.scrollTop,r=document.documentElement.scrollHeight||document.body.scrollHeight,!(n>=r-1e3-10*e.pagination.page&&e.imageLists.length<e.total)){t.next=6;break}return e.pagination.page=e.pagination.page+1,t.next=6,e.getImageLists(e.pagination);case 6:case"end":return t.stop()}}),t)})))()}}},O=(n("23c4"),n("d959")),w=n.n(O);const j=w()(v,[["render",f]]);t["default"]=j}}]);
//# sourceMappingURL=chunk-07426f5a.f11b7e05.js.map