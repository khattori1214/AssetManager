<?php $__env->startSection('content'); ?>

    <div class="content-area">

        <?php if($overdueCount > 0): ?>
            <div class="error-message">
                【警告】返却期限を過ぎている資産があります
                （<?php echo e($overdueCount); ?>件）。
            </div>
        <?php endif; ?>

        <a href="/histories" class="<?php echo e(request()->is('histories*') ? 'active' : ''); ?>">
            <h1>利用履歴・返却画面</h1>
        </a>

        <h2>現在借りている資産</h2>
        <table border="1">
            <tr>
                <th>NO.</th>
                <th>資産名</th>
                <th>カテゴリ</th>
                <th>種別</th>
                <th>貸出日</th>
                <th>返却期限</th>
                <th>状態</th>
                <th>操作</th>
            </tr>

            <?php $__empty_1 = true; $__currentLoopData = $loanHistoryData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($history->asset_name); ?></td>
                    <td><?php echo e($history->category_name); ?></td>
                    <td>貸出資産</td>
                    <td><?php echo e($history->loan_date); ?></td>
                    <td><?php echo e($history->due_date); ?></td>

                    <td>
                        <?php if($history->due_date < today()): ?>
                            期限超過
                        <?php else: ?>
                            貸出中
                        <?php endif; ?>
                    </td>

                    <td>
                        <!-- 返却ボタン -->
                        <button type="button"
                            onclick="document.getElementById('returnModal<?php echo e($history->loan_history_id); ?>').showModal()">
                            返却する
                        </button>



                        <!-- モーダル -->
                        <dialog id="returnModal<?php echo e($history->loan_history_id); ?>">
                            <h2>返却確認</h2>

                            <p>
                                「<?php echo e($history->asset_name); ?>」を返却しますが、よろしいですか？
                            </p>

                            <form action="/histories/return" method="POST">
                                <?php echo csrf_field(); ?>

                                <input type="hidden" name="loan_history_id" value="<?php echo e($history->loan_history_id); ?>">

                                <button type="submit">はい</button>

                                <button type="button"
                                    onclick="document.getElementById('returnModal<?php echo e($history->loan_history_id); ?>').close()">
                                    キャンセル
                                </button>
                            </form>
                        </dialog>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8">
                        現在貸出中の資産はありません。
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <div class="pagination-wrapper">
            <?php echo e($loanHistoryData->links('pagination::bootstrap-4')); ?>

        </div>

        <h2>過去に申請した資産・消耗品一覧</h2>
        <table border="1">
            <tr>
                <th>NO.</th>
                <th>資産名</th>
                <th>カテゴリ</th>
                <th>種別</th>
                <th>申請日・貸出日</th>
                <th>返却日・数量</th>
                <th>状態</th>
            </tr>

            <?php if(
                    $pastLoanHistoryData->count() === 0 &&
                    $consumableHistoryData->count() === 0
                ): ?>
                <tr>
                    <td colspan="7">
                        利用履歴はありません。
                    </td>
                </tr>
            <?php endif; ?>


            <?php $__currentLoopData = $pastLoanHistoryData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($history->asset_name); ?></td>
                    <td><?php echo e($history->category_name); ?></td>
                    <td>貸出資産</td>
                    <td><?php echo e($history->loan_date); ?></td>
                    <td><?php echo e($history->return_date); ?></td>
                    <td>返却済</td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


            <?php $__currentLoopData = $consumableHistoryData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($history->asset_name); ?></td>
                    <td><?php echo e($history->category_name ?? '-'); ?></td>
                    <td>消耗品</td>
                    <td><?php echo e($history->request_date); ?></td>
                    <td><?php echo e($history->quantity); ?> <?php echo e($history->unit); ?></td>
                    <td>取得済</td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>

        <div class="pagination-wrapper">
            <?php echo e($pastLoanHistoryData->links('pagination::bootstrap-4')); ?>

        </div>


        <div class="pagination-wrapper">
            <?php echo e($consumableHistoryData->links('pagination::bootstrap-4')); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/histories/index.blade.php ENDPATH**/ ?>