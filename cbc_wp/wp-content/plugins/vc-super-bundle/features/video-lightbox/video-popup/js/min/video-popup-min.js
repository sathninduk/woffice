!function(t){var e={};function o(n){if(e[n])return e[n].exports;var r=e[n]={i:n,l:!1,exports:{}};return t[n].call(r.exports,r,r.exports,o),r.l=!0,r.exports}o.m=t,o.c=e,o.d=function(t,e,n){o.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},o.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},o.t=function(t,e){if(1&e&&(t=o(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var r in t)o.d(n,r,function(e){return t[e]}.bind(null,r));return n},o.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return o.d(e,"a",e),e},o.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},o.p="",o(o.s=2)}([function(t,e,o){t.exports=function(){var t,e=[],o=document,n=(o.documentElement.doScroll?/^loaded|^c/:/^loaded|^i|^c/).test(o.readyState);return n||o.addEventListener("DOMContentLoaded",t=function(){for(o.removeEventListener("DOMContentLoaded",t),n=1;t=e.shift();)t()}),function(t){n?setTimeout(t,0):e.push(t)}}()},function(t,e){!function(){var e,o,n,r,i,a,c,u,d,p,l,s,f,b,m,g,v,h,y,x,w,_,S,A,k,E,O,M,z,L,T,C=window,D=[],P={},j=document,I="appendChild",H="createElement",q="removeChild",N="innerHTML",R="pointer-events:auto",F="clientHeight",V="clientWidth",$="addEventListener",B=C.setTimeout,G=C.clearTimeout;function Y(){var t=e.getBoundingClientRect();return nt("transform:","translate3D("+(t.left-(n[V]-t.width)/2)+"px, "+(t.top-(n[F]-t.height)/2)+"px, 0) scale3D("+e[V]/r[V]+", "+e[F]/r[F]+", 0);")}function X(t){var e=M.length-1;if([(O=Math.max(0,Math.min(O+t,e)))-1,O,O+1].forEach(function(t){if(t=Math.max(0,Math.min(t,e)),!P[t]){var o=M[t].src,n=j[H]("IMG");n[$]("load",K.bind(null,o)),n.src=o,P[t]=n}}),P[O].complete)return Z();s=!0,ot(b,"opacity:.4;"),n[I](b),P[O].onload=function(){x&&Z()},P[O].onerror=function(){M[O]={error:"Error loading image"},x&&Z()}}function Z(){s&&(n[q](b),s=!1);var t=M[O];if(t.error)alert(t.error);else{var o=t.src;i.src=o,t.el&&(e=t.el)}z[N]=O+1+"/"+M.length}function U(){4===r.readyState?Q():a.error?Q("video"):f=B(U,35)}function W(t){T||(t&&ot(b,"top:"+e.offsetTop+"px;left:"+e.offsetLeft+"px;height:"+e[F]+"px;width:"+e[V]+"px"),e.parentElement[t?I:q](b),s=t)}function J(t){t&&(g[N]=t),ot(m,"opacity:"+(t?"1;"+R:"0"))}function K(t){!~D.indexOf(t)&&D.push(t)}function Q(t){return s&&W(),S&&S(),"string"==typeof t?(et(),alert("Error: The requested "+t+" could not be loaded.")):(_&&K(d),x?J(M[O].caption):(ot(r,Y()),ot(n,"opacity:1;"+R),A=B(A,410),y=!0,x=!!M,void B(function(){ot(r,nt("transition:","transform .35s;")+nt("transform:","none;")),v&&B(J,250,v)},60)))}function tt(t){var e=t.target,o=[m,h,a,g,E,k,b];e&&e.blur(),w||~o.indexOf(e)||(r.style.cssText+=Y(),ot(n,R),B(et,350),G(A),y=!1,w=!0)}function et(){j.body[q](n),n[q](r),ot(n,""),(r===c?u:r).removeAttribute("src"),J(!1),x&&(s&&n[q](b),n[q](z),x=M=!1,P={},L||n[q](k),L||n[q](E)),w=s=!1}function ot(t,e){t.style.cssText=e}function nt(t,e){var o=t+e;return"-webkit-"+o+t+"-webkit-"+e+o}t.exports=function(t){o||function(){var t;function e(){var t=j[H]("button");return t.className="bp-x",t[N]="&#215;",t}function r(t,e){var o=j[H]("button");return o.className="bp-lr",o[N]='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 129 129" height="75" fill="#fff"><path d="M88.6 121.3c.8.8 1.8 1.2 2.9 1.2s2.1-.4 2.9-1.2a4.1 4.1 0 0 0 0-5.8l-51-51 51-51a4.1 4.1 0 0 0-5.8-5.8l-54 53.9a4.1 4.1 0 0 0 0 5.8l54 53.9z"/></svg>',ot(o,e),o.onclick=function(e){e.stopPropagation(),X(t)},o}var d=j[H]("STYLE");d[N]=".bp-lr,.bp-x:active{outline:0}#bp_caption,#bp_container{bottom:0;left:0;right:0;position:fixed;opacity:0;backface-visibility:hidden}#bp_container>*,#bp_loader,.bp-x{position:absolute;right:0;z-index:10}#bp_container{top:0;z-index:9999;background:rgba(0,0,0,.7);opacity:0;pointer-events:none;transition:opacity .35s}#bp_loader{top:0;left:0;bottom:0;display:-webkit-flex;display:flex;margin:0;cursor:wait;z-index:9}#bp_count,.bp-lr,.bp-x{cursor:pointer;color:#fff}#bp_loader svg{width:50%;max-width:300px;max-height:50%;margin:auto}#bp_container img,#bp_sv,#bp_vid{user-select:none;max-height:96%;max-width:96%;top:0;bottom:0;left:0;margin:auto;box-shadow:0 0 3em rgba(0,0,0,.4);z-index:-1}#bp_sv{width:171vh}#bp_caption{font-size:.9em;padding:1.3em;background:rgba(15,15,15,.94);color:#fff;text-align:center;transition:opacity .3s}#bp_count,.bp-x{top:0;opacity:.8;font-size:3em;padding:0 .3em;background:0 0;border:0;text-shadow:0 0 2px rgba(0,0,0,.6)}#bp_caption .bp-x{left:2%;top:auto;right:auto;bottom:100%;padding:0 .6em;background:#d74040;border-radius:2px 2px 0 0;font-size:2.3em;text-shadow:none}.bp-x:focus,.bp-x:hover{opacity:1}@media (max-aspect-ratio:9/5){#bp_sv{height:53vw}}.bp-lr{top:50%;top:calc(50% - 138px);padding:99px 1vw;background:0 0;border:0;opacity:.4;transition:opacity .1s}.bp-lr:focus,.bp-lr:hover{opacity:.8}@media (max-width:600px){.bp-lr{font-size:15vw}}#bp_count{left:0;display:table;padding:14px;color:#fff;font-size:22px;opacity:.7;cursor:default;right:auto}",j.head[I](d),(n=j[H]("DIV")).id="bp_container",n.onclick=tt,p=e(),n[I](p),"ontouchstart"in C&&(L=!0,n.ontouchstart=function(e){t=e.changedTouches[0].pageX},n.ontouchmove=function(t){t.preventDefault()},n.ontouchend=function(e){if(x){var o=e.changedTouches[0].pageX-t;o<-30&&X(1),o>30&&X(-1)}}),i=j[H]("IMG"),(a=j[H]("VIDEO")).id="bp_vid",a.autoplay=!0,a.setAttribute("playsinline",!0),a.controls=!0,a.loop=!0,(z=j[H]("span")).id="bp_count",(m=j[H]("DIV")).id="bp_caption",(h=e()).onclick=J.bind(null,!1),m[I](h),g=j[H]("SPAN"),m[I](g),n[I](m),k=r(1,nt("transform:","scalex(-1);")),E=r(-1,"left:0;right:auto"),(b=j[H]("DIV")).id="bp_loader",b[N]='<svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 32 32" fill="#fff" opacity=".8"><path d="M16 0a16 16 0 0 0 0 32 16 16 0 0 0 0-32m0 4a12 12 0 0 1 0 24 12 12 0 0 1 0-24" fill="#000" opacity=".5"/><path d="M16 0a16 16 0 0 1 16 16h-4A12 12 0 0 0 16 4z"><animateTransform attributeName="transform" dur="1s" from="0 16 16" repeatCount="indefinite" to="360 16 16" type="rotate"/></path></svg>',(c=j[H]("DIV")).id="bp_sv",(u=j[H]("IFRAME")).allowFullscreen=!0,u.onload=Q,ot(u,"border:0px;height:100%;width:100%"),c[I](u),i.onload=Q,i.onerror=Q.bind(null,"image"),C[$]("resize",function(){x||s&&W(!0)}),j[$]("keyup",function(t){var e=t.keyCode;27===e&&y&&tt(n),x&&(39===e&&X(1),37===e&&X(-1),38===e&&X(10),40===e&&X(-10))}),j[$]("keydown",function(t){x&&~[37,38,39,40].indexOf(t.keyCode)&&t.preventDefault()}),j[$]("focus",function(t){y&&!n.contains(t.target)&&(t.stopPropagation(),p.focus())},!0),o=!0}(),s&&(G(f),et()),l=t.ytSrc||t.vimeoSrc,S=t.animationStart,A=t.animationEnd,T=t.noLoader,e=t.el,_=!1,v=e.getAttribute("caption"),t.gallery?function(t){if(Array.isArray(t))O=0,M=t,v=t[0].caption;else{var o=(M=[].slice.call("string"==typeof t?j.querySelectorAll(t+" [data-bp]"):t)).indexOf(e);O=-1!==o?o:0,M=M.map(function(t){return{el:t,src:t.getAttribute("data-bp"),caption:t.getAttribute("caption")}})}_=!0,d=M[O].src,!~D.indexOf(d)&&W(!0),M.length>1?(n[I](z),z[N]=O+1+"/"+M.length,L||(n[I](k),n[I](E))):M=!1,(r=i).src=d}(t.gallery):l?(W(!0),r=c,function(t){var e=t?"www.youtube.com/embed/"+l+"?html5=1&rel=0&showinfo=0&playsinline=1&":"player.vimeo.com/video/"+l+"?";u.src="https://"+e+"autoplay=1"}(!!t.ytSrc)):t.imgSrc?(_=!0,d=t.imgSrc,!~D.indexOf(d)&&W(!0),(r=i).src=d):t.vidSrc?(W(!0),function(t){Array.isArray(t)?(r=a.cloneNode(),t.forEach(function(t){var e=j[H]("SOURCE");e.src=t,e.type="video/"+t.match(/.(\w+)$/)[1],r[I](e)})):(r=a).src=t}(t.vidSrc),U()):(r=i).src="IMG"===e.tagName?e.src:C.getComputedStyle(e).backgroundImage.replace(/^url|[(|)|'|"]/g,""),n[I](r),j.body[I](n)}}()},function(t,e,o){"use strict";o.r(e);var n=o(0),r=o.n(n);const i=[],a=window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(t){window.setTimeout(t,1e3/60)},c=()=>{i.forEach(({el:t,options:e})=>{((t,e)=>{const o=window.scrollY||window.pageYOffset,n=t.getBoundingClientRect().top+o,r=o,i=o+window.innerHeight,a=n,c=n+t.clientHeight;return c>=r&&c<=i||a<=i&&a>=r})(t)&&(e.callback(),(t=>{i.forEach(({el:e},o)=>{e===t&&i.splice(o,1)})})(t))})};r()(()=>{const t=()=>a(c);t(),window.addEventListener("scroll",t),window.addEventListener("resize",t)});var u=o(1),d=o.n(u);var p=(t,e={})=>{const o=((t,e,o)=>Object.assign(((t,e)=>{const o=Object.keys(e).reduce((e,o)=>{const n=`data-${(t=>t.replace(/([a-zA-Z])(?=[A-Z])/g,"$1-").toLowerCase())(o)}`,r=t.getAttribute(n);return r&&(e[o]=r),e},{});return Object.assign(e,o)})(t,e),o))(t,{video:""},e);(t=>{t._videoPopupHandler&&t.removeEventListener("click",t._videoPopupHandler)})(t),t._videoPopupHandler=(e=>{e.preventDefault(),l(t,o.video)}),t.addEventListener("click",t._videoPopupHandler)};const l=(t,e)=>{const o={el:t,noLoader:!0};if(Array.isArray(e))o.vidSrc=e;else{const t=(t=>t.match(/^\d+$/g)?"vimeo":t.match(/^https?:\/\//g)?"url":"youtube")(e);"vimeo"===t?o.vimeoSrc=e:"youtube"===t?o.ytSrc=e:o.vidSrc=e}d()(o)};r()(function(){document.querySelectorAll(".eb-video-popup").forEach(function(t){var e={};t.getAttribute("data-webm")&&(e.video=[t.getAttribute("data-webm"),t.getAttribute("data-video")]),p(t,e)})})}]);