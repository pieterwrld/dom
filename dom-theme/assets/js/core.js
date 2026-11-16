(function(){
'use strict';
var RM = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
var FINE = window.matchMedia('(pointer:fine)').matches;
var $  = function(s,c){return (c||document).querySelector(s);};
var $$ = function(s,c){return Array.prototype.slice.call((c||document).querySelectorAll(s));};
var lerp=function(a,b,t){return a+(b-a)*t;};
var clamp=function(v,a,b){return Math.max(a,Math.min(b,v));};
var eur = function(v){return '€'+v.toFixed(2).replace('.',',');};
window.DOM = window.DOM || {}; window.DOM.eur = eur;

/* ============ 0. IMAGE FALLBACK ============ */
window.addEventListener('error', function(e){
  var t = e.target;
  if(!t || t.tagName !== 'IMG') return;
  if(t.classList.contains('logo')){ var b=t.closest('.brand'); if(b) b.classList.add('txt'); return; }
  var box = t.parentElement; if(!box) return;
  box.classList.add('ph');
  var s = t.getAttribute('src')||'', seed=0;
  for(var i=0;i<s.length;i++) seed=(seed*31+s.charCodeAt(i))%200;
  box.style.setProperty('--ph',(60+seed)+'deg');
  if(!box.querySelector('.ph-l')){
    var l=document.createElement('span'); l.className='ph-l';
    l.textContent=t.getAttribute('alt')||''; box.appendChild(l);
  }
  t.style.visibility='hidden';
}, true);

/* ============ 1. SMOOTH SCROLL ============ */
(function(){
  if(RM || !FINE) return;
  var target=window.scrollY, cur=target, applied=target, run=false;
  function max(){ return document.documentElement.scrollHeight - window.innerHeight; }
  window.addEventListener('wheel', function(e){
    if(document.body.classList.contains('lock')) return;
    if(e.ctrlKey) return;
    if(e.target.closest && e.target.closest('.drw,.menu,.crail,.rail,.thumbs,.bfs-in')) return;
    e.preventDefault();
    target = clamp(target + e.deltaY*(e.deltaMode===1?18:1), 0, max());
    if(!run){ run=true; requestAnimationFrame(tick); }
  }, {passive:false});
  function tick(){
    if(Math.abs(window.scrollY-applied)>3){ target=cur=window.scrollY; }
    cur = lerp(cur,target,.13);
    if(Math.abs(target-cur)<.4){ cur=target; run=false; }
    applied=cur; window.scrollTo(0,cur);
    if(run) requestAnimationFrame(tick);
  }
  window.DOM.scrollTo = function(y){ target=clamp(y,0,max()); if(!run){run=true;requestAnimationFrame(tick);} };
})();

/* ============ 2. TEXT MOTION ============ */
function splitWords(el){
  var items=[];
  (function walk(node){
    Array.prototype.slice.call(node.childNodes).forEach(function(ch){
      if(ch.nodeType===3){
        if(!ch.textContent.trim()) return;
        var frag=document.createDocumentFragment();
        ch.textContent.split(/(\s+)/).forEach(function(p){
          if(!p.trim()){ frag.appendChild(document.createTextNode(' ')); return; }
          var w=document.createElement('span'); w.className='word';
          var i=document.createElement('i'); i.textContent=p;
          w.appendChild(i); frag.appendChild(w); items.push(i);
        });
        ch.parentNode.replaceChild(frag,ch);
      } else if(ch.nodeType===1){
        if(ch.classList && (ch.classList.contains('silver')||ch.classList.contains('redchrome')||ch.classList.contains('acc'))){
          var w2=document.createElement('span'); w2.className='word';
          var i2=document.createElement('i');
          ch.parentNode.replaceChild(w2,ch); i2.appendChild(ch); w2.appendChild(i2);
          items.push(i2);
        } else walk(ch);
      }
    });
  })(el);
  items.forEach(function(i,ix){ i.style.setProperty('--d',(ix*55)+'ms'); });
}
function splitChars(el){
  var txt = el.textContent, out = '';
  txt.split('').forEach(function(c,ix){
    out += c === ' ' ? ' ' : '<span class="ch" style="--d:'+(ix*26)+'ms">'+c+'</span>';
  });
  el.innerHTML = out;
}
if(!RM){
  $$('.split').forEach(splitWords);
  $$('.chars').forEach(splitChars);
}
window.DOM.chars = function(el){ if(!RM) splitChars(el); };
window.DOM.words = function(el){ if(!RM) splitWords(el); };

/* ============ 3. REVEAL + COUNTERS ============ */
var RIO = new IntersectionObserver(function(es){
  es.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('in'); RIO.unobserve(e.target); } });
}, {rootMargin:'0px 0px -7% 0px', threshold:0.05});
window.DOM.reveal = function(el){ RIO.observe(el); };
$$('[data-rv],.split,.chars,.hair,.tx,.rev-sum,.usp').forEach(function(el){ RIO.observe(el); });

var CIO = new IntersectionObserver(function(es){
  es.forEach(function(e){
    if(!e.isIntersecting) return;
    var el=e.target, to=parseFloat(el.dataset.count), t0=performance.now();
    (function run(now){
      var p=clamp((now-t0)/1400,0,1);
      el.textContent = Math.round(to*(1-Math.pow(1-p,3)));
      if(p<1) requestAnimationFrame(run);
    })(t0);
    CIO.unobserve(el);
  });
}, {threshold:0.4});
$$('[data-count]').forEach(function(el){ CIO.observe(el); });

/* ============ 4. HEADER / PARALLAX ============ */
(function(){
  var hdr=$('#siteHeader'), hero=$('#hero'), media=$('.hero-media'), tick=false;
  function frame(){
    var y=window.scrollY;
    if(hdr) hdr.classList.toggle('stuck', y>6);
    if(media && hero){
      var p=clamp(y/(hero.offsetHeight||1),0,1);
      media.style.transform='translate3d(0,'+(p*8)+'%,0)';
    }
    tick=false;
  }
  window.addEventListener('scroll', function(){ if(!tick){tick=true;requestAnimationFrame(frame);} },{passive:true});
  frame();
})();

/* ============ 5. HI-RES UPGRADE ============ */
(function(){
  var io=new IntersectionObserver(function(es){
    es.forEach(function(e){
      if(!e.isIntersecting) return;
      var img=e.target; io.unobserve(img);
      var src=img.getAttribute('src');
      if(!src || src.indexOf('-300x450')<0) return;
      var c=[src.replace('-300x450','-scaled'),src.replace('-300x450','')];
      (function test(i){
        if(i>=c.length) return;
        var p=new Image();
        p.onload=function(){ if(p.naturalWidth>320) img.src=c[i]; else test(i+1); };
        p.onerror=function(){ test(i+1); };
        p.src=c[i];
      })(0);
    });
  },{rootMargin:'300px'});
  $$('img[data-up]').forEach(function(i){ io.observe(i); });
})();

/* ============ 6. CURSOR ============ */
(function(){
  if(!FINE||RM) return;
  var cur=$('#cur'); if(!cur) return;
  var cx=innerWidth/2,cy=innerHeight/2,tx=cx,ty=cy;
  window.addEventListener('pointermove',function(e){ tx=e.clientX;ty=e.clientY;cur.classList.add('vis'); });
  document.addEventListener('pointerover',function(e){
    cur.classList.toggle('big', !!(e.target.closest && e.target.closest('.ctile,.accb,.pcard,.rev,.kk,.cv,.btn,.dealbar')));
  });
  window.addEventListener('pointerleave',function(){ cur.classList.remove('vis'); });
  (function loop(){
    cx=lerp(cx,tx,.22); cy=lerp(cy,ty,.22);
    cur.style.transform='translate3d('+cx+'px,'+cy+'px,0) translate(-50%,-50%)';
    requestAnimationFrame(loop);
  })();
})();

/* ============ 7. HERO WEBGL ============ */
(function(){
  var cv=$('#glHero'); if(!cv) return;
  var gl=null;
  try{ gl=cv.getContext('webgl',{alpha:true,antialias:false,premultipliedAlpha:false}); }catch(e){}
  if(!gl){ cv.style.display='none'; return; }
  var VS='attribute vec2 p;void main(){gl_Position=vec4(p,0.,1.);}';
  var FS=['precision highp float;uniform vec2 u_res;uniform float u_t;uniform vec2 u_m;',
  'float hash(vec2 p){return fract(sin(dot(p,vec2(127.1,311.7)))*43758.5453);}',
  'float nz(vec2 p){vec2 i=floor(p),f=fract(p);f=f*f*(3.-2.*f);',
  'float a=hash(i),b=hash(i+vec2(1.,0.)),c=hash(i+vec2(0.,1.)),d=hash(i+vec2(1.,1.));',
  'return mix(mix(a,b,f.x),mix(c,d,f.x),f.y);}',
  'float fbm(vec2 p){float v=0.,a=.5;for(int i=0;i<5;i++){v+=a*nz(p);p*=2.03;a*=.5;}return v;}',
  'void main(){vec2 uv=gl_FragCoord.xy/u_res;vec2 p=uv;p.x*=u_res.x/u_res.y;',
  'vec2 mo=(u_m-0.5)*0.28;float t=u_t*0.042;',
  'vec2 q=vec2(fbm(p*1.5+t+mo),fbm(p*1.5-t*1.15+4.2-mo));',
  'float h=fbm(p*2.05+q*1.6+t*0.5);float e=0.004;',
  'float hx=fbm((p+vec2(e,0.))*2.05+q*1.6+t*0.5);',
  'float hy=fbm((p+vec2(0.,e))*2.05+q*1.6+t*0.5);',
  'vec3 n=normalize(vec3((h-hx)*2.2,(h-hy)*2.2,0.06));',
  'vec3 L=normalize(vec3(0.42,0.78,0.48));float dif=max(dot(n,L),0.0);',
  'float spec=pow(dif,22.0);',
  'vec3 col=mix(vec3(0.36,0.30,0.32),vec3(0.90,0.90,0.92),clamp(dif*1.15,0.,1.))+vec3(spec*0.55);',
  'col=mix(col,vec3(0.82,0.18,0.26),(1.0-uv.y)*0.10*(1.0-dif));',
  'float m=pow(1.0-uv.y,1.45);m*=0.45+0.55*smoothstep(0.08,0.92,h);',
  'float edge=smoothstep(0.0,0.16,uv.x)*smoothstep(1.0,0.84,uv.x);',
  'gl_FragColor=vec4(col,m*edge*0.42);}'].join('\n');
  function sh(ty,src){var s=gl.createShader(ty);gl.shaderSource(s,src);gl.compileShader(s);
    return gl.getShaderParameter(s,gl.COMPILE_STATUS)?s:null;}
  var vs=sh(gl.VERTEX_SHADER,VS),fs=sh(gl.FRAGMENT_SHADER,FS);
  if(!vs||!fs){cv.style.display='none';return;}
  var pr=gl.createProgram();gl.attachShader(pr,vs);gl.attachShader(pr,fs);gl.linkProgram(pr);
  if(!gl.getProgramParameter(pr,gl.LINK_STATUS)){cv.style.display='none';return;}
  gl.useProgram(pr);
  var buf=gl.createBuffer();gl.bindBuffer(gl.ARRAY_BUFFER,buf);
  gl.bufferData(gl.ARRAY_BUFFER,new Float32Array([-1,-1,3,-1,-1,3]),gl.STATIC_DRAW);
  var loc=gl.getAttribLocation(pr,'p');gl.enableVertexAttribArray(loc);
  gl.vertexAttribPointer(loc,2,gl.FLOAT,false,0,0);
  gl.enable(gl.BLEND);gl.blendFunc(gl.SRC_ALPHA,gl.ONE_MINUS_SRC_ALPHA);
  var uR=gl.getUniformLocation(pr,'u_res'),uT=gl.getUniformLocation(pr,'u_t'),uM=gl.getUniformLocation(pr,'u_m');
  var mx=.5,my=.5,tmx=.5,tmy=.5,vis=true;
  function size(){var d=Math.min(devicePixelRatio||1,FINE?1.75:1.25);
    var w=Math.floor(cv.clientWidth*d),h=Math.floor(cv.clientHeight*d);
    if(cv.width!==w||cv.height!==h){cv.width=w;cv.height=h;gl.viewport(0,0,w,h);}}
  addEventListener('resize',size);
  if(FINE) addEventListener('pointermove',function(e){tmx=e.clientX/innerWidth;tmy=1-e.clientY/innerHeight;});
  new IntersectionObserver(function(es){vis=es[0].isIntersecting;},{threshold:.01}).observe(cv);
  size();
  var t0=performance.now();
  (function draw(now){requestAnimationFrame(draw);if(!vis)return;
    mx=lerp(mx,tmx,.045);my=lerp(my,tmy,.045);
    gl.uniform2f(uR,cv.width,cv.height);gl.uniform1f(uT,RM?12:(now-t0)/1000);gl.uniform2f(uM,mx,my);
    gl.drawArrays(gl.TRIANGLES,0,3);})(t0);
})();

/* ============ 8. MARQUEES ============ */
(function(){
  $$('.ann-t').forEach(function(t){
    var first = t.firstElementChild;
    if(first && !t.querySelector('.ann-g + .ann-g')) t.appendChild(first.cloneNode(true));
  });
  $$('.ftm-t').forEach(function(t){
    var first = t.firstElementChild;
    if(first && !t.querySelector('.ftm-g + .ftm-g')) t.appendChild(first.cloneNode(true));
  });
})();

/* ============ 9. COUNTDOWN ============ */
(function(){
  var strip = $('#bf-strip'); if(!strip) return;
  var endStr = strip.dataset.end;
  var end = endStr ? new Date(endStr) : new Date(2026,10,27,23,59,59);
  var cd = $('#bf-cd'); if(!cd) return;
  var units = $$('.u',cd);
  var uD=null,uH=null,uM=null,uS=null;
  units.forEach(function(u){
    var d=u.dataset.unit;
    if(d==='d') uD=u.firstElementChild;
    if(d==='h') uH=u.firstElementChild;
    if(d==='m') uM=u.firstElementChild;
    if(d==='s') uS=u.firstElementChild;
  });
  if(!uD) return;
  function p(n){ return String(n).padStart(2,'0'); }
  (function t(){
    var ms=Math.max(0,end-new Date());
    var d=Math.floor(ms/864e5),h=Math.floor(ms/36e5)%24,m=Math.floor(ms/6e4)%60,s=Math.floor(ms/1e3)%60;
    uD.textContent=p(d); uH.textContent=p(h); uM.textContent=p(m); uS.textContent=p(s);
    if(ms>0) setTimeout(t,1000);
  })();
  var x=$('.bfs-close',strip);
  if(x) x.addEventListener('click',function(){ strip.style.display='none'; });
})();

/* ============ 10. RAILS ============ */
$$('.rnav[data-rail]').forEach(function(b){
  b.addEventListener('click', function(){
    var id='rail'+b.dataset.rail.charAt(0).toUpperCase()+b.dataset.rail.slice(1);
    var rail=document.getElementById(id); if(!rail) return;
    var c=rail.firstElementChild;
    rail.scrollBy({left:(c?c.getBoundingClientRect().width+12:260)*parseInt(b.dataset.dir,10)*2,behavior:'smooth'});
  });
});

/* ============ 11. MENU / CART / SEARCH OVERLAYS ============ */
(function(){
  var ovl=$('.js-overlay'), menu=$('#siteMenu');
  var searchOvl=$('.js-search-overlay');

  function lockBody(){ document.body.classList.add('lock'); }
  function unlockBody(){ document.body.classList.remove('lock'); }

  function openMenu(){
    if(!menu) return;
    menu.classList.add('on');
    if(ovl) ovl.classList.add('on');
    lockBody();
    var burger=$('.js-menu-open');
    if(burger) burger.setAttribute('aria-expanded','true');
  }

  function closeMenu(){
    if(!menu) return;
    menu.classList.remove('on');
    if(ovl) ovl.classList.remove('on');
    unlockBody();
    var burger=$('.js-menu-open');
    if(burger) burger.setAttribute('aria-expanded','false');
  }

  function openCart(){
    var drw=$('.drw');
    if(!drw) return;
    drw.classList.add('on');
    if(ovl) ovl.classList.add('on');
    lockBody();
  }

  function closeCart(){
    var drw=$('.drw');
    if(!drw) return;
    drw.classList.remove('on');
    if(ovl) ovl.classList.remove('on');
    unlockBody();
  }

  function openSearch(){
    if(!searchOvl) return;
    searchOvl.classList.add('on');
    lockBody();
    var inp=$('input[type="search"]',searchOvl);
    if(inp) setTimeout(function(){ inp.focus(); },100);
  }

  function closeSearch(){
    if(!searchOvl) return;
    searchOvl.classList.remove('on');
    unlockBody();
  }

  function closeAll(){
    closeMenu(); closeCart(); closeSearch();
  }

  $$('.js-menu-open').forEach(function(b){ b.addEventListener('click', openMenu); });
  $$('.js-menu-close').forEach(function(b){ b.addEventListener('click', closeMenu); });
  $$('.js-cart-open').forEach(function(b){ b.addEventListener('click', openCart); });
  $$('.js-cart-close').forEach(function(b){ b.addEventListener('click', closeCart); });
  $$('.js-search-open').forEach(function(b){ b.addEventListener('click', openSearch); });
  $$('.js-search-close').forEach(function(b){ b.addEventListener('click', closeSearch); });
  if(ovl) ovl.addEventListener('click', closeAll);

  document.addEventListener('keydown', function(e){
    if(e.key==='Escape') closeAll();
  });

  window.DOM.openCart = function(){ openCart(); };
  window.DOM.closeCart = closeCart;
})();

/* ============ 12. REVIEWS CAROUSEL ============ */
window.DOM.buildReviews = function(trackSel, dotsSel, prevSel, nextSel){
  var track=$(trackSel); if(!track) return;
  var cards = $$('.rev', track);
  var N = cards.length; if(!N) return;
  var page=0, timer=null;
  function per(){ var w=innerWidth; return w<=760?2:(w<=1080?3:4); }
  function pages(){ return Math.max(1, Math.ceil(N/per())); }
  var dots=$(dotsSel);
  function paint(){
    var p=per(), tot=pages();
    page = Math.max(0, Math.min(tot-1, page));
    var card=track.firstElementChild;
    if(!card) return;
    var gap=parseFloat(getComputedStyle(track).gap)||12;
    var step=(card.getBoundingClientRect().width+gap)*p;
    track.style.transform='translate3d('+(-page*step)+'px,0,0)';
    if(dots){
      if(dots.children.length!==tot){
        dots.innerHTML=''; for(var j=0;j<tot;j++){
          var d=document.createElement('span'); d.className='rvdot'; d.dataset.k=j; dots.appendChild(d); }
      }
      Array.prototype.forEach.call(dots.children,function(d,j){ d.classList.toggle('on', j===page); });
    }
  }
  function go(i){ page=(i+pages())%pages(); paint(); }
  paint();
  addEventListener('resize', paint);
  if(prevSel) $(prevSel).addEventListener('click',function(){ go(page-1); restart(); });
  if(nextSel) $(nextSel).addEventListener('click',function(){ go(page+1); restart(); });
  if(dots) dots.addEventListener('click',function(e){
    var d=e.target.closest('.rvdot'); if(d){ go(parseInt(d.dataset.k,10)); restart(); } });
  function play(){ if(!RM) timer=setInterval(function(){ go(page+1); }, 4200); }
  function stop(){ clearInterval(timer); }
  function restart(){ stop(); play(); }
  play();
  var wrap=track.closest('.rvwrap');
  if(wrap){ wrap.addEventListener('mouseenter',stop); wrap.addEventListener('mouseleave',play); }
  var x0=null;
  track.addEventListener('touchstart',function(e){ x0=e.touches[0].clientX; stop(); },{passive:true});
  track.addEventListener('touchend',function(e){
    if(x0===null) return;
    var dx=e.changedTouches[0].clientX-x0;
    if(Math.abs(dx)>40) go(page+(dx<0?1:-1));
    x0=null; play();
  });
};

/* ============ 13. COVERFLOW ============ */
window.DOM.buildCover = function(hostSel){
  var host=$(hostSel); if(!host) return;
  var cards=$$('.cv',host), N=cards.length; if(!N) return;
  var act=0, timer=null;
  var dots=$('#cvDots'), nameEl=$('#cvName'), subEl=$('#cvSub');
  function render(){
    cards.forEach(function(c,k){
      var o=k-act;
      if(o>N/2) o-=N;
      if(o<-N/2) o+=N;
      var ao=Math.abs(o);
      c.style.transform='translateX('+(o*60)+'%) translateZ('+(-ao*230)+'px) rotateY('+(-o*32)+'deg)';
      c.style.opacity=String(ao>2.2?0:1-ao*0.18);
      c.style.zIndex=String(50-Math.round(ao*10));
      c.style.filter=o===0?'none':'brightness(.5) saturate(.7)';
      c.classList.toggle('act',o===0);
    });
    if(nameEl && cards[act]){
      nameEl.textContent=cards[act].dataset.name||'';
      if(subEl) subEl.textContent=cards[act].dataset.sub||'';
    }
    $$('.cvdot').forEach(function(d,k){ d.classList.toggle('on',k===act); });
  }
  function go(i){ act=(i+N)%N; render(); }
  render();
  var pv=$('#cvPrev'), nx=$('#cvNext');
  if(pv) pv.addEventListener('click',function(){ go(act-1); restart(); });
  if(nx) nx.addEventListener('click',function(){ go(act+1); restart(); });
  if(dots) dots.addEventListener('click',function(e){
    var d=e.target.closest('.cvdot'); if(d){ go(parseInt(d.dataset.k,10)); restart(); } });
  function play(){ if(!RM) timer=setInterval(function(){ go(act+1); },3400); }
  function stop(){ clearInterval(timer); }
  function restart(){ stop(); play(); }
  play();
  var sec=host.closest('.cover-sec');
  if(sec){ sec.addEventListener('mouseenter',stop); sec.addEventListener('mouseleave',play); }
  var x0=null;
  host.addEventListener('touchstart',function(e){ x0=e.touches[0].clientX; stop(); },{passive:true});
  host.addEventListener('touchend',function(e){
    if(x0===null) return;
    var dx=e.changedTouches[0].clientX-x0;
    if(Math.abs(dx)>36) go(act+(dx<0?1:-1));
    x0=null; play();
  });
};

/* ============ 14. NEWSLETTER ============ */
(function(){
  var f=$('#nForm'); if(!f) return;
  f.addEventListener('submit',function(e){
    e.preventDefault();
    var b=f.querySelector('button'); b.textContent='Aangemeld';
    setTimeout(function(){ b.textContent='Aanmelden'; },2200);
  });
})();

/* ============ 15. STAR SVG HELPER ============ */
window.DOM.STAR = '<svg viewBox="0 0 24 24" fill="#F59E0B"><path d="M12 2l3 7h7l-5.5 4.3L18.5 21 12 16.8 5.5 21l2-7.7L2 9h7z"/></svg>';

})();
