-- SQL-1
SELECT o.order_number AS 注文番号, o.order_date AS 注文日, c.client_name AS 顧客名, SUM(
        od.quantity * od.purchase_unit_price
    ) AS 注文金額
FROM
    orders AS o
    JOIN clients AS c ON o.client_id = c.client_id
    JOIN order_details AS od ON od.order_id = o.order_id
GROUP BY
    o.order_id;

-- SQL-2
SELECT c.client_id AS 顧客ID, COALESCE(
        SUM(
            od.quantity * od.purchase_unit_price
        ), 0
    ) AS 注文総額
FROM
    clients AS c
    LEFT JOIN orders AS o ON c.client_id = o.client_id
    AND o.order_status <> 'キャンセル'
    LEFT JOIN order_details AS od ON o.order_id = od.order_id
GROUP BY
    c.client_id;

-- SQL-3
SELECT p.product_code AS 商品コード, p.product_name AS 現在の商品名, COALESCE(SUM(od.quantity), 0) AS 販売数量, COALESCE(
        SUM(
            od.quantity * od.purchase_unit_price
        ), 0
    ) AS 売上金額
FROM products AS p
    LEFT JOIN (
        order_details AS od
        JOIN orders AS o ON o.order_id = od.order_id
        AND o.order_status <> 'キャンセル'
    ) ON p.product_id = od.product_id
GROUP BY
    p.product_id;

-- SQL-4
SELECT e.employee_id AS 社員ID, e.employee_name AS 社員名, COALESCE(
        SUM(
            od.quantity * od.purchase_unit_price
        ), 0
    ) AS 注文総額
FROM
    employees AS e
    JOIN departments AS dep ON e.department_id = dep.department_id
    LEFT JOIN orders AS o ON o.employee_id = e.employee_id
    AND o.order_status <> 'キャンセル'
    LEFT JOIN order_details AS od ON o.order_id = od.order_id
WHERE
    dep.department_name = '営業部'
GROUP BY
    e.employee_id;

-- SQL-5
SELECT
    o.order_number AS 注文番号,
    p.product_code AS 商品コード,
    od.product_name_at_order AS 注文時商品名,
    p.product_name AS 現在商品名,
    od.purchase_unit_price AS 注文時単価,
    p.current_unit_price AS 現在価格
FROM
    orders AS o
    JOIN order_details AS od ON o.order_id = od.order_id
    JOIN products AS p ON od.product_id = p.product_id
WHERE
    od.purchase_unit_price <> p.current_unit_price
ORDER BY o.order_id;

-- SQL-6
SELECT o.order_id AS 注文ID, od.product_name_at_order AS 注文時商品名, p.product_name AS 現在の商品名
FROM
    orders AS o
    LEFT JOIN order_details AS od ON o.order_id = od.order_id
    LEFT JOIN products AS p ON p.product_id = od.product_id
WHERE
    od.product_name_at_order <> p.product_name
ORDER BY o.order_id;

-- SQL-7
SELECT i.invoice_id AS 請求ID, COALESCE(SUM(d.deposit_amount), 0) AS 入金合計
FROM invoices AS i
    LEFT JOIN deposits AS d ON i.invoice_id = d.invoice_id
GROUP BY
    i.invoice_id
ORDER BY i.invoice_id;

-- SQL-8
SELECT i.invoice_id AS 請求ID, i.invoice_amount - COALESCE(SUM(d.deposit_amount), 0) AS 未入金残高
FROM invoices AS i
    LEFT JOIN deposits AS d ON i.invoice_id = d.invoice_id
GROUP BY
    i.invoice_id;

-- SQL-9
SELECT
    i.invoice_id AS 請求ID,
    CASE
        WHEN COALESCE(SUM(d.deposit_amount), 0) = 0 THEN '未入金'
        WHEN SUM(d.deposit_amount) < i.invoice_amount THEN '一部入金'
        WHEN SUM(d.deposit_amount) = i.invoice_amount THEN '入金済'
    END AS 請求状態
FROM invoices AS i
    LEFT JOIN deposits AS d ON i.invoice_id = d.invoice_id
GROUP BY
    i.invoice_id,
    i.invoice_amount;

-- SQL-10
SELECT
    i.invoice_id AS 請求ID,
    CASE
        WHEN COALESCE(SUM(d.deposit_amount), 0) = 0 THEN '未入金'
    END AS 請求状態
FROM invoices AS i
    LEFT JOIN deposits AS d ON i.invoice_id = d.invoice_id
GROUP BY
    i.invoice_id
HAVING
    COALESCE(SUM(d.deposit_amount), 0) = 0;

-- SQL-11
SELECT
    i.invoice_id AS 請求ID,
    CASE
        WHEN COALESCE(SUM(d.deposit_amount), 0) > 0
        AND COALESCE(SUM(d.deposit_amount), 0) < i.invoice_amount THEN '一部入金'
    END AS 請求状態
FROM invoices AS i
    LEFT JOIN deposits AS d ON i.invoice_id = d.invoice_id
GROUP BY
    i.invoice_id,
    i.invoice_amount
HAVING
    COALESCE(SUM(d.deposit_amount), 0) > 0
    AND COALESCE(SUM(d.deposit_amount), 0) < i.invoice_amount;

-- SQL-12
SELECT
    i.invoice_id AS 請求ID,
    i.invoice_amount AS 請求金額,
    CASE
        WHEN COALESCE(SUM(d.deposit_amount), 0) = i.invoice_amount THEN '入金済'
    END AS 請求状態
FROM invoices AS i
    JOIN deposits AS d ON i.invoice_id = d.invoice_id
GROUP BY
    i.invoice_id
HAVING
    COALESCE(SUM(d.deposit_amount), 0) = i.invoice_amount;

-- SQL-13
SELECT
    i.invoice_number AS 請求番号,
    c.client_name AS 顧客名,
    i.invoice_due_date AS 支払期限,
    i.invoice_amount AS 請求金額,
    COALESCE(SUM(d.deposit_amount), 0) AS 入金合計,
    i.invoice_amount - COALESCE(SUM(d.deposit_amount), 0) AS 未入金残高
FROM
    invoices AS i
    LEFT JOIN deposits AS d ON i.invoice_id = d.invoice_id
    LEFT JOIN orders AS o ON i.order_id = o.order_id
    LEFT JOIN clients AS c ON o.client_id = c.client_id
GROUP BY
    i.invoice_id
HAVING
    i.invoice_due_date < CURRENT_DATE
    AND i.invoice_amount - COALESCE(SUM(d.deposit_amount), 0) > 0;

-- SQL-14
WITH
    deposit_totals AS (
        SELECT i.invoice_id AS 請求ID, i.invoice_amount AS 請求金額, COALESCE(SUM(d.deposit_amount), 0) AS 入金合計
        FROM invoices AS i
            LEFT JOIN deposits AS d ON i.invoice_id = d.invoice_id
        GROUP BY
            i.invoice_id
    ),
    balances AS (
        SELECT dt.請求ID, dt.請求金額, dt.入金合計, dt.請求金額 - dt.入金合計 AS 未入金残高
        FROM deposit_totals AS dt
    ),
    invoice_status AS (
        SELECT
            b.請求ID,
            b.請求金額,
            b.入金合計,
            b.未入金残高,
            CASE
                WHEN b.入金合計 = 0 THEN '未入金'
                WHEN b.入金合計 < b.請求金額 THEN '一部入金'
                WHEN b.入金合計 = b.請求金額 THEN '入金済'
            END AS 請求状態
        FROM balances AS b
    )
SELECT 請求ID, 請求金額, 入金合計, 未入金残高, 請求状態
FROM invoice_status
ORDER BY 請求ID;

-- SQL-15
SELECT
    c.client_code AS 顧客コード,
    c.client_name AS 顧客名,
    COUNT(o.order_id) AS 注文件数,
    COALESCE(SUM(od.order_total), 0) AS 注文総額,
    COUNT(i.invoice_id) AS 請求件数,
    COALESCE(SUM(i.invoice_amount), 0) AS 請求総額,
    COALESCE(SUM(d.deposit_total), 0) AS 入金総額,
    COALESCE(SUM(i.invoice_amount), 0) - COALESCE(SUM(d.deposit_total), 0) AS 未入金残高
FROM
    clients AS c
    LEFT JOIN orders AS o ON c.client_id = o.client_id
    AND o.order_status <> 'キャンセル'
    LEFT JOIN (
        SELECT order_id, SUM(
                quantity * purchase_unit_price
            ) AS order_total
        FROM order_details
        GROUP BY
            order_id
    ) AS od ON o.order_id = od.order_id
    LEFT JOIN invoices AS i ON i.order_id = o.order_id
    LEFT JOIN (
        SELECT invoice_id, SUM(deposit_amount) AS deposit_total
        FROM deposits
        GROUP BY
            invoice_id
    ) AS d ON i.invoice_id = d.invoice_id
GROUP BY
    c.client_id;

-- SQL-16
SELECT pc.product_category_id AS 商品カテゴリID, COALESCE(
        SUM(
            od.quantity * od.purchase_unit_price
        ), 0
    ) AS 売上金額
FROM
    product_categories AS pc
    LEFT JOIN products AS p ON pc.product_category_id = p.product_category_id
    LEFT JOIN (
        order_details AS od
        JOIN orders AS o ON o.order_id = od.order_id
        AND o.order_status <> 'キャンセル'
    ) ON od.product_id = p.product_id
GROUP BY
    pc.product_category_id;

-- SQL-17
SELECT o.order_id AS 注文ID, SUM(
        od.quantity * od.purchase_unit_price
    ) AS 注文金額
FROM order_details AS od
    LEFT JOIN orders AS o ON o.order_id = od.order_id
GROUP BY
    o.order_id
HAVING
    SUM(
        od.quantity * od.purchase_unit_price
    ) >= 100000;

-- SQL-18
SELECT
    i.invoice_number AS 請求番号,
    o.order_number AS 注文番号,
    i.invoice_amount AS 請求金額,
    SUM(
        od.quantity * od.purchase_unit_price
    ) AS 注文明細合計,
    i.invoice_amount - SUM(
        od.quantity * od.purchase_unit_price
    ) AS 差額
FROM
    invoices AS i
    LEFT JOIN orders AS o ON i.order_id = o.order_id
    LEFT JOIN order_details AS od ON o.order_id = od.order_id
GROUP BY
    i.invoice_id;

-- SQL-18 補足
-- 今回のFixtureでは全10請求の差額が0であるが、一般に、送料が追加されたり割引が適用されたりすると差額が発生する。

-- 1. COALESCEを使用した理由
-- SUMの結果がnullになった場合、0として計算、表示させるため。

-- 2. 保存済みの請求状態を使わず、入金合計と請求金額から状態を導出できる理由。また、保存値と導出値が食い違う可能性は何か
-- 入金合計と請求金額を比較すれば、入金済・一部入金・未入金というように現在の請求状態を導出できるから。また、入金履歴のみ更新し、入金状態の更新を忘れると、保存値と導出値が食い違う可能性がある。

-- 3. 現在価格と注文時単価を分ける理由
-- 注文時と現在の価格は変化している可能性があり、現在価格と注文時単価が違う場合でも、注文時の単価で過去の注文金額を計算できるようにするため。

-- 4. 注文時商品名を保持する理由
--　商品名が変更され、注文時の商品名と異なった場合でも注文時の商品名を確認できるようにするため。

-- 5. SQL-15で集計値の水増しをどう防いだか
-- 注文明細を注文ごとに1行、入金金額を請求ごとに1行へ先に集約してからJOINした。
-- 1注文最大1請求という前提もあるため、JOIN後も1注文につき1行となり、明細数や入金回数による件数・金額の水増しを防いだ。