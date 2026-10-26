<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Asset;
use App\Models\User;
use App\Models\LoanHistory;
use Database\Seeders\RoleSeeder;
class BorrowAssetTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_貸出可能な資産は借りられる(): void
    {
        //rolesに管理者・一般社員を登録
        $this->seed(RoleSeeder::class);
        // 準備:ユーザーと資産を用意する
        $user = User::factory()->create();
        $asset = Asset::factory()->loan()->create();

        // 実行:貸出資産を借りる
        $response = $this->actingAs($user)->post('/assets/borrow', [
            'asset_id' => $asset->asset_id
        ]);

        // 確認:入力エラーなくリダイレクトされる
        $response->assertRedirect();
        $this->assertDatabaseHas('loan_histories', [
            'asset_id' => $asset->asset_id,
        ]);
        $response->assertSessionHasNoErrors();

        // 確認:貸出履歴が1件登録される
        $this->assertDatabaseCount('loan_histories', 1);

        // 確認:今回のユーザーが、この資産を借りていて未返却である
        $this->assertDatabaseHas('loan_histories', [
            'user_id' => $user->user_id,
            'asset_id' => $asset->asset_id,
            'return_date' => null,
        ]);
    }

    public function test_貸出中の資産は借りられない(): void
    {
        $this->seed(RoleSeeder::class);
        // 準備:借りようとしているユーザー
        $user = User::factory()->create();
        // 準備:現在借りているユーザー
        $borrower = User::factory()->create();
        // 準備:貸出資産を準備する
        $asset = Asset::factory()->loan()->create();
        // 準備:その資産を貸出中にする
        $loanhistory = LoanHistory::factory()->create([
            'user_id' => $borrower->user_id,
            'asset_id' => $asset->asset_id
        ]);
        // 実行:別のユーザーが同じ資産を借りようとする
        $response = $this->actingAs($user)->post('/assets/borrow', [
            'asset_id' => $asset->asset_id
        ]);

        // 確認:拒否され、エラーメッセージが設定される
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // 確認:貸出履歴が1件のまま
        $this->assertDatabaseCount('loan_histories', 1);

        // 確認:もとの貸出履歴は未返却のまま残っている
        $this->assertDatabaseHas('loan_histories', [
            'loan_history_id' => $loanhistory->loan_history_id,
            'user_id' => $borrower->user_id,
            'asset_id' => $asset->asset_id,
            'return_date' => null,
        ]);

        // 確認:今回のユーザーの履歴は作られていない
        $this->assertDatabaseMissing('loan_histories', [
            'user_id' => $user->user_id,
            'asset_id' => $asset->asset_id,
        ]);
    }

    public function test_7日以上の期限超過がある社員は借りられない(): void
    {
        $this->seed(RoleSeeder::class);
        // 準備:現在借りているユーザー
        $borrower = User::factory()->create();
        // 準備:以前から借りている資産をつくる
        $overdueAsset = Asset::factory()->loan()->create();
        // 準備:返却期限を7日過ぎても未返却の履歴を作る
        LoanHistory::factory()->create([
            'user_id' => $borrower->user_id,
            'asset_id' => $overdueAsset->asset_id,
            'loan_date' => now()->subDays(21),
            'due_date' => now()->subDays(7),
            'return_date' => null
        ]);

        // 準備:新しく借りようとする、誰も借りていない資産
        $newAsset = Asset::factory()->loan()->create();
        // 実行:期限超過している本人が、別の資産を借りようとする
        $response = $this->actingAs($borrower)->post('/assets/borrow', [
            'asset_id' => $newAsset->asset_id
        ]);

        // 確認:拒否され、エラーメッセージが設定される
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // 確認:貸出履歴が1件のまま
        $this->assertDatabaseCount('loan_histories', 1);

        // 確認:新しく借りようとした資産の貸出履歴は作られていない
        $this->assertDatabaseMissing('loan_histories', [
            'user_id' => $borrower->user_id,
            'asset_id' => $newAsset->asset_id,
        ]);
    }
}
