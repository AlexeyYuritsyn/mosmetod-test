(this["webpackJsonpfront.mosmetod"]=this["webpackJsonpfront.mosmetod"]||[]).push([[25],{328:function(e,t,a){},505:function(e,t,a){"use strict";a.r(t),a.d(t,"default",(function(){return w}));var n=a(17),r=a(25),s=a(22),c=a(21),m=a(0),l=a.n(m),o=a(2),i=a.n(o),p=a(7),u=(a(328),a(391)),d=a(390),b=function(e){Object(s.a)(a,e);var t=Object(c.a)(a);function a(e){var r;return Object(n.a)(this,a),(r=t.call(this,e)).SignUpSchema=d.a({name:d.c().required("\u0412\u0432\u0435\u0434\u0438\u0442\u0435 \u0424\u0418\u041e").max(100,"\u041d\u0435 \u0431\u043e\u043b\u044c\u0448\u0435 100 \u0441\u0438\u043c\u0432\u043e\u043b\u043e\u0432"),email:d.c().email("\u041d\u0435\u043f\u0440\u0430\u0432\u0438\u043b\u044c\u043d\u044b\u0439 \u0438\u043c\u0435\u0439\u043b").required("\u0412\u0432\u0435\u0434\u0438\u0442\u0435 \u0438\u043c\u0435\u0439\u043b").test("password-check","\u041f\u043e\u043b\u044c\u0437\u043e\u0432\u0430\u0442\u0435\u043b\u044c \u0441 \u0442\u0430\u043a\u0438\u043c email \u0443\u0436\u0435 \u0437\u0430\u0440\u0435\u0433\u0438\u0441\u0442\u0440\u0438\u0440\u043e\u0432\u0430\u043d",Object(p.a)(i.a.mark((function e(){return i.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,fetch("https://raw.githubusercontent.com/DmitryKeymakh/front/master/api/check-password.json");case 2:return e.t0=e.sent.status,e.abrupt("return",200!==e.t0);case 4:case"end":return e.stop()}}),e)})))),password:d.c().required("\u0412\u0432\u0435\u0434\u0438\u0442\u0435 \u043f\u0430\u0440\u043e\u043b\u044c").min(6,"\u041c\u0438\u043d\u0438\u043c\u0430\u043b\u044c\u043d\u0430\u044f \u0434\u043b\u0438\u043d\u0430: 6 \u0441\u0438\u043c\u0432\u043e\u043b\u043e\u0432").max(20,"\u041c\u0430\u043a\u0441\u0438\u043c\u0430\u043b\u044c\u043d\u0430\u044f \u0434\u043b\u0438\u043d\u0430: 20 \u0441\u0438\u043c\u0432\u043e\u043b\u043e\u0432").matches(/[a-zA-Z0-9]\w/,"\u0418\u0441\u043f\u043e\u043b\u044c\u0437\u0443\u0439\u0442\u0435 \u043b\u0430\u0442\u0438\u043d\u0441\u043a\u0438\u0435 \u0431\u0443\u043a\u0432\u044b, \u0446\u0438\u0444\u0440\u044b \u0438 \u0437\u043d\u0430\u043a \u043d\u0438\u0436\u043d\u0435\u0433\u043e \u043f\u043e\u0434\u0447\u0435\u0440\u043a\u0438\u0432\u0430\u043d\u0438\u044f"),passwordRepeat:d.c().oneOf([d.b("password"),null],"\u041f\u0430\u0440\u043e\u043b\u044c \u0434\u043e\u043b\u0436\u0435\u043d \u0441\u043e\u0432\u043f\u0430\u0434\u0430\u0442\u044c")}),r}return Object(r.a)(a,[{key:"render",value:function(){return l.a.createElement(u.d,{initialValues:{name:"",email:"",password:"",passwordRepeat:""},validationSchema:this.SignUpSchema,onSubmit:function(e,t){var a=t.setSubmitting;setTimeout((function(){alert(JSON.stringify(e,null,2)),a(!1)}),400)}},(function(e){var t=e.errors,a=e.touched;return l.a.createElement(u.c,null,l.a.createElement("label",{className:"text-label",htmlFor:"name"},"\u0412\u0432\u0435\u0434\u0438\u0442\u0435 \u0424\u0418\u041e"),l.a.createElement(u.b,{className:t.name&&a.name?"text-input error":"text-input",placeholder:"\u041c\u043d\u0430\u0446\u0430\u043a\u0430\u043d\u044f\u043d \u0410\u0440\u043c\u0435\u043d \u0412\u0430\u043b\u0435\u0440\u044c\u0435\u0432\u0438\u0447",name:"name",type:"text"}),l.a.createElement(u.a,{component:"div",className:"input-feedback",name:"name"}),l.a.createElement("label",{className:"text-label",htmlFor:"email"},"Email"),l.a.createElement(u.b,{className:t.email&&a.email?"text-input error":"text-input",placeholder:"\u0412\u0432\u0435\u0434\u0438\u0442\u0435 email",name:"email",type:"email"}),l.a.createElement(u.a,{component:"div",className:"input-feedback",name:"email"}),l.a.createElement("label",{className:"text-label",htmlFor:"password"},"\u041f\u0430\u0440\u043e\u043b\u044c"),l.a.createElement(u.b,{className:t.password&&a.password?"text-input input-password error":"text-input input-password",placeholder:"\u0412\u0432\u0435\u0434\u0438\u0442\u0435 \u043f\u0430\u0440\u043e\u043b\u044c",name:"password",type:"password"}),l.a.createElement(u.a,{component:"div",className:"input-feedback",name:"password"}),l.a.createElement("label",{className:"text-label",htmlFor:"passwordRepeat"},"\u041f\u043e\u0432\u0442\u043e\u0440\u0438\u0442\u0435 \u043f\u0430\u0440\u043e\u043b\u044c"),l.a.createElement(u.b,{className:t.passwordRepeat&&a.passwordRepeat?"text-input input-password error":"text-input input-password",placeholder:"\u041f\u043e\u0432\u0442\u043e\u0440\u0438\u0442\u0435 \u043f\u0430\u0440\u043e\u043b\u044c",name:"passwordRepeat",type:"password"}),l.a.createElement(u.a,{component:"div",className:"input-feedback",name:"passwordRepeat"}),l.a.createElement("button",{className:"button-form",type:"reset"},"Reset"),l.a.createElement("button",{className:"button-form",type:"submit"},"Submit"))}))}}]),a}(l.a.Component),w=function(e){Object(s.a)(a,e);var t=Object(c.a)(a);function a(){return Object(n.a)(this,a),t.apply(this,arguments)}return Object(r.a)(a,[{key:"render",value:function(){return l.a.createElement(l.a.Fragment,null,l.a.createElement(b,null))}}]),a}(l.a.Component)}}]);
//# sourceMappingURL=25.3e6898bc.chunk.js.map