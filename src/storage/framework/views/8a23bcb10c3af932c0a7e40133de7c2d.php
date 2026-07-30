<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>利用履歴・返却画面</title>
</head>

<body>
    <h1>利用履歴・返却画面（仮）</h1>

    <h2>現在借りている資産</h2>
    <table border="1">
        <tr>
            <th>ユーザーID</th>
            <th>資産ID</th>
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
            <th>ユーザーID</th>
            <th>資産ID</th>
        </tr>

        <?php $__currentLoopData = $pastloanhistoryData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($history->user_id); ?></td>
                <td><?php echo e($history->asset_id); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
</body>

</html><?php /**PATH /var/www/html/resources/views/histories/index.blade.php ENDPATH**/ ?>