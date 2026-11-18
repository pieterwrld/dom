(function(){
'use strict';
var U = window.DOM.U, eur = window.DOM.eur;
var $  = function(s,c){return (c||document).querySelector(s);};
var $$ = function(s,c){return Array.prototype.slice.call((c||document).querySelectorAll(s));};
var SZ = {
shoe:['40','41','42','43','44','45','46'],
cl:['S','M','L','XL','XXL'],
clx:['XS','S','M','L','XL','XXL'],
jn:['30/S','31/S/M','32/M','33/M/L','34/L','36/XL','38/XXL'],
os:['One size']
};
var P = [
{c:'sneakers',b:'Hermes',t:'Hermes bouncing leer/suede | zwart & wit',p:185,s:138.75,z:'shoe',i:['2025/03/IMG_6820-300x450.jpeg','2025/03/img_9380-300x450.jpeg','2025/03/img_9382-300x450.jpeg']},
{c:'sneakers',b:'Hermes',t:'Hermes bouncing technical/suede | Navy Blue',p:185,n:1,z:'shoe',i:['2026/05/img_9991-300x450.jpeg','2026/05/img_9993-1-300x450.jpeg']},
{c:'sneakers',b:'Louis Vuitton',t:'LV Runner Tatic sneakers | black on black',p:185,s:148,z:'shoe',i:['2025/10/IMG_6874-300x450.jpeg','2025/10/IMG_6875-300x450.jpeg']},
{c:'sneakers',b:'Dior',t:'Dior B22 | wit',p:185,z:'shoe',i:['2025/03/IMG_6869-300x450.jpeg','2025/03/IMG_6872-300x450.jpeg']},
{c:'sneakers',b:'Prada',t:"Prada cup's | grey / silver",p:185,n:1,z:'shoe',i:['2026/11/img_1049-300x450.jpeg','2026/11/img_1052-300x450.jpeg']},
{c:'sneakers',b:'Prada',t:"Prada cup's | black / black",p:185,n:1,z:'shoe',i:['2026/11/img_1058-1-300x450.jpeg','2026/11/img_1060-1-300x450.jpeg']},
{c:'sneakers',b:'Loro Piana',t:'Loro Piana week end walk | technical mesh',p:185,s:148,z:'shoe',i:['2025/10/img_8586-1-300x450.jpeg','2025/10/img_8590-1-300x450.jpeg']},
{c:'sneakers',b:'Chanel',t:'Chanel sneakers | Wit',p:185,z:'shoe',i:['2025/12/IMG_8765-300x450.jpeg','2025/12/IMG_8766-300x450.jpeg']},
{c:'sneakers',b:'Balenciaga',t:'Balenciaga Tracks | Black',p:195,s:136.5,z:'shoe',i:['2025/03/img_8894-300x450.jpeg','2025/03/img_8895-300x450.jpeg']},
{c:'sneakers',b:'Dior',t:'Dior B30 | all grey',p:185,s:129.99,z:'shoe',i:['2025/03/img_6830-300x450.jpeg','2025/03/img_6832-300x450.jpeg']},
{c:'sneakers',b:'Louis Vuitton',t:'LV Skate sneakers',p:185,s:129.99,o:1,z:'shoe',i:['2025/09/IMG_8283-300x450.jpeg','2025/09/IMG_8284-300x450.jpeg']},
{c:'sneakers',b:'Hermes',t:'Hermes bouncing | Etoupe',p:185,z:'shoe',i:['2026/06/IMG_9852-300x450.jpeg','2026/06/IMG_9854-300x450.jpeg']},
{c:'sneakers',b:'Dior',t:'Dior B27 Uptown low | zwart & wit',p:185,z:'shoe',i:['2026/02/img_9337-300x450.jpeg','2026/02/img_9339-300x450.jpeg']},
{c:'sneakers',b:'Louis Vuitton',t:'LV Run Away sneakers | wit',p:185,s:148,z:'shoe',i:['2025/09/IMG_8296-300x450.jpeg','2025/09/IMG_8294-300x450.jpeg']},
{c:'jassen',b:'Canada Goose',t:'Canada Goose Macmillan | black / black logo',p:199.99,s:149.99,z:'clx',i:['2025/10/9e7abb0d-4e60-42fc-8880-db9eb38627ea-300x450.jpeg','2025/10/c0c7cf32-2198-4444-9005-ac1efe5543e2-300x450.jpeg']},
{c:'jassen',b:'Moncler',t:'Moncler Madeira | Grey',p:199.99,n:1,z:'cl',i:['2026/09/IMG_1469-300x450.jpeg','2026/09/IMG_1470-300x450.jpeg']},
{c:'jassen',b:'Burberry',t:'Burberry winterjas | afneembare mouwen',p:199.99,s:159.99,z:'clx',i:['2025/10/IMG_8311-300x450.jpeg','2025/10/IMG_8312-300x450.jpeg']},
{c:'jassen',b:'Moncler',t:'Moncler Madeira Winterjas | blauw',p:200,s:79.99,z:'cl',i:['2025/10/599a32de-bf83-4b02-a5b7-53f947d0c83e-300x450.jpeg','2025/10/acff8fff-2055-452b-971d-30f28e12c40c-300x450.jpeg']},
{c:'jassen',b:'Moncler',t:'Moncler Grimpeurs | Olive green',p:149.99,z:'cl',i:['2026/05/8542b02d-ccab-43ca-815e-9791f9da43e1-1-300x450.jpeg','2026/05/afa9b740-0db0-46ec-8060-0e8e0dfe619f-300x450.jpeg']},
{c:'jassen',b:'Stone Island',t:'Stone Island zomerjack | zwart',p:135,s:94.5,z:'cl',i:['2026/04/IMG_6902-300x450.jpeg','2026/04/IMG_6903-300x450.jpeg']},
{c:'jassen',b:'Louis Vuitton',t:'Louis Vuitton reversible jacket | Navy blue',p:169.99,z:'cl',i:['2026/05/img_8997-300x450.jpeg','2026/05/IMG_0368-300x450.jpeg']},
{c:'jassen',b:'Dior',t:'Dior oblique cardigan | grey',p:159.99,s:119.99,z:'cl',i:['2026/02/IMG_9682-300x450.jpeg','2026/02/IMG_9683-300x450.jpeg']},
{c:'jassen',b:'Canada Goose',t:'Canada Goose Macmillan | grey / black logo',p:199.99,z:'clx',i:['2025/10/6f1a1d7a-5d91-42fc-b275-f382ec2f31a8-300x450.jpeg','2025/10/7ccf631d-23d7-4a6f-8853-75441d385ffe-300x450.jpeg']},
{c:'jeans',b:'Loro Piana',t:'Loro Piana jeans | green label',p:64.99,n:1,z:'jn',i:['2026/10/img_5406-300x450.jpeg','2026/10/img_5408-300x450.jpeg']},
{c:'jeans',b:'Amiri',t:'Amiri jeans | MX1 super ripped',p:74.99,n:1,z:'jn',i:['2026/10/img_5414-300x450.jpeg','2026/10/img_5416-300x450.jpeg']},
{c:'jeans',b:'DSQUARED2',t:'DSQ2 jeans | golden label',p:64.99,s:45.49,z:'jn',i:['2026/10/img_5338-300x450.jpeg','2026/10/img_5339-300x450.jpeg']},
{c:'jeans',b:'Gucci',t:'GG jeans | bordeaux label',p:64.99,z:'jn',i:['2026/10/img_5369-300x450.jpeg','2026/10/img_5371-300x450.jpeg']},
{c:'jeans',b:'D&G',t:'D&G jeans | grey (pocket logo)',p:64.99,z:'jn',i:['2026/10/img_5343-300x450.jpeg','2026/10/img_5345-300x450.jpeg']},
{c:'jeans',b:'D&G',t:'D&G jeans | dark blue',p:64.99,s:34.99,z:'jn',i:['2026/02/img_9545-300x450.jpeg','2026/02/img_9543-1-300x450.jpeg']},
{c:'jeans',b:'Loewe',t:'Loewe jeans | light blue (pocket logo)',p:64.99,z:'jn',i:['2026/10/img_5354-300x450.jpeg','2026/10/img_5356-300x450.jpeg']},
{c:'jeans',b:'DSQUARED2',t:'DSQ2 short jeans | black',p:50,s:35,z:'jn',i:['2025/05/IMG_7427-300x450.jpeg']},
{c:'jeans',b:'Valentino',t:'Valentino jeans | grey',p:64.99,z:'jn',i:['2026/06/IMG_0757-300x450.jpeg','2026/06/IMG_0758-300x450.jpeg']},
{c:'trainingspakken',b:'Essentials',t:'Essentials Trainingspak 1977 | Iron Black',p:159.99,s:127.99,z:'clx',i:['2025/03/IMG_6927-300x450.jpeg','2025/03/IMG_4896-scaled-300x450.jpeg']},
{c:'trainingspakken',b:'Essentials',t:'Essentials Trainingspak | Classic Fleece Timber',p:159.99,n:1,z:'clx',i:['2026/10/42f11c4d-7800-4dc1-a99b-bb3c5fe52f74-300x450.png','2026/10/img_0544-300x386.jpeg']},
{c:'trainingspakken',b:'Fear of God',t:'Essentials trainingspak | Black & White',p:159.99,s:127.99,z:'clx',i:['2026/04/IMG_8539-300x450.jpeg','2026/04/IMG_8525-300x450.jpeg']},
{c:'trainingspakken',b:'Fear of God',t:'Essentials zomerset | Seafoam',p:115,s:80.5,z:'clx',i:['2026/06/IMG_9714-300x450.jpeg','2026/06/IMG_9715-300x450.jpeg']},
{c:'trainingspakken',b:'Prada',t:'Tech suit | hooded navy blue',p:109.99,z:'cl',i:['2026/03/IMG_0027-300x450.jpeg','2026/03/IMG_0028-300x450.jpeg']},
{c:'trainingspakken',b:'Jacquemus',t:'Jacquemus zomerset | zwart',p:94.99,s:66.49,z:'cl',i:['2026/06/IMG_0679-300x450.jpeg','2026/06/IMG_0678-300x450.jpeg']},
{c:'trainingspakken',b:'Essentials',t:'Essentials Trainingspak (Dark Oatmeal)',p:160,z:'clx',i:['2025/10/IMG_6702-300x450.jpeg','2025/10/IMG_4742-300x450.jpeg']},
{c:'sweaters',b:'Stone Island',t:'Stone-Island sweater | zwart',p:59.99,s:41.99,z:'cl',i:['2026/10/img_6751-1-300x450.jpeg','2026/10/img_5319-300x450.jpeg']},
{c:'sweaters',b:'Amiri',t:'Amiri sweater | wit',p:59.99,n:1,z:'cl',i:['2026/10/9115f7bf-1ee3-4106-b35a-3c99991095cc-300x450.jpeg','2026/10/41cff6f3-3ba0-446c-9d97-33c9e5e4a440-300x450.jpeg']},
{c:'sweaters',b:'Moncler',t:'Moncler sweater logo | zwart',p:59.99,z:'cl',i:['2026/10/53f91a55-ad1b-40a8-9879-49da8f2d4749-300x450.png','2026/10/7621a6f2-1b75-4197-92d0-60a977d44e9f-300x450.png']},
{c:'sweaters',b:'Prada',t:'Prada sweater | zwart',p:59.99,z:'cl',i:['2026/10/413367a9-7921-47f1-8c9f-46c68c101718-300x450.jpeg']},
{c:'sweaters',b:'Dior',t:'CD sweater | wit',p:59.99,s:41.99,z:'cl',i:['2026/10/30b3fa72-ecf7-4b84-ae3b-71fb30dd864d-300x450.jpeg']},
{c:'tshirts',b:'Jacquemus',t:'Jacquemus t-shirt | crochet logo | black',p:50,n:1,z:'cl',i:['2026/10/IMG_1685-300x450.jpeg','2026/10/IMG_1689-300x450.jpeg']},
{c:'tshirts',b:'Celine',t:'Celine t-shirt | white',p:50,z:'cl',i:['2026/10/IMG_1663-300x450.jpeg','2026/10/IMG_1664-300x450.jpeg']},
{c:'tshirts',b:'Loro Piana',t:'Loro Piana | Mercerised T-shirt | brown',p:50,z:'cl',i:['2026/10/IMG_1677-300x450.jpeg','2026/10/IMG_1679-300x450.jpeg']},
{c:'tshirts',b:'Celine',t:'Celine t-shirt | black',p:50,z:'cl',i:['2026/10/IMG_1661-300x450.jpeg','2026/10/IMG_1666-300x450.jpeg']},
{c:'tshirts',b:'Jacquemus',t:'Jacquemus t-shirt | crochet logo | brown',p:50,z:'cl',i:['2026/10/IMG_1687-300x450.jpeg','2026/10/IMG_1688-300x450.jpeg']},
{c:'zwembroeken',b:'Burberry',t:'Burberry zwembroek | bruin',p:32.5,z:'cl',i:['2025/05/IMG_0796-300x450.jpeg']},
{c:'zwembroeken',b:'Prada',t:'Prada zwembroek | donkerblauw',p:45.5,s:31.85,z:'cl',i:['2026/06/IMG_0617-300x450.jpeg']},
{c:'zwembroeken',b:'Burberry',t:'Burberry zwembroek | babyblauw',p:32.5,z:'cl',i:['2025/05/IMG_0794-300x450.jpeg']},
{c:'slippers',b:'Burberry',t:'Burberry slides',p:80.49,s:56.34,z:'shoe',i:['2025/04/img_1024-300x450.jpeg']},
{c:'slippers',b:'Hermes',t:'Hermes Chypre sandalen | etoupè',p:90.99,z:'shoe',i:['2026/06/IMG_9723-300x450.jpeg']},
{c:'slippers',b:'Burberry',t:'Burberry slides | black/navy',p:80.49,z:'shoe',i:['2025/04/img_1028-300x450.jpeg']},
{c:'petten',b:'Loro Piana',t:'Loro Piana cap | black',p:93.5,z:'os',i:['2026/06/9ffdeec8-730c-473a-80d8-1e89eb55b7cd-300x450.jpeg']},
{c:'tassen',b:'Hermes',t:'Hermes Hac a Dos PM | Black',p:187,z:'os',i:['2025/10/img_8582-1-300x450.jpeg']},
{c:'tassen',b:'Goyard',t:'Goyard Capetien MM messenger bag | black / brown',p:170,n:1,z:'os',i:['2026/10/img_1791-300x450.jpeg']},
{c:'tassen',b:'Dior',t:'CD Saddle bag | black',p:170,s:136,z:'os',i:['2025/10/IMG_8580-300x450.jpeg']},
{c:'tassen',b:'Hermes',t:'Hermes Kelly | Depeches (Black)',p:140.25,z:'os',i:['2025/10/IMG_8579-300x450.jpeg']},
{c:'brillen',b:'Dita',t:'Dita Flight 006 | black / Gold',p:99.99,z:'os',i:['2025/03/img_5576-300x450.jpeg']},
{c:'brillen',b:'Cartier',t:'Cartier glasses | silver / black tiger',p:109.99,s:87.99,z:'os',i:['2025/05/img_5548-300x450.jpeg']},
{c:'brillen',b:'Dita',t:'Dita Flight 006 | black / Gold / brown',p:99.99,z:'os',i:['2025/03/img_5572-300x450.jpeg']}
];
P.forEach(function(x,ix){ x.id=ix; x.pop=(P.length-ix)+(x.n?22:0)+(x.s?14:0); });
var B = {
alles:{img:'2025/09/2.jpg',t:'De Outfitman Shop',g:'Alles',c:'Sneakers, jassen, jeans, trainingspakken en meer. Voor 17:00 besteld = morgen in huis.'},
sneakers:{img:'2025/03/IMG_6820-300x450.jpeg',t:'Sneakers',g:'Collectie',c:'Hermes · Dior · Louis Vuitton · Prada · Loro Piana · Balenciaga'},
jassen:{img:'2025/10/IMG_8311-300x450.jpeg',t:'(Winter-) Jassen',g:'Collectie',c:'Moncler · Canada Goose · Burberry · Stone Island'},
jeans:{img:'2026/10/img_5414-300x450.jpeg',t:'Jeans',g:'Collectie',c:'Amiri · DSQUARED2 · Loro Piana · Loewe · D&G'},
trainingspakken:{img:'2025/03/IMG_6927-300x450.jpeg',t:'Trainingspakken',g:'Collectie',c:'Essentials · Fear of God · Tech suits · Zomersets'},
sweaters:{img:'2026/10/img_6751-1-300x450.jpeg',t:'Sweaters / Hoodies',g:'Collectie',c:'Stone Island · Amiri · Moncler · Prada · CD'},
tshirts:{img:'2026/10/IMG_1685-300x450.jpeg',t:'T-Shirts',g:'3 voor €130',c:'Jacquemus · Celine · Loro Piana. Combineer 3 shirts of zwembroeken voor €130.'},
zwembroeken:{img:'2025/05/IMG_0796-300x450.jpeg',t:'Zwembroeken',g:'3 voor €130',c:'Burberry · Prada. Combineer met T-Shirts voor de 3 voor €130 deal.'},
slippers:{img:'2025/04/img_1024-300x450.jpeg',t:'Slippers',g:'Collectie',c:'Burberry slides · Hermes Chypre sandalen'},
petten:{img:'2026/06/9ffdeec8-730c-473a-80d8-1e89eb55b7cd-300x450.jpeg',t:'Petten',g:'Accessoires',c:'Loro Piana. Maakt elke outfit af.'},
tassen:{img:'2025/10/img_8582-1-300x450.jpeg',t:'Tassen',g:'Accessoires',c:'Hermes · Goyard · Louis Vuitton · CD'},
brillen:{img:'2025/03/img_5576-300x450.jpeg',t:'Brillen',g:'Accessoires',c:'Dita · Cartier. Ook colour changing modellen.'},
sale:{img:'2026/09/IMG_1469-300x450.jpeg',t:'Black Friday Sale',g:'Tot -50%',c:'Afgeprijsd uit alle collecties. Laatste maten, zolang de voorraad strekt.'}
};
var NAME={alles:'Alles',sneakers:'Sneakers',jassen:'Jassen',jeans:'Jeans',trainingspakken:'Trainingspakken',
sweaters:'Sweaters / Hoodies',tshirts:'T-Shirts',zwembroeken:'Zwembroeken',slippers:'Slippers',
petten:'Petten',tassen:'Tassen',brillen:'Brillen',sale:'Sale'};
var st = {cat:'sneakers', max:200, size:null, brands:[], sort:'pop', shown:12};
var grid=$('#grid'), empty=$('#empty'), loadw=$('#loadw');
function base(){
return P.filter(function(x){
if(st.cat === 'sale') return !!x.s;
if(st.cat === 'alles') return true;
return x.c === st.cat;
});
}
function list(){
var out = base().filter(function(x){
var price = x.s || x.p;
if(st.max < 200 && price > st.max) return false;
if(st.size && SZ[x.z].indexOf(st.size) < 0) return false;
if(st.brands.length && st.brands.indexOf(x.b) < 0) return false;
return true;
});
if(st.sort==='asc')  out.sort(function(a,b){return (a.s||a.p)-(b.s||b.p);});
if(st.sort==='desc') out.sort(function(a,b){return (b.s||b.p)-(a.s||a.p);});
if(st.sort==='new')  out.sort(function(a,b){return (b.n?1:0)-(a.n?1:0);});
if(st.sort==='pop')  out.sort(function(a,b){return b.pop-a.pop;});
if(st.sort==='disc') out.sort(function(a,b){
return (b.s?1-b.s/b.p:0)-(a.s?1-a.s/a.p:0);});
return out;
}
function card(x, ix){
var a = document.createElement('a');
a.className='pcard'; a.href='product.html'; a.setAttribute('data-rv','up');
a.style.setProperty('--d',(ix%8)*50+'ms');
var imgs = x.i.map(function(src){
return '<img src="'+U+src+'" alt="'+x.t+'" loading="lazy" draggable="false">'; }).join('');
var dots = x.i.length>1 ? '<div class="pdots">'+x.i.map(function(_,k){
return '<span class="pdot'+(k?'':' on')+'"></span>';}).join('')+'</div>' : '';
var arrows = x.i.length>1 ?
'<button class="parrow l" data-d="-1" aria-label="Vorige"><svg viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7"/></svg></button>' +
'<button class="parrow r" data-d="1" aria-label="Volgende"><svg viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></button>' : '';
var bd = '';
if(x.s) bd += '<span class="pb pb-bf">-'+Math.round((1-x.s/x.p)*100)+'%</span>';
if(x.n) bd += '<span class="pb pb-new">Nieuw</span>';
if(x.o) bd += '<span class="pb pb-out">Uitverkocht</span>';
a.innerHTML =
'<div class="pmedia"><div class="ptrack">'+imgs+'</div><span class="sheen"></span>' +
'<div class="pbadges">'+bd+'</div>'+arrows+dots +
(x.i.length>1?'<div class="slidebars">'+x.i.map(function(_,k){return '<i'+(k?'':' class="on"')+'></i>';}).join('')+'</div>':'')+'</div>' +
'<div class="pinfo"><div class="ptitle">'+x.t+'</div>' +
'<div class="pprice'+(x.s?' sale':'')+'">'+(x.s?eur(x.s)+' <s>'+eur(x.p)+'</s>':eur(x.p))+'</div>' +
'<div class="psizes">'+SZ[x.z].slice(0,6).map(function(s){return '<span>'+s+'</span>';}).join('')+'</div></div>';
a._p = x;
window.DOM.reveal(a);
return a;
}
function brandList(){
var counts = {};
base().forEach(function(x){ counts[x.b] = (counts[x.b]||0)+1; });
var names = Object.keys(counts).sort();
$('#brandList').innerHTML = names.map(function(n){
return '<button class="brb'+(st.brands.indexOf(n)>=0?' on':'')+'" data-b="'+n+'">' +
'<i><svg viewBox="0 0 24 24"><path d="M4 12l5 5L20 6"/></svg></i>'+n+'</button>';
}).join('');
}
function render(reset){
if(reset) st.shown = 12;
var L = list();
grid.innerHTML = '';
L.slice(0, st.shown).forEach(function(x,ix){ grid.appendChild(card(x,ix)); });
$('#total').textContent = L.length;
$('#shown').textContent = Math.min(st.shown, L.length);
$('#loadBar').style.width = (L.length ? Math.min(st.shown,L.length)/L.length*100 : 0)+'%';
empty.style.display = L.length ? 'none' : 'block';
loadw.style.display = L.length ? 'grid' : 'none';
$('#loadMore').style.display = st.shown >= L.length ? 'none' : 'inline-flex';
$('#fbBtn').classList.toggle('act', st.brands.length > 0);
$('#fsBtn').classList.toggle('act', !!st.size);
$('#fpBtn').classList.toggle('act', st.max < 200);
chips();
}
(function(){
function setIdx(media, idx){
var track = $('.ptrack', media), imgs = $$('img', track);
idx = Math.max(0, Math.min(imgs.length-1, idx));
media._i = idx;
track.style.transition = 'transform .6s cubic-bezier(.16,1,.3,1)';
track.style.transform = 'translate3d('+(-idx*100)+'%,0,0)';
$$('.pdot', media).forEach(function(d,k){ d.classList.toggle('on', k===idx); });
$$('.slidebars i', media).forEach(function(d,k){ d.classList.toggle('on', k===idx); });
var l=$('.parrow.l',media), r=$('.parrow.r',media);
if(l) l.disabled = idx===0;
if(r) r.disabled = idx===imgs.length-1;
}
grid.addEventListener('click', function(e){
var ar = e.target.closest('.parrow');
if(ar){ e.preventDefault(); e.stopPropagation();
var m = ar.closest('.pmedia'); setIdx(m,(m._i||0)+parseInt(ar.dataset.d,10)); return; }
});
var act=null,x0=0,dx=0,w=0;
grid.addEventListener('pointerdown', function(e){
var t = e.target.closest('.ptrack'); if(!t) return;
if($$('img',t).length < 2) return;
act=t; x0=e.clientX; dx=0; w=t.getBoundingClientRect().width;
t.classList.add('drag'); t.style.transition='none';
t.closest('.pmedia').classList.add('touched');
});
window.addEventListener('pointermove', function(e){
if(!act) return;
dx = e.clientX - x0;
var m = act.closest('.pmedia');
act.style.transform = 'translate3d('+(-(m._i||0)*w+dx)+'px,0,0)';
});
window.addEventListener('pointerup', function(){
if(!act) return;
var m = act.closest('.pmedia'), t = act;
act.classList.remove('drag'); act = null;
setIdx(m, (m._i||0) + (Math.abs(dx)>34 ? (dx<0?1:-1) : 0));
if(Math.abs(dx)>6){
t.closest('.pcard').addEventListener('click', function(ev){ ev.preventDefault(); ev.stopPropagation(); },
{capture:true, once:true});
}
dx = 0;
});
})();
function chips(){
var w = $('#chips'); w.innerHTML='';
function add(label,key,val){
var c=document.createElement('span'); c.className='chip';
c.innerHTML = label+'<button data-k="'+key+'" data-v="'+(val||'')+'" aria-label="Verwijder">' +
'<svg width="9" height="9" viewBox="0 0 24 24" stroke="#fff" stroke-width="3" fill="none"><path d="M6 6l12 12M18 6L6 18"/></svg></button>';
w.appendChild(c);
}
if(st.max<200) add('Tot '+eur(st.max),'price');
if(st.size) add('Maat '+st.size,'size');
st.brands.forEach(function(b){ add(b,'brand',b); });
w.style.display = w.children.length ? 'flex' : 'none';
}
$('#chips').addEventListener('click', function(e){
var b = e.target.closest('button'); if(!b) return;
if(b.dataset.k==='price') resetPrice();
if(b.dataset.k==='size') resetSize();
if(b.dataset.k==='brand'){
st.brands = st.brands.filter(function(x){ return x !== b.dataset.v; });
$('#fbVal').textContent = st.brands.length ? st.brands.length+' gekozen' : 'Alles';
brandList();
}
render(true);
});
function resetPrice(){ st.max=200; $('#rng').value=200; $('#rngNow').textContent='€200+'; $('#fpVal').textContent='Alles'; }
function resetSize(){ st.size=null; $$('.szb').forEach(function(s){s.classList.remove('on');}); $('#fsVal').textContent='Alles'; }
function resetBrand(){ st.brands=[]; $('#fbVal').textContent='Alles'; brandList(); }
(function(){
var pairs=[['#fpBtn','#fpPop'],['#fsBtn','#fsPop'],['#fbBtn','#fbPop'],['#soBtn','#soPop']];
pairs.forEach(function(pr){
var b=$(pr[0]), p=$(pr[1]);
b.addEventListener('click', function(e){
e.stopPropagation();
var was = p.classList.contains('on');
pairs.forEach(function(q){ $(q[1]).classList.remove('on'); $(q[0]).classList.remove('on'); });
if(!was){ p.classList.add('on'); b.classList.add('on'); }
});
p.addEventListener('click', function(e){ e.stopPropagation(); });
});
document.addEventListener('click', function(){
pairs.forEach(function(q){ $(q[1]).classList.remove('on'); $(q[0]).classList.remove('on'); });
});
})();
$('#rng').addEventListener('input', function(){
st.max = parseInt(this.value,10);
$('#rngNow').textContent = st.max>=200 ? '€200+' : eur(st.max);
$('#fpVal').textContent = st.max>=200 ? 'Alles' : '≤ '+eur(st.max);
render(true);
});
(function(){
function mk(host, arr){
host.innerHTML='';
arr.forEach(function(s){
var b=document.createElement('button'); b.className='szb'; b.textContent=s;
b.addEventListener('click', function(){
var on = b.classList.contains('on');
$$('.szb').forEach(function(q){ q.classList.remove('on'); });
st.size = on ? null : s;
if(!on) b.classList.add('on');
$('#fsVal').textContent = st.size || 'Alles';
render(true);
});
host.appendChild(b);
});
}
mk($('#szClothes'), ['XS','S','M','L','XL','XXL','32/M','34/L']);
mk($('#szShoes'), SZ.shoe);
})();
$('#brandList').addEventListener('click', function(e){
var b = e.target.closest('.brb'); if(!b) return;
var n = b.dataset.b, i = st.brands.indexOf(n);
if(i >= 0) st.brands.splice(i,1); else st.brands.push(n);
b.classList.toggle('on', i < 0);
$('#fbVal').textContent = st.brands.length ? st.brands.length+' gekozen' : 'Alles';
render(true);
});
$$('[data-sort]').forEach(function(b){
b.addEventListener('click', function(){
st.sort = b.dataset.sort;
$('#soVal').textContent = b.textContent.trim();
render(true);
});
});
$$('[data-clear]').forEach(function(b){
b.addEventListener('click', function(){
if(b.dataset.clear==='price') resetPrice();
if(b.dataset.clear==='size') resetSize();
if(b.dataset.clear==='brand') resetBrand();
render(true);
});
});
$('#resetAll').addEventListener('click', function(){ resetPrice(); resetSize(); resetBrand(); render(true); });
$('#loadMore').addEventListener('click', function(){ st.shown += 12; render(false); });
function setCat(c){
st.cat = c;
st.brands = [];
$('#fbVal').textContent = 'Alles';
$$('.qcb').forEach(function(p){ p.classList.toggle('on', p.dataset.c === c); });
var b = B[c] || B.alles, ban = $('#cbanner'), im = $('#cbImg');
ban.classList.add('swap');
setTimeout(function(){
im.removeAttribute('data-up');
im.src = U + b.img; im.alt = b.t;
$('#cbTitle').textContent = b.t; $('#cbTag').textContent = b.g; $('#cbCopy').textContent = b.c;
ban.classList.remove('swap');
}, 240);
$('#crumbCat').textContent = NAME[c];
var h=$('#catTitle'); h.classList.remove('in'); h.textContent = NAME[c];
if(window.DOM.chars) window.DOM.chars(h);
void h.offsetWidth; h.classList.add('in');
$('#catCopy').textContent = b.c;
brandList();
render(true);
}
$('#qc').addEventListener('click', function(e){
var b = e.target.closest('.qcb'); if(!b) return;
setCat(b.dataset.c);
if(window.DOM.scrollTo) window.DOM.scrollTo(window.scrollY + $('.fwrap').getBoundingClientRect().top - 70);
else window.scrollTo({top:window.scrollY + $('.fwrap').getBoundingClientRect().top - 70, behavior:'smooth'});
});
brandList();
render(true);
})();