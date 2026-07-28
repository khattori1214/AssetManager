<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>資産一覧・申請画面（仮）</h1>

    <!-- 貸出資産一覧 -->
    <table border="1">
        <tr>
            <th>NO.</th>
            <th>資産名</th>
            <th>カテゴリ</th>
            <th>状態</th>
            <th>最大貸出期間</th>
            <th>操作</th>
        </tr>

        <tr>
            <?php $__currentLoopData = $assetData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <td scope="col" class="px6 py-2"><?php echo e($asset->asset_id); ?></td>
                <td scope="col" class="px6 py-2"><?php echo e($asset->asset_name); ?></td>
                <td scope="col" class="px6 py-2"><?php echo e($asset->category_id); ?></td>
                <td scope="col" class="px6 py-2"><?php echo e($asset->max_loan_days); ?></td>
                
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
    </table>

    <!-- 消耗品一覧 -->
    <table>
        <tr>
            <th>NO.</th>
            <th>品名</th>
            <th>カテゴリ</th>
            <th>在庫数</th>
            <th></th>
        </tr>

        <tr>
            <?php $__currentLoopData = $assetData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <td scope="col" class="px6 py-2"><?php echo e($asset->asset_id); ?></td>
            <td scope="col" class="px6 py-2"><?php echo e($asset->asset_name); ?></td>
            <td scope="col" class="px6 py-2"><?php echo e($asset->category_id); ?></td>
            <td scope="col" class="px6 py-2"><?php echo e($asset->stock); ?></td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
    </table>

</body>

</html><?php /**PATH /var/www/html/resources/views/assets/index.blade.php ENDPATH**/ ?>