(()=>{"use strict";var e={295:(e,t,n)=>{function r(e){return r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},r(e)}Object.defineProperty(t,"__esModule",{value:!0}),t.Collapse=void 0;var o,i=(o=n(196))&&o.__esModule?o:{default:o};function s(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function a(e,t){return a=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e},a(e,t)}function l(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}function c(e){return c=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)},c(e)}function u(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}var p=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&a(e,t)}(h,e);var t,n,o,p,d=(o=h,p=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return!1}}(),function(){var e,t=c(o);if(p){var n=c(this).constructor;e=Reflect.construct(t,arguments,n)}else e=t.apply(this,arguments);return function(e,t){if(t&&("object"===r(t)||"function"==typeof t))return t;if(void 0!==t)throw new TypeError("Derived constructors may only return object or undefined");return l(e)}(this,e)});function h(e){var t;return function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,h),u(l(t=d.call(this,e)),"timeout",void 0),u(l(t),"container",void 0),u(l(t),"content",void 0),u(l(t),"onResize",(function(){if(clearTimeout(t.timeout),t.container&&t.content){var e=t.props,n=e.isOpened,r=e.checkTimeout,o=Math.floor(t.container.clientHeight),i=Math.floor(t.content.clientHeight),s=n&&Math.abs(i-o)<=1,a=!n&&Math.abs(o)<=1;s||a?t.onRest({isFullyOpened:s,isFullyClosed:a,isOpened:n,containerHeight:o,contentHeight:i}):(t.onWork({isFullyOpened:s,isFullyClosed:a,isOpened:n,containerHeight:o,contentHeight:i}),t.timeout=setTimeout((function(){return t.onResize()}),r))}})),u(l(t),"onRest",(function(e){var n=e.isFullyOpened,r=e.isFullyClosed,o=e.isOpened,i=e.containerHeight,s=e.contentHeight;if(t.container&&t.content){var a=o&&t.container.style.height==="".concat(s,"px"),l=!o&&"0px"===t.container.style.height;if(a||l){t.container.style.overflow=o?"initial":"hidden",t.container.style.height=o?"auto":"0px";var c=t.props.onRest;c&&c({isFullyOpened:n,isFullyClosed:r,isOpened:o,containerHeight:i,contentHeight:s})}}})),u(l(t),"onWork",(function(e){var n=e.isFullyOpened,r=e.isFullyClosed,o=e.isOpened,i=e.containerHeight,s=e.contentHeight;if(t.container&&t.content){var a=o&&t.container.style.height==="".concat(s,"px"),l=!o&&"0px"===t.container.style.height;if(!a&&!l){t.container.style.overflow="hidden",t.container.style.height=o?"".concat(s,"px"):"0px";var c=t.props.onWork;c&&c({isFullyOpened:n,isFullyClosed:r,isOpened:o,containerHeight:i,contentHeight:s})}}})),u(l(t),"onRefContainer",(function(e){t.container=e})),u(l(t),"onRefContent",(function(e){t.content=e})),e.initialStyle?t.initialStyle=e.initialStyle:t.initialStyle=e.isOpened?{height:"auto",overflow:"initial"}:{height:"0px",overflow:"hidden"},t}return t=h,(n=[{key:"componentDidMount",value:function(){this.onResize()}},{key:"shouldComponentUpdate",value:function(e){var t=this.props,n=t.theme,r=t.isOpened;return t.children!==e.children||r!==e.isOpened||Object.keys(n).some((function(t){return n[t]!==e.theme[t]}))}},{key:"getSnapshotBeforeUpdate",value:function(){if(!this.container||!this.content)return null;if("auto"===this.container.style.height){var e=this.content.clientHeight;this.container.style.height="".concat(e,"px")}return null}},{key:"componentDidUpdate",value:function(){this.onResize()}},{key:"componentWillUnmount",value:function(){clearTimeout(this.timeout)}},{key:"render",value:function(){var e=this.props,t=e.theme,n=e.children,r=e.isOpened;return i.default.createElement("div",{ref:this.onRefContainer,className:t.collapse,style:this.initialStyle,"aria-hidden":!r},i.default.createElement("div",{ref:this.onRefContent,className:t.content},n))}}])&&s(t.prototype,n),h}(i.default.Component);t.Collapse=p,u(p,"defaultProps",{theme:{collapse:"ReactCollapse--collapse",content:"ReactCollapse--content"},initialStyle:void 0,onRest:void 0,onWork:void 0,checkTimeout:50})},619:(e,t,n)=>{function r(e){return r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},r(e)}Object.defineProperty(t,"__esModule",{value:!0}),t.UnmountClosed=void 0;var o,i=(o=n(196))&&o.__esModule?o:{default:o},s=n(295),a=["isOpened"],l=["isOpened"];function c(){return c=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e},c.apply(this,arguments)}function u(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function p(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?u(Object(n),!0).forEach((function(t){b(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):u(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function d(e,t){if(null==e)return{};var n,r,o=function(e,t){if(null==e)return{};var n,r,o={},i=Object.keys(e);for(r=0;r<i.length;r++)n=i[r],t.indexOf(n)>=0||(o[n]=e[n]);return o}(e,t);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(e);for(r=0;r<i.length;r++)n=i[r],t.indexOf(n)>=0||Object.prototype.propertyIsEnumerable.call(e,n)&&(o[n]=e[n])}return o}function h(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function m(e,t){return m=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e},m(e,t)}function f(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}function y(e){return y=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)},y(e)}function b(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}var v=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&m(e,t)}(g,e);var t,n,o,u,v=(o=g,u=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return!1}}(),function(){var e,t=y(o);if(u){var n=y(this).constructor;e=Reflect.construct(t,arguments,n)}else e=t.apply(this,arguments);return function(e,t){if(t&&("object"===r(t)||"function"==typeof t))return t;if(void 0!==t)throw new TypeError("Derived constructors may only return object or undefined");return f(e)}(this,e)});function g(e){var t;return function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,g),b(f(t=v.call(this,e)),"onWork",(function(e){var n=e.isOpened,r=d(e,a);t.setState({isResting:!1,isOpened:n});var o=t.props.onWork;o&&o(p({isOpened:n},r))})),b(f(t),"onRest",(function(e){var n=e.isOpened,r=d(e,l);t.setState({isResting:!0,isOpened:n,isInitialRender:!1});var o=t.props.onRest;o&&o(p({isOpened:n},r))})),b(f(t),"getInitialStyle",(function(){var e=t.state,n=e.isOpened;return e.isInitialRender&&n?{height:"auto",overflow:"initial"}:{height:"0px",overflow:"hidden"}})),t.state={isResting:!0,isOpened:e.isOpened,isInitialRender:!0},t}return t=g,(n=[{key:"componentDidUpdate",value:function(e){var t=this.props.isOpened;e.isOpened!==t&&this.setState({isResting:!1,isOpened:t,isInitialRender:!1})}},{key:"render",value:function(){var e=this.state,t=e.isResting,n=e.isOpened;return t&&!n?null:i.default.createElement(s.Collapse,c({},this.props,{initialStyle:this.getInitialStyle(),onWork:this.onWork,onRest:this.onRest}))}}])&&h(t.prototype,n),g}(i.default.PureComponent);t.UnmountClosed=v,b(v,"defaultProps",{onWork:void 0,onRest:void 0})},180:(e,t,n)=>{var r=n(295).Collapse,o=n(619).UnmountClosed;e.exports=o,o.Collapse=r,o.UnmountClosed=o},251:(e,t,n)=>{var r=n(196),o=Symbol.for("react.element"),i=Symbol.for("react.fragment"),s=Object.prototype.hasOwnProperty,a=r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,l={key:!0,ref:!0,__self:!0,__source:!0};function c(e,t,n){var r,i={},c=null,u=null;for(r in void 0!==n&&(c=""+n),void 0!==t.key&&(c=""+t.key),void 0!==t.ref&&(u=t.ref),t)s.call(t,r)&&!l.hasOwnProperty(r)&&(i[r]=t[r]);if(e&&e.defaultProps)for(r in t=e.defaultProps)void 0===i[r]&&(i[r]=t[r]);return{$$typeof:o,type:e,key:c,ref:u,props:i,_owner:a.current}}t.Fragment=i,t.jsx=c,t.jsxs=c},893:(e,t,n)=>{e.exports=n(251)},196:e=>{e.exports=window.React}},t={};function n(r){var o=t[r];if(void 0!==o)return o.exports;var i=t[r]={exports:{}};return e[r](i,i.exports,n),i.exports}n.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return n.d(t,{a:t}),t},n.d=(e,t)=>{for(var r in t)n.o(t,r)&&!n.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t);var r={};(()=>{n.d(r,{J:()=>O});var e=n(196),t=n.n(e);const o=window.wc.wcBlocksRegistry,i=window.wp.htmlEntities,s=window.wc.wcSettings;var a=n(180);class l{static messages={};static setMessages(e){l.messages=e}static getMessage(e,t=""){return void 0===l.messages[e]?t:l.messages[e]}}var c=n(893);class u extends t().Component{constructor(e){super(e),this.state={...this.state,blikCode:"",blikError:""}}render(){return(0,c.jsxs)(c.Fragment,{children:[(0,c.jsx)("input",{type:"text",inputMode:"numeric",value:this.state.blikCode,onChange:this.handleBlikCodeChange,onFocus:this.handleOnFocus,placeholder:l.getMessage("enter_the_blik_code","Payment failed"),maxLength:6,className:"blik-input",pattern:"\\d{6}"}),(0,c.jsx)("span",{className:"atp-block-payment-description",children:l.getMessage("the_code_has_6_digits_note","The code has 6 digits. You'll find it in your banking app.")}),this.state.blikError&&(0,c.jsx)("span",{className:"atp-block-payment-description blik-error",children:this.state.blikError})]})}handleBlikCodeChange=e=>{const t=e.target.value;if(!/^\d*$/.test(t))return;const n=/^\d{6}$/.test(t);this.setState({blikCode:t,blikError:n?"":l.getMessage("code_is_invalid_code_should_be_6_digits","The code you provided is invalid. Code should be 6 digits.")}),n&&this.props.onBlikCodeChange(t)};handleOnFocus=e=>{this.props.onFocus()}}class p{static distillHtmlClassByString(e){return e.toLowerCase().trim().replace(/[\s_-]+/g,"_").replace(/^-+|-+$/g,"").normalize("NFD").replace(/[\u0300-\u036f]/g,"").replace(/[^a-z0-9_]/g,"")}}class d extends t().Component{hasParent=!1;static instances=[];isBlik0=!1;blikCode="";constructor(e){super(e),this.state={isOpen:!1,isSelected:!1},this.hasParent=!!e.hasParent&&e.hasParent,d.instances.push(this)}render(){const{numericChannelId:t,id:n,value:r,name:o,label:i,icon:s,description:l,items:c,hasParent:d,data:m}=this.props;this.numericChannelId=t;const f=Array.isArray(c)&&c.length>0,y=509===t&&m&&"object"==typeof m&&"blik0"in m&&!0===m.blik0;return this.isBlik0=y,(0,e.createElement)("li",{className:`atp-${p.distillHtmlClassByString(i)} atp-block-payment-item ${f&&"atp-has-children"}`},(0,e.createElement)("label",{className:this.state.isSelected?"atp-active":"",htmlFor:n},(0,e.createElement)("input",{onClick:this.hasParent?this.handleToggleSelected:f?this.handleToggle:this.closeAllInstances,id:n,value:r,type:"radio",name:o}),(0,e.createElement)("img",{src:s,alt:o}),(0,e.createElement)("div",{className:"atp-block-payment-item-wrapper"},y?(0,e.createElement)(u,{placeholder:"******",paymentChannel:this,onBlikCodeChange:this.handleBlikCodeChange,onFocus:this.handleToggleSelected}):(0,e.createElement)("span",{className:"atp-block-payment-label"},i),(0,e.createElement)("span",{className:"atp-block-payment-description",dangerouslySetInnerHTML:{__html:l}}))),(0,e.createElement)(a.Collapse,{isOpened:this.state.isOpen},f?(0,e.createElement)(h,{items:c,hasParent:!0}):null))}handleBlikCodeChange=e=>{this.blikCode=e,this.handleToggleSelected()};handleToggle=()=>{this.handleToggleSelected(),this.setState({isOpen:!this.state.isOpen,isSelected:this.state.isSelected})};handleToggleSelected=()=>{this.unselectAllInstances(),this.setState({isOpen:this.state.isOpen,isSelected:!0}),O.setPaymentChannelId(this.numericChannelId),O.setIsBlik0(this.isBlik0),O.setBlikCode(this.blikCode)};closeAllInstances=()=>{d.instances.forEach((e=>{e.setState({isOpen:!1,isSelected:!1})})),this.handleToggleSelected()};unselectAllInstances(){d.instances.forEach((e=>{e.setState({isOpen:e.state.isOpen,isSelected:!1})}))}}class h extends t().Component{hasParent=!1;constructor(e){super(e),this.list=e.items,this.hasParent=!!e.hasParent&&e.hasParent}render(){if(this.list&&0!==this.list.length)return(0,e.createElement)("ul",{className:"atp-block-payment"},this.list.map((t=>(0,e.createElement)(d,{numericChannelId:t.numericChannelId,id:t.key,value:t.value,name:t.name,label:t.label,icon:t.icon,items:t.items,description:t.description,data:t.data,hasParent:this.hasParent}))))}}class m extends t().Component{constructor(e){super(e)}render(){const{label:t,iconSrc:n,iconAlt:r}=this.props;return(0,e.createElement)(e.Fragment,null,(0,e.createElement)("span",{className:"wc-block-components-payment-method-label"},t),(0,e.createElement)("img",{src:n,alt:r}))}}class f{static APPLE_PAY_CHANNEL_ID=1513;static processItem({key:e,value:t,name:n,label:r,icon:o,id:i,block_description:s,items:a,data:l}){let c={numericChannelId:i=parseInt(i),key:e,value:t,name:n,label:r,icon:o,description:s,items:[],data:l};if(a&&a.length>0)for(const e of a)c.items.push(f.processItem(e));return c}static createChannelsFromServer(e){let t={items:[]};for(const n of e){let e=f.processItem(n);this.validateItem(e)&&t.items.push(e)}return t}static validateItem(e){return!(e.numericChannelId===this.APPLE_PAY_CHANNEL_ID&&!this.isApplePay())}static isApplePay(){return window.ApplePaySession}}class y{static create({title:e,description:t,icon_src:n,whitelabel:r,supports:o,place_order_button_label:i,channels:s,messages:a,adminAjaxUrl:l,nonce:c}){try{return{label:e,description:t,iconSrc:n,whitelabel:r,placeOrderButtonLabel:i,supports:o,channels:f.createChannelsFromServer(s),messages:a,adminAjaxUrl:l,nonce:c}}catch(e){throw e("Create PaymentMethodData failed")}}}let b=function(e){return e.STATUS_SUCCESS="payment_success",e.STATUS_ERROR="error",e.STATUS_CHECK_DEVICE="check_device",e.STATUS_WAIT="wait",e}({});class v extends t().Component{constructor(e){super(e),this.state={message:"",status:b.STATUS_WAIT,visible:!1}}show(e,t){this.setState({message:e,status:t,visible:!0}),document.body.style.overflow="hidden"}hide(){this.setState({message:"",status:b.STATUS_WAIT,visible:!1}),document.body.style.overflow="auto"}render(){return this.state.visible?(0,e.createElement)("div",{className:"bm-blik-overlay"},(0,e.createElement)("div",{className:"bm-blik-overlay-content",onClick:e=>e.stopPropagation()},(0,e.createElement)("p",null,(0,e.createElement)("span",{className:`bm-blik-overlay-status ${this.state.status}`},this.state.message)))):null}}const g=(0,s.getPaymentMethodData)("bluemedia",{});class O extends t().Component{payment_data=["test"];constructor(){super({}),this.blikOverlayRef=t().createRef()}static init(t){O.paymentMethodDataFromServer=y.create(g),l.setMessages(O.paymentMethodDataFromServer.messages),(0,o.registerPaymentMethod)({name:"bluemedia",label:(0,e.createElement)(m,{label:this.paymentMethodDataFromServer.label,iconSrc:this.paymentMethodDataFromServer.iconSrc,iconAlt:this.paymentMethodDataFromServer.label}),content:(0,e.createElement)(O,null),edit:(0,e.createElement)(O,null),ariaLabel:this.paymentMethodDataFromServer.label,supports:{features:this.paymentMethodDataFromServer.supports},canMakePayment:()=>!0,placeOrderButtonLabel:this.paymentMethodDataFromServer.placeOrderButtonLabel,savedTokenComponent:(0,e.createElement)(O,null)})}static setPaymentChannelId(e){O.paymentChannelId=e}static setIsBlik0(e){O.isBlik0=e}static setBlikCode(e){O.blikCode=e}showBlikOverlay(e,t){this.blikOverlayRef.current&&this.blikOverlayRef.current.show(e,t)}hideBlikOverlay(){this.blikOverlayRef.current&&this.blikOverlayRef.current.hide()}render(){return O.paymentMethodDataFromServer.whitelabel?(0,e.createElement)(e.Fragment,null,(0,e.createElement)(v,{ref:this.blikOverlayRef})," ",(0,e.createElement)(h,{items:O.paymentMethodDataFromServer.channels.items})):(0,e.createElement)(e.Fragment,null,(0,i.decodeEntities)(O.paymentMethodDataFromServer.description||""))}componentDidMount(){const{eventRegistration:e,emitResponse:t}=this.props,{onPaymentProcessing:n,onCheckoutSuccess:r}=e;this.unsubscribe=n((async()=>{if(O.paymentMethodDataFromServer.whitelabel){const e=O.paymentChannelId;if(e){const n=O.isBlik0,r=O.blikCode;return n?{type:t.responseTypes.SUCCESS,meta:{paymentMethodData:{autopay_numeric_channel_Id:e.toString(),bluemedia_blik_code:r,blik_0_block_payment:"1"}}}:{type:t.responseTypes.SUCCESS,meta:{paymentMethodData:{autopay_numeric_channel_Id:e.toString()}}}}return{type:t.responseTypes.ERROR,message:l.getMessage("no_payment_channel_selected","No payment channel selected.")}}})),this.unsubscribeCheckoutSuccess=r((async()=>{const e=O.paymentChannelId,n=O.isBlik0;if(e&&n)try{const e=await this.performAjaxRequests();return this.showBlikOverlay(e.message,e.status),await new Promise((e=>setTimeout(e,3e3))),this.hideBlikOverlay(),{type:t.responseTypes.SUCCESS}}catch(e){return this.showBlikOverlay(l.getMessage("payment_failed","Payment failed"),b.STATUS_ERROR),await new Promise((e=>setTimeout(e,3e3))),{type:t.responseTypes.SUCCESS}}}))}unsubscribe=null;unsubscribeCheckoutSuccess=null;async performAjaxRequests(){console.log(O.paymentMethodDataFromServer);const e=O.paymentMethodDataFromServer.adminAjaxUrl,t=O.paymentMethodDataFromServer.nonce,n=Date.now(),r=async()=>{if(Date.now()-n>12e4)throw new Error("Operation timed out after 2 minutes.");const o=new FormData;o.append("action","bm_payment_get_status_action"),o.append("nonce",t);const i=await fetch(e,{method:"POST",body:o});if(!i.ok)throw new Error("Request failed");const s=await i.json();return console.log(i),s.status===b.STATUS_SUCCESS?s:s.status===b.STATUS_CHECK_DEVICE||s.status===b.STATUS_WAIT?(this.showBlikOverlay(s.message,s.status),await new Promise((e=>setTimeout(e,3e3))),r()):s()};return await r()}componentWillUnmount(){this.unsubscribe&&this.unsubscribe()}}O.init(g)})()})();