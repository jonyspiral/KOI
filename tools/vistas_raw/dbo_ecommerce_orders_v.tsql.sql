CREATE VIEW [dbo].[ecommerce_orders_v] AS
CREATE VIEW ecommerce_orders_v AS

SELECT o.*, c.firstname, c.lastname, c.cod_usergroup cod_usergroup, u.nombre nombre_usergroup
FROM ecommerce_orders o
LEFT JOIN ecommerce_customers c ON o.cod_customer = c.cod_customer
LEFT JOIN ecommerce_usergroups u ON c.cod_usergroup = u.cod_usergroup
WHERE o.anulado = 'N'
GO
