# DBML リレーション記法一覧

DBMLでER図のリレーションを記述する際のチートシートです。

特に、`1 : 0..N` と `1 : 1..N` の違いや、optional relationship（`?`）の使い方を重点的に整理しています。

---

# 目次・多重度早見表

## 目次

1. 基本のリレーション記号
2. よく使う書き方
3. optional relationship（`?`）
4. `1 : 1..N`
5. `1 : 0..N`
6. 子側が親を持たなくてもよい場合
7. 両側ともoptional
8. `?>` と `>?` の違い
9. `1 : 1`
10. `1 : 0..1`
11. `N : N`
12. 課題でよく使うパターン
13. Inline形式
14. Short form
15. Long form
16. リレーション名
17. ON DELETE / ON UPDATE
18. Composite Foreign Key
19. コメント
20. 注意点

---

## 多重度早見表

## 子テーブル側から `>` を使う場合

| DBML | 親から見た子 | 意味 |
|---|---|---|
| `child.parent_id > parent.id` | `1 : 1..N` | 親には最低1件の子が存在 |
| `child.parent_id ?> parent.id` | `1 : 0..N` | 親は子0件でもよい |
| `child.parent_id >? parent.id` | 子→親が `0..1` | 子は親なしでもよい |
| `child.parent_id ?>? parent.id` | 両側optional | 双方で0を許可 |

---

## 1 : 1の場合

| DBML | 意味 |
|---|---|
| `parent.id - child.parent_id` | `1 : 1` |
| `parent.id -? child.parent_id` | `1 : 0..1` |
| `parent.id ?- child.parent_id` | 左側をoptional |
| `parent.id ?-? child.parent_id` | 両側optional |

---

---

# 1. 基本のリレーション記号

DBMLでは、主に以下の4種類の記号を使用します。

| 記号 | 意味 | 例 |
|---|---|---|
| `<` | 1 : N | `users.id < posts.user_id` |
| `>` | N : 1 | `posts.user_id > users.id` |
| `-` | 1 : 1 | `users.id - user_profiles.user_id` |
| `<>` | N : N | `authors.id <> books.id` |

`<` と `>` は、どちら側から記述するかが違うだけで、同じ1:Nを表現できます。

例えば以下は同じ関係です。

```dbml
Ref: users.id < posts.user_id
```

```dbml
Ref: posts.user_id > users.id
```

意味：

```text
users 1 : N posts
```

---

# 2. よく使う書き方

## 2.1 N : 1

子テーブル側から親テーブルを参照する書き方です。

```dbml
Ref: orders.customer_id > customers.id
```

意味：

```text
orders N : 1 customers
```

別の見方をすると、

```text
customers 1 : N orders
```

です。

---

## 2.2 1 : N

親テーブル側から記述することもできます。

```dbml
Ref: customers.id < orders.customer_id
```

意味：

```text
customers 1 : N orders
```

実際のDB設計では、FKを持つ子テーブル側から `>` を使う書き方が分かりやすいため、

```dbml
Ref: orders.customer_id > customers.id
```

の形式を使用することが多いです。

---

# 3. optional relationship（`?`）

DBMLでは、リレーション記号の左右に `?` を付けることで、

```text
0件を許可する
```

ことを表現できます。

基本ルールは、

> `?` を付けた側が optional（0件を許可）

です。

---

# 4. 1 : 1..N

例えば、

```text
注文には必ず1件以上の注文明細が存在する
```

という場合です。

```dbml
Ref: order_items.order_id > orders.id
```

ER図上の意味：

```text
orders 1 : 1..N order_items
```

つまり、

```text
1注文
↓
最低1件以上の注文明細
```

という関係です。

---

# 5. 1 : 0..N

例えば、

```text
顧客は注文を一度も行っていない場合がある
```

という場合です。

```dbml
Ref: orders.customer_id ?> customers.id
```

ER図上の意味：

```text
customers 1 : 0..N orders
```

つまり、

```text
顧客
↓
注文0件でもよい
注文があれば複数件
```

という関係です。

`?` が `>` の左側にあるため、

```text
?>
```

となります。

---

# 6. 子側が親を持たなくてもよい場合

例えば、

```text
投稿にはユーザーが設定されていない場合がある
```

という場合です。

```dbml
Ref: posts.user_id >? users.id
```

意味：

```text
posts N : 0..1 users
```

1件のpostについて、

```text
user 1件
または
userなし
```

を許可します。

`?` が `>` の右側にある点に注意してください。

---

# 7. 両側とも optional

両側とも0件を許可する場合は、

```dbml
Ref: orders.coupon_id ?>? coupons.id
```

と書けます。

意味としては、

```text
注文にはクーポンがない場合がある
クーポンは一度も利用されていない場合がある
```

という関係です。

---

# 8. `?>` と `>?` の違い

ここが特に混乱しやすい部分です。

## `?>`

```dbml
Ref: orders.customer_id ?> customers.id
```

`?` が左側にあります。

意味：

```text
customers 1 : 0..N orders
```

つまり、

> 顧客には注文が0件の場合がある

という意味です。

---

## `>?`

```dbml
Ref: posts.user_id >? users.id
```

`?` が右側にあります。

意味：

```text
posts N : 0..1 users
```

つまり、

> 投稿にはユーザーが存在しない場合がある

という意味です。

---

## `?>?`

```dbml
Ref: orders.coupon_id ?>? coupons.id
```

両側に `?` があります。

意味：

```text
注文側も0を許可
クーポン側も0を許可
```

します。

---

# 9. 1 : 1

1対1は `-` を使用します。

```dbml
Ref: users.id - user_profiles.user_id
```

意味：

```text
users 1 : 1 user_profiles
```

Short form / Long formで `-` を使用する場合、DBMLでは基本的に後ろ側のカラムがFKとして扱われます。

上記の場合、

```text
user_profiles.user_id
```

がFK側です。

---

# 10. 1 : 0..1

例えば、

```text
ユーザーはプロフィールをまだ登録していない場合がある
```

という場合です。

```dbml
Ref: users.id -? user_profiles.user_id
```

意味：

```text
users 1 : 0..1 user_profiles
```

つまり、

```text
1ユーザー
↓
プロフィール0件または1件
```

となります。

---

# 11. N : N

多対多は `<>` で表現できます。

```dbml
Ref: authors.id <> books.id
```

意味：

```text
authors N : N books
```

ただし、実際のRDB設計では中間テーブルを作ることが多いため、研修では基本的に中間テーブルを明示する設計を推奨します。

例えば、

```dbml
Table authors {
  id int [pk]
  name varchar
}

Table books {
  id int [pk]
  title varchar
}

Table author_books {
  author_id int
  book_id int
}

Ref: author_books.author_id > authors.id
Ref: author_books.book_id > books.id
```

構造：

```text
authors
   1
   |
   N
author_books
   N
   |
   1
books
```

これによって、

```text
authors N : N books
```

を実現します。

---

# 12. 課題でよく使うパターン

## 顧客 1 : 0..N 注文

顧客登録直後など、注文が0件の場合があります。

```dbml
Ref: orders.customer_id ?> customers.id
```

```text
customers 1 : 0..N orders
```

---

## 注文 1 : 1..N 注文明細

注文には最低1件の商品が必要です。

```dbml
Ref: order_items.order_id > orders.id
```

```text
orders 1 : 1..N order_items
```

---

## 注文 1 : 0..1 請求

注文直後は、まだ請求書が発行されていない場合があります。

```dbml
Ref: orders.id -? invoices.order_id
```

```text
orders 1 : 0..1 invoices
```

---

## 請求 1 : 0..N 入金

請求直後は入金0件の場合があります。

```dbml
Ref: payments.invoice_id ?> invoices.id
```

```text
invoices 1 : 0..N payments
```

---

## 顧客 1 : 0..N 配送先

顧客登録時点では配送先がまだ登録されていない場合があります。

```dbml
Ref: delivery_addresses.customer_id ?> customers.id
```

```text
customers 1 : 0..N delivery_addresses
```

---

## 注文 1 : 0..N 出荷

注文受付直後は出荷0件です。

```dbml
Ref: shipments.order_id ?> orders.id
```

```text
orders 1 : 0..N shipments
```

---

## 出荷 N : N 注文明細

1回の出荷に複数の注文明細を含み、

同じ注文明細が複数回に分けて出荷される場合があります。

実際のDBでは中間テーブルを使用します。

```dbml
Table shipments {
  id int [pk]
  order_id int
  shipped_at datetime
}

Table order_items {
  id int [pk]
  order_id int
  product_id int
  quantity int
}

Table shipment_items {
  shipment_id int
  order_item_id int
  quantity int
}

Ref: shipment_items.shipment_id > shipments.id
Ref: shipment_items.order_item_id > order_items.id
```

論理的には、

```text
shipments N : N order_items
```

です。

---

# 13. Inline形式

カラム定義の中に直接書くこともできます。

```dbml
Table orders {
  id int [pk]

  customer_id int [ref: ?> customers.id]
}
```

これは以下と同じ考え方です。

```dbml
Ref: orders.customer_id ?> customers.id
```

---

# 14. Short form

1行で書く形式です。

```dbml
Ref: orders.customer_id ?> customers.id
```

研修では、この形式がリレーション一覧を確認しやすいためおすすめです。

---

# 15. Long form

複数行で書くこともできます。

```dbml
Ref {
  orders.customer_id ?> customers.id
}
```

リレーションに設定を追加する場合などに使用できます。

---

# 16. リレーション名

リレーションに名前を付けることもできます。

```dbml
Ref customer_orders {
  orders.customer_id ?> customers.id
}
```

Short formでも指定できます。

```dbml
Ref customer_orders: orders.customer_id ?> customers.id
```

---

# 17. ON DELETE / ON UPDATE

参照アクションも記述できます。

```dbml
Ref: orders.customer_id > customers.id [
  delete: restrict,
  update: cascade
]
```

指定可能な代表例：

```text
cascade
restrict
set null
set default
no action
```

---

# 18. Composite Foreign Key

複合外部キーも記述できます。

```dbml
Ref:
  merchant_periods.(merchant_id, country_code)
  >
  merchants.(id, country_code)
```

---

# 19. コメント

DBMLではコメントも使用できます。

## 1行コメント

```dbml
// 注文には0件以上の出荷が存在する
Ref: shipments.order_id ?> orders.id
```

## 複数行コメント

```dbml
/*
注文受付直後は未出荷のため、
orders : shipments は 1 : 0..N
*/
Ref: shipments.order_id ?> orders.id
```

研修では、判断理由をコメントとして残しても構いません。

---



# 20. 注意点

DBMLで多重度を表現できても、そのすべてがDBのFK制約だけで保証できるとは限りません。

例えば、

```text
orders 1 : 1..N order_items
```

と設計しても、一般的なFK制約だけでは、

```text
order_itemsが0件のorders
```

の作成を完全には防げません。

そのため、

```text
ER図上の業務ルール
DB制約で保証できるルール
アプリケーション側で保証するルール
```

は分けて考える必要があります。

---

# 参考

DBML公式ドキュメント：

https://dbml.dbdiagram.io/docs/

Optional relationships：

https://community.dbdiagram.io/t/tutorial-optional-relationships/5894
