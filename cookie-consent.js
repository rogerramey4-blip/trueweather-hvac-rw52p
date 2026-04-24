/* Cookie Consent — GDPR/CCPA — 2026 */
(function(){
  if(localStorage.getItem('cc_accepted')) return;
  var bar = document.createElement('div');
  bar.id = 'cc-bar';
  bar.style.cssText = 'position:fixed;bottom:0;left:0;right:0;background:#1a1a1a;color:#fff;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;z-index:99999;font-size:13px;box-shadow:0 -2px 12px rgba(0,0,0,.3);';
  bar.innerHTML = '<span>We use cookies to enhance your experience. See our <a href="cookies.html" style="color:#1d4ed8;">Cookie Policy</a> and <a href="privacy-policy.html" style="color:#1d4ed8;">Privacy Policy</a>.</span>'
    +'<div style="display:flex;gap:8px;">'
    +'<button id="cc-accept-all" style="background:#0ea5e9;color:#fff;border:none;padding:8px 18px;border-radius:4px;cursor:pointer;font-weight:700;">Accept All</button>'
    +'<button id="cc-accept-essential" style="background:#444;color:#fff;border:none;padding:8px 14px;border-radius:4px;cursor:pointer;">Essential Only</button>'
    +'</div>';
  document.body.appendChild(bar);
  function accept(all){
    localStorage.setItem('cc_accepted', all ? 'all' : 'essential');
    bar.style.display='none';
    if(all && typeof gtag === 'function') gtag('consent','update',{analytics_storage:'granted',ad_storage:'granted'});
  }
  document.getElementById('cc-accept-all').onclick = function(){ accept(true); };
  document.getElementById('cc-accept-essential').onclick = function(){ accept(false); };
})();