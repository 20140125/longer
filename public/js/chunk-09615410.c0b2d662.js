(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-09615410"],{2824:function(e,t,n){"use strict";var r=n("7a23"),o={key:0,class:"pagination"};function a(e,t,n,a,i,c){var s=Object(r["resolveComponent"])("el-form"),l=Object(r["resolveComponent"])("el-pagination"),u=Object(r["resolveComponent"])("el-main"),p=Object(r["resolveComponent"])("el-skeleton");return Object(r["openBlock"])(),Object(r["createBlock"])(p,{rows:5,animated:"",loading:n.loading},{default:Object(r["withCtx"])((function(){return[Object(r["createVNode"])(s,{inline:!0,class:"form"},{default:Object(r["withCtx"])((function(){return[Object(r["renderSlot"])(e.$slots,"header")]})),_:3}),Object(r["createVNode"])(u,null,{default:Object(r["withCtx"])((function(){return[Object(r["renderSlot"])(e.$slots,"body"),i.T_pagination.show_page?(Object(r["openBlock"])(),Object(r["createBlock"])("div",o,[Object(r["createVNode"])(l,{onCurrentChange:c.__currentChange,"page-size":i.T_pagination.limit,layout:"total, prev, pager, next",total:i.T_pagination.total,"current-page":i.T_pagination.page},null,8,["onCurrentChange","page-size","total","current-page"])])):Object(r["createCommentVNode"])("",!0)]})),_:3}),Object(r["renderSlot"])(e.$slots,"dialog")]})),_:3},8,["loading"])}var i={name:"BaseLayout",props:{loading:{type:Boolean,default:function(){return!1}},pagination:{type:Object,default:function(){}}},watch:{pagination:function(){this.T_pagination=this.pagination}},data:function(){return{T_pagination:this.pagination||{limit:10,page:1,total:0,show_page:!1}}},methods:{__currentChange:function(e){this.$parent.currentPageChange(e)}}};n("36b5");i.render=a;t["a"]=i},"2ed6":function(e,t,n){"use strict";var r=n("7a23"),o=Object(r["withScopeId"])("data-v-a3cc4b78"),a=o((function(e,t,n,o,a,i){var c=Object(r["resolveComponent"])("el-button");return Object(r["openBlock"])(),Object(r["createBlock"])(c,{icon:a.attr["icon"][a.model.status],circle:"",type:a.attr["type"][a.model.status],onClick:i.changeStatus,size:"medium"},null,8,["icon","type","onClick"])})),i=n("1da1"),c=(n("96cf"),{name:"StatusRadio",props:{url:{type:String,default:function(){return""}},statusModel:{type:Object,default:function(){}}},data:function(){return{model:JSON.parse(JSON.stringify(this.statusModel)),attr:{icon:{1:"el-icon-check",2:"el-icon-close"},type:{1:"success",2:"danger"}}}},watch:{statusModel:function(){this.model=JSON.parse(JSON.stringify(this.statusModel))}},methods:{changeStatus:function(){var e=this;return Object(i["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:e.$confirm("确认修改当前状态",{showClose:!1,confirmButtonText:"确定",cancelButtonText:"取消",type:"success"}).then((function(){e.model.status=1===e.model.status?2:1;var t={url:e.url,model:{id:e.model.id,status:e.model.status,act:"status"}};e.$store.dispatch("UPDATE_ACTIONS",t).then((function(){setTimeout((function(){e.$emit("closeDialog")}),500)}))})).catch((function(){e.$message.info("取消修改")}));case 1:case"end":return t.stop()}}),t)})))()}}});c.render=a,c.__scopeId="data-v-a3cc4b78";t["a"]=c},"36b5":function(e,t,n){"use strict";n("601a")},"4e06":function(e,t,n){"use strict";n.r(t);var r=n("7a23"),o=Object(r["withScopeId"])("data-v-241d6a82");Object(r["pushScopeId"])("data-v-241d6a82");var a=Object(r["createTextVNode"])("权限申请");Object(r["popScopeId"])();var i=o((function(e,t,n,i,c,s){var l=Object(r["resolveComponent"])("el-button"),u=Object(r["resolveComponent"])("el-form-item"),p=Object(r["resolveComponent"])("PermissionLists"),d=Object(r["resolveComponent"])("PermissionDialog"),m=Object(r["resolveComponent"])("BaseLayout");return Object(r["openBlock"])(),Object(r["createBlock"])(m,{loading:c.loading,pagination:c.pagination},{header:o((function(){return[Object(r["createVNode"])(u,null,{default:o((function(){return[e.Permission.auth.indexOf(c.savePermission)>-1?(Object(r["openBlock"])(),Object(r["createBlock"])(l,{key:0,type:"primary",plain:"",size:"mini",onClick:s.permissionApply,icon:"el-icon-plus"},{default:o((function(){return[a]})),_:1},8,["onClick"])):Object(r["createCommentVNode"])("",!0)]})),_:1})]})),body:o((function(){return[Object(r["createVNode"])(p,{"permission-lists":s.permissionLists,onPermissionUpdate:s.permissionUpdate,onGetPermissionApply:s.getPermissionApply},null,8,["permission-lists","onPermissionUpdate","onGetPermissionApply"])]})),dialog:o((function(){return[Object(r["createVNode"])(d,{form:c.form,syncVisible:c.syncVisible,reForm:c.reForm,permissionAttr:c.permissionAttr,onGetPermissionApply:s.getPermissionApply,onGetUserAuth:s.getUserAuth},null,8,["form","syncVisible","reForm","permissionAttr","onGetPermissionApply","onGetUserAuth"])]})),_:1},8,["loading","pagination"])})),c=n("1da1"),s=(n("96cf"),n("2824")),l=Object(r["createTextVNode"])("续期");function u(e,t,n,o,a,i){var c=Object(r["resolveComponent"])("el-table-column"),s=Object(r["resolveComponent"])("el-table"),u=Object(r["resolveComponent"])("StatusRadio"),p=Object(r["resolveComponent"])("el-button");return Object(r["openBlock"])(),Object(r["createBlock"])(s,{data:n.permissionLists},{default:Object(r["withCtx"])((function(){return[Object(r["createVNode"])(c,{type:"expand"},{default:Object(r["withCtx"])((function(e){return[Object(r["createVNode"])(s,{data:e.row.applyLog,border:""},{default:Object(r["withCtx"])((function(){return[Object(r["createVNode"])(c,{label:"执行者",prop:"user_name",align:"center"}),Object(r["createVNode"])(c,{label:"执行时间",prop:"created_at",align:"center"}),Object(r["createVNode"])(c,{label:"执行记录",prop:"desc",align:"center"})]})),_:2},1032,["data"])]})),_:1}),Object(r["createVNode"])(c,{label:"#ID",prop:"id",width:"100px"}),Object(r["createVNode"])(c,{label:"申请人",prop:"username"}),Object(r["createVNode"])(c,{label:"申请地址",prop:"href"}),Object(r["createVNode"])(c,{label:"授权通过",align:"center",width:"150px"},{default:Object(r["withCtx"])((function(n){return[Object(r["createVNode"])(u,{url:a.URL,"status-model":n.row,onCloseDialog:t[1]||(t[1]=function(t){return e.$emit("getPermissionApply",{page:1,limit:15,show_page:!0,refresh:!0})})},null,8,["url","status-model"])]})),_:1}),Object(r["createVNode"])(c,{label:"申请时间",prop:"created_at",align:"center"}),Object(r["createVNode"])(c,{label:"通过时间",prop:"updated_at",align:"center"}),Object(r["createVNode"])(c,{label:"权限期限",prop:"expires",align:"center"}),Object(r["createVNode"])(c,{label:"操作",width:"200px",align:"right"},{default:Object(r["withCtx"])((function(t){return[t.row.refresh?(Object(r["openBlock"])(),Object(r["createBlock"])(p,{key:0,type:"primary",plain:"",icon:"el-icon-refresh-right",size:"mini",onClick:function(n){return e.$emit("permissionUpdate",t.row)}},{default:Object(r["withCtx"])((function(){return[l]})),_:2},1032,["onClick"])):Object(r["createCommentVNode"])("",!0)]})),_:1})]})),_:1},8,["data"])}var p=n("2ed6"),d=n("4f8d"),m={name:"PermissionLists",props:["permissionLists"],components:{StatusRadio:p["a"]},data:function(){return{URL:d["a"].permission.update}}};m.render=u;var b=m,f=Object(r["withScopeId"])("data-v-53c6ceb8"),h=f((function(e,t,n,o,a,i){var c=Object(r["resolveComponent"])("el-option"),s=Object(r["resolveComponent"])("el-select"),l=Object(r["resolveComponent"])("el-form-item"),u=Object(r["resolveComponent"])("el-date-picker"),p=Object(r["resolveComponent"])("el-input"),d=Object(r["resolveComponent"])("SubmitButton"),m=Object(r["resolveComponent"])("el-form"),b=Object(r["resolveComponent"])("el-dialog");return Object(r["openBlock"])(),Object(r["createBlock"])("div",null,[Object(r["createVNode"])(b,{modelValue:e.visible,"onUpdate:modelValue":t[7]||(t[7]=function(t){return e.visible=t}),title:"权限申请","show-close":!1,"close-on-click-modal":!1,"close-on-press-escape":!1,center:""},{default:f((function(){return[Object(r["createVNode"])(m,{model:a.localForm,ref:n.reForm,"label-position":"left","label-width":"100px",rules:a.rules},{default:f((function(){return[Object(r["createVNode"])(l,{label:"申请人：",prop:"user_id"},{default:f((function(){return[Object(r["createVNode"])(s,{filterable:"",modelValue:a.localForm.user_id,"onUpdate:modelValue":t[1]||(t[1]=function(e){return a.localForm.user_id=e}),modelModifiers:{number:!0},clearable:"",onChange:t[2]||(t[2]=function(t){return e.$emit("getUserAuth",a.localForm.user_id)})},{default:f((function(){return[(Object(r["openBlock"])(!0),Object(r["createBlock"])(r["Fragment"],null,Object(r["renderList"])(n.permissionAttr.userLists,(function(e,t){return Object(r["openBlock"])(),Object(r["createBlock"])(c,{label:e.username,key:t,value:e.id},null,8,["label","value"])})),128))]})),_:1},8,["modelValue"])]})),_:1}),Object(r["createVNode"])(l,{label:"申请地址：",prop:"href"},{default:f((function(){return[Object(r["createVNode"])(s,{filterable:"",modelValue:a.localForm.href,"onUpdate:modelValue":t[3]||(t[3]=function(e){return a.localForm.href=e}),disabled:"update"===n.reForm},{default:f((function(){return[(Object(r["openBlock"])(!0),Object(r["createBlock"])(r["Fragment"],null,Object(r["renderList"])(n.permissionAttr.authLists,(function(e,t){return Object(r["openBlock"])(),Object(r["createBlock"])(c,{label:i.setAuthName(e),clearable:"",key:t,value:e.api,disabled:e.disable},null,8,["label","value","disabled"])})),128))]})),_:1},8,["modelValue","disabled"])]})),_:1}),Object(r["createVNode"])(l,{label:"申请时间：",prop:"expires"},{default:f((function(){return[Object(r["createVNode"])(u,{modelValue:a.localForm.expires,"onUpdate:modelValue":t[4]||(t[4]=function(e){return a.localForm.expires=e}),clearable:"",editable:!1,type:"datetime","value-format":"YYYY-MM-DD HH:mm:ss",placeholder:"选择申请时间"},null,8,["modelValue"])]})),_:1}),Object(r["createVNode"])(l,{label:"申请理由：",prop:"desc"},{default:f((function(){return[Object(r["createVNode"])(p,{modelValue:a.localForm.desc,"onUpdate:modelValue":t[5]||(t[5]=function(e){return a.localForm.desc=e}),modelModifiers:{trim:!0},maxlength:"200",placeholder:"请输入申请理由","show-word-limit":"",resize:"none",autosize:{minRows:4},type:"textarea"},null,8,["modelValue"])]})),_:1}),Object(r["createVNode"])(d,{form:a.submitForm,reForm:n.reForm,onCloseDialog:t[6]||(t[6]=function(t){return e.$emit("getPermissionApply",{page:1,limit:15,show_page:!0,refresh:!0})})},null,8,["form","reForm"])]})),_:1},8,["model","rules"])]})),_:1},8,["modelValue"])])})),g=(n("a15b"),n("b0c0"),n("c827")),O=n("58ea"),j={name:"PermissionDialog",components:{SubmitButton:g["a"]},props:{reForm:{type:String,default:function(){return"created"}},form:{type:Object,default:function(){}},permissionAttr:{type:Object,default:function(){}}},mixins:[O["a"]],data:function(){return{localForm:this.form,submitForm:{},rules:{user_id:[{required:!0,message:"请选择申请人",trigger:"blur"}],href:[{required:!0,message:"请选择申请地址",trigger:"blur"}],expires:[{required:!0,message:"请选择申请时间",trigger:"change",type:"date"}],desc:[{required:!0,message:"请输入申请理由",trigger:"blur"}]}}},watch:{form:function(){var e=this;this.localForm=this.form,this.$nextTick((function(){setTimeout((function(){e.submitForm={model:e.localForm,$refs:e.$refs,url:"created"===e.reForm?d["a"].permission.save:d["a"].permission.update},e.localForm.user_id=e.$store.state.login.userInfo.id,e.$parent.$parent.$parent.getUserAuth(e.localForm.user_id)}),1e3)}))}},methods:{setAuthName:function(e){return Array(e.level+1).join("　　")+e.name}}};j.render=h,j.__scopeId="data-v-53c6ceb8";var v=j,y={name:"Apply",components:{PermissionDialog:v,PermissionLists:b,BaseLayout:s["a"]},data:function(){return{loading:!0,pagination:{page:1,limit:15,total:0,show_page:!0,refresh:!1},syncVisible:!1,reForm:"created",form:{},permissionAttr:{userLists:[],authList:[]},savePermission:d["a"].permission.save}},computed:{permissionLists:function(){return this.$store.state.apply.permissionLists}},mounted:function(){var e=this;this.$nextTick(Object(c["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,e.getPermissionApply(e.pagination);case 2:case"end":return t.stop()}}),t)}))))},methods:{getPermissionApply:function(e){var t=this;return Object(c["a"])(regeneratorRuntime.mark((function n(){return regeneratorRuntime.wrap((function(n){while(1)switch(n.prev=n.next){case 0:return t.loading=!0,t.syncVisible=!1,n.next=4,t.$store.dispatch("apply/getPermissionApply",e).then((function(){t.loading=!1,t.pagination.total=t.$store.state.apply.total}));case 4:case"end":return n.stop()}}),n)})))()},currentPageChange:function(e){var t=this;return Object(c["a"])(regeneratorRuntime.mark((function n(){return regeneratorRuntime.wrap((function(n){while(1)switch(n.prev=n.next){case 0:return t.pagination.page=e,n.next=3,t.getPermissionApply(t.pagination);case 3:case"end":return n.stop()}}),n)})))()},getUserAuth:function(e){var t=this;return Object(c["a"])(regeneratorRuntime.mark((function n(){return regeneratorRuntime.wrap((function(n){while(1)switch(n.prev=n.next){case 0:return n.next=2,t.$store.dispatch("apply/getUserAuth",{user_id:e,refresh:!0}).then((function(){t.permissionAttr={userLists:t.$store.state.users.cacheUsers,authLists:t.$store.state.apply.authLists}}));case 2:case"end":return n.stop()}}),n)})))()},permissionApply:function(){var e=this;return Object(c["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e.syncVisible=!0,t.next=3,e.$store.dispatch("users/getCacheUserLists",{}).then((function(){e.permissionAttr={userLists:e.$store.state.users.cacheUsers,authList:[]},e.form={username:"",user_id:"",href:"",expires:"",desc:""},e.reForm="created"}));case 3:case"end":return t.stop()}}),t)})))()},permissionUpdate:function(e){var t=this;return Object(c["a"])(regeneratorRuntime.mark((function n(){return regeneratorRuntime.wrap((function(n){while(1)switch(n.prev=n.next){case 0:t.$store.dispatch("UPDATE_ACTIONS",{url:d["a"].permission.update,model:{id:e.id,status:e.status}}).then((function(){t.$message.success("权限续期成功"),t.getPermissionApply({page:1,limit:15,show_page:!0,refresh:!0})}));case 1:case"end":return n.stop()}}),n)})))()}}};y.render=i,y.__scopeId="data-v-241d6a82";t["default"]=y},"58ea":function(e,t,n){"use strict";n.d(t,"a",(function(){return r}));var r={props:["syncVisible"],data:function(){return{visible:this.syncVisible}},watch:{syncVisible:function(){this.visible=this.syncVisible}}}},"601a":function(e,t,n){},c827:function(e,t,n){"use strict";var r=n("7a23"),o=Object(r["withScopeId"])("data-v-60a89228"),a=o((function(e,t,n,a,i,c){var s=Object(r["resolveComponent"])("el-button"),l=Object(r["resolveComponent"])("el-main");return Object(r["openBlock"])(),Object(r["createBlock"])(l,{style:{"text-align":"center"}},{default:o((function(){return[Object(r["createVNode"])(s,{type:"primary",size:"medium",plain:"",onClick:c.saveForm},{default:o((function(){return[Object(r["createTextVNode"])(Object(r["toDisplayString"])(n.confirmButton||"确定"),1)]})),_:1},8,["onClick"]),Object(r["createVNode"])(s,{type:"default",size:"medium",onClick:t[1]||(t[1]=function(t){return e.$emit("closeDialog")}),plain:""},{default:o((function(){return[Object(r["createTextVNode"])(Object(r["toDisplayString"])(n.cancelButton||"取消"),1)]})),_:1})]})),_:1})})),i=n("1da1"),c=(n("96cf"),{name:"SubmitButton",props:["confirmButton","cancelButton","form","reForm"],methods:{saveForm:function(){var e=this;return Object(i["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,e.form.$refs[e.reForm].validate((function(t){if(!t)return!1;e.$store.dispatch("UPDATE_ACTIONS",e.form).then((function(){setTimeout((function(){e.$emit("closeDialog")}),500)}))}));case 2:case"end":return t.stop()}}),t)})))()}}});c.render=a,c.__scopeId="data-v-60a89228";t["a"]=c}}]);
//# sourceMappingURL=chunk-09615410.c0b2d662.js.map