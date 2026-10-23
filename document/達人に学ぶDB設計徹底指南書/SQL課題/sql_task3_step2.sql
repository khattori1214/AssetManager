-- Step2
-- SQL-1
SET time_zone = '+09:00';

START TRANSACTION;

SELECT da.delivery_address_code, o.order_address, da.updated_at, da.usage_status, o.order_number, o.order_address_name
FROM
    delivery_addresses AS da
    JOIN orders AS o ON o.delivery_address_id = da.delivery_address_id
WHERE
    da.delivery_address_code = 'DLV0002'
    AND o.order_number = 'ORD0013';

-- DLV0002をusage_status = '利用停止'へUPDATEし、updated_atを更新
UPDATE delivery_addresses
SET
    usage_status = '利用停止',
    updated_at = NOW()
WHERE
    delivery_address_code = 'DLV0002';

-- usage_status='利用停止'、updated_atが更新されたことを確認し、ORD0013の過去の注文時住所が変わっていないことを確認する
SELECT da.delivery_address_code, o.order_address, da.updated_at, da.usage_status, o.order_number, o.order_address_name
FROM
    delivery_addresses AS da
    JOIN orders AS o ON o.delivery_address_id = da.delivery_address_id
WHERE
    da.delivery_address_code = 'DLV0002'
    AND o.order_number = 'ORD0013';

-- 動作確認後、トランザクション内で行った変更をすべて取り消し、処理開始前の状態に戻す
ROLLBACK;

-- SQL-2
SET time_zone = '+09:00';

START TRANSACTION;

SELECT
    o.order_id,
    o.order_number,
    od.order_detail_id,
    o.order_status,
    o.client_id AS order_client_id,
    o.delivery_address_id,
    p.product_code,
    od.product_name_at_order,
    od.quantity AS 注文数量,
    od.price AS 明細金額,
    da.usage_status,
    da.client_id AS delivery_client_id,
    s.shipment_id,
    s.shipment_number AS 今回の出荷番号,
    s.shipment_day AS 今回の出荷日,
    s.order_id AS 出荷の注文ID,
    added.shipment_amount AS 今回の出荷数量,
    COALESCE(sod.shipment_total, 0) AS 出荷済数量,
    COALESCE(c.cancel_total, 0) AS キャンセル済数量,
    od.quantity - COALESCE(c.cancel_total, 0) - COALESCE(sod.shipment_total, 0) AS 未処理数量
FROM
    orders AS o
    LEFT JOIN order_details AS od ON o.order_id = od.order_id
    LEFT JOIN products AS p ON p.product_id = od.product_id
    LEFT JOIN delivery_addresses AS da ON da.delivery_address_id = o.delivery_address_id
    LEFT JOIN (
        SELECT
            order_detail_id,
            SUM(shipment_amount) AS shipment_total
        FROM shipments_order_details
        GROUP BY
            order_detail_id
    ) AS sod ON sod.order_detail_id = od.order_detail_id
    LEFT JOIN (
        SELECT order_detail_id, SUM(cancel_quantity) AS cancel_total
        FROM cancels
        GROUP BY
            order_detail_id
    ) AS c ON c.order_detail_id = od.order_detail_id
    LEFT JOIN shipments AS s ON s.shipment_id = @shipment_id
    LEFT JOIN shipments_order_details AS added ON added.shipment_id = s.shipment_id
    AND added.order_detail_id = od.order_detail_id
WHERE
    o.order_number = 'ORD0016'
    AND p.product_code = 'P031';

-- 対象注文
-- 注文をロックし、キャンセル状態でないことを確認する。
SELECT order_id, order_status
FROM orders
WHERE
    order_id = 16 FOR
UPDATE;

-- 注文明細
-- 明細をロックし、注文16に所属することを確認する。
SELECT
    order_detail_id,
    order_id,
    quantity
FROM order_details
WHERE
    order_id = 16
    AND order_detail_id = 29 FOR
UPDATE;

-- 配送先
-- 注文と配送先の顧客IDが一致し、配送先が利用中か確認する。
SELECT
    o.client_id AS order_client_id,
    da.usage_status AS usage_status,
    da.client_id AS client_id
FROM
    orders AS o
    LEFT JOIN delivery_addresses AS da ON o.delivery_address_id = da.delivery_address_id
WHERE
    o.order_id = 16;

-- 出荷
-- 出荷履歴をロック付きで取得する。
SELECT sod.shipment_id, sod.shipment_amount, s.shipment_number, sod.order_detail_id
FROM
    shipments_order_details AS sod
    LEFT JOIN shipments AS s ON s.shipment_id = sod.shipment_id
WHERE
    sod.order_detail_id = 29 FOR
UPDATE;

-- キャンセル
-- キャンセル履歴をロック付きで取得する。
SELECT
    cancel_id,
    cancel_quantity,
    order_detail_id
FROM cancels
WHERE
    order_detail_id = 29 FOR
UPDATE;

-- 出荷番号の重複確認
-- SHP0007が存在しないことを確認する。
SELECT shipment_id, shipment_number
FROM shipments
WHERE
    shipment_number = 'SHP0007';

-- ここで止め、注文・明細・配送先・出荷番号の条件を確認する。
-- ロック付きで取得した履歴から残数を計算する。
-- 初期状態では20－10－5＝5。今回の数量2が正数かつ残数以内なら登録する。
-- 条件を満たさない場合はROLLBACKし、INSERTしない。
-- どちらかのINSERTでエラーが出た場合も、そこで止めてROLLBACKする。

INSERT INTO
    shipments (
        shipment_number,
        shipment_day,
        order_id,
        created_at,
        updated_at
    )
VALUES (
        'SHP0007',
        '2026-07-17',
        '16',
        NOW(),
        NOW()
    );

SET @shipment_id = LAST_INSERT_ID();

INSERT INTO
    shipments_order_details (
        shipment_id,
        shipment_amount,
        order_detail_id
    )
VALUES (@shipment_id, 2, '29');

-- 登録内容、出荷合計12・キャンセル合計5・残数3を確認する。
-- 注文数量20・明細金額9800が変わっていないことを確認する。
SELECT
    o.order_id,
    o.order_number,
    od.order_detail_id,
    o.order_status,
    o.client_id AS order_client_id,
    o.delivery_address_id,
    p.product_code,
    od.product_name_at_order,
    od.quantity AS 注文数量,
    od.price AS 明細金額,
    da.usage_status,
    da.client_id AS delivery_client_id,
    s.shipment_id,
    s.shipment_number AS 今回の出荷番号,
    s.shipment_day AS 今回の出荷日,
    s.order_id AS 出荷の注文ID,
    added.shipment_amount AS 今回の出荷数量,
    COALESCE(sod.shipment_total, 0) AS 出荷済数量,
    COALESCE(c.cancel_total, 0) AS キャンセル済数量,
    od.quantity - COALESCE(c.cancel_total, 0) - COALESCE(sod.shipment_total, 0) AS 未処理数量
FROM
    orders AS o
    LEFT JOIN order_details AS od ON o.order_id = od.order_id
    LEFT JOIN products AS p ON p.product_id = od.product_id
    LEFT JOIN delivery_addresses AS da ON da.delivery_address_id = o.delivery_address_id
    LEFT JOIN (
        SELECT
            order_detail_id,
            SUM(shipment_amount) AS shipment_total
        FROM shipments_order_details
        GROUP BY
            order_detail_id
    ) AS sod ON sod.order_detail_id = od.order_detail_id
    LEFT JOIN (
        SELECT order_detail_id, SUM(cancel_quantity) AS cancel_total
        FROM cancels
        GROUP BY
            order_detail_id
    ) AS c ON c.order_detail_id = od.order_detail_id
    LEFT JOIN shipments AS s ON s.shipment_id = @shipment_id
    LEFT JOIN shipments_order_details AS added ON added.shipment_id = s.shipment_id
    AND added.order_detail_id = od.order_detail_id
WHERE
    o.order_number = 'ORD0016'
    AND p.product_code = 'P031';
--
-- 動作確認後、トランザクション内で行った変更をすべて取り消し、処理開始前の状態に戻す
ROLLBACK;
--

-- SQL-3

START TRANSACTION;

SELECT
    o.order_id,
    e.employee_id,
    e.employee_number,
    od.order_detail_id,
    o.order_status,
    p.product_code,
    od.quantity AS 注文数量,
    od.price AS 明細金額,
    COALESCE(sod.shipment_total, 0) AS 出荷済数量,
    COALESCE(c.cancel_total, 0) AS キャンセル済数量,
    od.quantity - COALESCE(c.cancel_total, 0) - COALESCE(sod.shipment_total, 0) AS 未処理数量
FROM
    orders AS o
    LEFT JOIN order_details AS od ON o.order_id = od.order_id
    LEFT JOIN (
        SELECT
            order_detail_id,
            SUM(shipment_amount) AS shipment_total
        FROM shipments_order_details
        GROUP BY
            order_detail_id
    ) AS sod ON sod.order_detail_id = od.order_detail_id
    JOIN employees AS e ON e.employee_number = 'E002'
    LEFT JOIN products AS p ON p.product_id = od.product_id
    LEFT JOIN (
        SELECT order_detail_id, SUM(cancel_quantity) AS cancel_total
        FROM cancels
        GROUP BY
            order_detail_id
    ) AS c ON c.order_detail_id = od.order_detail_id
WHERE
    o.order_number = 'ORD0016'
    AND p.product_code = 'P031';

-- 注文
-- 注文をロックし、キャンセル状態でないことを確認する。

SELECT order_id, order_status
FROM orders
WHERE
    order_id = 16 FOR
UPDATE;

-- 注文明細
-- 明細をロックし、注文16に所属することを確認する。
SELECT
    order_detail_id,
    order_id,
    quantity
FROM order_details
WHERE
    order_id = 16
    AND order_detail_id = 29 FOR
UPDATE;

-- 社員
-- E002に対応する社員IDを確認する。
SELECT employee_id, employee_name
FROM employees
WHERE
    employee_number = 'E002';

-- 出荷
-- 出荷履歴をロック付きで取得する。
SELECT sod.shipment_id, sod.shipment_amount, s.shipment_number, sod.order_detail_id
FROM
    shipments_order_details AS sod
    LEFT JOIN shipments AS s ON s.shipment_id = sod.shipment_id
WHERE
    sod.order_detail_id = 29 FOR
UPDATE;

-- キャンセル
-- キャンセル履歴をロック付きで取得する。
SELECT
    cancel_id,
    cancel_quantity,
    order_detail_id
FROM cancels
WHERE
    order_detail_id = 29 FOR
UPDATE;

-- 注文・明細・社員の存在と、注文がキャンセル状態でないことを確認する。
-- ロック後に取得した履歴から残数5を確認した。
-- 数量0は正数ではなく、数量6は残数を超えるため、登録不可と判断した。
-- 今回の数量2は正数かつ残数以内なので登録する。
-- 条件不成立・エラーならROLLBACKして処理を止める。
INSERT INTO
    cancels (
        order_detail_id,
        employee_id,
        cancel_reason,
        cancel_quantity,
        cancel_date,
        created_at,
        updated_at
    )
VALUES (
        29,
        2,
        '演習追加キャンセル',
        2,
        '2026-07-17 10:00:00',
        NOW(),
        NOW()
    );


-- キャンセル合計7・未処理数量3を確認する。
-- 注文数量・明細金額が登録前と変わっていないことを確認する。
SELECT
    o.order_id,
    e.employee_id,
    e.employee_number,
    od.order_detail_id,
    o.order_status,
    p.product_code,
    od.quantity AS 注文数量,
    od.price AS 明細金額,
    COALESCE(sod.shipment_total, 0) AS 出荷済数量,
    COALESCE(c.cancel_total, 0) AS キャンセル済数量,
    od.quantity - COALESCE(c.cancel_total, 0) - COALESCE(sod.shipment_total, 0) AS 未処理数量
FROM
    orders AS o
    LEFT JOIN order_details AS od ON o.order_id = od.order_id
    LEFT JOIN (
        SELECT
            order_detail_id,
            SUM(shipment_amount) AS shipment_total
        FROM shipments_order_details
        GROUP BY
            order_detail_id
    ) AS sod ON sod.order_detail_id = od.order_detail_id
    JOIN employees AS e ON e.employee_number = 'E002'
    LEFT JOIN products AS p ON p.product_id = od.product_id
    LEFT JOIN (
        SELECT order_detail_id, SUM(cancel_quantity) AS cancel_total
        FROM cancels
        GROUP BY
            order_detail_id
    ) AS c ON c.order_detail_id = od.order_detail_id
WHERE
    o.order_number = 'ORD0016'
    AND p.product_code = 'P031';

-- 動作確認後、トランザクション内で行った変更をすべて取り消し、処理開始前の状態に戻す
ROLLBACK;

-- SQL-4
START TRANSACTION;

-- 対象注文・明細・社員IDを確認する。
SELECT o.order_id, e.employee_id, e.employee_number, od.order_detail_id, o.order_status, p.product_code, od.quantity - COALESCE(c.cancel_total, 0) - COALESCE(sod.shipment_total, 0)
FROM
    orders AS o
    LEFT JOIN order_details AS od ON o.order_id = od.order_id
    LEFT JOIN (
        SELECT
            order_detail_id,
            SUM(shipment_amount) AS shipment_total
        FROM shipments_order_details
        GROUP BY
            order_detail_id
    ) AS sod ON sod.order_detail_id = od.order_detail_id
    JOIN employees AS e ON e.employee_number = 'E002'
    LEFT JOIN products AS p ON p.product_id = od.product_id
    LEFT JOIN (
        SELECT order_detail_id, SUM(cancel_quantity) AS cancel_total
        FROM cancels
        GROUP BY
            order_detail_id
    ) AS c ON c.order_detail_id = od.order_detail_id
WHERE
    o.order_number = 'ORD0016'
    AND p.product_code = 'P031';

-- 注文をロックし、非キャンセル状態を確認する。
SELECT order_id, order_status
FROM orders
WHERE
    order_id = 16 FOR
UPDATE;

-- 明細をロックし、注文16への所属を確認する。
SELECT
    order_detail_id,
    order_id,
    quantity
FROM order_details
WHERE
    order_detail_id = 29
    AND order_id = 16 FOR
UPDATE;

-- 出荷履歴をロック付きで取得する。
SELECT
    shipment_id,
    shipment_amount,
    order_detail_id
FROM shipments_order_details
WHERE
    order_detail_id = 29 FOR
UPDATE;

-- キャンセル履歴をロック付きで取得する。
SELECT
    cancel_id,
    cancel_quantity,
    order_detail_id
FROM cancels
WHERE
    order_detail_id = 29 FOR
UPDATE;

-- 取得した履歴から残数5を確認する。
-- Aはここで止め、別接続Bで以下を実行する。

-- 接続B：同じDBで開始し、注文ロックの待機を確認する。
START TRANSACTION;

SELECT order_id, order_status
FROM orders
WHERE
    order_id = 16 FOR
UPDATE;

-- Bが待機していることを確認したら、接続Aへ戻る。
-- 数量3が残数以内で、ほかの条件も満たすことを確認して登録する。
INSERT INTO
    cancels (
        order_detail_id,
        employee_id,
        cancel_reason,
        cancel_quantity,
        cancel_date,
        created_at,
        updated_at
    )
VALUES (
        29,
        '2',
        '同時実行A',
        3,
        '2026-07-17 11:00:00',
        NOW(),
        NOW()
    );

COMMIT;

-- 接続B：注文ロックの待機解除後、以下を実行する。
-- START TRANSACTIONは既に実行済みなので、再実行しない。
-- 明細をロックする。
SELECT
    order_detail_id,
    order_id,
    quantity
FROM order_details
WHERE
    order_detail_id = 29
    AND order_id = 16 FOR
UPDATE;

-- 最新の出荷履歴をロック付きで取得する。
SELECT
    shipment_id,
    shipment_amount,
    order_detail_id
FROM shipments_order_details
WHERE
    order_detail_id = 29 FOR
UPDATE;

-- Aの追加分を含む最新のキャンセル履歴をロック付きで取得する。
SELECT
    cancel_id,
    cancel_quantity,
    order_detail_id
FROM cancels
WHERE
    order_detail_id = 29 FOR
UPDATE;
--
SELECT o.order_id, e.employee_id, e.employee_number, od.order_detail_id, o.order_status, p.product_code, od.quantity - COALESCE(c.cancel_total, 0) - COALESCE(sod.shipment_total, 0)
FROM
    orders AS o
    LEFT JOIN order_details AS od ON o.order_id = od.order_id
    LEFT JOIN (
        SELECT
            order_detail_id,
            SUM(shipment_amount) AS shipment_total
        FROM shipments_order_details
        GROUP BY
            order_detail_id
    ) AS sod ON sod.order_detail_id = od.order_detail_id
    JOIN employees AS e ON e.employee_number = 'E002'
    LEFT JOIN products AS p ON p.product_id = od.product_id
    LEFT JOIN (
        SELECT order_detail_id, SUM(cancel_quantity) AS cancel_total
        FROM cancels
        GROUP BY
            order_detail_id
    ) AS c ON c.order_detail_id = od.order_detail_id
WHERE
    o.order_number = 'ORD0016'
    AND p.product_code = 'P031';

-- ロック付きで取得した最新履歴から残数2を確認する。
-- 数量3は残数を超えるため、BではINSERTしない。
-- Bの処理を取り消し、ロックを解除する。Aの登録は残る。
ROLLBACK;

-- SQL-5
START TRANSACTION;

-- 完了・キャンセル以外の対象注文と明細IDを確認する。
SELECT o.order_id, o.order_number, od.order_detail_id, o.order_status
FROM orders AS o
    LEFT JOIN order_details AS od ON o.order_id = od.order_id
WHERE
    o.order_number IN (
        'ORD0013',
        'ORD0014',
        'ORD0015',
        'ORD0016',
        'ORD0017'
    )
    AND o.order_status NOT IN('完了', 'キャンセル');
--
-- 対象注文を注文ID昇順でロックし、現在の状態を確認する。
SELECT order_id, order_status
FROM orders
WHERE
    order_id IN (15) FOR
UPDATE;

SELECT order_id, order_status
FROM orders
WHERE
    order_id IN (16) FOR
UPDATE;

SELECT order_id, order_status
FROM orders
WHERE
    order_id IN (17) FOR
UPDATE;

-- 注文明細
-- 各注文の明細を主キー昇順でロックし、所属と注文数量を確認する。
SELECT
    order_detail_id,
    order_id,
    quantity
FROM order_details
WHERE
    order_detail_id IN (28)
    AND order_id IN (15) FOR
UPDATE;

SELECT
    order_detail_id,
    order_id,
    quantity
FROM order_details
WHERE
    order_detail_id IN (29)
    AND order_id IN (16) FOR
UPDATE;

SELECT
    order_detail_id,
    order_id,
    quantity
FROM order_details
WHERE
    order_detail_id IN (30)
    AND order_id IN (17) FOR
UPDATE;

-- 出荷
-- 対象明細の出荷履歴をロック付きで取得する。
SELECT
    shipment_id,
    shipment_amount,
    order_detail_id
FROM shipments_order_details
WHERE
    order_detail_id IN (28, 29, 30) FOR
UPDATE;

-- キャンセル
-- 対象明細のキャンセル履歴をロック付きで取得する。
SELECT
    cancel_id,
    cancel_quantity,
    order_detail_id
FROM cancels
WHERE
    order_detail_id IN (28, 29, 30) FOR
UPDATE;

-- 明細が1件以上あることを確認する。
-- ロック付きで取得した最新履歴を明細ごとに合計し、
-- 全明細で出荷合計＋キャンセル合計＝注文数量か確認する。
-- 初期状態では注文15だけが条件を満たすため、以下を実行する。
-- 条件不成立・エラーならUPDATEせずROLLBACKする。
UPDATE orders
SET
    order_status = '完了',
    updated_at = NOW()
WHERE
    order_id = 15
    AND order_status NOT IN('完了', 'キャンセル');

SELECT
    order_id,
    order_number,
    order_status,
    updated_at
FROM orders
WHERE
    order_id IN (15, 16, 17)
ORDER BY order_id;

-- 動作確認後、トランザクション内で行った変更をすべて取り消し、処理開始前の状態に戻す
ROLLBACK;

-- SQL-6
-- DELETEの基本構文：
-- DELETE FROM テーブル名 WHERE 条件;
-- 条件に一致する行を削除する。今回は削除を実行しない。

-- WHEREがない場合、テーブル内の全行が削除対象になる。
-- テーブルの構造自体は残る。

-- UPDATEによる利用停止は、行を残して利用状態だけを変更する。
-- DELETEは行そのものを削除する。

-- 子レコードから参照されている親レコードは、
-- 外部キーの削除時の設定によって、削除を拒否される場合がある。
-- CASCADEの場合は、関連する子レコードも削除される。

-- 削除すると、過去の注文から配送先を参照できなくなったり、
-- 関連する履歴を失ったりする可能性があるため、利用停止で行を残す。

-- 振り返り

-- 1.残数確認とINSERTを同一トランザクションにする理由。
-- ロック取得後に最新の残数を確認し、INSERTの確定までロックを保持するため。
-- 確認と登録の間に他の処理が割り込み、残数を超えて登録することを防ぐ。

-- 2.FOR UPDATEでどの行をロックし、履歴0件でも競合を防ぐ方法。
-- 対象注文→対象明細→出荷履歴→キャンセル履歴の順にロックする。
-- 履歴が0件でも、必ず存在する親の注文・明細を共通のロック対象にする。
-- 全登録処理が同じ手順を守ることで、同じ注文への書き込みを順番に行う。

-- 3.COMMITとROLLBACKの違い。DDLを同じ感覚で戻せない理由。
-- COMMITはトランザクション内の変更を確定し、ロックを解除する。
-- ROLLBACKは未確定の変更を取り消し、ロックを解除する。
-- COMMIT済みの変更は、その後のROLLBACKでは取り消せない。

-- 4.同時キャンセルと出荷・キャンセル競合で起こり得る問題。
-- 複数の処理が同じ古い残数を見て登録すると、合計数量が残数を超える。
-- 例えば残数5に対して各処理が3ずつ登録すると、合計6となり1超過する。
-- キャンセル同士だけでなく、出荷とキャンセルの組み合わせでも起こり得る。

-- 5.物理DELETEを避ける理由。
-- 過去の参照や履歴を残すため。
-- 外部キーによって削除が拒否されたり、削除設定によって関連履歴まで
-- 消えたりする可能性があるため、利用停止で管理する。

-- 6.注文状態の更新タイミングと、ロック手順を揃える理由。
-- 注文をロックし、最新の出荷・キャンセル履歴を確認してから、
-- 完了条件を満たす注文の状態を更新する。
-- 出荷・キャンセルの登録も最初に同じ注文をロックすれば、
-- 状態を判断している途中で数量が変わることを防げる。

-- 7.主キー・INDEXとロック対象検索の関係。
-- 主キーやINDEXを使って対象行を効率よく検索できる。
-- 適切なINDEXがないと、対象を探すために多くの行を調べることになり、
-- 必要以上の範囲がロックされて、他の処理を待たせる可能性がある。

-- 8.複数の履歴テーブルを直接JOINせず別々に集計する理由。
-- 同じ明細に出荷履歴2件、キャンセル履歴2件があると、
-- 直接JOINした結果は2×2＝4行となり、数量を重複して集計してしまう。
-- 明細ごとに出荷とキャンセルを別々に集計してからJOINし、水増しを防ぐ。