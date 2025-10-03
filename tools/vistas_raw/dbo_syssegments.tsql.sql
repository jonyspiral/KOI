CREATE VIEW [dbo].[syssegments] AS
CREATE VIEW syssegments (segment, name, status) AS
	SELECT  0, 'system'     , 0  UNION
	SELECT	1, 'default'    , 1  UNION
	SELECT	2, 'logsegment' , 0

GO
