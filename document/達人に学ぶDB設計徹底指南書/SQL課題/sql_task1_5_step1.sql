-- Step1

-- SQL-1
SELECT
    c.client_code AS 顧客コード,
    c.client_name AS 顧客名,
    co.contract_number AS 契約番号,
    s.service_name AS サービス名,
    co.start_date AS 契約開始日,
    co.end_date AS 契約終了日,
    co.contract_status AS 契約状態
FROM
    contracts AS co
    JOIN clients AS c ON c.client_id = co.client_id
    JOIN services AS s ON s.service_id = co.service_id
ORDER BY co.contract_number ASC;

-- SQL-2
SELECT
    c.client_code AS 顧客コード,
    c.client_name AS 顧客名,
    co.contract_number AS 契約番号,
    s.service_name AS サービス名,
    co.start_date AS 契約開始日
FROM
    clients AS c
    JOIN contracts AS co ON c.client_id = co.client_id
    JOIN services AS s ON s.service_id = co.service_id
WHERE
    co.contract_status = '契約中';

-- SQL-3
SELECT
    c.client_code AS 顧客コード,
    c.client_name AS 顧客名,
    cp.phonenumber AS 電話番号,
    cp.is_primary AS 代表電話かどうか
FROM
    clients AS c
    JOIN client_phonenumbers AS cp ON c.client_id = cp.client_id;

-- SQL-4
SELECT
    c.client_code AS 顧客コード,
    c.client_name AS 顧客名,
    ce.email AS メールアドレス,
    ce.is_primary AS 代表メールかどうか
FROM clients AS c
    JOIN client_emails AS ce ON c.client_id = ce.client_id
ORDER BY c.client_code;

-- SQL-5
SELECT
    co.contract_number AS 契約番号,
    c.client_name AS 顧客名,
    s.service_name AS サービス名,
    e.employee_number AS 担当社員番号,
    e.employee_name AS 担当社員名,
    d.department_name AS 担当部署
FROM
    clients AS c
    JOIN contracts AS co ON c.client_id = co.client_id
    JOIN services AS s ON s.service_id = co.service_id
    JOIN contract_sales_reps AS csr ON csr.contract_id = co.contract_id
    JOIN employees AS e ON e.employee_id = csr.employee_id
    JOIN departments AS d ON d.department_id = e.department_id
ORDER BY co.contract_number ASC;

-- SQL-6
SELECT co.contract_id AS 契約ID, co.contract_number AS 契約番号, COUNT(csr.employee_id) AS 担当者数
FROM
    contracts AS co
    JOIN contract_sales_reps AS csr ON co.contract_id = csr.contract_id
GROUP BY
    co.contract_id
HAVING
    COUNT(csr.employee_id) >= 2;

-- SQL-7
SELECT COUNT(co.contract_id) AS 契約数, e.employee_number AS 社員番号
FROM
    employees AS e
    LEFT JOIN contract_sales_reps AS csr ON e.employee_id = csr.employee_id
    LEFT JOIN contracts AS co ON co.contract_id = csr.contract_id
GROUP BY
    e.employee_id;

-- SQL-8
SELECT
    c.client_code AS 顧客コード,
    c.client_name AS 顧客名,
    i.inquiry_number AS 問い合わせ番号,
    i.inquiry_date AS 問い合わせ日時,
    i.inquiry_type AS 問い合わせ種別,
    i.inquiry_content AS 問い合わせ内容
FROM clients AS c
    JOIN inquiry AS i ON c.client_id = i.client_id
ORDER BY i.inquiry_date DESC;

-- SQL-9
SELECT i.inquiry_number AS 問い合わせ番号, COUNT(ir.inquiry_response_id) AS 対応回数
FROM
    inquiry AS i
    LEFT JOIN inquiry_responses AS ir ON i.inquiry_id = ir.inquiry_id
GROUP BY
    i.inquiry_number;

-- SQL-10
SELECT i.inquiry_id AS 問い合わせID, i.inquiry_number AS 問い合わせ番号
FROM
    inquiry AS i
    JOIN inquiry_responses AS ir ON i.inquiry_id = ir.inquiry_id
GROUP BY
    i.inquiry_id
HAVING
    count(ir.inquiry_response_id) >= 2;

-- SQL-11
SELECT
    i.inquiry_number AS 問い合わせ番号,
    i.inquiry_date AS 問い合わせ日時,
    ir.inquiry_responses_date AS 対応日時,
    e.employee_number AS 対応担当社員番号,
    e.employee_name AS 対応担当者名,
    ir.inquiry_responses_content AS 対応内容
FROM
    inquiry AS i
    JOIN inquiry_responses AS ir ON i.inquiry_id = ir.inquiry_id
    JOIN employees AS e ON e.employee_id = ir.employee_id
WHERE
    i.inquiry_type = '障害'
ORDER BY ir.inquiry_responses_date ASC;

-- SQL-12
SELECT c.client_id AS 顧客ID, c.client_name AS 顧客名
FROM clients AS c
WHERE
    EXISTS (
        SELECT 1
        FROM inquiry AS i
        WHERE
            c.client_id = i.client_id
    );

-- SQL-13
SELECT c.client_id AS 顧客ID, c.client_name AS 顧客名
FROM clients AS c
WHERE
    NOT EXISTS (
        SELECT 1
        FROM inquiry AS i
        WHERE
            c.client_id = i.client_id
    );

-- SQL-14
SELECT e.employee_name AS 社員名
FROM employees AS e
WHERE
    NOT EXISTS (
        SELECT 1
        FROM inquiry_responses AS ir
        WHERE
            e.employee_id = ir.employee_id
    );

-- 1.N:Nを中間テーブル経由でJOINする理由
-- 双方のテーブルのデータの関係が中間テーブルに保存され、その関係に従ってデータを結びつけるため。

-- 2.INNER JOINとLEFT JOINをどう使い分けたか
-- LEFT JOIN は、関連データがなくても左側の行を残したい時、 INNER JOIN は結合条件に一致する行のみを取得したい場合に使用する。

-- 3.EXISTSとNOT EXISTSは何を判定しているか
-- EXISTSはサブクエリの結果に1行以上存在する場合、NOT EXISTSはサブクエリの結果に1行も存在しない場合にTRUEとなる。

-- 4.関連データが0件でも親側を残したい場合、どのようなSQLが必要か
-- 残したいテーブル(親)を左側にして LEFT JOIN で結合する。

-- 5.問い合わせと対応履歴を別テーブルにしたことで、SQLがどう変わったか
-- 問い合わせの情報は inquiry、対応日時・担当者・対応内容は inquiry_responses から取得する。両方の情報を表示する際は inquiry_id で JOIN する必要がある。