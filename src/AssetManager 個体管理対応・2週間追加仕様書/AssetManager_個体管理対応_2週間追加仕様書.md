PS C:\Users\bonds\docker\asset-manager\AssetManager> docker exec -it asset-manager-app php artisan test tests/Unit/ExampleTest.php

   PASS  Tests\Unit\ExampleTest
  ✓ that true is true                                                                                            0.01s

  Tests:    1 passed (1 assertions)
  Duration: 12.79s

## 10. 既存不具合

今回の改修範囲に関係する既存不具合です。**個体管理の実装に入る前に、まず再現を確認し、特性テストで記録してください。** 修正は「13. 実施計画」の該当日に行います。

| No  | 場所                          | 内容                                                               | 起きる問題                       |
| --- | ----------------------------- | ------------------------------------------------------------------ | -------------------------------- |
| 1   | `LoanHistory::countOverdue()` | `where('user_id', $user)` と User オブジェクトそのものを渡している | 期限超過件数が正しく取得できない |

$user = App\Models\User::findOrFail(5);
App\Models\LoanHistory::countOverdue($user);
実行結果
> $user = App\Models\User::findOrFail(5);
App\Models\LoanHistory::countOverdue($user);

= 0


| 2 | `Asset::consumableAssetData()` | 要発注の条件が `stock > 'min_stock'` になっている。不等号が逆で、さらに `'min_stock'` がカラムではなく文字列として比較されている | 要発注の消耗品が検索できない |


App\Models\Asset::consumableAssetData(null, null, '要発注');
実行結果
>= Illuminate\Pagination\LengthAwarePaginator {#7935
    +onEachSide: 3,
  }

| 3 | `UsageHistoryController::returnAsset()` | 対象の貸出履歴が見つからない場合の null チェックがない | 不正なIDや二重送信で500エラーになる |
確認用DBで、自分の未返却の貸出履歴を選ぶ。
返却リクエストを1回送る。
同じloan_history_idで、同じリクエストをもう一度送る。


<input type="hidden" name="loan_history_id" value="14">

1. 開発者ツールの「Elements（要素）」で、この行の 14をダブルクリック。
2. DBに存在しないID（例：999999）に変えてEnter。<input type="hidden" name="loan_history_id" value="999999">
3. ページを再読み込みせず、画面の返却確認の「はい」を押す。
504 Gateway Time-out
local.ERROR: Call to a member function returnAsset()

| 4 | `AssetApplicationController::borrow()` | 7日以上の期限超過で貸出を制限したときのメッセージが「すでに貸出中です」になっている | 利用者が制限の理由を理解できない |

文言変更

| 5 | `AssetApplicationController::borrow()` / `acquire()` | 資産が見つからない場合に `throw new \Exception` している | 500エラー画面が表示される |


App\Http\Controllers\AssetApplicationController::borrow(Request $request)



| 6 | `AssetApplicationController::acquire()` | 在庫数のチェックがトランザクションの外で行われ、`decreaseStock()` の戻り値（更新件数）も確認していない | 同時に取得されると、在庫不足なのに取得履歴だけが登録される |

2つのアカウントでログインし、同時に同じ貸出資産を取得する
どちらも取得できた


**No.2 のヒント**：カラム同士を比較するには `where()` ではなく `whereColumn()` を使います。

**No.6 のヒント**：`decreaseStock()` は `WHERE stock >= 数量` の条件付きで減算しているため、在庫不足なら更新件数が0になります。この戻り値を確認すれば在庫を超えた減算は防げますが、その時点ではすでに取得履歴が登録されています。「何を、どの順番で、トランザクションのどこで確認すべきか」を整理してください。

---

## 11. テスト

### 11.4 特性テスト（改修の前に書く）

個体管理の実装を始める**前に**、現在の貸出・返却・消耗品取得がどう動いているかをテストで記録します。これを**特性テスト**と呼びます。

| テスト | 確認する現在の動作                        |
| ------ | ----------------------------------------- |
| 貸出   | 貸出資産を借りると貸出履歴が1件登録される |

| 貸出 | 貸出中の資産は借りられない |

| 貸出 | 7日以上の期限超過がある社員は借りられない |
| 返却 | 自分の貸出を返却すると返却日時が登録される |
| 消耗品 | 取得すると在庫が減り、取得履歴が登録される |
| 消耗品 | 在庫を超える数量は取得できない |

特性テストを書く目的は2つあります。

1. 改修後に同じテストを（個体管理に合わせて修正した上で）実行し、既存の業務ルールが壊れていないことを確認する
2. 10章の既存不具合を「テストが失敗する」形で再現し、修正後に成功することを確認する

特性テストは、個体管理の実装に合わせて書き換えることになります。書き換えるときは、**テストの「意図」（守っている業務ルール）を変えずに、手段（送信するIDなど）だけを変える**ことを意識してください。
