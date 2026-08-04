<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>資産編集</title>
</head>

<body>
  <h1>資産編集</h1>

  <form action="/admin/assets/<?php echo e($asset->asset_id); ?>" method="post">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div>
      <label>資産名</label>
      <input
        type="text"
        name="asset_name"
        value="<?php echo e(old('asset_name', $asset->asset_name)); ?>"
        required>
    </div>

    <div>
      <label>カテゴリID</label>
      <input
        type="number"
        name="category_id"
        value="<?php echo e(old('category_id', $asset->category_id)); ?>"
        required>
    </div>

    <div>
      <label>資産種別</label>

      <select name="asset_type" required>
        <option
          value="loan"
          <?php if(old('asset_type', $asset->asset_type) === 'loan'): echo 'selected'; endif; ?>
          >
          貸出資産
        </option>

        <option
          value="consumable"
          <?php if(old('asset_type', $asset->asset_type) === 'consumable'): echo 'selected'; endif; ?>
          >
          消耗品
        </option>
      </select>
    </div>

    <div>
      <label>在庫数</label>
      <input
        type="number"
        name="stock"
        min="0"
        value="<?php echo e(old('stock', $asset->stock)); ?>">
    </div>

    <div>
      <label>最低在庫数</label>
      <input
        type="number"
        name="min_stock"
        min="0"
        value="<?php echo e(old('min_stock', $asset->min_stock)); ?>">
    </div>

    <button type="submit">更新</button>

    <a href="/admin">戻る</a>
  </form>
  <h2>経理連携CSV一覧</h2>

  <table border="1">
    <tr>
      <th>対象月</th>
      <th>ファイル名</th>
      <th>操作</th>
    </tr>

    <?php $__currentLoopData = $csvData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $csv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
      <td><?php echo e($csv->target_month); ?></td>
      <td><?php echo e($csv->file_name); ?></td>
      <td>
        ダウンロード
      </td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

  </table>

</body>

</html><?php /**PATH /var/www/html/resources/views/admin/edit.blade.php ENDPATH**/ ?>