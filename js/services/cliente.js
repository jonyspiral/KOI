Koi.factory('ServiceCliente', ['$http', function ($http) {
  var service = {
    basePath: '/content/cliente/',

    post: function (url, obj, callback) {
      $http.post(url, obj).success(function (result) {
        if (funciones.getJSONType(result) !== funciones.jsonSuccess) {
          callback(funciones.getJSONMsg(result));
        } else {
          callback(null, result);
        }
      });
    },

    addFavorito: function (articulo, callback) {
      this.post(
        this.basePath + 'favoritos/agregar.php',
        {idArticulo: articulo.idArticulo, idColor: articulo.idColorPorArticulo},
        callback
      );
    },
addFavoritoBatch: async function (favoritos) {
  const payload = { favorites: favoritos };
  console.log("📦 JSON ENVIADO:", JSON.stringify(payload));

  const res = await fetch('/content/cliente/favoritos/agregarVarios.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });

  const raw = await res.text();
  console.log("🔴 Respuesta cruda del servidor (add):", raw);

  if (!res.ok) throw new Error(`HTTP ${res.status} - ${raw || 'sin cuerpo'}`);

  let json;
  try {
    json = JSON.parse(raw);
  } catch (e) {
    console.error("❌ No se pudo parsear JSON (add):", e);
    throw e;
  }
  console.log("✅ Respuesta parseada JSON (add):", json);
  return json;
},




    removeFavorito: function (articulo, callback) {
      //console.log("articulo", articulo, "callback", callback);
      this.post(
        this.basePath + 'favoritos/borrar.php',
        {idArticulo: articulo.idArticulo, idColor: articulo.idColorPorArticulo},
        callback
      );
    },

   removeFavoritoBatch: async function (favoritos) {
  const payload = { favorites: favoritos };
  console.log("📦 JSON ENVIADO (remove):", JSON.stringify(payload));

  const res = await fetch('/content/cliente/favoritos/borrarVarios.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });

  const raw = await res.text();
  console.log("🔴 Respuesta cruda del servidor (remove):", raw);

  if (!res.ok) throw new Error(`HTTP ${res.status} - ${raw || 'sin cuerpo'}`);

  let json;
  try {
    json = JSON.parse(raw);
  } catch (e) {
    console.error("❌ No se pudo parsear JSON (remove):", e);
    throw e;
  }
  console.log("✅ Respuesta parseada JSON (remove):", json);
  return json;
},

removeAllFavs: async function () {
  const res = await fetch('/content/cliente/favoritos/borrarTodos.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' }
  });

  const raw = await res.text();
  console.log("🔴 Respuesta cruda del servidor (removeAll):", raw);

  if (!res.ok) throw new Error(`HTTP ${res.status} - ${raw || 'sin cuerpo'}`);

  let json;
  try {
    json = JSON.parse(raw);
  } catch (e) {
    console.error("❌ No se pudo parsear JSON (removeAll):", e);
    throw e;
  }
  console.log("✅ Respuesta parseada JSON (removeAll):", json);
  return json;
},


    updateCurva: function (articulo, curva, callback) {
      this.post(
        this.basePath + 'favoritos/editarCurva.php',
        {idArticulo: articulo.idArticulo, idColor: articulo.idColorPorArticulo, idCurva: curva.id, unidades: curva.unidadesSeleccionadas},
        callback
      );
    },
    updateLibre: function (articulo, callback) {
      var art = articulo && articulo.fav ? articulo.fav : articulo;

      if ((!art || !art.idArticulo || !art.idColorPorArticulo || !art.paresLibres) &&
          articulo && articulo.subArticulos && articulo.subArticulos.length === 1) {
        art = articulo.subArticulos[0];
      }

      var cantidades = [];
      if (art && art.paresLibres) {
        for (var i = 0; i < 10; i++) {
          cantidades[i] = funciones.toInt(art.paresLibres[i] || 0);
        }
      }

      this.post(
        this.basePath + 'favoritos/editarLibre.php',
        {
          idArticulo: art ? art.idArticulo : null,
          idColor: art ? art.idColorPorArticulo : null,
          cantidades: cantidades
        },
        callback
      );
    },

    confirmarPedido: function (datos, callback) {
      this.post(this.basePath + 'pedidos/agregar.php', datos, callback);
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

