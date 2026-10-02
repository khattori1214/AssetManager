//② 既存データを維持してスキーマ変更の確認
DESC orders;
DESC departments;

SHOW CREATE TABLE delivery_addresses\G
SHOW CREATE TABLE shipments\G
SHOW CREATE TABLE shipments_order_details\G
SHOW CREATE TABLE cancels\G

//③ 配送先6件を登録・確認後の確認
SELECT
    da.delivery_address_code,
    c.client_code,
    da.delivery_address_name,
    da.addressee,
    da.delivery_address_postcode,
    da.delivery_address,
    da.phonenumber,
    da.usage_status
FROM delivery_addresses AS da
JOIN clients AS c
    ON c.client_id = da.client_id
ORDER BY da.delivery_address_code;

//④ 注文4件・注文明細6件を登録・確認
-- 注文4件と注文時配送先
SELECT
    o.order_number,
    o.order_date,
    o.order_status,
    c.client_code,
    e.employee_number,
    da.delivery_address_code,
    o.order_address_name,
    o.order_address_postcode,
    o.order_address,
    o.order_addressee,
    o.order_phonenumber
FROM orders AS o
JOIN clients AS c
    ON c.client_id = o.client_id
JOIN employees AS e
    ON e.employee_id = o.employee_id
JOIN delivery_addresses AS da
    ON da.delivery_address_id = o.delivery_address_id
WHERE o.order_number IN ('ORD0013', 'ORD0014', 'ORD0015', 'ORD0016')
ORDER BY o.order_number;


-- 注文明細6件
SELECT
    o.order_number,
    od.order_detail_id,
    p.product_code,
    od.product_name_at_order,
    od.quantity,
    od.purchase_unit_price,
    od.price
FROM order_details AS od
JOIN orders AS o
    ON o.order_id = od.order_id
JOIN products AS p
    ON p.product_id = od.product_id
WHERE o.order_number IN ('ORD0013', 'ORD0014', 'ORD0015', 'ORD0016')
ORDER BY o.order_number, p.product_code;

//⑤ 出荷6件・出荷明細8件を登録・確認
-- 出荷6件
SELECT
    o.order_number,
    s.shipment_number,
    s.shipment_day
FROM shipments AS s
JOIN orders AS o
    ON o.order_id = s.order_id
ORDER BY s.shipment_number;


-- 出荷明細8件
SELECT
    o.order_number,
    s.shipment_number,
    s.shipment_day,
    p.product_code,
    sd.shipment_amount
FROM shipments_order_details AS sd
JOIN shipments AS s
    ON s.shipment_id = sd.shipment_id
JOIN orders AS o
    ON o.order_id = s.order_id
JOIN order_details AS od
    ON od.order_detail_id = sd.order_detail_id
JOIN products AS p
    ON p.product_id = od.product_id
ORDER BY s.shipment_number, p.product_code;

//⑥ キャンセル履歴3件を登録・確認
SELECT
    o.order_number,
    p.product_code,
    ca.cancel_date,
    ca.cancel_quantity,
    ca.cancel_reason,
    e.employee_number
FROM cancels AS ca
JOIN order_details AS od
    ON od.order_detail_id = ca.order_detail_id
JOIN orders AS o
    ON o.order_id = od.order_id
JOIN products AS p
    ON p.product_id = od.product_id
JOIN employees AS e
    ON e.employee_id = ca.employee_id
ORDER BY o.order_number, ca.cancel_date;

//⑦ 運用例との照合・既存データの維持を確認
//現在住所と注文時住所の違い：
SELECT
    o.order_number,
    da.delivery_address AS 現在住所,
    o.order_address AS 注文時住所
FROM orders AS o
JOIN delivery_addresses AS da
    ON da.delivery_address_id = o.delivery_address_id
WHERE o.order_number = 'ORD0013';

//出荷・キャンセル・未処理数量：
SELECT
    o.order_number AS 注文番号,
    p.product_code AS 商品コード,
    od.quantity AS 注文数量,
    COALESCE(sh.shipped, 0) AS 出荷済数量,
    COALESCE(ca.canceled, 0) AS キャンセル済数量,
    od.quantity
        - COALESCE(sh.shipped, 0)
        - COALESCE(ca.canceled, 0) AS 未処理数量
FROM order_details AS od
JOIN orders AS o
    ON o.order_id = od.order_id
JOIN products AS p
    ON p.product_id = od.product_id
LEFT JOIN (
    SELECT order_detail_id, SUM(shipment_amount) AS shipped
    FROM shipments_order_details
    GROUP BY order_detail_id
) AS sh
    ON sh.order_detail_id = od.order_detail_id
LEFT JOIN (
    SELECT order_detail_id, SUM(cancel_quantity) AS canceled
    FROM cancels
    GROUP BY order_detail_id
) AS ca
    ON ca.order_detail_id = od.order_detail_id
WHERE o.order_number IN ('ORD0013', 'ORD0014', 'ORD0015', 'ORD0016')
ORDER BY o.order_number, p.product_code;

//既存データの件数
SELECT '既存注文' AS 対象, COUNT(*) AS 件数
FROM orders
WHERE order_number BETWEEN 'ORD0001' AND 'ORD0012'

UNION ALL

SELECT '既存注文明細', COUNT(*)
FROM order_details AS od
JOIN orders AS o ON o.order_id = od.order_id
WHERE o.order_number BETWEEN 'ORD0001' AND 'ORD0012'

UNION ALL

SELECT '請求', COUNT(*) FROM invoices

UNION ALL

SELECT '入金', COUNT(*) FROM deposits;

SELECT
    order_id, order_number, order_date,
    client_id, order_status, employee_id,
    created_at, updated_at
FROM orders
WHERE order_number BETWEEN 'ORD0001' AND 'ORD0012'
ORDER BY order_id;

SELECT od.*
FROM order_details AS od
JOIN orders AS o ON o.order_id = od.order_id
WHERE o.order_number BETWEEN 'ORD0001' AND 'ORD0012'
ORDER BY od.order_detail_id;

SELECT * FROM invoices ORDER BY invoice_id;
SELECT * FROM deposits ORDER BY deposit_id;

SELECT * FROM products ORDER BY product_id;