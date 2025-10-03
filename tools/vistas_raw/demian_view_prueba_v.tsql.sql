CREATE VIEW [demian].[view_prueba_v] AS

CREATE VIEW view_prueba_v AS
SELECT     *
                       FROM          mp_mov_extraor_vw
                       UNION ALL
                       SELECT     *
                       FROM         mp_remitos_vw
                       UNION ALL
                       SELECT     *
                       FROM         tranferencias_materias_primas_v
GO
