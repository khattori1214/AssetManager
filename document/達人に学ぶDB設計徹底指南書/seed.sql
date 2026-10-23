-- ==================================================
-- ③ 配送先マスタ6件を登録
-- 現在の配送先情報を保存する
-- DLV0002には移転後の川崎市の住所を登録する
-- ==================================================

USE new_training2_0;

SET NAMES utf8mb4;

SET time_zone = '+09:00';

START TRANSACTION;

INSERT INTO
    delivery_addresses (
        delivery_address_code,
        delivery_address_name,
        addressee,
        delivery_address_postcode,
        delivery_address,
        phonenumber,
        usage_status,
        client_id,
        created_at,
        updated_at
    )
SELECT src.code, src.name, src.addressee, src.postcode, src.address, src.phone, src.status, c.client_id, NOW(), NOW()
FROM (
        SELECT
            'DLV0001' AS code, 'C0001' AS client_code, '本社' AS name, '株式会社青空商事' AS addressee, '101-0001' AS postcode, '東京都千代田区神田1-2-3' AS address, '03-1111-3333' AS phone, '利用中' AS status
        UNION ALL
        SELECT 'DLV0002', 'C0001', '東日本物流センター', '株式会社青空商事 東日本物流センター', '210-0001', '神奈川県川崎市川崎区本町1-1', '044-111-2222', '利用中'
        UNION ALL
        SELECT 'DLV0003', 'C0002', '札幌本社', '株式会社北山物流', '060-0001', '北海道札幌市中央区北1条西1-1', '011-222-1111', '利用中'
        UNION ALL
        SELECT 'DLV0004', 'C0002', '石狩物流センター', '株式会社北山物流 石狩物流センター', '061-3241', '北海道石狩市新港西1-1-1', '0133-11-2222', '利用中'
        UNION ALL
        SELECT 'DLV0005', 'C0003', '仙台本社', '東日本システム株式会社', '980-0001', '宮城県仙台市青葉区中央1-1', '022-333-1111', '利用中'
        UNION ALL
        SELECT 'DLV0006', 'C0003', '旧物流センター', '東日本システム株式会社 物流センター', '983-0001', '宮城県仙台市宮城野区港1-1-1', '022-333-2222', '利用停止'
    ) AS src
    JOIN clients AS c ON c.client_code = src.client_code
WHERE
    NOT EXISTS (
        SELECT 1
        FROM delivery_addresses AS existing
        WHERE
            existing.delivery_address_code = src.code
    );

-- ==================================================
-- ④ 注文4件を登録
-- 注文時の配送先情報は、現在のマスタとは別に保存する
-- 請求・入金レコードは追加しない
-- ==================================================
USE new_training2_0;

SET NAMES utf8mb4;

SET time_zone = '+09:00';

START TRANSACTION;

-- 注文4件
INSERT INTO
    orders (
        order_number,
        order_date,
        order_status,
        client_id,
        employee_id,
        delivery_address_id,
        order_address_name,
        order_address_postcode,
        order_address,
        order_addressee,
        order_phonenumber,
        created_at,
        updated_at
    )
SELECT src.order_number, src.order_date, src.order_status, c.client_id, e.employee_id, da.delivery_address_id, src.address_name, src.postcode, src.address, NULL, NULL, NOW(), NOW()
FROM (
        SELECT
            'ORD0013' AS order_number,
            '2026-07-01' AS order_date,
            '完了' AS order_status,
            'C0001' AS client_code,
            'E001' AS employee_number,
            'DLV0002' AS delivery_code,
            '東日本物流センター' AS address_name,
            '220-0002' AS postcode,
            '神奈川県横浜市西区南幸2-1-1' AS address
        UNION ALL
        SELECT 'ORD0014', '2026-07-04', '完了', 'C0002', 'E002', 'DLV0004', '石狩物流センター', '061-3241', '北海道石狩市新港西1-1-1'
        UNION ALL
        SELECT 'ORD0015', '2026-07-10', '処理中', 'C0003', 'E003', 'DLV0005', '仙台本社', '980-0001', '宮城県仙台市青葉区中央1-1'
        UNION ALL
        SELECT 'ORD0016', '2026-07-13', '処理中', 'C0002', 'E002', 'DLV0003', '札幌本社', '060-0001', '北海道札幌市中央区北1条西1-1'
    ) AS src
    JOIN clients AS c ON c.client_code = src.client_code
    JOIN employees AS e ON e.employee_number = src.employee_number
    JOIN delivery_addresses AS da ON da.delivery_address_code = src.delivery_code
    AND da.client_id = c.client_id
WHERE
    NOT EXISTS (
        SELECT 1
        FROM orders AS existing
        WHERE
            existing.order_number = src.order_number
    );

-- ==================================================
-- ④ 注文明細6件を登録
-- 商品名・購入単価は投入元データの値を使用する
-- 数量は出荷・キャンセル前の当初注文数量を保存する
-- 商品マスタの現在価格は変更しない
-- ==================================================

-- 注文明細6件
INSERT INTO
    order_details (
        order_id,
        product_id,
        quantity,
        purchase_unit_price,
        price,
        product_name_at_order,
        created_at,
        updated_at
    )
SELECT o.order_id, p.product_id, src.quantity, src.unit_price, src.quantity * src.unit_price, src.product_name, NOW(), NOW()
FROM (
        SELECT
            'ORD0013' AS order_number,
            'P001' AS product_code,
            'ノートPC Pro' AS product_name,
            5 AS quantity,
            126000 AS unit_price
        UNION ALL
        SELECT 'ORD0013', 'P010', 'ワイヤレスマウス', 5, 3800
        UNION ALL
        SELECT 'ORD0014', 'P002', 'デスクトップPC Standard', 10, 100000
        UNION ALL
        SELECT 'ORD0014', 'P012', '液晶モニター24インチ', 10, 22500
        UNION ALL
        SELECT 'ORD0015', 'P001', 'ノートPC Pro', 5, 126000
        UNION ALL
        SELECT 'ORD0016', 'P031', 'A4コピー用紙500枚', 20, 490
    ) AS src
    JOIN orders AS o ON o.order_number = src.order_number
    JOIN products AS p ON p.product_code = src.product_code
WHERE
    NOT EXISTS (
        SELECT 1
        FROM order_details AS existing
        WHERE
            existing.order_id = o.order_id
            AND existing.product_id = p.product_id
    );

-- ==================================================
-- ⑤ 出荷6件を登録
-- 1つの出荷番号につき1件登録し、注文に結び付ける
-- ==================================================

USE new_training2_0;

SET NAMES utf8mb4;

SET time_zone = '+09:00';

START TRANSACTION;

-- 出荷6件
INSERT INTO
    shipments (
        shipment_number,
        shipment_day,
        order_id,
        created_at,
        updated_at
    )
SELECT src.shipment_number, src.shipment_day, o.order_id, NOW(), NOW()
FROM (
        SELECT
            'SHP0001' AS shipment_number, '2026-07-03' AS shipment_day, 'ORD0013' AS order_number
        UNION ALL
        SELECT 'SHP0002', '2026-07-05', 'ORD0013'
        UNION ALL
        SELECT 'SHP0003', '2026-07-06', 'ORD0014'
        UNION ALL
        SELECT 'SHP0004', '2026-07-08', 'ORD0014'
        UNION ALL
        SELECT 'SHP0005', '2026-07-11', 'ORD0015'
        UNION ALL
        SELECT 'SHP0006', '2026-07-14', 'ORD0016'
    ) AS src
    JOIN orders AS o ON o.order_number = src.order_number
WHERE
    NOT EXISTS (
        SELECT 1
        FROM shipments AS existing
        WHERE
            existing.shipment_number = src.shipment_number
    );

-- ==================================================
-- ⑤ 出荷明細8件を登録
-- どの注文明細を、どの出荷で何個出荷したかを保存する
-- ==================================================

-- 出荷明細8件
INSERT INTO
    shipments_order_details (
        shipment_id,
        order_detail_id,
        shipment_amount
    )
SELECT s.shipment_id, od.order_detail_id, src.amount
FROM (
        SELECT
            'SHP0001' AS shipment_number,
            'P001' AS product_code,
            3 AS amount
        UNION ALL
        SELECT 'SHP0001', 'P010', 5
        UNION ALL
        SELECT 'SHP0002', 'P001', 2
        UNION ALL
        SELECT 'SHP0003', 'P002', 6
        UNION ALL
        SELECT 'SHP0003', 'P012', 10
        UNION ALL
        SELECT 'SHP0004', 'P002', 4
        UNION ALL
        SELECT 'SHP0005', 'P001', 2
        UNION ALL
        SELECT 'SHP0006', 'P031', 10
    ) AS src
    JOIN shipments AS s ON s.shipment_number = src.shipment_number
    JOIN products AS p ON p.product_code = src.product_code
    JOIN order_details AS od ON od.order_id = s.order_id
    AND od.product_id = p.product_id
WHERE
    NOT EXISTS (
        SELECT 1
        FROM
            shipments_order_details AS existing
        WHERE
            existing.shipment_id = s.shipment_id
            AND existing.order_detail_id = od.order_detail_id
    );

-- ==================================================
-- ⑥ キャンセル履歴3件を登録
-- 同じ注文明細への複数回キャンセルを別々の履歴として保存
-- 営業担当者とは別に、キャンセル処理社員を指定する
-- ==================================================

USE new_training2_0;

SET NAMES utf8mb4;

SET time_zone = '+09:00';

START TRANSACTION;

INSERT INTO
    cancels (
        cancel_date,
        cancel_quantity,
        cancel_reason,
        employee_id,
        order_detail_id,
        created_at,
        updated_at
    )
SELECT src.cancel_date, src.quantity, src.reason, e.employee_id, od.order_detail_id, NOW(), NOW()
FROM (
        SELECT
            'ORD0015' AS order_number,
            'P001' AS product_code,
            '2026-07-12 10:30:00' AS cancel_date,
            3 AS quantity,
            '予算変更' AS reason,
            'E003' AS employee_number
        UNION ALL
        SELECT 'ORD0016', 'P031', '2026-07-14 15:00:00', 3, '必要数変更', 'E002'
        UNION ALL
        SELECT 'ORD0016', 'P031', '2026-07-16 09:30:00', 2, '追加の数量調整', 'E002'
    ) AS src
    JOIN orders AS o ON o.order_number = src.order_number
    JOIN products AS p ON p.product_code = src.product_code
    JOIN order_details AS od ON od.order_id = o.order_id
    AND od.product_id = p.product_id
    JOIN employees AS e ON e.employee_number = src.employee_number
WHERE
    NOT EXISTS (
        SELECT 1
        FROM cancels AS existing
        WHERE
            existing.order_detail_id = od.order_detail_id
            AND existing.cancel_date = src.cancel_date
            AND existing.cancel_quantity = src.quantity
            AND existing.cancel_reason = src.reason
            AND existing.employee_id = e.employee_id
    );