# SQL課題3 Step1：配送・出荷・キャンセルの検索・集計

提出ファイル：`sql_task3_step1.sql`。各SQLに番号をコメントで記載し、振り返りも末尾にコメントで回答してください。Step1のレビュー完了後にStep2へ進みます。

目的はSUM、COALESCE、CASE、GROUP BY、CTE、集約サブクエリを使い、数量と処理状態を導出することです。

## 使用DBとデータの前提

対象はMySQL 8.0.16以降、InnoDBです。`SQL課題3_Fixture_研修者DBML対応.sql`を空の演習専用DBへ全体実行して開始します。標準DB名は`sql_training_task3`です。SQL課題2のDBへ直接適用しないでください。このFixtureは完成状態の新規作成用で、DB設計課題の移行スクリプトとは別です。

同名DBがあれば停止し、未使用名へ`CREATE DATABASE`と`USE`を同時に変更してください。作成権限がない場合は、用意された空DBを選び、この2文を省きます。エラー時は続行しません。DDLはROLLBACKで戻せません。

提出`schema_after`のテーブル・カラム名を使用します。配送情報が不明な既存注文を残すため、`orders`の配送関係6列だけNULL許可に補正しています。構造は`DB設計課題3_SQL演習用補正版.dbml`を参照してください。

- `ORD0001`〜`ORD0012`：課題2の既存注文。配送先・出荷・キャンセルの過去履歴が原資料にないため、配送関係6列はNULL、履歴行は追加しません。履歴0件から実際の未出荷とは判断しません。
- `ORD0013`〜`ORD0016`：DB課題3の元データ。数量は当初注文数量で、出荷・キャンセルに応じて減らしません。注文時宛名・電話番号は不明なのでNULLです。
- `ORD0017`：履歴0件の確認用に追加した演習注文。C0001/E001/DLV0001、2026-07-16、受付済、P010（ワイヤレスマウス）2個、単価3,800円。出荷・キャンセル・請求なし。配送先の現在値を注文時点の値とした架空のケースです。
- 旧請求10件・入金11件を保持。請求との結合は`orders.invoice_id = invoices.invoice_id`です。`invoices.order_id`はありません。
- `ORD0013`〜`ORD0017`に請求・入金は追加しません。金額や請求状態の更新は課題3の対象外です。
- 商品名の表示は`order_details.product_name_at_order`（注文時商品名）。番号・コードと主キーIDを区別してください。
- データの日時は日本時間。接続ごとに`SET time_zone = '+09:00';`を実行してください。日時列の自動入力・自動更新はないため、INSERT/UPDATE時に明示します。

### 使用する主なカラム

| テーブル | 主なカラム |
| --- | --- |
| clients | client_id, client_code, client_name |
| products | product_id, product_code, product_name, current_unit_price |
| employees | employee_id, employee_number, employee_name, department_id |
| orders | order_id, order_number, order_status, client_id, invoice_id, delivery_address_id, order_address_name, order_address_postcode, order_address, order_addressee, order_phonenumber |
| order_details | order_detail_id, order_id, product_id, quantity, purchase_unit_price, price, product_name_at_order |
| delivery_addresses | delivery_address_id, delivery_address_code, client_id, delivery_address_name, addressee, delivery_address_postcode, delivery_address, phonenumber, usage_status |
| shipments | shipment_id, shipment_number, shipment_day, order_id, created_at, updated_at |
| shipments_order_details | shipments_order_details_id, shipment_id, order_detail_id, shipment_amount |
| cancels | cancel_id, order_detail_id, cancel_date, cancel_quantity, cancel_reason, employee_id, created_at, updated_at |

社員番号は`employees.employee_number`です。`employee_code`ではありません。商品コードは`products.product_code`、顧客コードは`clients.client_code`です。過去の課題の命名を流用せず、このDBの列名で解答してください。

注文時配送先は別テーブルではなく`orders`内のスナップショットです。配送先の利用状態は`usage_status`の「利用中」「利用停止」を使います。

## 対象範囲・集計ルール

SQL-1〜9・12は、履歴の揃った`ORD0013`〜`ORD0017`（5注文・7明細）を対象とします。SQL-10・11は全注文が対象です。

未処理数量＝当初注文数量−出荷済数量−キャンセル済数量。キャンセル可能数量＝未処理数量とします。出荷履歴・キャンセル履歴が0件なら、演習対象内では合計を0にします。

出荷とキャンセルを明細単位に先に集計してから結合してください。複数の履歴を直接JOINすると数量が水増しされます。`SUM(DISTINCT 数量)`では同量の別履歴を落とすため解決できません。負の残数は0へ丸めず、不整合として扱います。

## 出荷・キャンセル集計

### SQL-1

注文明細ごとの出荷済数量を取得してください。出荷0件も表示します。
取得項目：注文明細ID、注文番号、注文時商品名、注文数量、出荷済数量。

### SQL-2

注文明細ごとのキャンセル済数量を取得してください。キャンセル0件も表示します。
取得項目：注文明細ID、注文番号、注文時商品名、注文数量、キャンセル済数量。

### SQL-3

注文明細ID、注文番号、注文時商品名、注文数量、出荷済数量、キャンセル済数量、未処理数量を取得してください。

### SQL-4

SQL-3のうち未処理数量が1以上の明細だけを取得してください。

### SQL-5

注文明細ごとのキャンセル可能数量を取得してください。0件の明細も表示します。

## 分割出荷

### SQL-6

2回以上に分けて出荷された明細を取得してください。
取得項目：注文番号、注文明細ID、注文時商品名、出荷回数、出荷済数量。出荷回数は異なる出荷IDの数です。

### SQL-7

1回の出荷に2種類以上の注文明細が含まれる出荷を取得してください。
取得項目：出荷番号、出荷日、注文番号、注文明細数。数量ではなく異なる明細IDの数を数えます。

## 処理状態

### SQL-8

CASEで明細ごとの数量上の処理状態を判定してください。

| 出荷済数量＋キャンセル済数量 | 表示 |
| --- | --- |
| 0 | 未処理 |
| 0より大きく、注文数量より小さい | 一部処理 |
| 注文数量と等しい | 処理完了 |
| 注文数量を超える等の不整合 | 不整合 |

保存済みの`orders.order_status`で判定しないでください。

#### 不整合分岐の確認（指導者と実施）

初期Fixtureには不整合データがありません。SQL-8の「不整合」分岐は、次の一時変更で検証します。通常の7明細の結果を確認した後、ほかの接続が更新していない演習DBで実施してください。変更中にStep2を実行しないでください。

```sql
START TRANSACTION;

-- 検証のため、ORD0016/P031の出荷10個を一時的に16個へ変更する。
UPDATE shipments_order_details
SET shipment_amount = 16
WHERE shipment_id = (
    SELECT shipment_id FROM shipments WHERE shipment_number = 'SHP0006'
)
AND order_detail_id = (
    SELECT d.order_detail_id
    FROM order_details AS d
    JOIN orders AS o ON o.order_id = d.order_id
    JOIN products AS p ON p.product_id = d.product_id
    WHERE o.order_number = 'ORD0016' AND p.product_code = 'P031'
);

-- 同じ接続で、自分のSQL-3・SQL-8・SQL-12を実行して確認する。
-- 確認後、必ず次を実行する。COMMITはしない。
ROLLBACK;
```

UPDATEの対象が1行であることを確認してください。変更中はORD0016/P031が「注文20、出荷16、キャンセル5、未処理−1」となり、SQL-8では「不整合」になる必要があります。負の残数を0に丸めていないことも確認してください。ROLLBACK後に再実行し、出荷10・残数5・一部処理へ戻ったことを確認します。検証SQLと結果は提出ファイルのコメントへ記録してください。

これは不整合検出用に履歴を意図的に変更する検証で、業務の出荷登録処理とは区別します。トランザクション操作はStep2で詳しく扱います。


### SQL-9

注文内に明細が1件以上あり、すべての明細で「出荷済＋キャンセル済＝注文数量」を満たすか判定してください。
取得項目：注文番号、保存注文状態、数量上の注文処理状態（完了／未完了）。保存状態と導出状態が食い違う注文について説明してください。

## 配送先

### SQL-10

全注文について、注文番号、注文時配送先名・郵便番号・住所・宛名・電話番号、現在の配送先コード・住所を一覧表示してください。配送先未登録の既存注文も残し、不明な情報はNULLのまま表示します。
`delivery_addresses`はLEFT JOINで結合し、現在値で過去のスナップショットを補完しないでください。

### SQL-11

注文時住所と現在住所が両方判明しており、両者が異なる注文を取得してください。取得項目：注文番号、配送先コード、注文時住所、現在住所。
NULLは「住所が異なる」という判定に含めません。

## CTE

### SQL-12

出荷集計、キャンセル集計、注文明細の3つをCTEで分け、SQL-3と同じ列と結果を取得してください。

## 振り返り

1. `shipments_order_details`が必要な理由。
2. 出荷済数量を`order_details`へ直接保存せず履歴から導出する利点。
3. キャンセル履歴を別テーブルにした理由。
4. 出荷とキャンセルを先に別々に集計する利点と、単純JOINによる水増しの例。
5. 現在の配送先と注文時スナップショットを区別する理由。
6. 過去履歴が不明な既存注文を、履歴が0件というだけで未処理と判断できない理由。
