<?php $__env->startSection('content'); ?>

    <div class="content-area">

        <?php if(session('success')): ?>
            <div class="success-message">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="error-message">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <?php if($overdueCount > 0): ?>
            <div class="error-message">
                【警告】返却期限を過ぎている資産があります（<?php echo e($overdueCount); ?>件）。
            </div>
        <?php endif; ?>

        <h1>利用履歴・返却画面</h1>

        <h2>現在借りている資産</h2>
        <table border="1">
            <tr>
                <th>NO.</th>
                <th>資産名</th>
                <th>カテゴリ</th>
                <th>種別</th>
                <th>貸出日</th>
                <th>状態</th>
                <th>操作</th>
            </tr>

            <?php $__empty_1 = true; $__currentLoopData = $loanhistoryData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($history->asset_name); ?></td>
                    <td><?php echo e($history->category_name); ?></td>
                    <td><?php echo e($history->asset_type); ?></td>
                    <td><?php echo e($history->loan_date); ?></td>

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
                            返却
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
                                    いいえ
                                </button>
                            </form>
                        </dialog>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7">
                        現在貸出中の資産はありません。
                    </td>
                </tr>
            <?php endif; ?>
        </table>

        <div class="pagination-wrapper">
            <?php echo e($loanhistoryData->links('pagination::bootstrap-4')); ?>

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
                    $pastloanhistoryData->count() === 0 &&
                    $consumablehistoryData->count() === 0
                ): ?>
                <tr>
                    <td colspan="7">
                        利用履歴はありません。
                    </td>
                </tr>
            <?php endif; ?>


            <?php $__currentLoopData = $pastloanhistoryData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($history->asset_name); ?></td>
                    <td><?php echo e($history->category_name); ?></td>
                    <td><?php echo e($history->asset_type); ?></td>
                    <td><?php echo e($history->loan_date); ?></td>
                    <td><?php echo e($history->return_date); ?></td>
                    <td>返却済</td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


            <?php $__currentLoopData = $consumablehistoryData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($history->asset_name); ?></td>
                    <td><?php echo e($history->category_name ?? '-'); ?></td>
                    <td><?php echo e($history->asset_type); ?></td>
                    <td><?php echo e($history->request_date); ?></td>
                    <td><?php echo e($history->quantity); ?> <?php echo e($history->unit); ?></td>
                    <td>取得済</td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>

        <div class="pagination-wrapper">
            <?php echo e($pastloanhistoryData->links('pagination::bootstrap-4')); ?>

        </div>


        <div class="pagination-wrapper">
            <?php echo e($consumablehistoryData->links('pagination::bootstrap-4')); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/histories/index.blade.php ENDPATH**/ ?>