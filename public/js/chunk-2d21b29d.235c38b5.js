(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2d21b29d"],{bf36:function(e,t,n){"use strict";n.r(t);var r=n("7a23"),o={class:"grid"},c={class:"grid-item"},i={class:"info"},a={class:"info"},s=Object(r["createVNode"])("i",{class:"el-icon-arrow-right icon"},null,-1);function u(e,t,n,u,l,d){var b=Object(r["resolveComponent"])("el-avatar"),m=Object(r["resolveComponent"])("HomeLayout");return Object(r["openBlock"])(),Object(r["createBlock"])(m,{"header-title":"个人中心"},{body:Object(r["withCtx"])((function(){return[Object(r["createVNode"])("div",o,[Object(r["createVNode"])("div",c,[Object(r["createVNode"])(b,{size:80,src:(e.Permission||{}).avatar_url||""},null,8,["src"]),Object(r["createVNode"])("div",i,Object(r["toDisplayString"])((e.Permission||{}).username||""),1)]),(Object(r["openBlock"])(!0),Object(r["createBlock"])(r["Fragment"],null,Object(r["renderList"])(d.accountSetting,(function(e,t){return Object(r["openBlock"])(),Object(r["createBlock"])("div",{key:t,class:"grid-item",onClick:function(t){return d.handleClick(e)}},[Object(r["createVNode"])("i",{class:"".concat(e.icon)},null,2),Object(r["createVNode"])("div",a,Object(r["toDisplayString"])(e.label),1),s],8,["onClick"])})),128))])]})),_:1})}var l=n("1da1"),d=(n("96cf"),n("eb00")),b={name:"Users",components:{HomeLayout:d["a"]},computed:{accountSetting:function(){return this.$store.state.index.accountSetting}},mounted:function(){var e=this;this.$nextTick((function(){e.Permission||setTimeout((function(){e.$store.commit("UPDATE_MUTATIONS",{errorInfo:e.$store.state.index.error.login})}),500)}))},methods:{handleClick:function(e){var t=this;return Object(l["a"])(regeneratorRuntime.mark((function n(){return regeneratorRuntime.wrap((function(n){while(1)switch(n.prev=n.next){case 0:if(!t.Permission){n.next=7;break}if(!e.path){n.next=6;break}return n.next=4,t.$router.push({path:e.path});case 4:n.next=7;break;case 6:"logout"===e.value&&t.logoutSYS();case 7:case"end":return n.stop()}}),n)})))()},logoutSYS:function(){var e=this;this.$confirm("登出系统",{showClose:!1,type:"success"}).then(Object(l["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,e.$store.dispatch("login/logoutSYS",{remember_token:e.$store.state.token});case 2:return t.next=4,e.$router.push({path:"/home/users"});case 4:case"end":return t.stop()}}),t)})))).catch((function(){console.log("cancel logoutSYS")}))}}},m=n("d959"),h=n.n(m);const p=h()(b,[["render",u]]);t["default"]=p}}]);
//# sourceMappingURL=chunk-2d21b29d.235c38b5.js.map