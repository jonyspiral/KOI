/*		((CASE tipo_doc 
			WHEN 'NCR' THEN (-1)
			ELSE (1)
		END) * total_doc) total_documento
empresa, cod_prov, tipo_doc, nro_doc, fecha, total_doc importe_debe (FAC NDB), total_doc importe_haber (NCR OP)*/
CREATE VIEW dbo.cuenta_corriente_historica_