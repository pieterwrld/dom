(function(){
'use strict';
var U = 'https://deoutfitman.com/wp-content/uploads/';
var $  = function(s,c){return (c||document).querySelector(s);};
var $$ = function(s,c){return Array.prototype.slice.call((c||document).querySelectorAll(s));};
var eur = function(v){return '€' + v.toFixed(2).replace('.',',');};
var PR = {
title:'Hermes bouncing leer/suede | zwart & wit',
price:138.75,
was:185,
gallery:[
'2025/03/IMG_6820-scaled.jpeg','2025/03/img_9380-scaled.jpeg','2025/03/img_9379-scaled.jpeg',
'2025/03/img_9382-scaled.jpeg','2025/03/img_9381-scaled.jpeg','2025/03/img_9377-scaled.jpeg',
'2025/03/img_9378-scaled.jpeg','2025/03/img_9383-scaled.jpeg'
],
colors:[
{n:'Zwart & wit', i:'2025/03/IMG_6820-300x450.jpeg', g:null},
{n:'Navy Blue',   i:'2026/05/img_9991-300x450.jpeg', g:['2026/05/img_9991-300x450.jpeg','2026/05/img_9993-1-300x450.jpeg']},
{n:'Grijs',       i:'2025/12/IMG_8869-300x450.jpeg', g:['2025/12/IMG_8869-300x450.jpeg','2025/12/IMG_8828-300x450.jpeg']},
{n:'Etoupe',      i:'2026/06/IMG_9852-300x450.jpeg', g:['2026/06/IMG_9852-300x450.jpeg','2026/06/IMG_9854-300x450.jpeg']},
{n:'Zwart',       i:'2025/05/IMG_7478-300x450.jpeg', g:['2025/05/IMG_7478-300x450.jpeg','2025/05/IMG_7480-300x450.jpeg']},
{n:'Wit',         i:'2025/12/IMG_8870-300x450.jpeg', g:['2025/12/IMG_8870-300x450.jpeg','2025/12/IMG_8873-300x450.jpeg']}
],
sizes:[{s:'40',q:4},{s:'41',q:2},{s:'42',q:0},{s:'43',q:1},{s:'44',q:6},{s:'45',q:9},{s:'46',q:0}]
};
var UP = [
{t:'Loro Piana jeans | black', p:64.99, m:'Maat 32/M', i:'2026/10/img_5383-300x450.jpeg'},
{t:'Essentials Trainingspak 1977 | Iron Black', p:159.99, m:'Maat L', i:'2025/03/IMG_6927-300x450.jpeg'},
{t:'Loro Piana cap | black', p:93.5, m:'One size', i:'2026/06/9ffdeec8-730c-473a-80d8-1e89eb55b7cd-300x450.jpeg'},
{t:'Hermes Hac a Dos PM | Black', p:187, m:'One size', i:'2025/10/img_8582-1-300x450.jpeg'},
{t:'Stone-Island sweater | zwart', p:59.99, m:'Maat L', i:'2026/10/img_6751-1-300x450.jpeg'},
{t:'Amiri jeans | MX1 super ripped', p:74.99, m:'Maat 32/M', i:'2026/10/img_5414-300x450.jpeg'}
];
var SIM = [
{t:'Hermes bouncing technical/suede | Navy Blue', p:185, i:'2026/05/img_9991-300x450.jpeg', n:1},
{t:"Prada cup's | grey / silver", p:185, i:'2026/11/img_1049-300x450.jpeg', n:1},
{t:'Dior B22 | wit', p:185, i:'2025/03/IMG_6869-300x450.jpeg'},
{t:'LV Runner Tatic sneakers | black on black', p:185, i:'2025/10/IMG_6874-300x450.jpeg'},
{t:'Loro Piana week end walk | technical mesh', p:185, i:'2025/10/img_8586-1-300x450.jpeg'},
{t:'Balenciaga Tracks | Black', p:195, i:'2025/03/img_8894-300x450.jpeg'},
{t:'Dior B30 | all grey', p:185, s:154.99, i:'2025/03/img_6830-300x450.jpeg'}
];
var gi = 0, gal = PR.gallery.slice();
var track = $('#galTrack'), dots = $('#galDots'), thumbs = $('#thumbs');
function buildGal(){
track.innerHTML = gal.map(function(s,k){
return '<img src="' + U + s + '" alt="' + PR.title + '" draggable="false"' + (k ? ' loading="lazy"' : '') + '>';
}).join('');
dots.innerHTML = gal.map(function(_,k){ return '<span class="pdot' + (k?'':' on') + '"></span>'; }).join('');
thumbs.innerHTML = gal.map(function(s,k){
return '<button class="' + (k?'':'on') + '" data-k="' + k + '"><img src="' + U + s + '" alt="" loading="lazy"></button>';
}).join('');
$('#galN').textContent = gal.length;
setG(0, true);
}
function setG(i, instant){
gi = Math.max(0, Math.min(gal.length-1, i));
track.style.transition = instant ? 'none' : 'transform .66s cubic-bezier(.16,1,.3,1)';
track.style.transform = 'translate3d(' + (-gi*100) + '%,0,0)';
$$('.pdot', dots).forEach(function(d,k){ d.classList.toggle('on', k===gi); });
$$('button', thumbs).forEach(function(b,k){ b.classList.toggle('on', k===gi); });
$('#galI').textContent = gi+1;
$('#galL').disabled = gi === 0;
$('#galR').disabled = gi === gal.length-1;
}
buildGal();
$('#galL').addEventListener('click', function(e){ e.stopPropagation(); setG(gi-1); });
$('#galR').addEventListener('click', function(e){ e.stopPropagation(); setG(gi+1); });
thumbs.addEventListener('click', function(e){
var b = e.target.closest('button'); if(b) setG(parseInt(b.dataset.k,10));
});
dots.addEventListener('click', function(e){ e.stopPropagation(); });
var stage = $('#stage');
stage.addEventListener('pointermove', function(e){
var r = stage.getBoundingClientRect();
stage.style.setProperty('--gx', ((e.clientX-r.left)/r.width*100) + '%');
stage.style.setProperty('--gy', ((e.clientY-r.top)/r.height*100) + '%');
});
(function(){
var down = false, x0 = 0, dx = 0, w = 0, moved = false;
stage.addEventListener('pointerdown', function(e){
down = true; moved = false; x0 = e.clientX; dx = 0;
w = stage.getBoundingClientRect().width; track.style.transition = 'none';
});
window.addEventListener('pointermove', function(e){
if(!down) return;
dx = e.clientX - x0;
if(Math.abs(dx) > 5) moved = true;
track.style.transform = 'translate3d(' + (-gi*w + dx) + 'px,0,0)';
});
window.addEventListener('pointerup', function(){
if(!down) return;
down = false;
if(Math.abs(dx) > 42) setG(gi + (dx < 0 ? 1 : -1)); else setG(gi);
stage._moved = moved;
});
})();
var lb = $('#lb'), lbImg = $('#lbImg');
stage.addEventListener('click', function(){
if(stage._moved) { stage._moved = false; return; }
lbImg.src = U + gal[gi];
lb.classList.add('on'); document.body.classList.add('lock');
});
$('#lbX').addEventListener('click', function(){ lb.classList.remove('on'); lb.classList.remove('zoom');
lbImg.style.transform = ''; document.body.classList.remove('lock'); });
lb.addEventListener('click', function(e){
if(e.target === lbImg){ lb.classList.toggle('zoom');
lbImg.style.transform = lb.classList.contains('zoom') ? 'scale(1.7)' : ''; return; }
if(e.target === lb){ lb.classList.remove('on','zoom'); lbImg.style.transform='';
document.body.classList.remove('lock'); }
});
lbImg.addEventListener('pointermove', function(e){
if(!lb.classList.contains('zoom')) return;
var r = lbImg.getBoundingClientRect();
var px = (e.clientX-r.left)/r.width - .5, py = (e.clientY-r.top)/r.height - .5;
lbImg.style.transform = 'scale(1.7) translate(' + (-px*22) + '%,' + (-py*22) + '%)';
});
document.addEventListener('keydown', function(e){
if(e.key === 'ArrowRight') setG(gi+1);
if(e.key === 'ArrowLeft') setG(gi-1);
if(e.key === 'Escape'){ lb.classList.remove('on','zoom'); lbImg.style.transform=''; }
});
var colI = 0;
$('#cols').innerHTML = PR.colors.map(function(c,k){
return '<button class="colb' + (k?'':' on') + '" data-k="' + k + '" title="' + c.n + '">' +
'<img src="' + U + c.i + '" alt="' + c.n + '" loading="lazy"></button>';
}).join('');
$('#cols').addEventListener('click', function(e){
var b = e.target.closest('.colb'); if(!b) return;
colI = parseInt(b.dataset.k,10);
$$('.colb').forEach(function(q,k){ q.classList.toggle('on', k===colI); });
var c = PR.colors[colI];
$('#colName').textContent = c.n;
gal = c.g ? c.g.slice() : PR.gallery.slice();
buildGal();
});
var szI = -1;
$('#szRow').innerHTML = PR.sizes.map(function(s,k){
return '<button class="szp" data-k="' + k + '"' + (s.q === 0 ? ' disabled' : '') + '>' + s.s + '</button>';
}).join('');
function stockText(q){
if(q === 1) return ['Laatste op voorraad — wees er snel bij!', false];
if(q === 2) return ['Nog 2 op voorraad — twijfel niet!', false];
if(q >= 3 && q <= 6) return ['Nog ' + q + ' op voorraad — bijna uitverkocht', false];
if(q > 6) return ['Op voorraad — voor 17:00 besteld, morgen in huis', true];
return ['Deze maat is uitverkocht', false];
}
$('#szRow').addEventListener('click', function(e){
var b = e.target.closest('.szp'); if(!b || b.disabled) return;
szI = parseInt(b.dataset.k,10);
$$('.szp').forEach(function(q,k){ q.classList.toggle('on', k===szI); });
var s = PR.sizes[szI];
$('#szPick').textContent = 'Maat ' + s.s;
var r = stockText(s.q);
$('#stockTxt').textContent = r[0];
$('#stock').classList.toggle('ok', r[1]);
});
$('#atc').addEventListener('click', function(){
var b = this;
if(szI < 0){
$('#szPick').textContent = 'Kies eerst een maat';
$('#szRow').animate(
[{transform:'translateX(0)'},{transform:'translateX(-5px)'},{transform:'translateX(5px)'},{transform:'translateX(0)'}],
{duration:300, easing:'ease-in-out'});
return;
}
if(window.DOM && window.DOM.openCart) window.DOM.openCart({
t:PR.title, m:'Maat ' + PR.sizes[szI].s + ' · ' + PR.colors[colI].n,
p:PR.price, was:PR.was, i:U + PR.colors[colI].i
});
b.classList.add('ok');
b.innerHTML = 'Toegevoegd ✓';
setTimeout(function(){ b.classList.remove('ok'); b.innerHTML = 'In winkelwagen <span class="arw">&rarr;</span>'; }, 1800);
});
$$('.ai').forEach(function(ai){
var b = $('.ai-b', ai), c = $('.ai-c', ai), inr = $('.inr', ai);
b.addEventListener('click', function(){
var on = ai.classList.contains('on');
ai.classList.toggle('on', !on);
c.style.height = on ? '0px' : (inr.scrollHeight + 'px');
});
});
$$('[data-open]').forEach(function(a){
a.addEventListener('click', function(e){
e.preventDefault();
var ai = document.getElementById(a.dataset.open);
if(!ai) return;
if(!ai.classList.contains('on')) $('.ai-b', ai).click();
ai.scrollIntoView({behavior:'smooth', block:'center'});
});
});
function miniCard(x, href){
var badges = '';
if(x.n) badges += '<span class="pb pb-new">Nieuw</span>';
if(x.s) badges += '<span class="pb pb-sale">-' + Math.round((1-x.s/x.p)*100) + '%</span>';
return '<a class="pcard pcard-sm" href="' + (href||'#') + '" data-rv="up">' +
'<div class="pmedia"><img class="p1" src="' + U + x.i + '" alt="' + x.t + '" loading="lazy">' +
'<span class="sheen"></span><div class="pbadges">' + badges + '</div></div>' +
'<div class="pinfo"><div class="ptitle">' + x.t + '</div>' +
'<div class="pprice">' + (x.s ? eur(x.s) + ' <s>' + eur(x.p) + '</s>' : eur(x.p)) + '</div></div></a>';
}
$('#railUp').innerHTML = UP.map(function(x){
return '<a class="pcard pcard-sm" href="#" data-up-i="' + UP.indexOf(x) + '" data-rv="up">' +
'<div class="pmedia"><img class="p1" src="' + U + x.i + '" alt="' + x.t + '" loading="lazy">' +
'<span class="sheen"></span><span class="quick">Toevoegen</span></div>' +
'<div class="pinfo"><div class="ptitle">' + x.t + '</div>' +
'<div class="pprice">' + eur(x.p) + '</div></div></a>';
}).join('');
$('#railSim').innerHTML = SIM.map(function(x){ return miniCard(x, '#'); }).join('');
$('#railUp').addEventListener('click', function(e){
e.preventDefault();
var a = e.target.closest('[data-up-i]'); if(!a) return;
var x = UP[parseInt(a.dataset.upI,10)];
if(window.DOM && window.DOM.openCart) window.DOM.openCart({t:x.t, m:x.m, p:x.p, i:U + x.i});
var q = $('.quick', a); if(q){ q.textContent = 'Toegevoegd'; setTimeout(function(){ q.textContent = 'Toevoegen'; }, 1500); }
});
var SET = [UP[0], UP[2]];
var setSum = PR.price + SET.reduce(function(a,b){return a+b.p;}, 0);
var setNew = Math.round(setSum * 0.92 * 100)/100;
$('#setTotal').textContent = 'Set van 3: ' + eur(setNew) + ' in plaats van ' + eur(setSum) + ' — 8% voordeel';
$('#upsellSave').textContent = 'Nog ' + eur(Math.max(0, 200 - PR.price)) + ' tot gratis verzending';
$('#addSet').addEventListener('click', function(){
if(!window.DOM || !window.DOM.openCart) return;
if(szI < 0) szI = 1;
window.DOM.openCart({t:PR.title, m:'Maat ' + PR.sizes[szI].s + ' · ' + PR.colors[colI].n, p:PR.price, i:U + PR.colors[colI].i});
SET.forEach(function(x){ window.DOM.openCart({t:x.t, m:x.m, p:x.p, i:U + x.i}); });
var b = this; b.innerHTML = 'Set toegevoegd ✓';
setTimeout(function(){ b.innerHTML = 'Voeg de hele set toe <span class="arw">&rarr;</span>'; }, 1900);
});
var rio = new IntersectionObserver(function(es){
es.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('in'); rio.unobserve(e.target); } });
},{threshold:0.05});
$$('[data-rv]').forEach(function(el){ if(!el.classList.contains('in')) rio.observe(el); });
(function(){
var el = $('#pdT'); if(!el) return;
var end = new Date(2026,10,27,23,59,59);
function p(n){ return String(n).padStart(2,'0'); }
(function t(){
var ms = Math.max(0, end - new Date());
el.textContent = Math.floor(ms/864e5) + 'd ' + p(Math.floor(ms/36e5)%24) + ':' +
p(Math.floor(ms/6e4)%60) + ':' + p(Math.floor(ms/1e3)%60);
setTimeout(t, 1000);
})();
})();
(function(){
var d = new Date(); d.setDate(d.getDate() + (d.getHours() < 17 ? 1 : 2));
var dag = ['zondag','maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag'][d.getDay()];
var mnd = ['januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december'][d.getMonth()];
$('#delivDay').textContent = dag + ' ' + d.getDate() + ' ' + mnd + ' in huis';
})();
})();