(this["webpackJsonpfront.mosmetod"]=this["webpackJsonpfront.mosmetod"]||[]).push([[18],{254:function(e,t,a){},287:function(e,t,a){"use strict";var r=a(55),c=(a(254),a(0)),n=a.n(c),o=a(56);function s(){var e=Object(r.a)(["\n        color: #FFFFFF;\n        font-weight: 300;\n        font-size: 0.9rem;\n        letter-spacing: 0.15px;\n        padding: 10px;\n        height: 100%;\n        display: flex;\n        justify-content: center;\n        align-items: center;\n        text-align: center;\n        background: linear-gradient(to right top, ",", ",");\n        border-radius: 0 0 3px 3px;\n    "]);return s=function(){return e},e}t.a=function(e){var t=e.data,a=e.projectsPage,r=o.a.div(s(),t.gradient.start,t.gradient.finish);return n.a.createElement("div",{className:t.in_archive?"our-projects-item-wrap archive projects-item":"our-projects-item-wrap projects-item"},n.a.createElement("a",{className:a?"our-projects-page-item":"our-projects-item",href:t.url,target:"_blank",rel:"noopener noreferrer"},n.a.createElement("div",{className:"our-projects-wrap"},n.a.createElement("img",{className:"our-projects-logo",loading:"lazy",src:t.logo,alt:t.title}),n.a.createElement("div",{className:"our-projects-title"},t.title)),n.a.createElement("div",{className:"our-projects-title-wrap"},n.a.createElement(r,null,t.description))))}},288:function(e,t,a){},499:function(e,t,a){"use strict";a.r(t),a.d(t,"Projects",(function(){return d}));var r=a(63),c=a(2),n=a.n(c),o=a(7),s=(a(288),a(6)),i=a(0),u=a.n(i),l=a(5),p=a.n(l),m=a(12),f=a(29),h=a(58),g=a(287),j=Object(f.a)((function(e){return{projectsPageQuery:e.projectsPageReducer.projectsPageQuery}}),(function(e){return Object(m.b)({setProjectsPageQuery:h.h},e)}))((function(e){var t=e.projectsPageQuery,a=e.setProjectsPageQuery;return u.a.createElement("input",{className:"projects-page-form-input","aria-label":"search",name:"search",type:"search",placeholder:"\u041f\u043e\u0438\u0441\u043a",autoComplete:"off",onChange:function(e){a(e.target.value)},value:t})})),v=Object(f.a)((function(e){return{showArchive:e.projectsPageReducer.showArchive}}),(function(e){return Object(m.b)({setArchiveDataStatus:h.e},e)}))((function(e){var t=e.showArchive,a=e.setArchiveDataStatus;return u.a.createElement("label",{className:"control control-checkbox",htmlFor:"in-archive-checkbox"},"\u0430\u0440\u0445\u0438\u0432\u043d\u044b\u0435",u.a.createElement("br",null),"\u043f\u0440\u043e\u0435\u043a\u0442\u044b",u.a.createElement("input",{type:"checkbox",id:"in-archive-checkbox",checked:t,onChange:function(){return a(!t)}}),u.a.createElement("div",{className:"control_indicator"}))})),d=function(e){var t=e.projectsPageData,a=e.setProjectsPageData,c=e.projectsPageQuery,l=e.showArchive,m=e.liveSearchData,f=e.setLiveSearchData;return Object(i.useEffect)((function(){(function(){var e=Object(o.a)(n.a.mark((function e(){var t;return n.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,p()(s.a.projectPage);case 2:t=e.sent,a(t.data),f(t.data);case 5:case"end":return e.stop()}}),e)})));return function(){return e.apply(this,arguments)}})()()}),[s.a.projectPage]),Object(i.useEffect)((function(){var e=[],a=new RegExp("".concat(c),"i");t.map((function(t){t.title.match(a)&&(e=[].concat(Object(r.a)(e),[t]))})),f(c.length?e:t)}),[c]),u.a.createElement("div",{className:"projects-page"},u.a.createElement("div",{className:"projects-page-live-search"},u.a.createElement("div",{className:"projects-page-form"},u.a.createElement(j,null)),u.a.createElement("div",{className:"projects-page-filter"},u.a.createElement(v,null))),u.a.createElement("div",{className:"projects-page-list"},l?m.map((function(e){return u.a.createElement(g.a,{key:e.id,projectsPage:!0,data:e})})):m.filter((function(e){return!1===e.in_archive})).map((function(e){return u.a.createElement(g.a,{key:e.id,projectsPage:!0,data:e})}))))};t.default=Object(f.a)((function(e){var t=e.projectsPageReducer;return{projectsPageData:t.projectsPageData,projectsPageQuery:t.projectsPageQuery,showArchive:t.showArchive,liveSearchData:t.liveSearchData}}),(function(e){return Object(m.b)({setProjectsPageData:h.g,setLiveSearchData:h.f},e)}))(d)}}]);
//# sourceMappingURL=18.5561622b.chunk.js.map