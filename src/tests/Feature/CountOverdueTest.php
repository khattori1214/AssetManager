<?php

namespace Tests\Feature;

use App\Models\LoanHistory;
use App\Models\User;
use Tests\TestCase;

class CountOverdueTest extends TestCase
{
    public function test_期限超過の未返却履歴を数えられる(): void
    {
        // このユーザーに、期限が昨日以前・未返却の履歴が1件ある前提
        $user = User::findOrFail(); // 実際のユーザーIDに変更

        $actual = LoanHistory::countOverdue($user);

        $this->assertSame(1, $actual);
    }
}
