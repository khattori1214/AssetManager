<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Asset;
use App\Models\User;
use App\Models\LoanHistory;
use Database\Seeders\RoleSeeder;

class ReturnAssetTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_自分の貸出を返却すると返却日時が登録される(): void
    {
        $this->seed(RoleSeeder::class);
        // 準備:返却するユーザーを作る
        $borrower = User::factory()->create();
        // 準備:貸出資産を作る
        $asset = Asset::factory()->loan()->create();
        // 準備:そのユーザーが資産を借りていて未返却の状態にする
        $loanHistory = LoanHistory::factory()->create([
            'user_id' => $borrower->user_id,
            'asset_id' => $asset->asset_id,
            'return_date' => null
        ]);

        // 実行:自分の貸出を返却する
        $response = $this->actingAs($borrower)->post('/histories/return', [
            'loan_history_id' => $loanHistory->loan_history_id
        ]);

        // 確認:入力エラーなくリダイレクトされる
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // 確認:元の貸出履歴に返却日時が登録されている
        $loanHistory->refresh();
        $this->assertNotNull($loanHistory->return_date);

        // 確認:貸出履歴が1件となり、新しい履歴が増えていない
        $this->assertDatabaseCount('loan_histories', 1);
    }
}
