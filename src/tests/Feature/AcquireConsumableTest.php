<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Asset;
use App\Models\User;
use App\Models\LoanHistory;
use Database\Seeders\RoleSeeder;

class AcquireConsumableTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_取得すると在庫が減り、取得履歴が登録される(): void
    {
        $this->seed(RoleSeeder::class);
        // 準備:消耗品を取得しようとしているユーザー
        $user = User::factory()->create();
        // 準備:消耗品を準備する
        $asset = Asset::factory()->consumable()->create([
            'stock' => 10
        ]);

        // 実行:消耗品を取得する
        $response = $this->actingAs($user)->post('/assets/acquire', [
            'asset_id' => $asset->asset_id,
            'quantity' => 1,
        ]);

        // 確認:該当の消耗品を取得できることを確認する
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // 確認:在庫が減っていることを確認する
        $asset->refresh();
        $this->assertSame(9, (int) $asset->stock);


        // 確認:取得履歴が1件追加される
        $this->assertDatabaseCount('consumable_histories', 1);

        // 確認:取得履歴が正しい内容で登録されている
        $this->assertDatabaseHas('consumable_histories', [
            'asset_id' => $asset->asset_id,
            'user_id' => $user->user_id,
            'quantity' => 1,
        ]);
    }

    public function test_在庫を超える数量は取得できない(): void
    {
        $this->seed(RoleSeeder::class);
        // 準備:消耗品を取得しようとしているユーザー
        $user = User::factory()->create();
        // 準備:消耗品を準備する
        $asset = Asset::factory()->consumable()->create([
            'stock' => 1,
        ]);

        // 実行:消耗品を取得する
        $response = $this->actingAs($user)->post('/assets/acquire', [
            'asset_id' => $asset->asset_id,
            'quantity' => 5,
        ]);

        // 確認:該当の消耗品を取得できないことを確認する
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // 確認:取得履歴が0件のまま
        $this->assertDatabaseCount('consumable_histories', 0);

        // 確認:取得履歴が登録されないことを確認する
        $this->assertDatabaseMissing('consumable_histories', [
            'asset_id' => $asset->asset_id,
            'user_id' => $user->user_id,
            'quantity' => 5,
        ]);
    }

}
