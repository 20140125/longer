(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2903b433"],{"04fb":function(e,t,i){"use strict";i.r(t);var n=i("7a23"),r=Object(n["withScopeId"])("data-v-a61d4458");Object(n["pushScopeId"])("data-v-a61d4458");var o={class:"message-box"},s={class:"message-content"},c={class:"message-input__item"},a=Object(n["createVNode"])("i",{class:"el-icon-picture-outline-round"},null,-1),u=Object(n["createVNode"])("i",{class:"el-icon-circle-plus-outline"},null,-1);Object(n["popScopeId"])();var f=r((function(e,t,i,f,l,h){var d=Object(n["resolveComponent"])("el-avatar"),p=Object(n["resolveComponent"])("HomeLayout");return Object(n["openBlock"])(),Object(n["createBlock"])(p,{"header-title":h.receiver.client_name,ref:"homeLayout"},{body:r((function(){return[Object(n["createVNode"])("div",o,[Object(n["createVNode"])("div",s,[(Object(n["openBlock"])(!0),Object(n["createBlock"])(n["Fragment"],null,Object(n["renderList"])(h.messageLists,(function(t,i){return Object(n["openBlock"])(),Object(n["createBlock"])("div",{key:i},[Object(n["createVNode"])("div",{class:["".concat(t.from_client_name===e.Permission.username?"self-state":""),"user-info__message"]},[Object(n["createVNode"])(d,{class:"".concat(t.from_client_name===e.Permission.username?"self-state":""),size:40,src:t.client_img},null,8,["class","src"]),Object(n["createVNode"])("div",{class:["".concat(t.from_client_name===e.Permission.username?"self-state":""),"content"],innerHTML:h.replaceContent(t.content)},null,10,["innerHTML"])],2)])})),128))]),Object(n["createVNode"])("div",c,[Object(n["createVNode"])("div",{ref:"message",class:"input-content",contentEditable:"true",onKeyup:t[1]||(t[1]=Object(n["withKeys"])((function(){return h.sendMessage&&h.sendMessage.apply(h,arguments)}),["enter"]))},null,544),a,u])])]})),_:1},8,["header-title"])})),l=i("1da1"),h=(i("96cf"),i("159b"),i("498a"),i("caad"),i("ac1f"),i("5319"),i("4d63"),i("25f0"),i("eb00")),d=i("6171"),p=i("b730"),v=i.n(p),g={name:"Message",components:{HomeLayout:h["a"]},data:function(){return{uploadData:{token:this.$store.state.baseLayout.token||window.localStorage.getItem("token"),file_type:"text",round_name:!0}}},computed:{messageLists:function(){var e=this,t=JSON.parse(JSON.stringify(this.$store.state.index.messageLists)),i=[];return t.forEach((function(t){t.to_client_id!==e.receiver.uuid&&t.from_client_id!==e.receiver.uuid||i.push(t)})),i},receiver:function(){return this.$store.state.index.receiver},userLists:function(){return this.$store.state.index.userLists}},mounted:function(){var e=this;this.$nextTick(Object(l["a"])(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(e.Permission){t.next=3;break}return t.next=3,e.$router.push({path:"/"});case 3:case"end":return t.stop()}}),t)}))))},methods:{sendMessage:function(){if(""===this.$refs.message.innerHTML.trim())return!1;var e={type:"say",to_client_id:this.receiver.uuid,to_client_name:this.receiver.client_name,from_client_name:this.Permission.username,from_client_id:this.Permission.uuid,client_img:this.Permission.avatar_url,content:this.$refs.message.innerHTML.trim(),room_id:this.receiver.room_id,uuid:this.receiver.uuid,time:Object(d["e"])(Date.parse(new Date))};this.$refs.homeLayout.sendMessage(JSON.stringify(e)),this.messageLists.push(e),this.$store.commit("index/UPDATE_MUTATIONS",{messageLists:this.messageLists}),setTimeout((function(){Object(d["c"])(".message-content")}),100),this.$refs.message.innerHTML=""},clickEmotion:function(e){this.$refs.message.innerHTML+="<img src='"+e.icon+"' width='30px' height='30px' style='object-fit: contain;' alt='"+e.title+"'>"},uploadSuccess:function(e){if(200===e.code)switch((((e||{}).item||{}).lists||{}).file_type){case"image":this.$refs.message.innerHTML+="<img src='"+(((e||{}).item||{}).lists||{}).src+"' width='100px' height='100px' style='object-fit: contain;' alt='"+this.userInfo.username+"'>";break;case"video":this.$refs.message.innerHTML+="<video src='"+(((e||{}).item||{}).lists||{}).src+"' width='200px' height='200px' autoplay controls='controls'>";break;default:this.$refs.message.innerHTML+="";break}},beforeUpload:function(e){return["image/jpeg","image/jpg","image/png","image/gif"].includes(e.type)&&(this.uploadData.file_type="image",e.size>2097152)?(this.$message.error("upload image size error"),!1):["audio/mp4","video/mp4"].includes(e.type)&&(this.uploadData.file_type="video",e.size>5242880)?(this.$message.error("upload video size error"),!1):void 0},pushMessage:function(e){v.a.create("你有未读消息",{body:e,requireInteraction:!0,icon:"https://www.fanglonger.com/favicon.ico",timeout:6e4})},pushNotice:function(e){var t=this;return Object(l["a"])(regeneratorRuntime.mark((function i(){return regeneratorRuntime.wrap((function(i){while(1)switch(i.prev=i.next){case 0:return i.next=2,t.$store.commit("index/UPDATE_MUTATIONS",{configuration:{notice:{timestamp:Date.parse(new Date)/1e3,message:e}}});case 2:case"end":return i.stop()}}),i)})))()},replaceContent:function(e){return e.replace(new RegExp(/&lt;/g),"<").replace(new RegExp(/&gt;/g),">").replace(new RegExp(/&quot;/g),'"')}}},m=i("d959"),_=i.n(m);const w=_()(g,[["render",f],["__scopeId","data-v-a61d4458"]]);t["default"]=w},"0cb2":function(e,t,i){var n=i("7b0b"),r=Math.floor,o="".replace,s=/\$([$&'`]|\d{1,2}|<[^>]*>)/g,c=/\$([$&'`]|\d{1,2})/g;e.exports=function(e,t,i,a,u,f){var l=i+e.length,h=a.length,d=c;return void 0!==u&&(u=n(u),d=s),o.call(f,d,(function(n,o){var s;switch(o.charAt(0)){case"$":return"$";case"&":return e;case"`":return t.slice(0,i);case"'":return t.slice(l);case"<":s=u[o.slice(1,-1)];break;default:var c=+o;if(0===c)return n;if(c>h){var f=r(c/10);return 0===f?n:f<=h?void 0===a[f-1]?o.charAt(1):a[f-1]+o.charAt(1):n}s=a[c-1]}return void 0===s?"":s}))}},"498a":function(e,t,i){"use strict";var n=i("23e7"),r=i("58a8").trim,o=i("c8d2");n({target:"String",proto:!0,forced:o("trim")},{trim:function(){return r(this)}})},"4d63":function(e,t,i){var n=i("83ab"),r=i("da84"),o=i("94ca"),s=i("7156"),c=i("9112"),a=i("9bf2").f,u=i("241c").f,f=i("44e7"),l=i("ad6d"),h=i("9f7f"),d=i("6eeb"),p=i("d039"),v=i("5135"),g=i("69f3").enforce,m=i("2626"),_=i("b622"),w=i("fce3"),b=i("107c"),y=_("match"),k=r.RegExp,N=k.prototype,O=/^\?<[^\s\d!#%&*+<=>@^][^\s!#%&*+<=>@^]*>/,S=/a/g,x=/a/g,E=new k(S)!==S,j=h.UNSUPPORTED_Y,P=n&&(!E||j||w||b||p((function(){return x[y]=!1,k(S)!=S||k(x)==x||"/a/i"!=k(S,"i")}))),L=function(e){for(var t,i=e.length,n=0,r="",o=!1;n<=i;n++)t=e.charAt(n),"\\"!==t?o||"."!==t?("["===t?o=!0:"]"===t&&(o=!1),r+=t):r+="[\\s\\S]":r+=t+e.charAt(++n);return r},T=function(e){for(var t,i=e.length,n=0,r="",o=[],s={},c=!1,a=!1,u=0,f="";n<=i;n++){if(t=e.charAt(n),"\\"===t)t+=e.charAt(++n);else if("]"===t)c=!1;else if(!c)switch(!0){case"["===t:c=!0;break;case"("===t:O.test(e.slice(n+1))&&(n+=2,a=!0),r+=t,u++;continue;case">"===t&&a:if(""===f||v(s,f))throw new SyntaxError("Invalid capture group name");s[f]=!0,o.push([f,u]),a=!1,f="";continue}a?f+=t:r+=t}return[r,o]};if(o("RegExp",P)){for(var $=function(e,t){var i,n,r,o,a,u,h,d=this instanceof $,p=f(e),v=void 0===t,m=[];if(!d&&p&&e.constructor===$&&v)return e;if(E?p&&!v&&(e=e.source):e instanceof $&&(v&&(t=l.call(e)),e=e.source),e=void 0===e?"":String(e),t=void 0===t?"":String(t),i=e,w&&"dotAll"in S&&(r=!!t&&t.indexOf("s")>-1,r&&(t=t.replace(/s/g,""))),n=t,j&&"sticky"in S&&(o=!!t&&t.indexOf("y")>-1,o&&(t=t.replace(/y/g,""))),b&&(a=T(e),e=a[0],m=a[1]),u=s(E?new k(e,t):k(e,t),d?this:N,$),(r||o||m.length)&&(h=g(u),r&&(h.dotAll=!0,h.raw=$(L(e),n)),o&&(h.sticky=!0),m.length&&(h.groups=m)),e!==i)try{c(u,"source",""===i?"(?:)":i)}catch(_){}return u},M=function(e){e in $||a($,e,{configurable:!0,get:function(){return k[e]},set:function(t){k[e]=t}})},I=u(k),A=0;I.length>A;)M(I[A++]);N.constructor=$,$.prototype=N,d(r,"RegExp",$)}m("RegExp")},5319:function(e,t,i){"use strict";var n=i("d784"),r=i("d039"),o=i("825a"),s=i("50c4"),c=i("a691"),a=i("1d80"),u=i("8aa5"),f=i("0cb2"),l=i("14c3"),h=i("b622"),d=h("replace"),p=Math.max,v=Math.min,g=function(e){return void 0===e?e:String(e)},m=function(){return"$0"==="a".replace(/./,"$0")}(),_=function(){return!!/./[d]&&""===/./[d]("a","$0")}(),w=!r((function(){var e=/./;return e.exec=function(){var e=[];return e.groups={a:"7"},e},"7"!=="".replace(e,"$<a>")}));n("replace",(function(e,t,i){var n=_?"$":"$0";return[function(e,i){var n=a(this),r=void 0==e?void 0:e[d];return void 0!==r?r.call(e,n,i):t.call(String(n),e,i)},function(e,r){if("string"===typeof r&&-1===r.indexOf(n)&&-1===r.indexOf("$<")){var a=i(t,this,e,r);if(a.done)return a.value}var h=o(this),d=String(e),m="function"===typeof r;m||(r=String(r));var _=h.global;if(_){var w=h.unicode;h.lastIndex=0}var b=[];while(1){var y=l(h,d);if(null===y)break;if(b.push(y),!_)break;var k=String(y[0]);""===k&&(h.lastIndex=u(d,s(h.lastIndex),w))}for(var N="",O=0,S=0;S<b.length;S++){y=b[S];for(var x=String(y[0]),E=p(v(c(y.index),d.length),0),j=[],P=1;P<y.length;P++)j.push(g(y[P]));var L=y.groups;if(m){var T=[x].concat(j,E,d);void 0!==L&&T.push(L);var $=String(r.apply(void 0,T))}else $=f(x,d,E,j,L,r);E>=O&&(N+=d.slice(O,E)+$,O=E+x.length)}return N+d.slice(O)}]}),!w||!m||_)},b730:function(e,t,i){(function(t){
/**
 * @license
 *
 * Push v1.0.9
 * =========
 * A compact, cross-browser solution for the JavaScript Notifications API
 *
 * Credits
 * -------
 * Tsvetan Tsvetkov (ttsvetko)
 * Alex Gibson (alexgibson)
 *
 * License
 * -------
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2015-2017 Tyler Nickerson
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
!function(t,i){e.exports=i()}(0,(function(){"use strict";var e={errors:{incompatible:"".concat("PushError:"," Push.js is incompatible with browser."),invalid_plugin:"".concat("PushError:"," plugin class missing from plugin manifest (invalid plugin). Please check the documentation."),invalid_title:"".concat("PushError:"," title of notification must be a string"),permission_denied:"".concat("PushError:"," permission request declined"),sw_notification_error:"".concat("PushError:"," could not show a ServiceWorker notification due to the following reason: "),sw_registration_error:"".concat("PushError:"," could not register the ServiceWorker due to the following reason: "),unknown_interface:"".concat("PushError:"," unable to create notification: unknown interface")}};function i(e){return(i="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function n(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function r(e,t){for(var i=0;i<t.length;i++){var n=t[i];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}function o(e,t,i){return t&&r(e.prototype,t),i&&r(e,i),e}function s(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&a(e,t)}function c(e){return(c=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}function a(e,t){return(a=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function u(e,t){return!t||"object"!=typeof t&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}var f=function(){function e(t){n(this,e),this._win=t,this.GRANTED="granted",this.DEFAULT="default",this.DENIED="denied",this._permissions=[this.GRANTED,this.DEFAULT,this.DENIED]}return o(e,[{key:"request",value:function(e,t){return arguments.length>0?this._requestWithCallback.apply(this,arguments):this._requestAsPromise()}},{key:"_requestWithCallback",value:function(e,t){var i,n=this,r=this.get(),o=!1,s=function(){var i=arguments.length>0&&void 0!==arguments[0]?arguments[0]:n._win.Notification.permission;o||(o=!0,void 0===i&&n._win.webkitNotifications&&(i=n._win.webkitNotifications.checkPermission()),i===n.GRANTED||0===i?e&&e():t&&t())};r!==this.DEFAULT?s(r):this._win.webkitNotifications&&this._win.webkitNotifications.checkPermission?this._win.webkitNotifications.requestPermission(s):this._win.Notification&&this._win.Notification.requestPermission?(i=this._win.Notification.requestPermission(s))&&i.then&&i.then(s).catch((function(){t&&t()})):e&&e()}},{key:"_requestAsPromise",value:function(){var e=this,t=this.get(),i=t!==this.DEFAULT,n=this._win.Notification&&this._win.Notification.requestPermission,r=this._win.webkitNotifications&&this._win.webkitNotifications.checkPermission;return new Promise((function(o,s){var c,a=!1,u=function(t){a||(a=!0,function(t){return t===e.GRANTED||0===t}(t)?o():s())};i?u(t):r?e._win.webkitNotifications.requestPermission((function(e){u(e)})):n?(c=e._win.Notification.requestPermission(u))&&c.then&&c.then(u).catch(s):o()}))}},{key:"has",value:function(){return this.get()===this.GRANTED}},{key:"get",value:function(){return this._win.Notification&&this._win.Notification.permission?this._win.Notification.permission:this._win.webkitNotifications&&this._win.webkitNotifications.checkPermission?this._permissions[this._win.webkitNotifications.checkPermission()]:navigator.mozNotification?this.GRANTED:this._win.external&&this._win.external.msIsSiteMode?this._win.external.msIsSiteMode()?this.GRANTED:this.DEFAULT:this.GRANTED}}]),e}(),l=function(){function e(){n(this,e)}return o(e,null,[{key:"isUndefined",value:function(e){return void 0===e}},{key:"isNull",value:function(e){return null===obj}},{key:"isString",value:function(e){return"string"==typeof e}},{key:"isFunction",value:function(e){return e&&"[object Function]"==={}.toString.call(e)}},{key:"isObject",value:function(e){return"object"===i(e)}},{key:"objectMerge",value:function(e,t){for(var i in t)e.hasOwnProperty(i)&&this.isObject(e[i])&&this.isObject(t[i])?this.objectMerge(e[i],t[i]):e[i]=t[i]}}]),e}(),h=function e(t){n(this,e),this._win=t},d=function(e){function t(){return n(this,t),u(this,c(t).apply(this,arguments))}return s(t,h),o(t,[{key:"isSupported",value:function(){return void 0!==this._win.Notification}},{key:"create",value:function(e,t){return new this._win.Notification(e,{icon:l.isString(t.icon)||l.isUndefined(t.icon)||l.isNull(t.icon)?t.icon:t.icon.x32,body:t.body,tag:t.tag,requireInteraction:t.requireInteraction})}},{key:"close",value:function(e){e.close()}}]),t}(),p=function(t){function i(){return n(this,i),u(this,c(i).apply(this,arguments))}return s(i,h),o(i,[{key:"isSupported",value:function(){return void 0!==this._win.navigator&&void 0!==this._win.navigator.serviceWorker}},{key:"getFunctionBody",value:function(e){var t=e.toString().match(/function[^{]+{([\s\S]*)}$/);return null!=t&&t.length>1?t[1]:null}},{key:"create",value:function(t,i,n,r,o){var s=this;this._win.navigator.serviceWorker.register(r),this._win.navigator.serviceWorker.ready.then((function(r){var c={id:t,link:n.link,origin:document.location.href,onClick:l.isFunction(n.onClick)?s.getFunctionBody(n.onClick):"",onClose:l.isFunction(n.onClose)?s.getFunctionBody(n.onClose):""};void 0!==n.data&&null!==n.data&&(c=Object.assign(c,n.data)),r.showNotification(i,{icon:n.icon,body:n.body,vibrate:n.vibrate,tag:n.tag,data:c,requireInteraction:n.requireInteraction,silent:n.silent}).then((function(){r.getNotifications().then((function(e){r.active.postMessage(""),o(e)}))})).catch((function(t){throw new Error(e.errors.sw_notification_error+t.message)}))})).catch((function(t){throw new Error(e.errors.sw_registration_error+t.message)}))}},{key:"close",value:function(){}}]),i}(),v=function(e){function t(){return n(this,t),u(this,c(t).apply(this,arguments))}return s(t,h),o(t,[{key:"isSupported",value:function(){return void 0!==this._win.navigator.mozNotification}},{key:"create",value:function(e,t){var i=this._win.navigator.mozNotification.createNotification(e,t.body,t.icon);return i.show(),i}}]),t}(),g=function(e){function t(){return n(this,t),u(this,c(t).apply(this,arguments))}return s(t,h),o(t,[{key:"isSupported",value:function(){return void 0!==this._win.external&&void 0!==this._win.external.msIsSiteMode}},{key:"create",value:function(e,t){return this._win.external.msSiteModeClearIconOverlay(),this._win.external.msSiteModeSetIconOverlay(l.isString(t.icon)||l.isUndefined(t.icon)?t.icon:t.icon.x16,e),this._win.external.msSiteModeActivate(),null}},{key:"close",value:function(){this._win.external.msSiteModeClearIconOverlay()}}]),t}(),m=function(e){function t(){return n(this,t),u(this,c(t).apply(this,arguments))}return s(t,h),o(t,[{key:"isSupported",value:function(){return void 0!==this._win.webkitNotifications}},{key:"create",value:function(e,t){var i=this._win.webkitNotifications.createNotification(t.icon,e,t.body);return i.show(),i}},{key:"close",value:function(e){e.cancel()}}]),t}();return new(function(){function t(e){n(this,t),this._currentId=0,this._notifications={},this._win=e,this.Permission=new f(e),this._agents={desktop:new d(e),chrome:new p(e),firefox:new v(e),ms:new g(e),webkit:new m(e)},this._configuration={serviceWorker:"/serviceWorker.min.js",fallback:function(e){}}}return o(t,[{key:"_closeNotification",value:function(t){var i=!0,n=this._notifications[t];if(void 0!==n){if(i=this._removeNotification(t),this._agents.desktop.isSupported())this._agents.desktop.close(n);else if(this._agents.webkit.isSupported())this._agents.webkit.close(n);else{if(!this._agents.ms.isSupported())throw i=!1,new Error(e.errors.unknown_interface);this._agents.ms.close()}return i}return!1}},{key:"_addNotification",value:function(e){var t=this._currentId;return this._notifications[t]=e,this._currentId++,t}},{key:"_removeNotification",value:function(e){var t=!1;return this._notifications.hasOwnProperty(e)&&(delete this._notifications[e],t=!0),t}},{key:"_prepareNotification",value:function(e,t){var i,n=this;return i={get:function(){return n._notifications[e]},close:function(){n._closeNotification(e)}},t.timeout&&setTimeout((function(){i.close()}),t.timeout),i}},{key:"_serviceWorkerCallback",value:function(e,t,i){var n=this,r=this._addNotification(e[e.length-1]);navigator&&navigator.serviceWorker&&(navigator.serviceWorker.addEventListener("message",(function(e){var t=JSON.parse(e.data);"close"===t.action&&Number.isInteger(t.id)&&n._removeNotification(t.id)})),i(this._prepareNotification(r,t))),i(null)}},{key:"_createCallback",value:function(e,t,i){var n,r=this,o=null;if(t=t||{},n=function(e){r._removeNotification(e),l.isFunction(t.onClose)&&t.onClose.call(r,o)},this._agents.desktop.isSupported())try{o=this._agents.desktop.create(e,t)}catch(n){var s=this._currentId,c=this.config().serviceWorker;this._agents.chrome.isSupported()&&this._agents.chrome.create(s,e,t,c,(function(e){return r._serviceWorkerCallback(e,t,i)}))}else this._agents.webkit.isSupported()?o=this._agents.webkit.create(e,t):this._agents.firefox.isSupported()?this._agents.firefox.create(e,t):this._agents.ms.isSupported()?o=this._agents.ms.create(e,t):(t.title=e,this.config().fallback(t));if(null!==o){var a=this._addNotification(o),u=this._prepareNotification(a,t);l.isFunction(t.onShow)&&o.addEventListener("show",t.onShow),l.isFunction(t.onError)&&o.addEventListener("error",t.onError),l.isFunction(t.onClick)&&o.addEventListener("click",t.onClick),o.addEventListener("close",(function(){n(a)})),o.addEventListener("cancel",(function(){n(a)})),i(u)}i(null)}},{key:"create",value:function(t,i){var n,r=this;if(!l.isString(t))throw new Error(e.errors.invalid_title);return n=this.Permission.has()?function(e,n){try{r._createCallback(t,i,e)}catch(e){n(e)}}:function(n,o){r.Permission.request().then((function(){r._createCallback(t,i,n)})).catch((function(){o(e.errors.permission_denied)}))},new Promise(n)}},{key:"count",value:function(){var e,t=0;for(e in this._notifications)this._notifications.hasOwnProperty(e)&&t++;return t}},{key:"close",value:function(e){var t;for(t in this._notifications)if(this._notifications.hasOwnProperty(t)&&this._notifications[t].tag===e)return this._closeNotification(t)}},{key:"clear",value:function(){var e,t=!0;for(e in this._notifications)this._notifications.hasOwnProperty(e)&&(t=t&&this._closeNotification(e));return t}},{key:"supported",value:function(){var e=!1;for(var t in this._agents)this._agents.hasOwnProperty(t)&&(e=e||this._agents[t].isSupported());return e}},{key:"config",value:function(e){return(void 0!==e||null!==e&&l.isObject(e))&&l.objectMerge(this._configuration,e),this._configuration}},{key:"extend",value:function(t){var i,n={}.hasOwnProperty;if(!n.call(t,"plugin"))throw new Error(e.errors.invalid_plugin);for(var r in n.call(t,"config")&&l.isObject(t.config)&&null!==t.config&&this.config(t.config),i=new(0,t.plugin)(this.config()))n.call(i,r)&&l.isFunction(i[r])&&(this[r]=i[r])}}]),t}())("undefined"!=typeof window?window:t)}))}).call(this,i("c8ba"))},c8d2:function(e,t,i){var n=i("d039"),r=i("5899"),o="​᠎";e.exports=function(e){return n((function(){return!!r[e]()||o[e]()!=o||r[e].name!==e}))}}}]);
//# sourceMappingURL=chunk-2903b433.3a0d9a74.js.map