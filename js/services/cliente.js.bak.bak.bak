Koi.factory('ServiceCliente', ['$http', function ($http) {
  var service = {
    basePath: '/content/cliente/',



      post: function (url, obj, callback) {
          console.log("[ServiceCliente.post] →", url, obj);
          $http.post(url, obj)
              .success(function (result) {
                  console.log("[ServiceCliente.post] success ←", result);
                  if (funciones.getJSONType(result) !== funciones.jsonSuccess) {
                      var msg = funciones.getJSONMsg(result);
                      console.warn("[ServiceCliente.post] jsonError ←", msg);
                      callback(msg);
                  } else {
                      callback(null, result);
                  }
              })
              .error(function (err, status) {
                  var msg = err && err.message ? err.message : ('HTTP ' + (status || '') + ' al llamar ' + url);
                  console.error("[ServiceCliente.post] error ←", status, err, "→ msg:", msg);
                  callback(msg);
              });
      },
    addFavorito: function (articulo, callback) {
      this.post(
        this.basePath + 'favoritos/agregar.php',
        {idArticulo: articulo.idArticulo, idColor: articulo.idColorPorArticulo},
        callback
      );
    },
        removeFavorito: function (articulo, callback) {
      //console.log("articulo", articulo, "callback", callback);
      this.post(
        this.basePath + 'favoritos/borrar.php',
        {idArticulo: articulo.idArticulo, idColor: articulo.idColorPorArticulo},
        callback
      );
    },


      addFavoritoBatch: async function (favoritos) {
  const res = await fetch(this.basePath + 'favoritos/agregarVarios.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'include',                         // <-- importante
    body: JSON.stringify({ favorites: favoritos })
  });

  const txt = await res.text();
  try {
    const json = JSON.parse(txt);
    return json; // {status, message, data}
  } catch (e) {
    console.error('addFavoritoBatch parse error:', e, txt);
    throw e;
  }
},

removeFavoritoBatch: async function (favoritos) {
  const res = await fetch(this.basePath + 'favoritos/borrarVarios.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'include',                         // <-- importante
    body: JSON.stringify({ favorites: favoritos })
  });

  const txt = await res.text();
  try {
    const json = JSON.parse(txt);
    return json; // {status, message, data}
  } catch (e) {
    console.error('removeFavoritoBatch parse error:', e, txt);
    throw e;
  }
},

removeAllFavs: async function () {
  const res = await fetch('/content/cliente/favoritos/borrarTodos.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'include',          // ⬅️ envía PHPSESSID
    body: '{}'                       // (vacío, pero fuerza JSON)
  });

  const txt = await res.text();
  console.log('borrarTodos RAW:', txt);
  try {
    return JSON.parse(txt);
  } catch (e) {
    console.error('borrarTodos JSON parse error:', e);
    throw e;
  }
},

    updateCurva: function (articulo, curva, callback) {
      this.post(
        this.basePath + 'favoritos/editarCurva.php',
        {idArticulo: articulo.idArticulo, idColor: articulo.idColorPorArticulo, idCurva: curva.id, unidades: curva.unidadesSeleccionadas},
        callback
      );
    },

    updateLibre: function (articulo, callback) {
      this.post(
        this.basePath + 'favoritos/editarLibre.php',
        {idArticulo: articulo.idArticulo, idColor: articulo.idColorPorArticulo, cantidades: articulo.paresLibres},
        callback
      );
    },

      confirmarPedido: function (datos, callback) {
          console.log("[ServiceCliente.confirmarPedido] → datos:", datos);
          this.post(this.basePath + 'pedidos/agregar.php', datos, function (err, res) {
              console.log("[ServiceCliente.confirmarPedido] callback ←", { err: err, res: res });
              callback && callback(err, res);
          });
      },

    generarReportePedido: function (datos, callback) {
      this.post(this.basePath + 'pedidos/index.php', datos, callback);
    },

    cancelarPedido: function (pedido, callback) {
      this.post(this.basePath + 'pedidos/borrar.php', {id: pedido.id}, callback);
    }
  };

  return service;
}]);





