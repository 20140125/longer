(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2903b433"],{"04fb":function(t,e,i){"use strict";i.r(e);var n=i("7a23"),o=Object(n["withScopeId"])("data-v-2b52df76");Object(n["pushScopeId"])("data-v-2b52df76");var r={class:"message-input__item"},s=Object(n["createTextVNode"])("发送");Object(n["popScopeId"])();var c=o((function(t,e,i,c,a,u){var f=Object(n["resolveComponent"])("el-avatar"),l=Object(n["resolveComponent"])("el-button"),h=Object(n["resolveComponent"])("HomeLayout");return Object(n["openBlock"])(),Object(n["createBlock"])(h,{ref:"homeLayout","header-title":u.receiver.client_name,"connect-web-socket":!0},{body:o((function(){return[Object(n["createVNode"])("div",{style:a.cssStyle.box,class:"message-box"},[Object(n["createVNode"])("div",{style:a.cssStyle.content,class:"message-content"},[(Object(n["openBlock"])(!0),Object(n["createBlock"])(n["Fragment"],null,Object(n["renderList"])(u.messageLists,(function(e,i){return Object(n["openBlock"])(),Object(n["createBlock"])("div",{key:i},[Object(n["createVNode"])("div",{class:["".concat(e.from_client_name===t.Permission.username?"self-state":""),"user-info__message"]},[Object(n["createVNode"])(f,{class:"".concat(e.from_client_name===t.Permission.username?"self-state":""),size:40,src:e.client_img},null,8,["class","src"]),Object(n["createVNode"])("div",{class:["".concat(e.from_client_name===t.Permission.username?"self-state":""),"content"],innerHTML:u.replaceContent(e.content)},null,10,["innerHTML"])],2)])})),128))],4),Object(n["createVNode"])("div",r,[Object(n["createVNode"])("div",{ref:"message",class:"input-content",contentEditable:"true",onKeydown:e[1]||(e[1]=function(){return u.inputMessage&&u.inputMessage.apply(u,arguments)})},null,544),a.showSendButton?(Object(n["openBlock"])(),Object(n["createBlock"])(l,{key:0,type:"primary",size:"medium",onClick:u.sendMessage},{default:o((function(){return[s]})),_:1},8,["onClick"])):Object(n["createCommentVNode"])("",!0)])],4)]})),_:1},8,["header-title"])})),a=i("1da1"),u=(i("96cf"),i("159b"),i("498a"),i("ac1f"),i("5319"),i("4d63"),i("25f0"),i("eb00")),f=i("6171"),l=i("b730"),h=i.n(l),d={name:"Message",components:{HomeLayout:u["a"]},data:function(){return{uploadData:{token:this.$store.state.baseLayout.token,file_type:"text",round_name:!0},cssStyle:{box:"",content:""},showSendButton:!1}},computed:{messageLists:function(){var t=this,e=JSON.parse(JSON.stringify(this.$store.state.index.messageLists)),i=[];return e.forEach((function(e){e.to_client_id!==t.receiver.uuid&&e.from_client_id!==t.receiver.uuid||i.push(e)})),i},receiver:function(){return this.$store.state.index.receiver},userLists:function(){return this.$store.state.index.userLists}},mounted:function(){var t=this;this.$nextTick(Object(a["a"])(regeneratorRuntime.mark((function e(){return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:if(t.Permission){e.next=3;break}return e.next=3,t.$router.push({path:"/"});case 3:case"end":return e.stop()}}),e)}))))},created:function(){var t=document.documentElement.scrollHeight||document.body.scrollHeight;this.cssStyle.box="height: ".concat(t-120,"px"),this.cssStyle.content="height: ".concat(t-200,"px")},methods:{inputMessage:function(){this.showSendButton=""!==this.$refs.message.innerHTML.trim()},sendMessage:function(){if(""===this.$refs.message.innerHTML.trim())return!1;var t={type:"say",to_client_id:this.receiver.uuid,to_client_name:this.receiver.client_name,from_client_name:this.Permission.username,from_client_id:this.Permission.uuid,client_img:this.Permission.avatar_url,content:this.$refs.message.innerHTML.trim().replace("<div><br></div>",""),room_id:this.receiver.room_id,uuid:this.receiver.uuid,time:Object(f["g"])(Date.parse(new Date))};this.$refs.homeLayout.sendMessage(JSON.stringify(t)),this.messageLists.push(t),this.$store.commit("index/UPDATE_MUTATIONS",{messageLists:this.messageLists}),setTimeout((function(){Object(f["e"])(".message-content")}),100),this.$refs.message.innerHTML="",this.inputMessage()},pushMessage:function(t){h.a.create("你有未读消息",{body:t,requireInteraction:!0,icon:"https://www.fanglonger.com/favicon.ico",timeout:6e4})},replaceContent:function(t){return t.replace(new RegExp(/&lt;/g),"<").replace(new RegExp(/&gt;/g),">").replace(new RegExp(/&quot;/g),'"')}}},v=i("6b0d"),p=i.n(v);const g=p()(d,[["render",c],["__scopeId","data-v-2b52df76"]]);e["default"]=g},"0cb2":function(t,e,i){var n=i("7b0b"),o=Math.floor,r="".replace,s=/\$([$&'`]|\d{1,2}|<[^>]*>)/g,c=/\$([$&'`]|\d{1,2})/g;t.exports=function(t,e,i,a,u,f){var l=i+t.length,h=a.length,d=c;return void 0!==u&&(u=n(u),d=s),r.call(f,d,(function(n,r){var s;switch(r.charAt(0)){case"$":return"$";case"&":return t;case"`":return e.slice(0,i);case"'":return e.slice(l);case"<":s=u[r.slice(1,-1)];break;default:var c=+r;if(0===c)return n;if(c>h){var f=o(c/10);return 0===f?n:f<=h?void 0===a[f-1]?r.charAt(1):a[f-1]+r.charAt(1):n}s=a[c-1]}return void 0===s?"":s}))}},"498a":function(t,e,i){"use strict";var n=i("23e7"),o=i("58a8").trim,r=i("c8d2");n({target:"String",proto:!0,forced:r("trim")},{trim:function(){return o(this)}})},"4d63":function(t,e,i){var n=i("83ab"),o=i("da84"),r=i("94ca"),s=i("7156"),c=i("9112"),a=i("9bf2").f,u=i("241c").f,f=i("44e7"),l=i("ad6d"),h=i("9f7f"),d=i("6eeb"),v=i("d039"),p=i("5135"),g=i("69f3").enforce,_=i("2626"),m=i("b622"),w=i("fce3"),b=i("107c"),y=m("match"),k=o.RegExp,N=k.prototype,S=/^\?<[^\s\d!#%&*+<=>@^][^\s!#%&*+<=>@^]*>/,O=/a/g,x=/a/g,E=new k(O)!==O,P=h.UNSUPPORTED_Y,j=n&&(!E||P||w||b||v((function(){return x[y]=!1,k(O)!=O||k(x)==x||"/a/i"!=k(O,"i")}))),L=function(t){for(var e,i=t.length,n=0,o="",r=!1;n<=i;n++)e=t.charAt(n),"\\"!==e?r||"."!==e?("["===e?r=!0:"]"===e&&(r=!1),o+=e):o+="[\\s\\S]":o+=e+t.charAt(++n);return o},M=function(t){for(var e,i=t.length,n=0,o="",r=[],s={},c=!1,a=!1,u=0,f="";n<=i;n++){if(e=t.charAt(n),"\\"===e)e+=t.charAt(++n);else if("]"===e)c=!1;else if(!c)switch(!0){case"["===e:c=!0;break;case"("===e:S.test(t.slice(n+1))&&(n+=2,a=!0),o+=e,u++;continue;case">"===e&&a:if(""===f||p(s,f))throw new SyntaxError("Invalid capture group name");s[f]=!0,r.push([f,u]),a=!1,f="";continue}a?f+=e:o+=e}return[o,r]};if(r("RegExp",j)){for(var T=function(t,e){var i,n,o,r,a,u,h,d=this instanceof T,v=f(t),p=void 0===e,_=[];if(!d&&v&&t.constructor===T&&p)return t;if(E?v&&!p&&(t=t.source):t instanceof T&&(p&&(e=l.call(t)),t=t.source),t=void 0===t?"":String(t),e=void 0===e?"":String(e),i=t,w&&"dotAll"in O&&(o=!!e&&e.indexOf("s")>-1,o&&(e=e.replace(/s/g,""))),n=e,P&&"sticky"in O&&(r=!!e&&e.indexOf("y")>-1,r&&(e=e.replace(/y/g,""))),b&&(a=M(t),t=a[0],_=a[1]),u=s(E?new k(t,e):k(t,e),d?this:N,T),(o||r||_.length)&&(h=g(u),o&&(h.dotAll=!0,h.raw=T(L(t),n)),r&&(h.sticky=!0),_.length&&(h.groups=_)),t!==i)try{c(u,"source",""===i?"(?:)":i)}catch(m){}return u},C=function(t){t in T||a(T,t,{configurable:!0,get:function(){return k[t]},set:function(e){k[t]=e}})},A=u(k),I=0;A.length>I;)C(A[I++]);N.constructor=T,T.prototype=N,d(o,"RegExp",T)}_("RegExp")},5319:function(t,e,i){"use strict";var n=i("d784"),o=i("d039"),r=i("825a"),s=i("50c4"),c=i("a691"),a=i("1d80"),u=i("8aa5"),f=i("0cb2"),l=i("14c3"),h=i("b622"),d=h("replace"),v=Math.max,p=Math.min,g=function(t){return void 0===t?t:String(t)},_=function(){return"$0"==="a".replace(/./,"$0")}(),m=function(){return!!/./[d]&&""===/./[d]("a","$0")}(),w=!o((function(){var t=/./;return t.exec=function(){var t=[];return t.groups={a:"7"},t},"7"!=="".replace(t,"$<a>")}));n("replace",(function(t,e,i){var n=m?"$":"$0";return[function(t,i){var n=a(this),o=void 0==t?void 0:t[d];return void 0!==o?o.call(t,n,i):e.call(String(n),t,i)},function(t,o){if("string"===typeof o&&-1===o.indexOf(n)&&-1===o.indexOf("$<")){var a=i(e,this,t,o);if(a.done)return a.value}var h=r(this),d=String(t),_="function"===typeof o;_||(o=String(o));var m=h.global;if(m){var w=h.unicode;h.lastIndex=0}var b=[];while(1){var y=l(h,d);if(null===y)break;if(b.push(y),!m)break;var k=String(y[0]);""===k&&(h.lastIndex=u(d,s(h.lastIndex),w))}for(var N="",S=0,O=0;O<b.length;O++){y=b[O];for(var x=String(y[0]),E=v(p(c(y.index),d.length),0),P=[],j=1;j<y.length;j++)P.push(g(y[j]));var L=y.groups;if(_){var M=[x].concat(P,E,d);void 0!==L&&M.push(L);var T=String(o.apply(void 0,M))}else T=f(x,d,E,P,L,o);E>=S&&(N+=d.slice(S,E)+T,S=E+x.length)}return N+d.slice(S)}]}),!w||!_||m)},b730:function(t,e,i){(function(e){
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
!function(e,i){t.exports=i()}(0,(function(){"use strict";var t={errors:{incompatible:"".concat("PushError:"," Push.js is incompatible with browser."),invalid_plugin:"".concat("PushError:"," plugin class missing from plugin manifest (invalid plugin). Please check the documentation."),invalid_title:"".concat("PushError:"," title of notification must be a string"),permission_denied:"".concat("PushError:"," permission request declined"),sw_notification_error:"".concat("PushError:"," could not show a ServiceWorker notification due to the following reason: "),sw_registration_error:"".concat("PushError:"," could not register the ServiceWorker due to the following reason: "),unknown_interface:"".concat("PushError:"," unable to create notification: unknown interface")}};function i(t){return(i="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function n(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function o(t,e){for(var i=0;i<e.length;i++){var n=e[i];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}function r(t,e,i){return e&&o(t.prototype,e),i&&o(t,i),t}function s(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&a(t,e)}function c(t){return(c=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}function a(t,e){return(a=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t})(t,e)}function u(t,e){return!e||"object"!=typeof e&&"function"!=typeof e?function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t):e}var f=function(){function t(e){n(this,t),this._win=e,this.GRANTED="granted",this.DEFAULT="default",this.DENIED="denied",this._permissions=[this.GRANTED,this.DEFAULT,this.DENIED]}return r(t,[{key:"request",value:function(t,e){return arguments.length>0?this._requestWithCallback.apply(this,arguments):this._requestAsPromise()}},{key:"_requestWithCallback",value:function(t,e){var i,n=this,o=this.get(),r=!1,s=function(){var i=arguments.length>0&&void 0!==arguments[0]?arguments[0]:n._win.Notification.permission;r||(r=!0,void 0===i&&n._win.webkitNotifications&&(i=n._win.webkitNotifications.checkPermission()),i===n.GRANTED||0===i?t&&t():e&&e())};o!==this.DEFAULT?s(o):this._win.webkitNotifications&&this._win.webkitNotifications.checkPermission?this._win.webkitNotifications.requestPermission(s):this._win.Notification&&this._win.Notification.requestPermission?(i=this._win.Notification.requestPermission(s))&&i.then&&i.then(s).catch((function(){e&&e()})):t&&t()}},{key:"_requestAsPromise",value:function(){var t=this,e=this.get(),i=e!==this.DEFAULT,n=this._win.Notification&&this._win.Notification.requestPermission,o=this._win.webkitNotifications&&this._win.webkitNotifications.checkPermission;return new Promise((function(r,s){var c,a=!1,u=function(e){a||(a=!0,function(e){return e===t.GRANTED||0===e}(e)?r():s())};i?u(e):o?t._win.webkitNotifications.requestPermission((function(t){u(t)})):n?(c=t._win.Notification.requestPermission(u))&&c.then&&c.then(u).catch(s):r()}))}},{key:"has",value:function(){return this.get()===this.GRANTED}},{key:"get",value:function(){return this._win.Notification&&this._win.Notification.permission?this._win.Notification.permission:this._win.webkitNotifications&&this._win.webkitNotifications.checkPermission?this._permissions[this._win.webkitNotifications.checkPermission()]:navigator.mozNotification?this.GRANTED:this._win.external&&this._win.external.msIsSiteMode?this._win.external.msIsSiteMode()?this.GRANTED:this.DEFAULT:this.GRANTED}}]),t}(),l=function(){function t(){n(this,t)}return r(t,null,[{key:"isUndefined",value:function(t){return void 0===t}},{key:"isNull",value:function(t){return null===obj}},{key:"isString",value:function(t){return"string"==typeof t}},{key:"isFunction",value:function(t){return t&&"[object Function]"==={}.toString.call(t)}},{key:"isObject",value:function(t){return"object"===i(t)}},{key:"objectMerge",value:function(t,e){for(var i in e)t.hasOwnProperty(i)&&this.isObject(t[i])&&this.isObject(e[i])?this.objectMerge(t[i],e[i]):t[i]=e[i]}}]),t}(),h=function t(e){n(this,t),this._win=e},d=function(t){function e(){return n(this,e),u(this,c(e).apply(this,arguments))}return s(e,h),r(e,[{key:"isSupported",value:function(){return void 0!==this._win.Notification}},{key:"create",value:function(t,e){return new this._win.Notification(t,{icon:l.isString(e.icon)||l.isUndefined(e.icon)||l.isNull(e.icon)?e.icon:e.icon.x32,body:e.body,tag:e.tag,requireInteraction:e.requireInteraction})}},{key:"close",value:function(t){t.close()}}]),e}(),v=function(e){function i(){return n(this,i),u(this,c(i).apply(this,arguments))}return s(i,h),r(i,[{key:"isSupported",value:function(){return void 0!==this._win.navigator&&void 0!==this._win.navigator.serviceWorker}},{key:"getFunctionBody",value:function(t){var e=t.toString().match(/function[^{]+{([\s\S]*)}$/);return null!=e&&e.length>1?e[1]:null}},{key:"create",value:function(e,i,n,o,r){var s=this;this._win.navigator.serviceWorker.register(o),this._win.navigator.serviceWorker.ready.then((function(o){var c={id:e,link:n.link,origin:document.location.href,onClick:l.isFunction(n.onClick)?s.getFunctionBody(n.onClick):"",onClose:l.isFunction(n.onClose)?s.getFunctionBody(n.onClose):""};void 0!==n.data&&null!==n.data&&(c=Object.assign(c,n.data)),o.showNotification(i,{icon:n.icon,body:n.body,vibrate:n.vibrate,tag:n.tag,data:c,requireInteraction:n.requireInteraction,silent:n.silent}).then((function(){o.getNotifications().then((function(t){o.active.postMessage(""),r(t)}))})).catch((function(e){throw new Error(t.errors.sw_notification_error+e.message)}))})).catch((function(e){throw new Error(t.errors.sw_registration_error+e.message)}))}},{key:"close",value:function(){}}]),i}(),p=function(t){function e(){return n(this,e),u(this,c(e).apply(this,arguments))}return s(e,h),r(e,[{key:"isSupported",value:function(){return void 0!==this._win.navigator.mozNotification}},{key:"create",value:function(t,e){var i=this._win.navigator.mozNotification.createNotification(t,e.body,e.icon);return i.show(),i}}]),e}(),g=function(t){function e(){return n(this,e),u(this,c(e).apply(this,arguments))}return s(e,h),r(e,[{key:"isSupported",value:function(){return void 0!==this._win.external&&void 0!==this._win.external.msIsSiteMode}},{key:"create",value:function(t,e){return this._win.external.msSiteModeClearIconOverlay(),this._win.external.msSiteModeSetIconOverlay(l.isString(e.icon)||l.isUndefined(e.icon)?e.icon:e.icon.x16,t),this._win.external.msSiteModeActivate(),null}},{key:"close",value:function(){this._win.external.msSiteModeClearIconOverlay()}}]),e}(),_=function(t){function e(){return n(this,e),u(this,c(e).apply(this,arguments))}return s(e,h),r(e,[{key:"isSupported",value:function(){return void 0!==this._win.webkitNotifications}},{key:"create",value:function(t,e){var i=this._win.webkitNotifications.createNotification(e.icon,t,e.body);return i.show(),i}},{key:"close",value:function(t){t.cancel()}}]),e}();return new(function(){function e(t){n(this,e),this._currentId=0,this._notifications={},this._win=t,this.Permission=new f(t),this._agents={desktop:new d(t),chrome:new v(t),firefox:new p(t),ms:new g(t),webkit:new _(t)},this._configuration={serviceWorker:"/serviceWorker.min.js",fallback:function(t){}}}return r(e,[{key:"_closeNotification",value:function(e){var i=!0,n=this._notifications[e];if(void 0!==n){if(i=this._removeNotification(e),this._agents.desktop.isSupported())this._agents.desktop.close(n);else if(this._agents.webkit.isSupported())this._agents.webkit.close(n);else{if(!this._agents.ms.isSupported())throw i=!1,new Error(t.errors.unknown_interface);this._agents.ms.close()}return i}return!1}},{key:"_addNotification",value:function(t){var e=this._currentId;return this._notifications[e]=t,this._currentId++,e}},{key:"_removeNotification",value:function(t){var e=!1;return this._notifications.hasOwnProperty(t)&&(delete this._notifications[t],e=!0),e}},{key:"_prepareNotification",value:function(t,e){var i,n=this;return i={get:function(){return n._notifications[t]},close:function(){n._closeNotification(t)}},e.timeout&&setTimeout((function(){i.close()}),e.timeout),i}},{key:"_serviceWorkerCallback",value:function(t,e,i){var n=this,o=this._addNotification(t[t.length-1]);navigator&&navigator.serviceWorker&&(navigator.serviceWorker.addEventListener("message",(function(t){var e=JSON.parse(t.data);"close"===e.action&&Number.isInteger(e.id)&&n._removeNotification(e.id)})),i(this._prepareNotification(o,e))),i(null)}},{key:"_createCallback",value:function(t,e,i){var n,o=this,r=null;if(e=e||{},n=function(t){o._removeNotification(t),l.isFunction(e.onClose)&&e.onClose.call(o,r)},this._agents.desktop.isSupported())try{r=this._agents.desktop.create(t,e)}catch(n){var s=this._currentId,c=this.config().serviceWorker;this._agents.chrome.isSupported()&&this._agents.chrome.create(s,t,e,c,(function(t){return o._serviceWorkerCallback(t,e,i)}))}else this._agents.webkit.isSupported()?r=this._agents.webkit.create(t,e):this._agents.firefox.isSupported()?this._agents.firefox.create(t,e):this._agents.ms.isSupported()?r=this._agents.ms.create(t,e):(e.title=t,this.config().fallback(e));if(null!==r){var a=this._addNotification(r),u=this._prepareNotification(a,e);l.isFunction(e.onShow)&&r.addEventListener("show",e.onShow),l.isFunction(e.onError)&&r.addEventListener("error",e.onError),l.isFunction(e.onClick)&&r.addEventListener("click",e.onClick),r.addEventListener("close",(function(){n(a)})),r.addEventListener("cancel",(function(){n(a)})),i(u)}i(null)}},{key:"create",value:function(e,i){var n,o=this;if(!l.isString(e))throw new Error(t.errors.invalid_title);return n=this.Permission.has()?function(t,n){try{o._createCallback(e,i,t)}catch(t){n(t)}}:function(n,r){o.Permission.request().then((function(){o._createCallback(e,i,n)})).catch((function(){r(t.errors.permission_denied)}))},new Promise(n)}},{key:"count",value:function(){var t,e=0;for(t in this._notifications)this._notifications.hasOwnProperty(t)&&e++;return e}},{key:"close",value:function(t){var e;for(e in this._notifications)if(this._notifications.hasOwnProperty(e)&&this._notifications[e].tag===t)return this._closeNotification(e)}},{key:"clear",value:function(){var t,e=!0;for(t in this._notifications)this._notifications.hasOwnProperty(t)&&(e=e&&this._closeNotification(t));return e}},{key:"supported",value:function(){var t=!1;for(var e in this._agents)this._agents.hasOwnProperty(e)&&(t=t||this._agents[e].isSupported());return t}},{key:"config",value:function(t){return(void 0!==t||null!==t&&l.isObject(t))&&l.objectMerge(this._configuration,t),this._configuration}},{key:"extend",value:function(e){var i,n={}.hasOwnProperty;if(!n.call(e,"plugin"))throw new Error(t.errors.invalid_plugin);for(var o in n.call(e,"config")&&l.isObject(e.config)&&null!==e.config&&this.config(e.config),i=new(0,e.plugin)(this.config()))n.call(i,o)&&l.isFunction(i[o])&&(this[o]=i[o])}}]),e}())("undefined"!=typeof window?window:e)}))}).call(this,i("c8ba"))},c8d2:function(t,e,i){var n=i("d039"),o=i("5899"),r="​᠎";t.exports=function(t){return n((function(){return!!o[t]()||r[t]()!=r||o[t].name!==t}))}}}]);
//# sourceMappingURL=chunk-2903b433.d24d7af7.js.map