(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-search"],{"0cfc":function(e,t,o){"use strict";o.r(t);var n=o("b792"),a=o("9f9e");for(var r in a)"default"!==r&&function(e){o.d(t,e,(function(){return a[e]}))}(r);o("46d6");var i,c=o("f0c5"),l=Object(c["a"])(a["default"],n["b"],n["c"],!1,null,"3783e340",null,!1,n["a"],i);t["default"]=l.exports},"0e8e":function(e,t,o){var n=o("24fb");t=n(!1),t.push([e.i,'@charset "UTF-8";\r\n/**\r\n * 下方引入的为uView UI的集成样式文件，为scss预处理器，其中包含了一些"u-"开头的自定义变量\r\n * 使用的时候，请将下面的一行复制到您的uniapp项目根目录的uni.scss中即可\r\n * uView自定义的css类名和scss变量，均以"u-"开头，不会造成冲突，请放心使用 \r\n */.u-tag[data-v-114217d4]{box-sizing:border-box;align-items:center;border-radius:%?6?%;display:inline-block;line-height:1}.u-size-default[data-v-114217d4]{font-size:%?22?%;padding:%?12?% %?22?%}.u-size-mini[data-v-114217d4]{font-size:%?20?%;padding:%?6?% %?12?%}.u-mode-light-primary[data-v-114217d4]{background-color:#ecf5ff;color:#2979ff;border:1px solid #a0cfff}.u-mode-light-success[data-v-114217d4]{background-color:#dbf1e1;color:#19be6b;border:1px solid #71d5a1}.u-mode-light-error[data-v-114217d4]{background-color:#fef0f0;color:#fa3534;border:1px solid #fab6b6}.u-mode-light-warning[data-v-114217d4]{background-color:#fdf6ec;color:#f90;border:1px solid #fcbd71}.u-mode-light-info[data-v-114217d4]{background-color:#f4f4f5;color:#909399;border:1px solid #c8c9cc}.u-mode-dark-primary[data-v-114217d4]{background-color:#2979ff;color:#fff}.u-mode-dark-success[data-v-114217d4]{background-color:#19be6b;color:#fff}.u-mode-dark-error[data-v-114217d4]{background-color:#fa3534;color:#fff}.u-mode-dark-warning[data-v-114217d4]{background-color:#f90;color:#fff}.u-mode-dark-info[data-v-114217d4]{background-color:#909399;color:#fff}.u-mode-plain-primary[data-v-114217d4]{background-color:#fff;color:#2979ff;border:1px solid #2979ff}.u-mode-plain-success[data-v-114217d4]{background-color:#fff;color:#19be6b;border:1px solid #19be6b}.u-mode-plain-error[data-v-114217d4]{background-color:#fff;color:#fa3534;border:1px solid #fa3534}.u-mode-plain-warning[data-v-114217d4]{background-color:#fff;color:#f90;border:1px solid #f90}.u-mode-plain-info[data-v-114217d4]{background-color:#fff;color:#909399;border:1px solid #909399}.u-disabled[data-v-114217d4]{opacity:.55}.u-shape-circle[data-v-114217d4]{border-radius:%?100?%}.u-shape-circleRight[data-v-114217d4]{border-radius:0 %?100?% %?100?% 0}.u-shape-circleLeft[data-v-114217d4]{border-radius:%?100?% 0 0 %?100?%}.u-close-icon[data-v-114217d4]{margin-left:%?14?%;font-size:%?22?%;color:#19be6b}.u-icon-wrap[data-v-114217d4]{display:inline-flex;-webkit-transform:scale(.86);transform:scale(.86)}',""]),e.exports=t},"282b":function(e,t,o){"use strict";o.r(t);var n=o("faa0"),a=o("4093");for(var r in a)"default"!==r&&function(e){o.d(t,e,(function(){return a[e]}))}(r);o("56fd");var i,c=o("f0c5"),l=Object(c["a"])(a["default"],n["b"],n["c"],!1,null,"2d72a747",null,!1,n["a"],i);t["default"]=l.exports},"2c8d":function(e,t,o){"use strict";var n=o("aea5"),a=o.n(n);a.a},3585:function(e,t,o){"use strict";o.d(t,"b",(function(){return a})),o.d(t,"c",(function(){return r})),o.d(t,"a",(function(){return n}));var n={uIcon:o("034d").default},a=function(){var e=this,t=e.$createElement,o=e._self._c||t;return e.show?o("v-uni-view",{staticClass:"u-tag",class:[e.disabled?"u-disabled":"","u-size-"+e.size,"u-shape-"+e.shape,"u-mode-"+e.mode+"-"+e.type],style:[e.customStyle],on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.clickTag.apply(void 0,arguments)}}},[e._v(e._s(e.text)),o("v-uni-view",{staticClass:"u-icon-wrap",on:{click:function(t){t.stopPropagation(),arguments[0]=t=e.$handleEvent(t)}}},[e.closeable?o("u-icon",{staticClass:"u-close-icon",style:[e.iconStyle],attrs:{size:"22",color:e.closeIconColor,name:"close"},on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.close.apply(void 0,arguments)}}}):e._e()],1)],1):e._e()},r=[]},"3d92":function(e,t,o){"use strict";o("a9e3"),Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var n={name:"u-tag",props:{type:{type:String,default:"primary"},disabled:{type:[Boolean,String],default:!1},size:{type:String,default:"default"},shape:{type:String,default:"square"},text:{type:[String,Number],default:""},bgColor:{type:String,default:""},color:{type:String,default:""},borderColor:{type:String,default:""},closeColor:{type:String,default:""},index:{type:[Number,String],default:""},mode:{type:String,default:"light"},closeable:{type:Boolean,default:!1},show:{type:Boolean,default:!0}},data:function(){return{}},computed:{customStyle:function(){var e={};return this.color&&(e.color=this.color),this.bgColor&&(e.backgroundColor=this.bgColor),"plain"==this.mode&&this.color&&!this.borderColor?e.borderColor=this.color:e.borderColor=this.borderColor,e},iconStyle:function(){if(this.closeable){var e={};return"mini"==this.size?e.fontSize="20rpx":e.fontSize="22rpx","plain"==this.mode||"light"==this.mode?e.color=this.type:"dark"==this.mode&&(e.color="#ffffff"),this.closeColor&&(e.color=this.closeColor),e}},closeIconColor:function(){return this.closeColor?this.closeColor:this.color?this.color:"dark"==this.mode?"#ffffff":this.type}},methods:{clickTag:function(){this.disabled||this.$emit("click",this.index)},close:function(){this.$emit("close",this.index)}}};t.default=n},4093:function(e,t,o){"use strict";o.r(t);var n=o("abd9"),a=o.n(n);for(var r in n)"default"!==r&&function(e){o.d(t,e,(function(){return n[e]}))}(r);t["default"]=a.a},"46d6":function(e,t,o){"use strict";var n=o("fba8"),a=o.n(n);a.a},4923:function(e,t,o){"use strict";o.r(t);var n=o("3d92"),a=o.n(n);for(var r in n)"default"!==r&&function(e){o.d(t,e,(function(){return n[e]}))}(r);t["default"]=a.a},"56fd":function(e,t,o){"use strict";var n=o("7e66"),a=o.n(n);a.a},"7e66":function(e,t,o){var n=o("ccb2");"string"===typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);var a=o("4f06").default;a("0e750fea",n,!0,{sourceMap:!1,shadowMode:!1})},"9f9e":function(e,t,o){"use strict";o.r(t);var n=o("edc8"),a=o.n(n);for(var r in n)"default"!==r&&function(e){o.d(t,e,(function(){return n[e]}))}(r);t["default"]=a.a},abd9:function(e,t,o){"use strict";(function(e){var n=o("4ea4");o("c975"),o("a434"),Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0,o("96cf");var a=n(o("1da1")),r={data:function(){return{hotKeyWords:[],name:"",eyeName:"eye-fill",togger:!0,oldKeywordList:[]}},onShow:function(){this.loadOldKeyword()},mounted:function(){var e=this;this.$nextTick((0,a.default)(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,e.getHotKeyWords();case 2:case"end":return t.stop()}}),t)}))))},methods:{getHotKeyWords:function(){var e=this;return(0,a.default)(regeneratorRuntime.mark((function t(){var o;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,e.$u.api.getConfiguration({keyword:"hotKeyWord"});case 2:o=t.sent,e.hotKeyWords=o.lists||[];case 4:case"end":return t.stop()}}),t)})))()},doSearch:function(e){this.$router.push({path:"/pages/index?name=".concat(encodeURIComponent(e))}),this.saveKeyword(e||this.name)},loadOldKeyword:function(){var e=this;uni.getStorage({key:"OldKeys",success:function(t){var o=JSON.parse(t.data);e.oldKeywordList=o}})},saveKeyword:function(e){var t=this;uni.getStorage({key:"OldKeys",success:function(o){var n=JSON.parse(o.data),a=n.indexOf(e);-1==a||n.splice(a,1),n.unshift(e),n.length>10&&n.pop(),uni.setStorage({key:"OldKeys",data:JSON.stringify(n)}),t.oldKeywordList=n},fail:function(o){var n=[e];uni.setStorage({key:"OldKeys",data:JSON.stringify(n)}),t.oldKeywordList=n}})},oldDelete:function(){var t=this;uni.showModal({content:"确定清除历史搜索记录？",success:function(o){o.confirm?(e.log("用户点击确定"),t.oldKeywordList=[],uni.removeStorage({key:"OldKeys"})):o.cancel&&e.log("用户点击取消")}})},hotToggle:function(){this.togger=!this.togger,this.eyeName=this.togger?"eye-fill":"eye-off"}}};t.default=r}).call(this,o("5a52")["default"])},aea5:function(e,t,o){var n=o("0e8e");"string"===typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);var a=o("4f06").default;a("486ac9f7",n,!0,{sourceMap:!1,shadowMode:!1})},b792:function(e,t,o){"use strict";o.d(t,"b",(function(){return a})),o.d(t,"c",(function(){return r})),o.d(t,"a",(function(){return n}));var n={uIcon:o("034d").default},a=function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("v-uni-view",{staticClass:"u-search",style:{margin:e.margin},on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.clickHandler.apply(void 0,arguments)}}},[o("v-uni-view",{staticClass:"u-content",style:{backgroundColor:e.bgColor,borderRadius:"round"==e.shape?"100rpx":"10rpx",border:e.borderStyle,height:e.height+"rpx"}},[o("v-uni-view",{staticClass:"u-icon-wrap"},[o("u-icon",{staticClass:"u-clear-icon",attrs:{size:30,name:e.searchIcon,color:e.searchIconColor?e.searchIconColor:e.color}})],1),o("v-uni-input",{staticClass:"u-input",style:[{textAlign:e.inputAlign,color:e.color,backgroundColor:e.bgColor},e.inputStyle],attrs:{"confirm-type":"search",value:e.value,disabled:e.disabled,focus:e.focus,maxlength:e.maxlength,"placeholder-class":"u-placeholder-class",placeholder:e.placeholder,"placeholder-style":"color: "+e.placeholderColor,type:"text"},on:{blur:function(t){arguments[0]=t=e.$handleEvent(t),e.blur.apply(void 0,arguments)},confirm:function(t){arguments[0]=t=e.$handleEvent(t),e.search.apply(void 0,arguments)},input:function(t){arguments[0]=t=e.$handleEvent(t),e.inputChange.apply(void 0,arguments)},focus:function(t){arguments[0]=t=e.$handleEvent(t),e.getFocus.apply(void 0,arguments)}}}),e.keyword&&e.clearabled&&e.focused?o("v-uni-view",{staticClass:"u-close-wrap",on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.clear.apply(void 0,arguments)}}},[o("u-icon",{staticClass:"u-clear-icon",attrs:{name:"close-circle-fill",size:"34",color:"#c0c4cc"}})],1):e._e()],1),o("v-uni-view",{staticClass:"u-action",class:[e.showActionBtn||e.show?"u-action-active":""],style:[e.actionStyle],on:{click:function(t){t.stopPropagation(),t.preventDefault(),arguments[0]=t=e.$handleEvent(t),e.custom.apply(void 0,arguments)}}},[e._v(e._s(e.actionText))])],1)},r=[]},ccb2:function(e,t,o){var n=o("24fb");t=n(!1),t.push([e.i,'@charset "UTF-8";\r\n/**\r\n * 下方引入的为uView UI的集成样式文件，为scss预处理器，其中包含了一些"u-"开头的自定义变量\r\n * 使用的时候，请将下面的一行复制到您的uniapp项目根目录的uni.scss中即可\r\n * uView自定义的css类名和scss变量，均以"u-"开头，不会造成冲突，请放心使用 \r\n */.u-icon[data-v-2d72a747]{float:right}.u-card[data-v-2d72a747]{min-height:600px}.u-row-between[data-v-2d72a747]{justify-content:center}.hot-keywords[data-v-2d72a747]{margin:%?60?% 0;font-size:12px;color:#606266}.hot-keywords > uni-view[data-v-2d72a747]{margin-top:%?20?%}',""]),e.exports=t},d258:function(e,t,o){"use strict";o.r(t);var n=o("3585"),a=o("4923");for(var r in a)"default"!==r&&function(e){o.d(t,e,(function(){return a[e]}))}(r);o("2c8d");var i,c=o("f0c5"),l=Object(c["a"])(a["default"],n["b"],n["c"],!1,null,"114217d4",null,!1,n["a"],i);t["default"]=l.exports},e4ca:function(e,t,o){var n=o("24fb");t=n(!1),t.push([e.i,'@charset "UTF-8";\r\n/**\r\n * 下方引入的为uView UI的集成样式文件，为scss预处理器，其中包含了一些"u-"开头的自定义变量\r\n * 使用的时候，请将下面的一行复制到您的uniapp项目根目录的uni.scss中即可\r\n * uView自定义的css类名和scss变量，均以"u-"开头，不会造成冲突，请放心使用 \r\n */.u-search[data-v-3783e340]{\r\ndisplay:flex;flex-direction:row;\r\nalign-items:center;flex:1}.u-content[data-v-3783e340]{\r\ndisplay:flex;flex-direction:row;\r\nalign-items:center;padding:0 %?18?%;flex:1}.u-clear-icon[data-v-3783e340]{\r\ndisplay:flex;flex-direction:row;\r\nalign-items:center}.u-input[data-v-3783e340]{flex:1;font-size:%?28?%;line-height:1;margin:0 %?10?%;color:#909399}.u-close-wrap[data-v-3783e340]{width:%?40?%;height:100%;\r\ndisplay:flex;flex-direction:row;\r\nalign-items:center;justify-content:center;border-radius:50%}.u-placeholder-class[data-v-3783e340]{color:#909399}.u-action[data-v-3783e340]{font-size:%?28?%;color:#303133;width:0;overflow:hidden;transition:all .3s;white-space:nowrap;text-align:center}.u-action-active[data-v-3783e340]{width:%?80?%;margin-left:%?10?%}',""]),e.exports=t},edc8:function(e,t,o){"use strict";o("a9e3"),Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var n={name:"u-search",props:{shape:{type:String,default:"round"},bgColor:{type:String,default:"#f2f2f2"},placeholder:{type:String,default:"请输入关键字"},clearabled:{type:Boolean,default:!0},focus:{type:Boolean,default:!1},showAction:{type:Boolean,default:!0},actionStyle:{type:Object,default:function(){return{}}},actionText:{type:String,default:"搜索"},inputAlign:{type:String,default:"left"},disabled:{type:Boolean,default:!1},animation:{type:Boolean,default:!1},borderColor:{type:String,default:"none"},value:{type:String,default:""},height:{type:[Number,String],default:64},inputStyle:{type:Object,default:function(){return{}}},maxlength:{type:[Number,String],default:"-1"},searchIconColor:{type:String,default:""},color:{type:String,default:"#606266"},placeholderColor:{type:String,default:"#909399"},margin:{type:String,default:"0"},searchIcon:{type:String,default:"search"}},data:function(){return{keyword:"",showClear:!1,show:!1,focused:this.focus}},watch:{keyword:function(e){this.$emit("input",e),this.$emit("change",e)},value:{immediate:!0,handler:function(e){this.keyword=e}}},computed:{showActionBtn:function(){return!(this.animation||!this.showAction)},borderStyle:function(){return this.borderColor?"1px solid ".concat(this.borderColor):"none"}},methods:{inputChange:function(e){this.keyword=e.detail.value},clear:function(){var e=this;this.keyword="",this.$nextTick((function(){e.$emit("clear")}))},search:function(e){this.$emit("search",e.detail.value);try{uni.hideKeyboard()}catch(e){}},custom:function(){this.$emit("custom",this.keyword);try{uni.hideKeyboard()}catch(e){}},getFocus:function(){this.focused=!0,this.animation&&this.showAction&&(this.show=!0),this.$emit("focus",this.keyword)},blur:function(){var e=this;setTimeout((function(){e.focused=!1}),100),this.show=!1,this.$emit("blur",this.keyword)},clickHandler:function(){this.disabled&&this.$emit("click")}}};t.default=n},faa0:function(e,t,o){"use strict";o.d(t,"b",(function(){return a})),o.d(t,"c",(function(){return r})),o.d(t,"a",(function(){return n}));var n={uCard:o("c1ca").default,uFormItem:o("555c").default,uSearch:o("0cfc").default,uIcon:o("034d").default,uTag:o("d258").default},a=function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("v-uni-view",[o("u-card",{attrs:{title:"热门搜索","foot-border-top":!1}},[o("v-uni-view",{attrs:{slot:"body"},slot:"body"},[o("u-form-item",[o("u-search",{attrs:{height:"80",clearabled:!0,"input-align":"center","show-action":!1},on:{search:function(t){arguments[0]=t=e.$handleEvent(t),e.doSearch.apply(void 0,arguments)}},model:{value:e.name,callback:function(t){e.name=t},expression:"name"}})],1),o("v-uni-view",{staticClass:"hot-keywords"},[e.oldKeywordList.length>0?o("v-uni-view",[e._v("历史搜索"),o("u-icon",{attrs:{name:"trash-fill",size:"30"},on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.oldDelete.apply(void 0,arguments)}}})],1):e._e(),e._l(e.oldKeywordList,(function(t,n){return o("u-tag",{key:n,attrs:{type:"warning",mode:"plain",size:"mini",text:t},on:{click:function(o){arguments[0]=o=e.$handleEvent(o),e.doSearch(t)}}})}))],2),o("v-uni-view",{staticClass:"hot-keywords"},[o("v-uni-view",[e._v("热门搜索"),o("u-icon",{attrs:{name:e.eyeName,size:"40"},on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.hotToggle.apply(void 0,arguments)}}})],1),e._l(e.hotKeyWords,(function(t,n){return e.togger?o("u-tag",{key:n,attrs:{size:"mini",type:"warning",mode:"plain",text:t},on:{click:function(o){arguments[0]=o=e.$handleEvent(o),e.doSearch(t)}}}):e._e()}))],2)],1)],1)],1)},r=[]},fba8:function(e,t,o){var n=o("e4ca");"string"===typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);var a=o("4f06").default;a("09a41cc6",n,!0,{sourceMap:!1,shadowMode:!1})}}]);