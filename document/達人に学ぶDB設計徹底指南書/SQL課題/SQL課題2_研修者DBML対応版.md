# SQL課題2：受注・請求・入金管理システム

## 1. 課題概要

研修者が提出した`DB設計課題2(3).dbml`に合わせた演習専用DBを使用して、取引データの集計・導出を行います。

対象DBMS：

```text
MySQL 8系
```

使用する主なテーブル：

```text
clients
product_categories
products
orders
order_details
invoices
deposits
employees
```

## 検証用fixture

`SQL課題2_Fixture_研修者DBML対応.sql`を適用してから開始してください。
SQL課題1・1.5とは別の演習専用DB`sql_training_task2`を使用します。

元の注文12件と商品マスタ9件を重複整理して10テーブルへ投入しています。追加ケースは以下だけです。

- `C0010`：注文0件の顧客。
- `E005`：営業部に所属し、注文担当実績0件の社員。

未入金・一部入金・入金済、請求未発行、複数回入金、現在価格・商品名・住所と取引時点値の違いは元データで確認できます。
Fixtureは空のDBへ適用してください。同名DBがある場合は停止して指導者へ確認してください。

### 使用する主なカラム

| テーブル | 主キー | 主なカラム |
| --- | --- | --- |
| clients | client_id | client_code, client_name |
| client_contacts | client_contact_id | client_id, postcode, address, phonenumber, email |
| departments | department_id | department_name |
| employees | employee_id | employee_number, employee_name, department_id |
| product_categories | product_category_id | product_category_name |
| products | product_id | product_code, product_name, product_category_id, current_unit_price |
| orders | order_id | order_number, order_date, client_id, order_status, employee_id |
| order_details | order_detail_id | order_id, product_id, quantity, purchase_unit_price, price, product_name_at_order |
| invoices | invoice_id | invoice_number, order_id, invoice_date, invoice_due_date, invoice_amount, payment_status, addressee, postcode, address |
| deposits | deposit_id | invoice_id, deposit_date, deposit_amount, payment_method |

- 社員番号は`employee_number`、注文番号は`order_number`、請求番号は`invoice_number`です。主キーIDとは区別してください。
- 現在価格は`products.current_unit_price`、注文時単価は`order_details.purchase_unit_price`です。
- 現在の商品名は`products.product_name`、注文時商品名は`order_details.product_name_at_order`です。
- 現在の住所は`client_contacts`、請求時点の宛名・住所は`invoices`から取得します。Fixtureの連絡先は1顧客1件ですが、DBMLは複数件を許容します。
- 共通の`id`カラムはありません。DB構造は変更しません。

### 金額・集計のルール

- 金額は円単位の整数です。今回のデータでは税・送料・割引の追加計算は行いません。
- 注文金額は必ず`SUM(quantity * purchase_unit_price)`から求めます。提出DBMLの`price`は保持しますが、保存値のSUMだけで解答しないでください。
- SQL-1・17ではキャンセル注文も含め、登録された全注文の明細合計を表示します。
- SQL-2・3・4・15・16の注文総額・販売数量・売上・注文件数は、`order_status <> 'キャンセル'`の注文を対象とします。受付済・処理中も含む演習上の受注集計で、会計上の売上認識は扱いません。
- SQL-2・15は全顧客を表示します。キャンセル注文しかない顧客も残し、対象注文件数・注文総額を0としてください。
- SQL-3は全商品、SQL-4は営業部の全社員、SQL-16は全カテゴリを対象とし、対象実績0件なら数量・金額を0とします。
- 請求・入金は登録された全件を対象とします。今回キャンセル注文に請求はありません。
- 入金合計がない場合は0。未入金残高は請求金額から入金合計を引きます。請求未発行の注文は請求状態判定の対象外です。
- SQL-9〜14は保存済みの`invoices.payment_status`を条件・判定に使わず、入金合計と請求金額から導出してください。保存値の「請求済」と演習の表示名「未入金」は区別します。
- Fixtureの請求額は正数、入金額も正数で、過入金・返金はありません。これらの状態判定は今回の対象外です。
- `SELECT *`は原則使用せず、SQL番号をコメントで記載してください。件数・金額・NULL・重複を確認してください。

提出ファイル：

```text
sql_task2.sql
```

---

# 2. 目的

主に以下を使用します。

```text
SUM
CASE
COALESCE
GROUP BY
HAVING
サブクエリ
CTE
```

DB設計課題2で扱った、

```text
現在値
取引時点値
確定値
導出値
```

の違いをSQL側から確認します。

---

# 3. 注文・売上

## SQL-1

注文ごとの注文金額を取得してください。

注文金額は、

```text
SUM(order_details.quantity * order_details.purchase_unit_price)
```

で求めます。

取得項目：

```text
注文番号
注文日
顧客名
注文金額
```

---

## SQL-2

顧客ごとの注文総額を取得してください。

注文が0件の顧客も表示してください。

fixture内の「注文0件の顧客」が結果に残ることを確認してください。

---

## SQL-3

商品ごとの販売数量と売上金額を取得してください。

取得項目：

```text
商品コード
現在の商品名
販売数量
売上金額
```

---

## SQL-4

営業担当者ごとの注文総額を取得してください。

注文担当実績が0件の社員も表示してください。

fixture内の「受注実績0件の営業担当社員」が結果に残ることを確認してください。

---

# 4. 現在値と取引時点値

## SQL-5

注文時単価と現在の商品価格が異なる注文明細を取得してください。

取得項目：

```text
注文番号
商品コード
注文時商品名
現在商品名
注文時単価
現在価格
```

---

## SQL-6

注文時商品名と現在の商品名が異なる注文明細を取得してください。

---

# 5. 請求と入金

## SQL-7

請求ごとの入金合計を取得してください。

入金0件の場合は、

```text
0
```

と表示してください。

---

## SQL-8

請求ごとの未入金残高を取得してください。

```text
未入金残高
=
invoice_amount
-
入金合計
```

---

## SQL-9

請求ごとの状態をSQL上で判定してください。

判定ルール：

```text
入金合計 = 0
→ 未入金

0 < 入金合計 < 請求金額
→ 一部入金

入金合計 = 請求金額
→ 入金済
```

`CASE`を使用してください。

---

## SQL-10

未入金の請求だけを取得してください。

---

## SQL-11

一部入金の請求だけを取得してください。

---

## SQL-12

入金済の請求だけを取得してください。

---

# 6. 期限

## SQL-13

支払期限を過ぎているにもかかわらず、入金が完了していない請求を取得してください。

基準日はSQL実行日（`CURRENT_DATE`）とします。支払期限が基準日より前、かつ未入金残高が正数の請求を対象とし、当日期限は含めません。

指導者との検算では基準日を`2026-07-01`に固定しても構いません。その場合はSQLコメントで基準日を明記してください。

取得項目：

```text
請求番号
顧客名
支払期限
請求金額
入金合計
未入金残高
```

---

# 7. CTE

## SQL-14

以下をCTEで段階的に整理して取得してください。

```text
請求
↓
入金合計
↓
未入金残高
↓
請求状態
```

SQL-7〜SQL-9を1本に整理するイメージです。

---

# 8. 集計

## SQL-15

顧客ごとに以下を1行で取得してください。

```text
顧客コード
顧客名
注文件数
注文総額
請求件数
請求総額
入金総額
未入金残高
```

全顧客を対象とし、関連データが0件の場合の件数・金額は0にしてください。

複数の1:Nを単純JOINして集計値が水増しされないよう注意してください。同じ金額の別取引を落とす`SUM(DISTINCT 金額)`で解決しないでください。

1注文最大1請求でも、注文明細と入金履歴はそれぞれ複数行あるため、注文・請求単位などで先に集約する方法を検討してください。

---

## SQL-16

商品カテゴリごとの売上金額を取得してください。

---

## SQL-17

注文金額が10万円以上の注文だけを取得してください。

---

# 9. 確認問題

## SQL-18

請求金額と、その注文に紐づく注文明細合計を比較してください。

取得項目：

```text
請求番号
注文番号
請求金額
注文明細合計
差額
```

差額は「請求金額 − 注文明細合計」とします。請求が存在する注文だけを対象にしてください。

今回のFixtureでは全10請求の差額が0です。0でない行が出た場合はJOINによる水増しなどSQL側の原因も確認してください。一般に差額が発生する理由（税・送料・値引きなど）も説明してください。

---

# 10. 振り返り

以下を説明してください。

1. `COALESCE`を使用した理由
2. 保存済みの請求状態を使わず、入金合計と請求金額から状態を導出できる理由。また、保存値と導出値が食い違う可能性は何か
3. 現在価格と注文時単価を分ける理由
4. 注文時商品名を保持する理由
5. SQL-15で集計値の水増しをどう防いだか


振り返りの回答は、提出SQLの末尾にコメントとして記載してください。
