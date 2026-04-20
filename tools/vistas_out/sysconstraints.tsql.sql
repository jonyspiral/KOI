CREATE VIEW sysconstraints AS SELECT
	constid = convert(int, id),
	id = convert(int, parent_obj),
	colid = convert(smallint, info),
	spare1 = convert(tinyint, 0),
	status = convert(int,
			CASE xtype
				WHEN 'PK' THEN 1 WHEN 'UQ' THEN 2 WHEN 'F' T