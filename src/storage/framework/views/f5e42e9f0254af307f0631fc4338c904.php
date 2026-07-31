<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>資産一覧・申請画面</h1>

    <!-- 検索機能 -->
    <div class="box">
        <form action="/assets/search" method="get">
            <h3>検索条件</h3>
            <div>
                <label>資産名</label>
                <input type="text" name="keyword" value="<?php echo e(request('keyword')); ?>" placeholder="例: PC">
            </div>

            <!-- <div>
                <label>カテゴリ</label>
                <input type="text" name="max" value="<?php echo e(request('max')); ?>" placeholder="例: 2000">
            </div> -->

            <div><button type="submit" id="searchBtn">検索</button></div>
        </form>
    </div>

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


        <?php $__currentLoopData = $assetData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td scope="col" class="px6 py-2"><?php echo e($asset->asset_id); ?></td>
                <td scope="col" class="px6 py-2"><?php echo e($asset->asset_name); ?></td>
                <td scope="col" class="px6 py-2"><?php echo e($asset->category_id); ?></td>
                <td scope="col" class="px6 py-2"><?php echo e($asset->max_loan_days); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>

    <div>
        <?php echo e($assetData->links()); ?>

    </div>
    <!-- 消耗品一覧 -->
    <table border="1">
        <tr>
            <th>NO.</th>
            <th>品名</th>
            <th>カテゴリ</th>
            <th>在庫数</th>
            <th>状態</th>
            <th>操作</th>
        </tr>

        <tr>
            <?php $__currentLoopData = $assetData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <td scope="col" class="px6 py-2"><?php echo e($asset->asset_id); ?></td>
                <td scope="col" class="px6 py-2"><?php echo e($asset->asset_name); ?></td>
                <td scope="col" class="px6 py-2"><?php echo e($asset->category_id); ?></td>
                <td scope="col" class="px6 py-2"><?php echo e($asset->stock); ?></td>

                <!-- 貸出資産取得モーダル -->
                <button id="openButton" onClick="document.getElementById('modalDialog').showModal()">モーダルを開く(消耗品)</button>
                <dialog id="modalDialog" class="dialog">
                    <div id="dialog-container">
                        <header>
                            <span>取得</span>
                            <button id="closeButton" type="button" onclick="document.getElementById('modalDialog').close()">
                                <p>閉じる</p>
                            </button>
                        </header>
                        <div>Message</div>
                        <form method="post" action="/assets/acquire">
                            <?php echo csrf_field(); ?>

                            <input type="hidden" name="asset_id" value="<?php echo e($asset->asset_id); ?>">
                            <input type="number" name="quantity" min="1" max="<?php echo e($asset->stock); ?>" value="1" required>
                            <button type="submit">はい</button>
                            <button type="button" onclick="document.getElementById('modalDialog').close()">いいえ</button>
                        </form>
                    </div>
                </dialog>

                <!-- 消耗品取得モーダル -->
                <button id="openButton" onClick="document.getElementById('modalDialog').showModal()">モーダルを開く(消耗品)</button>
                <dialog id="modalDialog" class="dialog">
                    <div id="dialog-container">
                        <header>
                            <span>取得</span>
                            <button id="closeButton" type="button" onclick="document.getElementById('modalDialog').close()">
                                <p>閉じる</p>
                            </button>
                        </header>
                        <div>Message</div>
                        <form method="post" action="/assets/acquire">
                            <?php echo csrf_field(); ?>

                            <input type="hidden" name="asset_id" value="<?php echo e($asset->asset_id); ?>">
                            <input type="number" name="quantity" min="1" max="<?php echo e($asset->stock); ?>" value="1" required>
                            <button type="submit">はい</button>
                            <button type="button" onclick="document.getElementById('modalDialog').close()">いいえ</button>
                        </form>
                    </div>
                </dialog>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
    </table>


</body>

</html><?php /**PATH /var/www/html/resources/views/assets/index.blade.php ENDPATH**/ ?>