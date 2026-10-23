# SQL課題1.5 Step1：複数JOIN・N:N・存在判定

## 1. 課題概要

研修者が提出した`DB設計課題1.5(1).dbml`に合わせたSQL演習専用DBを使用します。

使用テーブル：

```text
departments
employees
clients
client_phonenumbers
client_emails
services
contracts
contract_sales_reps
inquiry
inquiry_responses
```

対象DBMS：

```text
MySQL 8系
```

### 使用する主なカラム（研修者提出DBMLに対応）

| テーブル | 主キー | 演習で使用する主なカラム |
| --- | --- | --- |
| departments | department_id | department_name |
| employees | employee_id | employee_number, employee_name, department_id |
| clients | client_id | client_code, client_name, postcode, address |
| client_phonenumbers | client_phonenumber_id | client_id, phonenumber, is_primary |
| client_emails | client_email_id | client_id, email, is_primary |
| services | service_id | service_name |
| contracts | contract_id | client_id, contract_number, service_id, start_date, end_date, contract_status |
| contract_sales_reps | contract_sales_rep_id | contract_id, employee_id |
| inquiry | inquiry_id | client_id, inquiry_number, inquiry_date, inquiry_type, inquiry_content |
| inquiry_responses | inquiry_response_id | inquiry_id, inquiry_responses_date, inquiry_responses_content, employee_id |

- 社員番号は`employees.employee_number`、顧客コードは`clients.client_code`です。内部IDと区別してください。
- 問い合わせテーブルは単数形の`inquiry`です。対応日時・対応内容は`inquiry_responses_date`・`inquiry_responses_content`です。
- 各テーブルの主キー名は異なります。`id`という共通のカラムはありません。
- `departments.department_id`と`employees.department_id`は両方とも`VARCHAR(100)`です。
- 問い合わせは顧客に紐づきます。契約IDを持たないため、元の表で同じ行に記載された契約との関連をJOINで再現する必要はありません。

### 件数・表示のルール

- 契約数は、指定がなければ商談中・契約中・解約済をすべて含めます。
- 「契約中」は`contract_status = '契約中'`で判定します。現在日付による追加判定は不要です。
- 電話・メールの件数には代表連絡先とその他の連絡先をすべて含めます。
- `is_primary`は1が代表、0がその他です。
- 0件を含める問題では件数を0とし、履歴がない場合の最終日時・担当者・内容は`NULL`にしてください。
- `SELECT *`は原則使用せず、必要なカラムを明示してください。DB構造は変更しません。
- SQLが動くだけでなく、取得件数・集計値・NULL・重複を確認してください。

## 検証用fixture

指導者が用意した`SQL課題1.5_Fixture_研修者DBML対応.sql`を適用してから開始してください。
SQL課題1とはテーブル構造が異なるため、別の演習専用DB`sql_training_task1_5`を使用します。

DB設計課題の元データを重複整理して10テーブルへ登録し、不足する境界ケースだけを追加しています。

- `C0009`：契約・問い合わせ・電話・メールがすべて0件の顧客。
- `INQ0013`：`C0004`からの問い合わせ。対応履歴は0件。
- 元データにも、契約担当0件の社員（`S003`・`S005`）と問い合わせ対応0件の社員（`E001`）が含まれます。

空の演習専用DBへ適用してください。同名DBが既にある場合は実行を停止し、指導者に確認してください。

提出ファイル：

```text
sql_task1_5_step1.sql
```

---

# 2. 目的

このStepでは、課題1より複雑なJOINと、N:N・存在判定を扱います。

主に以下を使用します。

```text
INNER JOIN
LEFT JOIN
N:N
中間テーブル
GROUP BY
HAVING
EXISTS
NOT EXISTS
```

---

# 3. JOIN

## SQL-1

契約が存在する顧客について、顧客と契約を一覧表示してください。1契約につき1行とし、契約0件の顧客は含めません。

取得項目：

```text
顧客コード
顧客名
契約番号
サービス名
契約開始日
契約終了日
契約状態
```

---

## SQL-2

現在`契約中`の契約を取得してください。

取得項目：

```text
顧客コード
顧客名
契約番号
サービス名
契約開始日
```

---

## SQL-3

登録されている電話番号を顧客情報とあわせて一覧表示してください。1電話番号につき1行とし、電話番号0件の顧客は含めません。

取得項目：

```text
顧客コード
顧客名
電話番号
代表電話かどうか
```

---

## SQL-4

登録されているメールアドレスを顧客情報とあわせて一覧表示してください。1メールアドレスにつき1行とし、メールアドレス0件の顧客は含めません。

取得項目：

```text
顧客コード
顧客名
メールアドレス
代表メールかどうか
```

---

# 4. N:N と中間テーブル

## SQL-5

各契約と営業担当者を一覧表示してください。1つの契約に担当者が複数いれば、担当者ごとに1行表示してください。

取得項目：

```text
契約番号
顧客名
サービス名
担当社員番号
担当社員名
担当部署
```

---

## SQL-6

営業担当者が2人以上設定されている契約を取得してください。

---

## SQL-7

各社員が担当している契約数を取得してください。

契約を1件も担当していない社員も含めてください。

---

# 5. 問い合わせと対応履歴

## SQL-8

問い合わせ一覧を顧客情報とあわせて取得してください。

取得項目は顧客コード・顧客名・問い合わせ番号・問い合わせ日時・問い合わせ種別・問い合わせ内容です。

問い合わせ日時の新しい順に表示してください。

---

## SQL-9

問い合わせごとの対応回数を取得してください。

取得項目は問い合わせ番号と対応回数です。

対応0件の問い合わせも表示してください。

fixture内の「対応履歴0件の問い合わせ」が結果に含まれることを確認してください。

---

## SQL-10

対応回数が2回以上ある問い合わせを取得してください。

---

## SQL-11

問い合わせ種別が`障害`の問い合わせについて、対応履歴を時系列で取得してください。

取得項目：

```text
問い合わせ番号
問い合わせ日時
対応日時
対応担当社員番号
対応担当者名
対応内容
```

---

# 6. EXISTS / NOT EXISTS

## SQL-12

問い合わせを1件以上行っている顧客を、

```text
EXISTS
```

で取得してください。

---

## SQL-13

一度も問い合わせを行っていない顧客を、

```text
NOT EXISTS
```

で取得してください。

fixture内の「問い合わせ0件の顧客」が取得できることを確認してください。

---

## SQL-14

一度も問い合わせ対応を行っていない社員を取得してください。

---

# 7. Step1 振り返り

以下を説明してください。

1. N:Nを中間テーブル経由でJOINする理由
2. `INNER JOIN`と`LEFT JOIN`をどう使い分けたか
3. `EXISTS`と`NOT EXISTS`は何を判定しているか
4. 関連データが0件でも親側を残したい場合、どのようなSQLが必要か
5. 問い合わせと対応履歴を別テーブルにしたことで、SQLがどう変わったか

Step1完了後、一度レビューを行います。


---

## 提出形式

各SQLの前に`-- SQL-1`のように課題番号を記載してください。振り返りの回答もSQLコメントとして提出ファイルに記載してください。
