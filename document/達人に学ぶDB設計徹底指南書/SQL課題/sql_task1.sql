-- Step1:

-- SQL-1
SELECT
    employee_id AS 社員番号,
    employee_name AS 社員名,
    email AS メールアドレス
from employees;

-- SQL-2
SELECT employee_name AS 社員名
from employees
where
    employee_code = 'E0003';

-- SQL-3
SELECT
    training_code AS 研修コード,
    training_name AS 研修名,
    training_category AS 研修カテゴリ,
    training_date AS 開催日
from trainings
where
    training_date >= '2026-06-01 00:00:00'
ORDER BY training_date ASC;

-- SQL-4
SELECT count(*) AS 研修件数
from trainings
where
    training_category = '技術研修';

-- SQL-5
SELECT count(*) AS 件数
from employee_trainings
where
    attendance_status = '受講済';

-- SQL-6
SELECT MAX(score) AS 最高点 from employee_trainings;

-- SQL-7
SELECT attendance_status AS 受講状況, COUNT(attendance_status) AS 件数
from employee_trainings
group by (attendance_status)
ORDER BY attendance_status DESC;

-- SQL-8
SELECT attendance_result AS 受講結果, COUNT(attendance_result) AS 件数
from employee_trainings
group by (attendance_result)
ORDER BY attendance_result ASC;

-- Step2:

-- SQL-9
SELECT
    employee_code AS 社員番号,
    employee_name AS 社員名,
    department_name AS 部署名
FROM employees AS e
    LEFT JOIN departments AS d ON d.department_id = e.department_id
ORDER BY e.employee_id ASC;

-- SQL-10
SELECT
    e.employee_code AS 社員番号,
    e.employee_name AS 社員名,
    et.attendance_status AS 受講状況,
    et.attendance_result AS 受講結果,
    et.score AS 受講点数
FROM
    employees AS e
    JOIN employee_trainings AS et ON e.employee_id = et.employee_id
    JOIN trainings AS t ON et.training_id = t.training_id
WHERE
    t.training_code = 'TR002';

-- SQL-11
SELECT e.employee_code AS 社員番号, e.employee_name AS 社員名, t.training_name AS 研修名, et.attendance_status AS 受講状況
FROM
    employees AS e
    JOIN employee_trainings AS et ON e.employee_id = et.employee_id
    JOIN trainings AS t ON et.training_id = t.training_id
WHERE
    t.training_code = 'TR002'
    AND et.attendance_status = '欠席';

-- SQL-12
SELECT
    e.employee_code AS 社員番号,
    e.employee_name AS 社員名,
    q.qualification_name AS 資格名,
    eq.acquired_date AS 取得日,
    eq.expiration_date AS 有効期限
FROM
    employees AS e
    JOIN employee_qualifications AS eq ON e.employee_id = eq.employee_id
    JOIN qualifications AS q ON q.qualification_id = eq.qualification_id;

-- SQL-13
SELECT e.employee_name AS 社員名
FROM
    employees AS e
    LEFT JOIN employee_qualifications AS eq ON e.employee_id = eq.employee_id
WHERE
    eq.qualification_id IS NULL;

-- SQL-14
SELECT e.employee_code AS 社員番号, e.employee_name AS 社員名, COUNT(eq.employee_qualification_id) AS 保有資格数
FROM
    employees AS e
    LEFT JOIN employee_qualifications AS eq ON e.employee_id = eq.employee_id
GROUP BY
    e.employee_id;

-- SQL-15
SELECT t.training_code AS 研修コード, t.training_name AS 研修名, COUNT(et.employee_trainings_id) AS 登録者数
FROM
    trainings AS t
    LEFT JOIN employee_trainings AS et ON t.training_id = et.training_id
GROUP BY
    t.training_id;

-- SQL-16
SELECT t.training_code AS 研修コード, t.training_name AS 研修名, COUNT(et.attendance_status) AS 登録者数
FROM
    trainings AS t
    LEFT JOIN employee_trainings AS et ON t.training_id = et.training_id
GROUP BY
    t.training_id
HAVING
    count(et.attendance_status) >= 3;

-- SQL-17
SELECT e.employee_name AS 社員名, e.employee_code AS 社員番号
FROM employees AS e
WHERE
    EXISTS (
        SELECT 1
        FROM employee_qualifications AS eq
        WHERE
            e.employee_id = eq.employee_id
    );

-- SQL-18
SELECT e.employee_name AS 社員名, e.employee_code AS 社員番号
FROM employees AS e
WHERE
    NOT EXISTS (
        select 1
        from employee_qualifications AS eq
        WHERE
            e.employee_id = eq.employee_id
    );

-- SQL-19
SELECT DISTINCT
    e.employee_code AS 社員番号,
    e.employee_name AS 社員名
FROM
    employees AS e
    JOIN departments AS d ON d.department_id = e.department_id
    JOIN employee_trainings AS et ON et.employee_id = e.employee_id
WHERE
    d.department_name = '開発部'
    AND et.attendance_status = '受講済';

-- SQL-20
SELECT
    e.employee_code AS 社員番号,
    e.employee_name AS 社員名,
    d.department_name AS 所属部署,
    COUNT(
        DISTINCT et.employee_trainings_id
    ) AS 研修登録件数,
    COUNT(
        DISTINCT CASE
            WHEN et.attendance_status = '受講済' THEN et.employee_trainings_id
        END
    ) AS 受講済研修数,
    COUNT(
        DISTINCT eq.employee_qualification_id
    ) AS 保有資格数
FROM
    employees AS e
    LEFT JOIN departments AS d ON e.department_id = d.department_id
    LEFT JOIN employee_qualifications AS eq ON e.employee_id = eq.employee_id
    LEFT JOIN employee_trainings AS et ON e.employee_id = et.employee_id
GROUP BY
    e.employee_id;