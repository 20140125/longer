(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-1d26daf1"],{2824:function(e,t,n){"use strict";var o=n("7a23"),a={key:0,class:"pagination"};function r(e,t,n,r,i,c){var u=Object(o["resolveComponent"])("el-form"),s=Object(o["resolveComponent"])("el-pagination"),l=Object(o["resolveComponent"])("el-main"),d=Object(o["resolveComponent"])("el-skeleton"),p=Object(o["resolveDirective"])("water-mark");return Object(o["withDirectives"])((Object(o["openBlock"])(),Object(o["createBlock"])("div",null,[Object(o["createVNode"])(d,{loading:n.loading,rows:5,animated:""},{default:Object(o["withCtx"])((function(){return[Object(o["createVNode"])(u,{inline:!0,class:"form"},{default:Object(o["withCtx"])((function(){return[Object(o["renderSlot"])(e.$slots,"header")]})),_:3}),Object(o["createVNode"])(l,null,{default:Object(o["withCtx"])((function(){return[Object(o["renderSlot"])(e.$slots,"body"),i.baseLayoutPagination.show_page?(Object(o["openBlock"])(),Object(o["createBlock"])("div",a,[Object(o["createVNode"])(s,{"current-page":i.baseLayoutPagination.page,"page-size":i.baseLayoutPagination.limit,total:i.baseLayoutPagination.total,layout:"total, prev, pager, next",onCurrentChange:c.currentChange},null,8,["current-page","page-size","total","onCurrentChange"])])):Object(o["createCommentVNode"])("",!0)]})),_:3}),Object(o["renderSlot"])(e.$slots,"dialog")]})),_:3},8,["loading"])],512)),[[p,{text:i.username,textColor:"rgba(0, 0, 0, .2)",font:"25px consolas, sans-serif",row:130,col:850}]])}n("99af");var i={name:"BaseLayout",props:{loading:{type:Boolean,default:function(){return!1}},pagination:{type:Object,default:function(){}}},watch:{pagination:function(){this.baseLayoutPagination=this.pagination}},data:function(){return{baseLayoutPagination:this.pagination||{limit:10,page:1,total:0,show_page:!1},username:"".concat(this.$store.state.baseLayout.username,"【").concat(this.$store.state.login.userInfo.local,"】").concat(this.$store.state.login.userInfo.now_time)}},methods:{currentChange:function(e){this.$parent.currentPageChange(e)}}},c=(n("9cbf"),n("d959")),u=n.n(c);const s=u()(i,[["render",r]]);t["a"]=s},6111:function(e,t,n){},"99af":function(e,t,n){"use strict";var o=n("23e7"),a=n("d039"),r=n("e8b5"),i=n("861d"),c=n("7b0b"),u=n("50c4"),s=n("8418"),l=n("65f0"),d=n("1dde"),p=n("b622"),f=n("2d00"),g=p("isConcatSpreadable"),b=9007199254740991,m="Maximum allowed index exceeded",h=f>=51||!a((function(){var e=[];return e[g]=!1,e.concat()[0]!==e})),O=d("concat"),j=function(e){if(!i(e))return!1;var t=e[g];return void 0!==t?!!t:r(e)},w=!h||!O;o({target:"Array",proto:!0,forced:w},{concat:function(e){var t,n,o,a,r,i=c(this),d=l(i,0),p=0;for(t=-1,o=arguments.length;t<o;t++)if(r=-1===t?i:arguments[t],j(r)){if(a=u(r.length),p+a>b)throw TypeError(m);for(n=0;n<a;n++,p++)n in r&&s(d,p,r[n])}else{if(p>=b)throw TypeError(m);s(d,p++,r)}return d.length=p,d}})},"9cbf":function(e,t,n){"use strict";n("6111")},fffb:function(e,t,n){"use strict";n.r(t);var o=n("7a23"),a=Object(o["withScopeId"])("data-v-a1be0972"),r=a((function(e,t,n,r,i,c){var u=Object(o["resolveComponent"])("PluginLists"),s=Object(o["resolveComponent"])("BaseLayout");return Object(o["openBlock"])(),Object(o["createBlock"])(s,{loading:i.loading},{body:a((function(){return[Object(o["createVNode"])(u,{pluginList:c.pluginList,onUpdatePlugin:c.updatePlugin},null,8,["pluginList","onUpdatePlugin"])]})),_:1},8,["loading"])})),i=n("1da1"),c=(n("96cf"),n("2824")),u=(n("b0c0"),Object(o["withScopeId"])("data-v-f2b1dc7e")),s=u((function(e,t,n,a,r,i){var c=Object(o["resolveComponent"])("el-table-column"),s=Object(o["resolveComponent"])("el-button"),l=Object(o["resolveComponent"])("el-tooltip"),d=Object(o["resolveComponent"])("el-table");return Object(o["openBlock"])(),Object(o["createBlock"])(d,{data:(n.pluginList||{}).children,border:""},{default:u((function(){return[Object(o["createVNode"])(c,{label:"#ID",align:"center",prop:"id"}),Object(o["createVNode"])(c,{label:"插件名称",align:"center"},{default:u((function(e){return[Object(o["createVNode"])(s,{disabled:2===e.row.status,type:1===e.row.status?"primary":"info",plain:"",style:{width:"140px"},onClick:function(t){return i.toLogin(e.row)},size:"small"},{default:u((function(){return[Object(o["createTextVNode"])(Object(o["toDisplayString"])(e.row.name.toUpperCase())+" 授权登录 ",1)]})),_:2},1032,["disabled","type","onClick"])]})),_:1}),Object(o["createVNode"])(c,{label:"插件状态",align:"center"},{default:u((function(e){return[Object(o["createVNode"])(s,{type:["primary","danger"][e.row.status-1],size:"small",plain:""},{default:u((function(){return[Object(o["createTextVNode"])(Object(o["toDisplayString"])(["启用","禁用"][e.row.status-1]),1)]})),_:2},1032,["type"])]})),_:1}),Object(o["createVNode"])(c,{label:"操作",align:"left"},{default:u((function(t){return[Object(o["createVNode"])(l,{placement:"right-start",content:1===t.row.status?"卸载插件后，将禁用".concat(t.row.name.toUpperCase(),"授权登录"):"安装插件后，将启用".concat(t.row.name.toUpperCase(),"授权登录")},{default:u((function(){return[Object(o["createVNode"])(s,{icon:1===t.row.status?"el-icon-delete-solid":"el-icon-circle-plus",size:"small",type:"primary",plain:"",onClick:function(n){return e.$emit("updatePlugin",t.row)}},{default:u((function(){return[Object(o["createTextVNode"])(Object(o["toDisplayString"])(1===t.row.status?"卸载插件":"安装插件"),1)]})),_:2},1032,["icon","onClick"])]})),_:2},1032,["content"])]})),_:1})]})),_:1},8,["data"])})),l={name:"PluginLists",props:{pluginList:{type:Object,default:function(){}}},methods:{toLogin:function(e){1===e.status&&window.open(e.value)}}},d=n("d959"),p=n.n(d);const f=p()(l,[["render",s],["__scopeId","data-v-f2b1dc7e"]]);var g=f,b=n("4f8d"),m={name:"Plugin",components:{PluginLists:g,BaseLayout:c["a"]},data:function(){return{loading:!1}},computed:{pluginList:function(){return this.$store.state.login.oauthConfig}},mounted:function(){var e=this;this.$nextTick(Object(i["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,e.getPlugin();case 2:case"end":return t.stop()}}),t)}))))},methods:{getPlugin:function(){var e=this;return Object(i["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e.loading=!0,t.next=3,e.$store.dispatch("login/getOauthConfig",{name:"Oauth",refresh:!0}).then((function(){e.loading=!1}));case 3:case"end":return t.stop()}}),t)})))()},updatePlugin:function(e){var t=this;return Object(i["a"])(regeneratorRuntime.mark((function n(){return regeneratorRuntime.wrap((function(n){while(1)switch(n.prev=n.next){case 0:return n.next=2,t.$store.dispatch("UPDATE_ACTIONS",{url:b["a"].config.plugin,model:{status:1===e.status?2:1,id:e.id,pid:e.pid}}).then(Object(i["a"])(regeneratorRuntime.mark((function e(){return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:return e.next=2,t.getPlugin();case 2:case"end":return e.stop()}}),e)}))));case 2:case"end":return n.stop()}}),n)})))()}}};const h=p()(m,[["render",r],["__scopeId","data-v-a1be0972"]]);t["default"]=h}}]);
//# sourceMappingURL=chunk-1d26daf1.3e792745.js.map