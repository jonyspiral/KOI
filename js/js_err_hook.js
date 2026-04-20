(function(){
  if (window.__jsErrHook) return; window.__jsErrHook = true;
  function send(payload){
    try{
      var x=new XMLHttpRequest();
      x.open('POST','/tools/js_error_log.php',true);
      x.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
      x.send(payload);
    }catch(_){}
  }
  window.addEventListener('error', function(ev){
    var e=ev.error||{};
    var d='msg='+encodeURIComponent(ev.message)
      +'&src='+encodeURIComponent(ev.filename||location.href)
      +'&line='+(ev.lineno||0)+'&col='+(ev.colno||0);
    if (e.stack) d+='&stack='+encodeURIComponent(e.stack);
    send(d);
  });
  window.addEventListener('unhandledrejection', function(ev){
    var r=ev.reason||{};
    var d='msg='+encodeURIComponent('unhandledrejection: '+(r.message||r))
      +'&src='+encodeURIComponent(location.href)
      +'&line=0&col=0';
    if (r.stack) d+='&stack='+encodeURIComponent(r.stack);
    send(d);
  });
})();
