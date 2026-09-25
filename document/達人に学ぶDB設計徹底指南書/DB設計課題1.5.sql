//顧客・契約・営業担当の一覧テーブル
CREATE TABLE client_contracts_new AS
SELECT
    c.client_code,
    c.client_name,
    c.postcode,
    c.address,

    (
        SELECT p.primary_phonenumber
        FROM client_phonenumbers AS p
        WHERE p.client_id = c.client_id
        ORDER BY p.client_phonenumber_id
        LIMIT 1
    ) AS primary_phonenumber,

    (
        SELECT p.secondary_phonenumber
        FROM client_phonenumbers AS p
        WHERE p.client_id = c.client_id
        ORDER BY p.client_phonenumber_id
        LIMIT 1
    ) AS secondary_phonenumber,

    (
        SELECT ce.primary_email
        FROM client_emails AS ce
        WHERE ce.client_id = c.client_id
        ORDER BY ce.client_email_id
        LIMIT 1
    ) AS primary_email,

    (
        SELECT ce.secondary_email
        FROM client_emails AS ce
        WHERE ce.client_id = c.client_id
        ORDER BY ce.client_email_id
        LIMIT 1
    ) AS secondary_email,

    co.contract_number,
    s.service_name,
    co.start_date,
    co.end_date,
    co.contract_status,
    emp.employee_number AS sales_employee_number,
    emp.employee_name AS sales_employee_name,
    d.department_name AS sales_department

FROM clients AS c
JOIN contracts AS co
    ON co.client_id = c.client_id
JOIN services AS s
    ON s.service_id = co.service_id
LEFT JOIN contract_sales_reps AS csr
    ON csr.contract_sales_rep_id = (
        SELECT MIN(csr2.contract_sales_rep_id)
        FROM contract_sales_reps AS csr2
        WHERE csr2.contract_id = co.contract_id
    )
LEFT JOIN employees AS emp
    ON emp.employee_id = csr.employee_id
LEFT JOIN departments AS d
    ON d.department_id = emp.department_id;

//insert文
INSERT INTO client_contracts_new (
    client_code, client_name, postcode, address,
    primary_phonenumber, secondary_phonenumber,
    primary_email, secondary_email,
    contract_number, service_name, start_date, end_date,
    contract_status, sales_employee_number,
    sales_employee_name, sales_department
) VALUES
('C0001', '株式会社青空商事', '100-0001', '東京都千代田区千代田1-1',
 '03-1111-1111', '03-1111-2222',
 'info@aozora.example.jp', 'sales@aozora.example.jp',
 'CT0001', '勤怠管理クラウド', '2025-04-01', NULL,
 '契約中', 'E001', '山田太郎', '営業部'),

('C0001', '株式会社青空商事', '100-0001', '東京都千代田区千代田1-1',
 '03-1111-1111', '03-1111-2222',
 'info@aozora.example.jp', 'sales@aozora.example.jp',
 'CT0001', '勤怠管理クラウド', '2025-04-01', NULL,
 '契約中', 'E001', '山田太郎', '営業部'),

('C0001', '株式会社青空商事', '100-0001', '東京都千代田区千代田1-1',
 '03-1111-1111', '03-1111-2222',
 'info@aozora.example.jp', 'sales@aozora.example.jp',
 'CT0002', '経費精算クラウド', '2026-01-01', NULL,
 '契約中', 'E004', '佐藤花子', '営業部'),

('C0002', '株式会社北山物流', '060-0001', '北海道札幌市中央区北1条西1-1',
 '011-222-1111', NULL,
 'logistics@kitayama.example.jp', NULL,
 'CT0003', '勤怠管理クラウド', '2025-07-01', NULL,
 '契約中', 'E002', '中村次郎', '営業部'),

('C0002', '株式会社北山物流', '060-0001', '北海道札幌市中央区北1条西1-1',
 '011-222-1111', NULL,
 'logistics@kitayama.example.jp', NULL,
 'CT0003', '勤怠管理クラウド', '2025-07-01', NULL,
 '契約中', 'E002', '中村次郎', '営業部'),

('C0003', '東日本システム株式会社', '980-0001', '宮城県仙台市青葉区中央1-1',
 '022-333-1111', '022-333-2222',
 'info@higashinihon.example.jp', 'admin@higashinihon.example.jp',
 'CT0004', '勤怠管理クラウド', '2024-10-01', '2026-03-31',
 '解約済', 'E003', '小林大輔', '営業部'),

('C0004', '西東京フーズ株式会社', '190-0001', '東京都立川市曙町1-1',
 '042-444-1111', NULL,
 'contact@nishitokyo-foods.example.jp', NULL,
 'CT0005', '経費精算クラウド', '2026-02-01', NULL,
 '商談中', 'E004', '佐藤花子', '営業部'),

('C0005', '南関東製作所', '220-0001', '神奈川県横浜市西区みなとみらい1-1',
 '045-555-1111', '045-555-2222',
 'soumu@minamikanto.example.jp', 'it@minamikanto.example.jp',
 'CT0006', '勤怠管理クラウド', '2025-05-01', NULL,
 '契約中', 'E002', '中村次郎', '営業部'),

('C0005', '南関東製作所', '220-0001', '神奈川県横浜市西区みなとみらい1-1',
 '045-555-1111', '045-555-2222',
 'soumu@minamikanto.example.jp', 'it@minamikanto.example.jp',
 'CT0006', '勤怠管理クラウド', '2025-05-01', NULL,
 '契約中', 'E002', '中村次郎', '営業部'),

('C0006', '株式会社中央サービス', '460-0001', '愛知県名古屋市中区丸の内1-1',
 '052-666-1111', NULL,
 'info@chuo-service.example.jp', NULL,
 'CT0007', '経費精算クラウド', '2025-11-01', NULL,
 '契約中', 'E001', '山田太郎', '営業部'),

('C0006', '株式会社中央サービス', '460-0001', '愛知県名古屋市中区丸の内1-1',
 '052-666-1111', NULL,
 'info@chuo-service.example.jp', NULL,
 'CT0007', '経費精算クラウド', '2025-11-01', NULL,
 '契約中', 'E001', '山田太郎', '営業部'),

('C0007', '株式会社東海企画', '420-0001', '静岡県静岡市葵区追手町1-1',
 '054-777-1111', NULL,
 'contact@tokai-kikaku.example.jp', NULL,
 'CT0008', '勤怠管理クラウド', '2026-04-01', NULL,
 '契約中', 'E004', '佐藤花子', '営業部'),

('C0008', '関西メディカル株式会社', '530-0001', '大阪府大阪市北区梅田1-1',
 '06-8888-1111', '06-8888-2222',
 'info@kansai-medical.example.jp', 'system@kansai-medical.example.jp',
 'CT0009', '勤怠管理クラウド', '2025-08-01', NULL,
 '契約中', 'E003', '小林大輔', '営業部'),

('C0008', '関西メディカル株式会社', '530-0001', '大阪府大阪市北区梅田1-1',
 '06-8888-1111', '06-8888-2222',
 'info@kansai-medical.example.jp', 'system@kansai-medical.example.jp',
 'CT0009', '勤怠管理クラウド', '2025-08-01', NULL,
 '契約中', 'E003', '小林大輔', '営業部'),

('C0008', '関西メディカル株式会社', '530-0001', '大阪府大阪市北区梅田1-1',
 '06-8888-1111', '06-8888-2222',
 'info@kansai-medical.example.jp', 'system@kansai-medical.example.jp',
 'CT0009', '勤怠管理クラウド', '2025-08-01', NULL,
 '契約中', 'E003', '小林大輔', '営業部');

//問い合わせ・対応の一覧テーブル
CREATE TABLE inquiry_list_new AS
SELECT
    i.inquiry_number,
    i.inquiry_date,
    i.inquiry_type,
    i.inquiry_content,
    emp.employee_number AS response_employee_number,
    emp.employee_name AS response_employee_name,
    d.department_name AS response_department,
    ir.inquiry_responses_date AS response_date,
    ir.inquiry_responses_content AS response_content

FROM inquiry AS i
LEFT JOIN inquiry_responses AS ir
    ON ir.inquiry_id = i.inquiry_id
LEFT JOIN employees AS emp
    ON emp.employee_id = ir.employee_id
LEFT JOIN departments AS d
    ON d.department_id = emp.department_id;

//insert文
INSERT INTO inquiry_list_new (
    inquiry_number,
    inquiry_date,
    inquiry_type,
    inquiry_content,
    response_employee_number,
    response_employee_name,
    response_department,
    response_date,
    response_content
) VALUES
('INQ0001', '2026-04-10 09:15:00', '操作方法', '有給申請の承認方法が分からない',
 'S003', '田中健太', 'サポート部', '2026-04-10 09:40:00', '操作手順を案内'),

('INQ0001', '2026-04-10 09:15:00', '操作方法', '有給申請の承認方法が分からない',
 'S005', '鈴木美咲', 'サポート部', '2026-04-10 13:20:00', '追加資料をメール送付'),

('INQ0002', '2026-05-12 14:10:00', '障害', '経費申請画面が表示されない',
 'S003', '田中健太', 'サポート部', '2026-05-12 14:25:00', 'ブラウザキャッシュ削除を案内'),

('INQ0003', '2026-03-15 10:00:00', '契約変更', '利用人数を50名から80名へ変更したい',
 'E002', '中村次郎', '営業部', '2026-03-15 11:00:00', '契約変更手続きを案内'),

('INQ0004', '2026-06-01 16:30:00', '請求', '請求書の宛名を変更したい',
 'S005', '鈴木美咲', 'サポート部', '2026-06-01 17:10:00', '変更方法を案内'),

('INQ0005', '2026-02-20 13:00:00', '解約', '契約を3月末で終了したい',
 'E003', '小林大輔', '営業部', '2026-02-20 15:30:00', '解約手続きを案内'),

('INQ0006', '2026-02-05 11:00:00', '料金', '料金プランについて詳しく知りたい',
 'E004', '佐藤花子', '営業部', '2026-02-05 11:45:00', '料金表と見積書を送付'),

('INQ0007', '2025-12-01 09:00:00', '担当変更', '営業担当者について確認したい',
 'E002', '中村次郎', '営業部', '2025-12-01 10:00:00', '現在の担当者を案内'),

('INQ0008', '2026-04-18 15:30:00', '操作方法', '勤怠データのCSV出力方法を知りたい',
 'S003', '田中健太', 'サポート部', '2026-04-18 15:50:00', 'CSV出力手順を案内'),

('INQ0009', '2026-01-20 10:10:00', '請求', '請求書をPDFで再発行してほしい',
 'S005', '鈴木美咲', 'サポート部', '2026-01-20 10:30:00', 'PDF請求書を再送'),

('INQ0010', '2026-07-02 09:20:00', '障害', 'ログインできない',
 'S003', '田中健太', 'サポート部', '2026-07-02 09:35:00', 'パスワード再設定を案内'),

('INQ0011', '2026-06-15 16:00:00', '操作方法', '管理者を追加したい',
 'S005', '鈴木美咲', 'サポート部', '2026-06-15 16:25:00', '管理者追加手順を案内'),

('INQ0012', '2026-05-10 10:30:00', '障害', '打刻データが反映されない',
 'S003', '田中健太', 'サポート部', '2026-05-10 11:00:00', '調査開始'),

('INQ0012', '2026-05-10 10:30:00', '障害', '打刻データが反映されない',
 'S003', '田中健太', 'サポート部', '2026-05-10 13:15:00', 'サーバー側の設定を修正'),

('INQ0012', '2026-05-10 10:30:00', '障害', '打刻データが反映されない',
 'S005', '鈴木美咲', 'サポート部', '2026-05-10 15:00:00', '顧客へ復旧連絡');