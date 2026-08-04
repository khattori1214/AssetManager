<?php $__env->startSection('content'); ?>

<h1>資産登録・在庫管理画面（仮）</h1>
<button type="button"
    onclick="document.getElementById('loanRegisterModal').showModal()">
    貸出資産を新規登録
</button>

<button type="button"
    onclick="document.getElementById('consumableRegisterModal').showModal()">
    消耗品を新規登録
</button>
<h2>貸出資産一覧</h2>

<table border="1">
    <tr>
        <th>NO.</th>
        <th>資産名</th>
        <th>カテゴリ</th>
        <th>状態</th>
        <th>最大貸出期間</th>
        <th>操作</th>
    </tr>


    <?php $__currentLoopData = $assetManagementData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($asset->asset_type == 'loan'): ?>
    <tr>
        <td><?php echo e($asset->asset_id); ?></td>
        <td><?php echo e($asset->asset_name); ?></td>
        <td><?php echo e($asset->category_name); ?></td>
        <td>利用可能</td>
        <td><?php echo e($asset->max_loan_days); ?>日</td>
        <td>
            <a href="/admin/assets/<?php echo e($asset->asset_id); ?>/edit">編集</a>

            <form action="/admin/assets/<?php echo e($asset->asset_id); ?>" method="post" style="display:inline;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit">削除</button>
            </form>
        </td>
    </tr>
    <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>

<div>
    <?php echo e($assetManagementData->links()); ?>

</div>
<!-- 消耗品一覧 -->
<table border="1">


    <?php $__currentLoopData = $assetManagementData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($asset->asset_type == 'consumable'): ?>
    <tr>
        <td><?php echo e($asset->asset_id); ?></td>
        <td><?php echo e($asset->asset_name); ?></td>
        <td><?php echo e($asset->category_name); ?></td>
        <td><?php echo e($asset->stock); ?></td>

        <td>
            <?php if($asset->stock <= $asset->min_stock): ?>
                要発注
                <?php else: ?>
                在庫あり
                <?php endif; ?>
        </td>

        <td>
            <a href="/admin/assets/<?php echo e($asset->asset_id); ?>/edit">編集</a>

            <form action="/admin/assets/<?php echo e($asset->asset_id); ?>/stock" method="post" style="display:inline;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>

                <input type="number"
                    name="stock"
                    value="<?php echo e($asset->stock); ?>"
                    min="0">

                <button type="submit">在庫更新</button>
            </form>

            <form action="/admin/assets/<?php echo e($asset->asset_id); ?>" method="post" style="display:inline;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>

                <button type="submit">削除</button>
            </form>

        </td>
    </tr>
    <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>



<dialog id="loanRegisterModal">
    <h2>貸出資産登録</h2>

    <form action="/admin/assets" method="post">
        <?php echo csrf_field(); ?>

        <input type="hidden" name="asset_type" value="loan">

        <div>
            <label>資産名</label>
            <input type="text" name="asset_name" required>
        </div>

        <div>
            <label>カテゴリID</label>
            <input type="number" name="category_id" required>
        </div>

        <button type="submit">登録</button>

        <button type="button"
            onclick="document.getElementById('loanRegisterModal').close()">
            閉じる
        </button>
    </form>
</dialog>

<dialog id="consumableRegisterModal">
    <h2>消耗品登録</h2>

    <form action="/admin/assets" method="post">
        <?php echo csrf_field(); ?>

        <input type="hidden" name="asset_type" value="consumable">

        <div>
            <label>品名</label>
            <input type="text" name="asset_name" required>
        </div>

        <div>
            <label>カテゴリID</label>
            <input type="number" name="category_id" required>
        </div>

        <div>
            <label>在庫数</label>
            <input type="number" name="stock" min="0" required>
        </div>

        <div>
            <label>最低在庫数</label>
            <input type="number" name="min_stock" min="0" required>
        </div>

        <button type="submit">登録</button>

        <button type="button"
            onclick="document.getElementById('consumableRegisterModal').close()">
            閉じる
        </button>
    </form>
</dialog>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/index.blade.php ENDPATH**/ ?>