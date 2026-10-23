-- SQL-1
select employee_id AS 社員番号, employee_name AS 社員名, email AS メールアドレス from employees;
-- SQL-2
select employee_name AS 社員名 from employees where employee_code='E0003';
-- SQL-3
select training_code AS 研修コード, training_name AS 研修名, training_category AS 研修カテゴリ, training_date AS 開催日 from trainings where training_date >= '2026-06-01 00:00:00';
-- SQL-4
select count(*) AS 研修件数 from trainings where training_category='技術研修';
-- SQL-5
select count(*) AS 件数 from employee_trainings where attendance_status='受講済';
-- SQL-6
select max(score) AS 最高点 from employee_trainings;
-- SQL-7
select attendance_status AS 受講状況, count(attendance_status) AS 件数 from employee_trainings group by(attendance_status);
-- SQL-8
select attendance_result AS 受講結果, count(attendance_result) AS 件数 from employee_trainings group by(attendance_result);

