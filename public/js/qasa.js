(function(){
"use strict";
var $=window.$q, $$=window.$$q, PROPS=window.PROPS, TIPO_COLOR=window.TIPO_COLOR, TIPO_LABEL=window.TIPO_LABEL, fmt=window.fmt;
var VIEWS={all:[-17.389,-66.160,11.4],centro:[-17.395,-66.158,13.5],norte:[-17.376,-66.146,12.6],oeste:[-17.377,-66.185,12.6],sur:[-17.425,-66.149,13],valle:[-17.33,-66.22,10.6]};
var mapEl=$("#map"), useLeaflet=!!window.L, map=null, markers=null, markerById={}, pinById={}, currentView="all";
function shortPrice(p){
  if(p.tipo==="alquiler")return "Bs "+fmt(p.precio)+"/m";
  if(p.tipo==="anticretico")return "Bs "+Math.round(p.precio/1000)+"k";
  if(p.precio>=1e6)return "$us "+(p.precio/1e6).toFixed(1).replace(".0","")+"M";
  return "$us "+Math.round(p.precio/1000)+"k";
}
function initLeaflet(){
  map=L.map("map",{scrollWheelZoom:false});
  L.tileLayer("https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png",{attribution:"© OpenStreetMap · © CARTO",maxZoom:20}).addTo(map);
  map.setView([VIEWS.all[0],VIEWS.all[1]],VIEWS.all[2]);
  markers=L.layerGroup().addTo(map);
  mapEl.addEventListener("click",function(e){var b=e.target.closest("[data-open]");if(b){map.closePopup();window.openModal(+b.dataset.open);}});
}
function project(p){
  var minLng=-66.30,maxLng=-66.11,minLat=-17.45,maxLat=-17.26;
  var x=((p.lng-minLng)/(maxLng-minLng))*100, y=((maxLat-p.lat)/(maxLat-minLat))*100;
  return [Math.min(96,Math.max(4,x)),Math.min(94,Math.max(5,y))];
}
function initLocal(){
  mapEl.innerHTML='<svg class="contours" viewBox="0 0 800 560" preserveAspectRatio="none" aria-hidden="true"><g fill="none" stroke="rgba(29,29,31,.08)" stroke-width="1.2"><path d="M-20,120 C160,60 300,180 470,120 S760,40 830,110"/><path d="M-20,230 C140,170 320,310 500,240 S740,160 830,240"/><path d="M-20,350 C180,280 340,430 520,350 S760,270 830,360"/><path d="M-20,470 C150,400 330,540 510,460 S750,380 830,470"/></g><g stroke="rgba(29,29,31,.05)"><line x1="200" y1="0" x2="200" y2="560"/><line x1="400" y1="0" x2="400" y2="560"/><line x1="600" y1="0" x2="600" y2="560"/><line x1="0" y1="180" x2="800" y2="180"/><line x1="0" y1="370" x2="800" y2="370"/></g><text x="24" y="536" font-family="monospace" font-size="11" fill="rgba(29,29,31,.4)">MAPA ILUSTRATIVO DEL VALLE · COCHABAMBA, BOLIVIA</text></svg><div id="pinLayer" style="position:absolute;inset:0"></div>';
}
try{ if(useLeaflet){initLeaflet();} else {initLocal();} }catch(err){ useLeaflet=false; initLocal(); }

window.renderMarkers=function(){
  var list=window.filtrar();
  if(useLeaflet&&map){
    markers.clearLayers(); markerById={};
    list.forEach(function(p){
      var icon=L.divIcon({className:"qmarker",html:'<span class="qpill" style="background:'+TIPO_COLOR[p.tipo]+'">'+shortPrice(p)+"</span>",iconSize:[0,0]});
      var mk=L.marker([p.lat,p.lng],{icon}).addTo(markers);
      mk.bindPopup('<span class="pop-t">'+p.titulo+"</span>"+p.zona+" · "+TIPO_LABEL[p.tipo]+" · "+p.imgs.length+' fotos<br><span class="pop-p">'+p.cur+" "+fmt(p.precio)+p.per+'</span><br><button class="link-btn" data-open="'+p.id+'">Ver ficha y galería →</button>',{closeButton:false});
      markerById[p.id]=mk;
    });
  }else{
    var layer=mapEl.querySelector("#pinLayer"); if(!layer)return;
    layer.innerHTML=""; pinById={};
    list.forEach(function(p){
      var xy=project(p), b=document.createElement("button");
      b.className="lpin"; b.dataset.id=p.id; b.dataset.g=p.grupo;
      b.style.left=xy[0]+"%"; b.style.top=xy[1]+"%";
      b.setAttribute("aria-label",p.titulo);
      b.innerHTML='<span class="qpill" style="background:'+TIPO_COLOR[p.tipo]+'">'+shortPrice(p)+'</span><span class="tip"><b>'+p.titulo+"</b><small>"+p.zona+" · "+TIPO_LABEL[p.tipo]+"</small><small>"+p.cur+" "+fmt(p.precio)+p.per+" · "+p.imgs.length+" fotos</small></span>";
      b.addEventListener("click",function(){window.openModal(p.id)});
      layer.appendChild(b); pinById[p.id]=b;
    });
    applyView(currentView);
  }
};
function applyView(v){
  currentView=v;
  if(useLeaflet&&map){var c=VIEWS[v];map.flyTo([c[0],c[1]],c[2],{duration:window.reduced?0:1});}
  else{Object.keys(pinById).forEach(function(id){var b=pinById[id];b.style.display=(v==="all"||b.dataset.g===v)?"":"none";});}
}
$("#flyChips").addEventListener("click",function(e){
  var c=e.target.closest(".chip"); if(!c)return;
  $$("#flyChips .chip").forEach(function(x){x.classList.toggle("active",x===c)});
  applyView(c.dataset.view);
});

/* toggle Pines / Google Maps embebido */
$("#modeChips").addEventListener("click",function(e){
  var c=e.target.closest(".chip"); if(!c)return;
  $$("#modeChips .chip").forEach(function(x){x.classList.toggle("active",x===c)});
  var sec=document.getElementById("mapa");
  if(c.dataset.mode==="google"){
    sec.classList.add("gmode");
    var fr=$("#gmapWrap iframe");
    if(fr.src==="about:blank"||!fr.src)fr.src="https://maps.google.com/maps?q=Cochabamba%2C%20Bolivia&z=13&output=embed";
  }else{
    sec.classList.remove("gmode");
    if(useLeaflet&&map)setTimeout(function(){map.invalidateSize()},200);
  }
});

window.showOnMap=function(id){
  var p=PROPS.filter(function(x){return x.id===id})[0]; if(!p)return;
  document.getElementById("mapa").scrollIntoView({behavior:window.reduced?"auto":"smooth"});
  setTimeout(function(){
    if(useLeaflet&&map&&markerById[id]){map.flyTo([p.lat,p.lng],15,{duration:window.reduced?0:1.1});setTimeout(function(){markerById[id].openPopup()},window.reduced?100:1200);}
    else if(pinById[id]){var b=pinById[id];b.classList.add("hot");setTimeout(function(){b.classList.remove("hot")},2600);}
  },300);
};
window.renderMarkers();
})();

(function(){
"use strict";
var hsSec=document.getElementById("destacados"),hsTrack=document.getElementById("hsTrack"),heroImg=document.getElementById("heroImg");
var hsMax=0;
function hsLayout(){
  if(window.reduced||innerWidth<900){hsSec.style.height="";hsTrack.style.transform="";return;}
  hsMax=Math.max(0,hsTrack.scrollWidth-innerWidth+innerWidth*.06);
  hsSec.style.height=(hsMax+innerHeight)+"px";
}
var ticking=false;
function onScroll(){
  var y=scrollY;
  if(!window.reduced){
    if(heroImg&&y<document.querySelector(".hero").offsetHeight+300)heroImg.style.transform="translateY("+y*.12+"px) scale(1.08)";
    if(hsMax>0){var r=hsSec.getBoundingClientRect();
      if(r.top<innerHeight&&r.bottom>0){var prog=Math.min(1,Math.max(0,-r.top/(hsSec.offsetHeight-innerHeight)));hsTrack.style.transform="translate3d("+(-prog*hsMax)+"px,0,0)";}}
  }
  ticking=false;
}
addEventListener("scroll",function(){if(!ticking){requestAnimationFrame(onScroll);ticking=true;}},{passive:true});
addEventListener("resize",hsLayout);
addEventListener("load",function(){hsLayout();onScroll();});
hsLayout();onScroll();
})();
/* ====== PARCHE FICHA v6 ====== */
;(function(){
  console.log('[QASA] parche ficha v6 activo');
  var SVG = {
    youtube:  '<svg viewBox="0 0 24 24"><path d="M23.5 6.19a3.02 3.02 0 0 0-2.12-2.14C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.38.5A3.02 3.02 0 0 0 .5 6.19C0 8.07 0 12 0 12s0 3.93.5 5.81a3.02 3.02 0 0 0 2.12 2.14c1.88.5 9.38.5 9.38.5s7.5 0 9.38-.5a3.02 3.02 0 0 0 2.12-2.14C24 15.93 24 12 24 12s0-3.93-.5-5.81zM9.55 15.57V8.43L15.82 12l-6.27 3.57z"/></svg>',
    tiktok:   '<svg viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>',
    instagram:'<svg viewBox="0 0 24 24"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 3.25.15 4.77 1.69 4.92 4.92.06 1.27.07 1.65.07 4.85 0 3.2-.01 3.58-.07 4.85-.15 3.23-1.66 4.77-4.92 4.92-1.27.06-1.64.07-4.85.07-3.2 0-3.58-.01-4.85-.07-3.26-.15-4.77-1.7-4.92-4.92-.06-1.27-.07-1.64-.07-4.85 0-3.2.01-3.58.07-4.85.15-3.23 1.66-4.77 4.92-4.92 1.27-.06 1.65-.07 4.85-.07zM12 0C8.74 0 8.33.01 7.05.07 2.7.27.27 2.69.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.2 4.36 2.62 6.78 6.98 6.98 1.28.06 1.69.07 4.95.07s3.67-.01 4.95-.07c4.36-.2 6.78-2.62 6.98-6.98.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95C21.73 2.69 19.31.27 14.95.07 13.67.01 13.26 0 12 0zm0 5.84A6.16 6.16 0 1 0 18.16 12 6.16 6.16 0 0 0 12 5.84zm0 10.16A4 4 0 1 1 16 12a4 4 0 0 1-4 4zm6.41-11.85a1.44 1.44 0 1 0 1.43 1.44 1.44 1.44 0 0 0-1.43-1.44z"/></svg>',
    facebook: '<svg viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z"/></svg>',
    play:     '<svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>'
  };
  function findProp(modal){
    var t = modal.querySelector('h1,h2,h3');
    var title = t ? t.textContent.trim() : '';
    var P = window.PROPS || [];
    for (var i=0;i<P.length;i++){ if (P[i].titulo === title) return P[i]; }
    return null;
  }
  function counterEl(modal){
    var els = modal.querySelectorAll('div,span,b');
    for (var i=0;i<els.length;i++){
      var t = (els[i].textContent||'').trim();
      if (/^\d+\s*\/\s*\d+$/.test(t) && els[i].children.length === 0) return els[i];
    }
    return null;
  }
  function navBtns(modal){
    var btns = modal.querySelectorAll('button'), prev = null, next = null;
    for (var i=0;i<btns.length;i++){
      var t = (btns[i].textContent||'').trim();
      if (t === '‹' || t === '<' || t === '←' || t === '❮') prev = btns[i];
      if (t === '›' || t === '>' || t === '→' || t === '❯') next = btns[i];
    }
    return {prev:prev, next:next};
  }
  function socialIcons(p){
    var S = (p && p.social) ? p.social : {}, out = '';
    if (S.youtube)   out += '<a class="qx-ico" title="YouTube" href="'+S.youtube+'" target="_blank" rel="noopener">'+SVG.youtube+'</a>';
    if (S.tiktok)    out += '<a class="qx-ico" title="TikTok" href="'+S.tiktok+'" target="_blank" rel="noopener">'+SVG.tiktok+'</a>';
    if (S.instagram) out += '<a class="qx-ico" title="Instagram" href="'+S.instagram+'" target="_blank" rel="noopener">'+SVG.instagram+'</a>';
    if (S.facebook)  out += '<a class="qx-ico" title="Facebook" href="'+S.facebook+'" target="_blank" rel="noopener">'+SVG.facebook+'</a>';
    return out;
  }
  function showVideo(st, ctr){
    st.videoActive = true; st.slide.style.display = 'block';
    if (st.main) st.main.style.opacity = '0';
    if (st.thumb) st.thumb.classList.add('on');
    if (ctr) ctr.textContent = (st.N+1) + ' / ' + (st.N+1);
    var v = st.slide.querySelector('video'); if (v && v.play) v.play();
  }
  function hideVideo(st, ctr){
    st.videoActive = false; st.slide.style.display = 'none';
    if (st.main) st.main.style.opacity = '1';
    if (st.thumb) st.thumb.classList.remove('on');
    if (ctr) ctr.textContent = st.idx + ' / ' + (st.N+1);
  }
  function enhance(){
    var nodes = document.querySelectorAll('body > div, body > section, body > dialog, body > aside');
    for (var m=0;m<nodes.length;m++){
      var modal = nodes[m];
      var cs = getComputedStyle(modal);
      if (cs.position !== 'fixed' || cs.display === 'none' || cs.visibility === 'hidden' || modal.offsetHeight < 300) continue;
      var btns = modal.querySelectorAll('button, a'), btn = null;
      for (var b=0;b<btns.length;b++){ if (btns[b].textContent.indexOf('Agendar visita') !== -1){ btn = btns[b]; break; } }
      if (!btn) continue;
      var p = findProp(modal);
      var cur = p ? p.titulo : '__sin__';
      if (modal.getAttribute('data-qx-title') === cur) continue;
      modal.setAttribute('data-qx-title', cur);
      console.log('[QASA] ficha enriquecida:', cur);

      var olds = modal.querySelectorAll('.qx-slide, .qx-thumb, .qx-extra, .qx-video-box, .qx-social-box');
      for (var o=0;o<olds.length;o++) olds[o].remove();
      modal.classList.add('qx-modal-fix');

      var ctr = counterEl(modal);
      var N = 0;
      if (ctr){ var m0 = (ctr.textContent||'').match(/(\d+)\s*\/\s*(\d+)/); N = m0 ? parseInt(m0[2],10) : 0; }
      var st = {N:N, idx:1, videoActive:false, slide:null, thumb:null, main:null, hasVideo:false};
      modal.__qxState = st;

      var vid = p ? (p.video || ((p.social && p.social.youtube) ? p.social.youtube : '')) : '';
      if (vid && N){
        var imgs = modal.querySelectorAll('img'), main = null, bestA = 0;
        for (var i2=0;i2<imgs.length;i2++){
          var a = imgs[i2].offsetWidth * imgs[i2].offsetHeight;
          if (a > bestA){ bestA = a; main = imgs[i2]; }
        }
        if (main){
          st.main = main;
          var wrap = main.parentElement; wrap.style.position = 'relative';
          var slide = document.createElement('div'); slide.className = 'qx-slide';
          if (vid.indexOf('youtu') !== -1){
            var mm = vid.match(/(?:youtu\.be\/|v=|embed\/)([\w-]{11})/);
            slide.innerHTML = mm ? '<iframe src="https://www.youtube.com/embed/'+mm[1]+'?autoplay=1&mute=1" allow="autoplay; encrypted-media" allowfullscreen></iframe>' : '';
          } else if (vid.indexOf('tiktok.com') !== -1 || vid.indexOf('instagram.com') !== -1){
            slide.innerHTML = '<div class="qx-slide-link"><a href="'+vid+'" target="_blank" rel="noopener">▶ Ver video en la red ↗</a></div>';
          } else {
            slide.innerHTML = '<video src="'+vid+'" muted loop playsinline controls autoplay></video>';
          }
          wrap.appendChild(slide); st.slide = slide; st.hasVideo = true;

          var others = [];
          for (var i3=0;i3<imgs.length;i3++){ if (imgs[i3] !== main) others.push(imgs[i3]); }
          if (others.length){
            var t0 = others[0];
            var strip = (t0.closest('button') ? t0.closest('button') : t0).parentElement;
            var th = document.createElement('button');
            th.type = 'button'; th.className = 'qx-thumb'; th.title = 'Video tour'; th.innerHTML = SVG.play;
            strip.appendChild(th); st.thumb = th;
          }
          var nb0 = navBtns(modal);
          [nb0.prev, nb0.next, ctr].forEach(function(el){ if (el) el.style.zIndex = '12'; });
          ctr.textContent = '1 / ' + (N+1);
        }
      }
      if (ctr && !modal.__qxObs){
        modal.__qxObs = true;
        new MutationObserver(function(){
          var s = modal.__qxState; if (!s || !s.N) return;
          var mm2 = (ctr.textContent||'').match(/(\d+)\s*\/\s*(\d+)/); if (!mm2) return;
          var k = parseInt(mm2[1],10), tot = parseInt(mm2[2],10);
          if (s.videoActive) return;
          if (s.slide && s.slide.style.display === 'block') hideVideo(s, ctr);
          s.idx = k;
          if (s.hasVideo && tot === s.N) ctr.textContent = k + ' / ' + (s.N+1);
        }).observe(ctr, {childList:true, characterData:true, subtree:true});
      }
      if (!modal.__qxNav){
        modal.__qxNav = true;
        modal.addEventListener('click', function(e){
          var s = modal.__qxState; if (!s || !s.hasVideo) return;
          var t = e.target.closest ? e.target.closest('button') : null; if (!t) return;
          if (s.thumb && t === s.thumb){ e.stopPropagation(); e.preventDefault(); showVideo(s, counterEl(modal)); return; }
          var nb = navBtns(modal);
          if (t === nb.next){
            if (s.videoActive){ hideVideo(s, counterEl(modal)); return; }
            if (s.idx >= s.N){ e.stopPropagation(); e.preventDefault(); showVideo(s, counterEl(modal)); return; }
          }
          if (t === nb.prev && s.videoActive){ e.stopPropagation(); e.preventDefault(); hideVideo(s, counterEl(modal)); return; }
        }, true);
      }
      var soc = socialIcons(p);
      if (soc){
        var panel = btn.parentElement;
        for (var up=0; up<5 && panel; up++){ if (panel.querySelector('h1,h2,h3')) break; panel = panel.parentElement; }
        var sb = document.createElement('div');
        sb.className = 'qx-extra qx-social-box';
        sb.innerHTML = '<span class="qx-label">Seguinos</span><div class="qx-social">'+soc+'</div>';
        (panel || modal).appendChild(sb);
      }
    }
  }
  if (window.MutationObserver){
    new MutationObserver(function(){ setTimeout(enhance, 60); })
      .observe(document.body, {childList:true, subtree:true, attributes:true, attributeFilter:['class','style','hidden']});
  }
  document.addEventListener('click', function(){ setTimeout(enhance, 120); setTimeout(enhance, 450); }, true);
})();