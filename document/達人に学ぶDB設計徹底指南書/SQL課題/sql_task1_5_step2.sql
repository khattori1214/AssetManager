-- Step2

-- SQL-1
SELECT c.client_name AS 顧客名, COUNT(co.contract_id) AS 契約数
FROM clients AS c
    LEFT JOIN contracts AS co ON c.client_id = co.client_id
GROUP BY
    c.client_id;

-- SQL-2
SELECT s.service_name AS サービス名, COUNT(co.contract_id) AS 契約数
FROM services AS s
    LEFT JOIN contracts AS co ON s.service_id = co.service_id
GROUP BY
    s.service_id
ORDER BY COUNT(co.contract_id) DESC;

-- SQL-3
SELECT i.inquiry_type AS 問い合わせ種別, COUNT(i.inquiry_id) AS 問い合わせ件数
FROM inquiry AS i
GROUP BY
    i.inquiry_type;

-- SQL-4
SELECT e.employee_name AS 社員名, COUNT(ir.inquiry_response_id) AS 問い合わせ対応回数
FROM
    employees AS e
    LEFT JOIN inquiry_responses AS ir ON e.employee_id = ir.employee_id
GROUP BY
    e.employee_id
ORDER BY COUNT(ir.inquiry_response_id) ASC;

-- SQL-5
SELECT i.inquiry_number AS 問い合わせ番号, MAX(ir.inquiry_responses_date) AS 最終対応日時
FROM
    inquiry AS i
    LEFT JOIN inquiry_responses AS ir ON i.inquiry_id = ir.inquiry_id
GROUP BY
    i.inquiry_number;

-- SQL-6

SELECT
    i.inquiry_number AS 問い合わせ番号,
    c.client_name AS 顧客名,
    ir.inquiry_responses_date AS 最終対応日時,
    e.employee_name AS 最終対応担当者名,
    ir.inquiry_responses_content AS 最終対応内容
FROM
    inquiry AS i
    LEFT JOIN inquiry_responses AS ir ON i.inquiry_id = ir.inquiry_id
    AND ir.inquiry_responses_date = (
        SELECT MAX(ir2.inquiry_responses_date)
        FROM inquiry_responses AS ir2
        WHERE
            ir2.inquiry_id = i.inquiry_id
    )
    LEFT JOIN clients AS c ON c.client_id = i.client_id
    LEFT JOIN employees AS e ON e.employee_id = ir.employee_id;

-- SQL-7
SELECT c.client_name AS 顧客名, COUNT(
        DISTINCT CASE
            WHEN co.contract_status = '契約中' then co.contract_id
        END
    ) AS 契約数, COUNT(DISTINCT i.inquiry_id) AS 問い合わせ回数
FROM
    clients AS c
    JOIN contracts AS co ON c.client_id = co.client_id
    JOIN inquiry AS i ON i.client_id = c.client_id
GROUP BY
    c.client_id
HAVING
    COUNT(
        DISTINCT CASE
            WHEN co.contract_status = '契約中' then co.contract_id
        END
    ) >= 1
    AND COUNT(DISTINCT i.inquiry_id) >= 1;

-- SQL-8
SELECT co.contract_number AS 契約番号, COUNT(csr.employee_id) AS 営業担当者数
FROM
    contracts AS co
    JOIN contract_sales_reps AS csr ON csr.contract_id = co.contract_id
WHERE
    co.contract_status = '契約中'
GROUP BY
    co.contract_id
HAVING
    COUNT(DISTINCT csr.employee_id) >= 2;

-- SQL-9
SELECT
    c.client_code AS 顧客コード,
    c.client_name AS 顧客名,
    COUNT(DISTINCT co.contract_id) AS 契約数,
    COUNT(DISTINCT i.inquiry_id) AS 問い合わせ数,
    COUNT(
        DISTINCT cp.client_phonenumber_id
    ) AS 電話番号数,
    COUNT(DISTINCT ce.client_email_id) AS メールアドレス数
FROM
    clients AS c
    LEFT JOIN contracts AS co ON c.client_id = co.client_id
    LEFT JOIN client_phonenumbers AS cp ON cp.client_id = c.client_id
    LEFT JOIN client_emails AS ce ON ce.client_id = c.client_id
    LEFT JOIN inquiry AS i ON i.client_id = c.client_id
GROUP BY
    c.client_id;

-- SQL-10
SELECT c.client_name AS 顧客名, MAX(i.inquiry_date) AS 最終問い合わせ日時
FROM clients AS c
    LEFT JOIN inquiry AS i ON c.client_id = i.client_id
GROUP BY
    c.client_id;

-- SQL-11
SELECT
    c.client_code AS 顧客コード,
    c.client_name AS 顧客名,
    COUNT(
        DISTINCT CASE
            WHEN co.contract_status = '契約中' THEN co.contract_id
        END
    ) AS 契約中契約数,
    COUNT(DISTINCT i.inquiry_id) AS 問い合わせ件数,
    MAX(i.inquiry_date) AS 最終問い合わせ日時,
    MAX(ir.inquiry_responses_date) AS 最終対応日時
FROM
    clients AS c
    LEFT JOIN contracts AS co ON c.client_id = co.client_id
    LEFT JOIN inquiry AS i ON i.client_id = c.client_id
    LEFT JOIN inquiry_responses AS ir ON ir.inquiry_id = i.inquiry_id
GROUP BY
    c.client_id;

-- 発展課題
-- SQL-6
SELECT r.問い合わせ番号, r.顧客名, r.最終対応日時, r.最終対応担当者名, r.最終対応内容
FROM (
        SELECT
            i.inquiry_number AS 問い合わせ番号, c.client_name AS 顧客名, ir.inquiry_responses_date AS 最終対応日時, e.employee_name AS 最終対応担当者名, ir.inquiry_responses_content AS 最終対応内容, ROW_NUMBER() OVER (
                PARTITION BY
                    i.inquiry_id
                ORDER BY ir.inquiry_responses_date DESC
            ) AS row_num
        FROM
            inquiry AS i
            LEFT JOIN inquiry_responses AS ir ON i.inquiry_id = ir.inquiry_id
            LEFT JOIN clients AS c ON c.client_id = i.client_id
            LEFT JOIN employees AS e ON e.employee_id = ir.employee_id
    ) AS r
WHERE
    r.row_num = 1;

-- 1. 最新日時だけでなく「最新行の内容」を取得するには何が必要か
-- MAXで最新日時を求め、その日時に一致する対応履歴の行を、
-- JOINやサブクエリで選ぶ必要がある。
-- MAXだけでは、その行の担当者や対応内容までは取得できない。

-- 2. 複数1:Nを同時JOINすると集計値が増える理由
-- JOINによって同じデータが複数行に現れ、
-- それを重複して数えてしまうため。

-- 3. COUNT(DISTINCT ...)だけでは解決できない集計があるのはなぜか
-- COUNT(DISTINCT ...)は件数の重複を除く方法であり、
-- SUMなどで合計する値の水増しは解決できないため。
-- SUM(DISTINCT 金額)を使うと、同じ金額は一回とカウントされてしまう。

-- 4. JOIN前に各テーブルを集約する方法にはどのような利点があるか
-- 各テーブルを顧客ごとに1行へ集約してからJOINすることで、
-- 子データ同士の組み合わせによる行の増加を防げる。

-- 5. SQL-9 / SQL-11で集計値の重複をどう回避したか
-- SQL-9では、各子テーブルの主キーをCOUNT(DISTINCT ...)で数え、
-- JOINによって繰り返された同じレコードを重複して数えないようにした。
-- SQL-11では、契約中の契約IDと問い合わせIDをDISTINCTで数えた。
-- 最終日時は、同じ日時が繰り返されても結果が変わらないMAXで取得した。