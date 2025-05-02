CREATE TRIGGER trg_sync_colores_por_articulo_to_encinitas
ON colores_por_articulo
AFTER INSERT, UPDATE, DELETE
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @cod_articulo VARCHAR(10)
    DECLARE @cod_color_articulo VARCHAR(7)

    DECLARE cur_sync CURSOR FOR
        SELECT cod_articulo, cod_color_articulo FROM inserted
        UNION
        SELECT cod_articulo, cod_color_articulo FROM deleted

    OPEN cur_sync
    FETCH NEXT FROM cur_sync INTO @cod_articulo, @cod_color_articulo

    WHILE @@FETCH_STATUS = 0
    BEGIN
        IF EXISTS (
            SELECT 1 FROM deleted d
            WHERE d.cod_articulo = @cod_articulo AND d.cod_color_articulo = @cod_color_articulo
        ) AND NOT EXISTS (
            SELECT 1 FROM inserted i
            WHERE i.cod_articulo = @cod_articulo AND i.cod_color_articulo = @cod_color_articulo
        )
        BEGIN
            DELETE FROM encinitas.dbo.colores_por_articulo
            WHERE cod_articulo = @cod_articulo AND cod_color_articulo = @cod_color_articulo
        END
        ELSE IF EXISTS (
            SELECT 1 FROM encinitas.dbo.colores_por_articulo
            WHERE cod_articulo = @cod_articulo AND cod_color_articulo = @cod_color_articulo
        )
        BEGIN
            UPDATE encinitas.dbo.colores_por_articulo
            SET
                denom_color = i.denom_color,
                disenio = i.disenio,
                precio_minorista_usd = CAST(i.precio_minorista_usd AS numeric(18,2)),
                precio_mayorista_usd = CAST(i.precio_mayorista_usd AS numeric(18,2)),
                precio_distrib = CAST(i.precio_distrib AS numeric(18,2)),
                precio_distrib_minorista = CAST(i.precio_distrib_minorista AS numeric(18,2)),
                fotografia = i.fotografia,
                fecha_actualiz_precio = i.fecha_actualiz_precio,
                muestra_porcentaje_vip = i.muestra_porcentaje_vip,
                precio_recargado = i.precio_recargado,
                aprob_disenio = i.aprob_disenio,
                aprob_produccion = i.aprob_produccion
            FROM inserted i
            WHERE i.cod_articulo = @cod_articulo AND i.cod_color_articulo = @cod_color_articulo
        END
        ELSE
        BEGIN
            INSERT INTO encinitas.dbo.colores_por_articulo (
                cod_articulo,
                cod_color_articulo,
                denom_color,
                disenio,
                precio_minorista_usd,
                precio_mayorista_usd,
                precio_distrib,
                precio_distrib_minorista,
                fotografia,
                fecha_actualiz_precio,
                muestra_porcentaje_vip,
                precio_recargado,
                aprob_disenio,
                aprob_produccion
            )
            SELECT
                cod_articulo,
                cod_color_articulo,
                denom_color,
                disenio,
                CAST(precio_minorista_usd AS numeric(18,2)),
                CAST(precio_mayorista_usd AS numeric(18,2)),
                CAST(precio_distrib AS numeric(18,2)),
                CAST(precio_distrib_minorista AS numeric(18,2)),
                fotografia,
                fecha_actualiz_precio,
                muestra_porcentaje_vip,
                precio_recargado,
                aprob_disenio,
                aprob_produccion
            FROM inserted
            WHERE cod_articulo = @cod_articulo AND cod_color_articulo = @cod_color_articulo
        END

        FETCH NEXT FROM cur_sync INTO @cod_articulo, @cod_color_articulo
    END

    CLOSE cur_sync
    DEALLOCATE cur_sync
END
