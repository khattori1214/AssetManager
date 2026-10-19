# SQL課題3 Step2：DML・トランザクション

Step1のレビュー完了後に実施します。提出ファイル：`sql_task3_step2.sql`。
INSERT、UPDATE、DELETEの意味、START TRANSACTION、COMMIT、ROLLBACK、SELECT ... FOR UPDATEを扱います。SQL番号、条件確認、更新後の確認SQL、振り返りをコメントで記載してください。

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

## 実行・提出のルール

各問はFixtureの初期状態から独立して実行します。SQL-1〜3・5の動作確認はトランザクション内で行い、結果をSELECTしてからROLLBACKしてください。成功時にCOMMITする実装もコメントまたは別ブロックで示します。成功・失敗の分岐を混ぜて一括実行しないでください。

SQL-4の2接続検証だけは専用の新規DBでCOMMITを使います。FixtureのDB名を未使用名へ変更して作り、他の問や他の課題と混ぜません。2接続とも同じDBを使用します。

通常のSQLスクリプトでIFは使えません。分岐はストアドプロシージャ、アプリ側、または判定結果を確認して該当ブロックだけ実行する手順として実装し、その方法を明記してください。エラー・条件不成立時はトランザクション全体をROLLBACKします。

## SQL-1：配送先の利用停止

`DLV0002`を`usage_status = '利用停止'`へUPDATEし、`updated_at`を更新してください。配送先を物理DELETEせず、`ORD0013`の注文時住所が変わらないことを確認してください。利用停止は過去注文の参照・表示を禁止する意味ではありません。

## SQL-2：出荷登録

`ORD0016`のP031明細に、出荷番号`SHP0007`、出荷日`2026-07-17`、数量2を登録してください。

`shipments`と`shipments_order_details`へのINSERTを1トランザクションにします。採番された出荷IDは`LAST_INSERT_ID()`などで取得し、主キーを固定値でINSERTしないでください。日時列も明示してください。

登録前に注文・明細の存在、注文の非キャンセル状態、配送先の顧客一致・利用中、出荷明細がその注文に所属すること、正数の数量、残数以内、出荷番号の一意性を確認します。登録後の未処理数量は3です。既存の当初注文数量と明細金額は変更しません。ヘッダだけ残る失敗を防いでください。

## SQL-3：キャンセル登録

`ORD0016`のP031明細へ、数量2、日時`2026-07-17 10:00:00`、理由「演習追加キャンセル」、処理社員番号`E002`を登録してください。

注文・社員・明細の存在、注文の非キャンセル状態、数量が正数で未処理数量以内であることを確認します。社員番号は社員IDへ対応付けます。`cancels`の日時列も明示します。
初期状態からの成功後は、キャンセル合計7、未処理数量3です。数量0と数量6は拒否し、履歴が増えないことも確認してください。当初注文数量・明細金額は減らしません。

## SQL-4：トランザクション・排他制御

SQL-3を、ロック取得後に残数を確認して登録する処理にしてください。出荷とキャンセルの全登録処理で、次の同じロック手順を守る前提とします。SQL-2もこの手順へ揃えてください。

1. START TRANSACTION。
2. 対象注文を主キーでSELECT ... FOR UPDATE。
3. 対象注文明細を主キーでSELECT ... FOR UPDATEし、注文への所属も確認。複数明細では主キー昇順で個別にロック。
4. 出荷・キャンセルの履歴行をSELECT ... FOR UPDATEで読み、取得した最新行の数量を合計して残数を確認。
5. 条件成立ならINSERTしCOMMIT。不成立・エラーならROLLBACK。

集計結果だけへのFOR UPDATEを排他制御の根拠にしないでください。履歴が0件でも親の注文・明細行が共通ロック対象になります。手順4は履歴そのものをロック付きで読み、その取得結果から合計します。更新前に読んだ残数を使い回しません。読み取り方式の詳しい説明は指導者用確認資料を参照してください。

注文状態更新も同じ注文ロックを先に取得します。全書き込み元がこの手順を守ることが前提です。CHECK/FKだけでは残数上限や出荷先注文の一致を保証できません。

### 2接続での検証

初期残数5の`ORD0016`・P031に、接続AとBがそれぞれ数量3をキャンセルしようとするケースを試してください。理由には「同時実行A」「同時実行B」、日時は`2026-07-17 11:00:00`、社員はE002を使用します。

- Aが注文・明細をロックし、残数5を確認。まだCOMMITしません。
- Bが同じ注文をロックしようとし、待機することを確認。
- Aが数量3をINSERTしてCOMMIT。
- Bがロック取得後に最新履歴を読み直す。残数2なので数量3を登録せずROLLBACK。
- 最後にキャンセル合計8、未処理数量2、今回の追加履歴は1件のみを確認。

タイムアウト・デッドロックが起きた場合はROLLBACKして、原因を記録してください。SQL-4を検証したDBは他問に再利用しません。単純なSELECT→INSERTだけの場合に起きる過剰キャンセルも説明してください。

## SQL-5：注文状態の更新

初期状態の`ORD0013`〜`ORD0017`に限定します。明細が1件以上あり、全明細で「出荷済数量＋キャンセル済数量＝注文数量」を満たし、保存状態が「完了」「キャンセル」以外の注文だけ、`order_status = '完了'`へ更新してください。`updated_at`も更新します。

対象注文・明細をSQL-4と同じ順序でロックし、最新履歴から条件を再確認してUPDATEします。SQL-4の共通手順を守る他の書き込みとは注文行で直列化します。複数注文では注文ID昇順で処理します。

初期状態ではORD0015だけが実際に更新されます。これはSQL演習用の完了定義です。元のDB課題では状態の業務定義を確定する必要があり、数量が揃ったことだけで常に完了とは一般化できません。履歴不明の既存注文は更新しません。

## SQL-6：DELETEの意味

DELETEの基本構文、WHEREがない場合の動作、UPDATEによる利用停止との違い、参照整合性・履歴への影響を説明してください。実データの削除は実行しません。

## 振り返り

1. 残数確認とINSERTを同一トランザクションにする理由。
2. FOR UPDATEでどの行をロックし、履歴0件でも競合を防ぐ方法。
3. COMMITとROLLBACKの違い。DDLを同じ感覚で戻せない理由。
4. 同時キャンセルと出荷・キャンセル競合で起こり得る問題。
5. 物理DELETEを避ける理由。
6. 注文状態の更新タイミングと、出荷・キャンセル処理とのロック手順を揃える理由。
7. 主キー・INDEXとロック対象検索の関係。
8. 複数の履歴テーブルを直接JOINせず別々に集計する理由。
