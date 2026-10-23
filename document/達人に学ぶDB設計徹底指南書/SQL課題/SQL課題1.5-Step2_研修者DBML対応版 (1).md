# SQL課題1.5 Step2：集計・最新行・複数1:N

## 1. 課題概要

SQL課題1.5 Step1のレビュー完了後に実施してください。

使用するDB・テーブルは研修者DBML対応版Step1と同じです。演習専用DBは`sql_training_task1_5`です。

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

Step1と同じ`SQL課題1.5_Fixture_研修者DBML対応.sql`を使用します。再投入は不要です。

fixtureでは少なくとも、

```text
契約0件の顧客
問い合わせ0件の顧客
対応履歴0件の問い合わせ
```

を確認できる状態とします。

提出ファイル：

```text
sql_task1_5_step2.sql
```

---

# 2. 目的

このStepでは、集計・最新レコード取得・複数条件・複数1:Nの集計を扱います。

主に以下を使用します。

```text
GROUP BY
HAVING
MAX
サブクエリ
CTE
複数JOIN
事前集約
```

ウィンドウ関数は必須ではありません。

---

# 3. 集計

## SQL-1

顧客ごとの契約数を取得してください。

契約0件の顧客も含めてください。

fixture内の「契約0件の顧客」が結果に残ることを確認してください。

---

## SQL-2

サービスごとの契約数を取得してください。

すべてのサービスを対象とし、契約0件のサービスがある場合も0件で表示してください。今回のFixtureでは両サービスに契約があります。

契約数の多い順に表示してください。

---

## SQL-3

問い合わせ種別ごとの問い合わせ件数を取得してください。

---

## SQL-4

社員ごとの問い合わせ対応回数を取得してください。

対応0件の社員も含めてください。

---

# 4. 最新レコード取得

## SQL-5

各問い合わせについて、最後に対応した日時を取得してください。

対応履歴がない問い合わせも表示し、最終対応日時を`NULL`としてください。

---

## SQL-6

各問い合わせについて、**最新の対応内容を1件だけ**取得してください。

対応履歴がない問い合わせも1行表示し、最終対応日時・担当者名・対応内容は`NULL`としてください。最新は`inquiry_responses_date`が最大の行です。

今回のFixtureでは、同じ問い合わせ内で対応日時は重複しません。一般化する場合は、同じ最大日時の行の中から`inquiry_response_id`が最大の行を採用してください（追加の発展事項）。対応IDだけの最大値を最新とみなすのではなく、日時を優先してください。

取得項目：

```text
問い合わせ番号
顧客名
最終対応日時
最終対応担当者名
最終対応内容
```

基本解法として、

```text
MAX
サブクエリ
CTE
JOIN
```

などを組み合わせてください。

### 注意

「最新日時」を取得することと、「最新日時の行にある担当者・対応内容」を取得することは別の問題です。

---

# 5. 複数条件

## SQL-7

以下を両方満たす顧客を取得してください。

```text
契約中の契約を1件以上持つ
問い合わせを1件以上行っている
```

同じ顧客を重複表示しないでください。

---

## SQL-8

以下を満たす契約を取得してください。

```text
契約状態 = 契約中
営業担当者が2人以上
```

---

# 6. 複数1:Nの集計

## SQL-9

顧客ごとに以下を1行で取得してください。

```text
顧客コード
顧客名
契約数
問い合わせ数
電話番号数
メールアドレス数
```

全顧客を対象とし、契約・問い合わせ・電話・メールが0件の場合も、各件数を0として表示してください。

### 注意

以下はすべて顧客に対して1:Nです。

```text
contracts
inquiry
client_phonenumbers
client_emails
```

単純にJOINすると件数が水増しされる可能性があります。

正しい件数を取得してください。

---

## SQL-10

各顧客について最後の問い合わせ日時を取得してください。

問い合わせ0件の顧客も表示してください。

fixture内の「問い合わせ0件の顧客」が結果に残ることを確認してください。

---

## SQL-11

各顧客について以下を1行で取得してください。

```text
顧客コード
顧客名
契約中契約数
問い合わせ件数
最終問い合わせ日時
最終対応日時
```

全顧客を対象とし、件数0件は0、問い合わせ・対応履歴がない場合の最終日時は`NULL`としてください。

最終対応日時は、その顧客の**すべての問い合わせに対する対応履歴**の中で最大の日時です。最後の問い合わせ1件に対する最終対応日時に限定しません。

JOINによる集計値の重複に注意してください。

---

# 7. 発展課題

必須ではありません。

SQL-6を、

```text
ROW_NUMBER()
```

等のウィンドウ関数を使用して書き直してください。

基本解法との違いも確認してください。

---

# 8. Step2 振り返り

以下を説明してください。

1. 最新日時だけでなく「最新行の内容」を取得するには何が必要か
2. 複数1:Nを同時JOINすると集計値が増える理由
3. `COUNT(DISTINCT ...)`だけでは解決できない集計があるのはなぜか
4. JOIN前に各テーブルを集約する方法にはどのような利点があるか
5. SQL-9 / SQL-11で集計値の重複をどう回避したか


---

## 提出形式

各SQLの前に`-- SQL-1`のように課題番号を記載してください。振り返りの回答もSQLコメントとして提出ファイルに記載してください。
