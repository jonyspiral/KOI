(function(w){
  try{ w.Koi = w.Koi || angular.module('Koi', []); }
  catch(e){ w.Koi = angular.module('Koi', []); }

  w.funciones = w.funciones || {
    toInt: function(v){ var n=parseInt(v,10); return isNaN(n)?0:n; },
    getJSONType: function(o){ if(!o) return 'OK';
      if(typeof o==='string'){ try{o=JSON.parse(o);}catch(_){return 'OK';} }
      return o.tipo || o.status || 'OK';
    },
    getJSONMsg: function(o){ if(!o) return '';
      if(typeof o==='string'){ try{o=JSON.parse(o);}catch(_){return o;} }
      return o.msg || o.message || '';
    },
    jsonNull:  function(o){ return o==null; },
    jsonEmpty: function(o){ return !o || (typeof o==='object' && Object.keys(o).length===0); },
    jsonError: function(o){ if(!o) return false;
      var t=((o.tipo||o.status)||'').toString().toUpperCase();
      return !!o.error || t==='ERROR' || t==='ERR';
    },
    jsonAlert: function(o){ if(!o) return false;
      var t=((o.tipo||o.status)||'').toString().toUpperCase();
      return t==='ALERTA' || t==='WARN' || t==='WARNING';
    },
    jsonObject: function(o){
      if(!o) return {}; if(typeof o==='object') return o;
      try{ return JSON.parse(o); }catch(_){ return {}; }
    },
    cambiarTitulo: function(t){ if(t) document.title = t; }
  };
})(window);
