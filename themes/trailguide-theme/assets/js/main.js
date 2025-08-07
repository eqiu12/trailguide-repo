
(function(){
  // Leaflet map init for [tg_map]
  function initMaps(){
    document.querySelectorAll('.tg-map').forEach(function(el){
      if (el.dataset.inited) return;
      el.dataset.inited = '1';
      var lat = parseFloat(el.dataset.lat || '0');
      var lng = parseFloat(el.dataset.lng || '0');
      var zoom = parseInt(el.dataset.zoom || '12', 10);
      var map = L.map(el).setView([lat, lng], zoom);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
      }).addTo(map);
      L.marker([lat,lng]).addTo(map);
    });
  }
  document.addEventListener('DOMContentLoaded', initMaps);
})();
