(this["webpackJsonpfront.mosmetod"]=this["webpackJsonpfront.mosmetod"]||[]).push([[20],{328:function(e,t,a){},475:function(e,t,a){},502:function(e,t,a){"use strict";a.r(t),a.d(t,"default",(function(){return d}));var n=a(17),r=a(25),c=a(22),i=a(21),s=a(0),l=a.n(s),o=(a(328),a(391)),m=a(390),u=(a(475),function(e){Object(c.a)(a,e);var t=Object(i.a)(a);function a(){return Object(n.a)(this,a),t.apply(this,arguments)}return Object(r.a)(a,[{key:"handleChange",value:function(){var e=document.querySelector(".input-password");"password"===e.getAttribute("type")?e.setAttribute("type","text"):e.setAttribute("type","password")}},{key:"render",value:function(){return l.a.createElement("label",{className:"show-hide text-label"},"\u041f\u043e\u043a\u0430\u0437\u044b\u0432\u0430\u0442\u044c \u043f\u0430\u0440\u043e\u043b\u044c",l.a.createElement("input",{className:"show-hide-input",type:"checkbox",onChange:this.handleChange}))}}]),a}(l.a.Component)),p=function(e){Object(c.a)(a,e);var t=Object(i.a)(a);function a(e){var r;return Object(n.a)(this,a),(r=t.call(this,e)).SignInSchema=m.a({email:m.c().email("\u041d\u0435\u043f\u0440\u0430\u0432\u0438\u043b\u044c\u043d\u044b\u0439 email").required("\u0412\u0432\u0435\u0434\u0438\u0442\u0435 email"),password:m.c().required("\u0412\u0432\u0435\u0434\u0438\u0442\u0435 \u043f\u0430\u0440\u043e\u043b\u044c").min(6,"\u041c\u0438\u043d\u0438\u043c\u0430\u043b\u044c\u043d\u0430\u044f \u0434\u043b\u0438\u043d\u0430: 6 \u0441\u0438\u043c\u0432\u043e\u043b\u043e\u0432")}),r}return Object(r.a)(a,[{key:"render",value:function(){return l.a.createElement(o.d,{initialValues:{password:"",email:""},validationSchema:this.SignInSchema,onSubmit:function(e,t){var a=t.setSubmitting;fetch("https://raw.githubusercontent.com/DmitryKeymakh/front/master/api/check-password.json").then((function(t){var n=document.querySelector(".check-in-warning");200===t.status?(n.classList.add("check-in-warning-hide"),setTimeout((function(){alert(JSON.stringify(e,null,2)),a(!1)}),400)):(n.classList.remove("check-in-warning-hide"),a(!1))}))}},(function(e){var t=e.errors,a=e.touched;return l.a.createElement(o.c,null,l.a.createElement("label",{className:"text-label",htmlFor:"email"},"Email"),l.a.createElement(o.b,{className:t.email&&a.email?"text-input error":"text-input",placeholder:"\u0412\u0432\u0435\u0434\u0438\u0442\u0435 email",name:"email",type:"email"}),l.a.createElement(o.a,{component:"div",className:"input-feedback",name:"email"}),l.a.createElement("label",{className:"text-label",htmlFor:"password"},"\u041f\u0430\u0440\u043e\u043b\u044c"),l.a.createElement(o.b,{className:t.password&&a.password?"text-input input-password error":"text-input input-password",placeholder:"\u0412\u0432\u0435\u0434\u0438\u0442\u0435 \u043f\u0430\u0440\u043e\u043b\u044c",name:"password",type:"password"}),l.a.createElement(o.a,{component:"div",className:"input-feedback",name:"password"}),l.a.createElement(o.b,{component:u}),l.a.createElement("button",{className:"button-form",type:"reset"},"Reset"),l.a.createElement("button",{className:"button-form",type:"submit"},"Submit"))}))}}]),a}(l.a.Component),d=function(e){Object(c.a)(a,e);var t=Object(i.a)(a);function a(){return Object(n.a)(this,a),t.apply(this,arguments)}return Object(r.a)(a,[{key:"render",value:function(){return l.a.createElement(l.a.Fragment,null,l.a.createElement("div",{className:"check-in-warning check-in-warning-hide"},"\u041d\u0435\u0432\u0435\u0440\u043d\u044b\u0439 email \u0438\u043b\u0438 \u043f\u0430\u0440\u043e\u043b\u044c."),l.a.createElement(p,null))}}]),a}(l.a.Component)}}]);
//# sourceMappingURL=20.00bfbaf9.chunk.js.map