

<?php $__env->startSection('content'); ?>
    <h1>利用履歴・返却画面（仮）</h1>

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

        <?php $__currentLoopData = $loanhistoryData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($history->user_id); ?></td>
                <td><?php echo e($history->asset_id); ?></td>
                <td>
                    <form action="/histories/return" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="loan_history_id" value="<?php echo e($history->loan_history_id); ?>">
                        <button type="submit">返却</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>


    <h2>過去に申請した資産</h2>
    <table border="1">
        <tr>
            <th>NO.</th>
            <th>資産名</th>
            <th>カテゴリ</th>
            <th>種別</th>
            <th>貸出日</th>
            <th>返却日</th>
            <th>状態</th>
        </tr>

        <?php $__currentLoopData = $pastloanhistoryData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($history->user_id); ?></td>
                <td><?php echo e($history->asset_id); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/histories/index.blade.php ENDPATH**/ ?>