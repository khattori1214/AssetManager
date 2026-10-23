-- Step1
-- SQL-1
SELECT
    od.order_detail_id AS 注文明細ID,
    o.order_number AS 注文番号,
    od.product_name_at_order AS 注文時商品名,
    od.quantity AS 注文数量,
    COALESCE(SUM(sod.shipment_amount), 0) AS 出荷済数量
FROM
    order_details AS od
    LEFT JOIN orders AS o ON o.order_id = od.order_id
    LEFT JOIN shipments_order_details AS sod ON sod.order_detail_id = od.order_detail_id
WHERE
    o.order_number in (
        'ORD0013',
        'ORD0014',
        'ORD0015',
        'ORD0016',
        'ORD0017'
    )
GROUP BY
    od.order_detail_id;

-- SQL-2
SELECT
    od.order_detail_id AS 注文明細ID,
    o.order_number AS 注文番号,
    od.product_name_at_order AS 注文時商品名,
    od.quantity AS 注文数量,
    COALESCE(SUM(c.cancel_quantity), 0) AS キャンセル済数量
FROM
    order_details AS od
    LEFT JOIN orders AS o ON o.order_id = od.order_id
    LEFT JOIN cancels AS c ON c.order_detail_id = od.order_detail_id
WHERE
    o.order_number in (
        'ORD0013',
        'ORD0014',
        'ORD0015',
        'ORD0016',
        'ORD0017'
    )
GROUP BY
    od.order_detail_id;

-- SQL-3
SELECT
    od.order_detail_id AS 注文明細ID,
    o.order_number AS 注文番号,
    od.product_name_at_order AS 注文時商品名,
    od.quantity AS 注文数量,
    COALESCE(sod.shipment_total, 0) AS 出荷済数量,
    COALESCE(c.cancel_total, 0) AS キャンセル済数量,
    COALESCE(od.quantity, 0) - COALESCE(sod.shipment_total, 0) - COALESCE(c.cancel_total, 0) AS 未処理数量
FROM
    order_details AS od
    JOIN orders AS o ON o.order_id = od.order_id
    LEFT JOIN (
        SELECT order_detail_id, SUM(cancel_quantity) AS cancel_total
        FROM cancels AS c
        GROUP BY
            order_detail_id
    ) AS c ON od.order_detail_id = c.order_detail_id
    LEFT JOIN (
        SELECT
            order_detail_id,
            SUM(shipment_amount) AS shipment_total
        FROM shipments_order_details AS sod
        GROUP BY
            order_detail_id
    ) AS sod ON sod.order_detail_id = od.order_detail_id
WHERE
    o.order_number in (
        'ORD0013',
        'ORD0014',
        'ORD0015',
        'ORD0016',
        'ORD0017'
    );

-- SQL-4
SELECT
    od.order_detail_id AS 注文明細ID,
    o.order_number AS 注文番号,
    od.product_name_at_order AS 注文時商品名,
    od.quantity AS 注文数量,
    COALESCE(sod.shipment_total, 0) AS 出荷済数量,
    COALESCE(c.cancel_total, 0) AS キャンセル済数量,
    COALESCE(od.quantity, 0) - COALESCE(sod.shipment_total, 0) - COALESCE(c.cancel_total, 0) AS 未処理数量
FROM
    order_details AS od
    LEFT JOIN orders AS o ON o.order_id = od.order_id
    LEFT JOIN (
        SELECT order_detail_id, SUM(cancel_quantity) AS cancel_total
        FROM cancels
        GROUP BY
            order_detail_id
    ) AS c ON c.order_detail_id = od.order_detail_id
    LEFT JOIN (
        SELECT
            order_detail_id,
            SUM(shipment_amount) AS shipment_total
        FROM shipments_order_details
        GROUP BY
            order_detail_id
    ) AS sod ON sod.order_detail_id = od.order_detail_id
WHERE
    o.order_number in (
        'ORD0013',
        'ORD0014',
        'ORD0015',
        'ORD0016',
        'ORD0017'
    )
    AND COALESCE(od.quantity, 0) - COALESCE(sod.shipment_total, 0) - COALESCE(c.cancel_total, 0) >= 1;

-- SQL-5
SELECT od.order_detail_id AS 注文明細ID, COALESCE(od.quantity, 0) - COALESCE(sod.shipment_total, 0) - COALESCE(c.cancel_total, 0) AS キャンセル可能数量
FROM
    order_details AS od
    JOIN orders AS o ON od.order_id = o.order_id
    LEFT JOIN (
        SELECT order_detail_id, SUM(cancel_quantity) AS cancel_total
        FROM cancels
        GROUP BY
            order_detail_id
    ) AS c ON od.order_detail_id = c.order_detail_id
    LEFT JOIN (
        SELECT
            order_detail_id,
            SUM(shipment_amount) AS shipment_total
        FROM shipments_order_details
        GROUP BY
            order_detail_id
    ) AS sod ON od.order_detail_id = sod.order_detail_id
WHERE
    o.order_number in (
        'ORD0013',
        'ORD0014',
        'ORD0015',
        'ORD0016',
        'ORD0017'
    );

-- SQL-6
SELECT
    o.order_number AS 注文番号,
    od.order_detail_id AS 注文明細ID,
    od.product_name_at_order AS 注文時商品名,
    COUNT(DISTINCT sod.shipment_id) AS 出荷回数,
    COALESCE(SUM(sod.shipment_amount), 0) AS 出荷済数量
FROM
    order_details AS od
    LEFT JOIN orders AS o ON o.order_id = od.order_id
    LEFT JOIN shipments_order_details AS sod ON sod.order_detail_id = od.order_detail_id
WHERE
    o.order_number in (
        'ORD0013',
        'ORD0014',
        'ORD0015',
        'ORD0016',
        'ORD0017'
    )
GROUP BY
    o.order_id,
    od.order_detail_id
HAVING
    COUNT(DISTINCT sod.shipment_id) >= 2;

-- SQL-7
SELECT s.shipment_number AS 出荷番号, s.shipment_day AS 出荷日, o.order_number AS 注文番号, COUNT(DISTINCT sod.order_detail_id) AS 注文明細数
FROM
    shipments AS s
    LEFT JOIN orders AS o ON s.order_id = o.order_id
    LEFT JOIN shipments_order_details AS sod ON s.shipment_id = sod.shipment_id
WHERE
    o.order_number in (
        'ORD0013',
        'ORD0014',
        'ORD0015',
        'ORD0016',
        'ORD0017'
    )
GROUP BY
    s.shipment_id,
    s.shipment_day,
    o.order_number
HAVING
    COUNT(DISTINCT sod.order_detail_id) >= 2;

-- SQL-8
SELECT
    od.order_detail_id AS 注文明細ID,
    COALESCE(sod.shipment_total, 0) + COALESCE(c.cancel_total, 0) AS '出荷済数量＋キャンセル済数量',
    CASE
        WHEN COALESCE(sod.shipment_total, 0) + COALESCE(c.cancel_total, 0) = 0 THEN '未処理'
        WHEN COALESCE(sod.shipment_total, 0) + COALESCE(c.cancel_total, 0) > 0
        AND COALESCE(sod.shipment_total, 0) + COALESCE(c.cancel_total, 0) < COALESCE(od.quantity, 0) THEN '一部処理'
        WHEN COALESCE(sod.shipment_total, 0) + COALESCE(c.cancel_total, 0) = COALESCE(od.quantity, 0) THEN '処理完了'
        ELSE '不整合'
    END AS 処理状態
FROM
    order_details AS od
    LEFT JOIN orders AS o ON od.order_id = o.order_id
    LEFT JOIN (
        SELECT
            order_detail_id,
            SUM(shipment_amount) AS shipment_total
        FROM shipments_order_details
        GROUP BY
            order_detail_id
    ) AS sod ON od.order_detail_id = sod.order_detail_id
    LEFT JOIN (
        SELECT order_detail_id, SUM(cancel_quantity) AS cancel_total
        FROM cancels
        GROUP BY
            order_detail_id
    ) AS c ON c.order_detail_id = od.order_detail_id
WHERE
    o.order_number IN (
        'ORD0013',
        'ORD0014',
        'ORD0015',
        'ORD0016',
        'ORD0017'
    );

-- SQL-9
SELECT
    o.order_number AS 注文番号,
    o.order_status AS 保存注文状態,
    CASE
        WHEN COUNT(od.order_detail_id) > 0
        AND COUNT(
            CASE
                WHEN COALESCE(sod.shipment_total, 0) + COALESCE(c.cancel_total, 0) = COALESCE(od.quantity, 0) THEN '完了'
            END
        ) = COUNT(od.order_detail_id) THEN '完了'
        ELSE '未完了'
    END AS '数量上の注文処理状態'
FROM
    orders AS o
    LEFT JOIN order_details AS od ON o.order_id = od.order_id
    LEFT JOIN (
        SELECT order_detail_id, SUM(cancel_quantity) AS cancel_total
        FROM cancels
        GROUP BY
            order_detail_id
    ) AS c ON c.order_detail_id = od.order_detail_id
    LEFT JOIN (
        SELECT
            order_detail_id,
            SUM(shipment_amount) AS shipment_total
        FROM shipments_order_details
        GROUP BY
            order_detail_id
    ) AS sod ON sod.order_detail_id = od.order_detail_id
WHERE
    o.order_number IN (
        'ORD0013',
        'ORD0014',
        'ORD0015',
        'ORD0016',
        'ORD0017'
    )
GROUP BY
    o.order_id;
-- SQL-9補足
-- 注文番号ORD0015は保存状態が処理中となっているが、注文数量5に対して出荷済数量2+キャンセル済数量3＝5となるため、
-- 数量上の注文処理状態は「完了」となり、保存状態と導出結果が異なっている。

-- SQL-10
SELECT
    o.order_number AS 注文番号,
    o.order_address_name AS 注文時配送先名,
    o.order_address_postcode AS 注文時郵便番号,
    o.order_address AS 注文時住所,
    o.order_addressee AS 注文時宛名,
    o.order_phonenumber AS 注文時電話番号,
    d.delivery_address_code AS 現在の配送先コード,
    d.delivery_address AS 住所
FROM
    orders AS o
    LEFT JOIN delivery_addresses AS d ON o.delivery_address_id = d.delivery_address_id;

-- SQL-11
SELECT o.order_number AS 注文番号, da.delivery_address_code AS 配送先コード, o.order_address AS 注文時住所, da.delivery_address AS 現在住所
FROM
    orders AS o
    LEFT JOIN delivery_addresses AS da ON da.delivery_address_id = o.delivery_address_id
WHERE
    o.order_address IS NOT NULL
    AND da.delivery_address IS NOT NULL
    AND o.order_address <> da.delivery_address
ORDER BY o.order_number;

-- SQL-12
WITH
    shipment_sum AS (
        SELECT
            order_detail_id,
            COALESCE(SUM(shipment_amount), 0) AS shipment_total
        FROM shipments_order_details
        GROUP BY
            order_detail_id
    ),
    cancel_sum AS (
        SELECT order_detail_id, COALESCE(SUM(cancel_quantity), 0) AS cancel_total
        FROM cancels
        GROUP BY
            order_detail_id
    ),
    detail_base AS (
        SELECT od.order_detail_id, o.order_number, od.product_name_at_order, od.quantity
        FROM order_details AS od
            JOIN orders AS o ON o.order_id = od.order_id
        WHERE
            o.order_number IN (
                'ORD0013',
                'ORD0014',
                'ORD0015',
                'ORD0016',
                'ORD0017'
            )
    )
SELECT
    d.order_detail_id AS 注文明細ID,
    d.order_number AS 注文番号,
    d.product_name_at_order AS 注文時商品名,
    d.quantity AS 注文数量,
    COALESCE(s.shipment_total, 0) AS 出荷済数量,
    COALESCE(c.cancel_total, 0) AS キャンセル済数量,
    d.quantity - COALESCE(s.shipment_total, 0) - COALESCE(c.cancel_total, 0) AS 未処理数量
FROM
    detail_base AS d
    LEFT JOIN shipment_sum AS s ON s.order_detail_id = d.order_detail_id
    LEFT JOIN cancel_sum AS c ON c.order_detail_id = d.order_detail_id;

-- 振り返り
-- 1.shipments_order_detailsが必要な理由。
-- shipments（出荷）とorder_details（注文明細)をshipments_order_detailsを通して結びつけるとともに、各回の出荷数量を記録するため。

-- 2.出荷済数量をorder_detailsへ直接保存せず履歴から導出する利点。
-- 出荷済数量の更新を忘れても履歴から正しい出荷状態を導出できるから。

-- 3.キャンセル履歴を別テーブルにした理由。
-- 複数回のキャンセルを記録できるよう、またキャンセル日時や理由などを記録するため。

-- 4.出荷とキャンセルを先に別々に集計する利点と、単純JOINによる水増しの例。
-- 同じ明細に出荷履歴2件、キャンセル履歴2件がある場合、単純JOINすると、組み合わせが2×2＝4行になる。
-- その結果、各履歴の数量を重複して足してしまう。
-- 明細ごとに出荷とキャンセルを別々に集計してから結合することで、数量の水増しを防げる。

-- 5.現在の配送先と注文時スナップショットを区別する理由。
-- 現在の配送先が変わっても注文時の配送先を参照できるようにするため。

-- 6.過去履歴が不明な既存注文を、履歴が0件というだけで未処理と判断できない理由。
--　既に出荷、キャンセルされていたとしても、その過去履歴がDBにまだ反映されていない場合もあるため。