<?php $__env->startSection('content'); ?>
    <div class="content-area">

        <h1>資産一覧・申請画面</h1>

        
        <?php if(session('success')): ?>
            <div>
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        
        <?php if(session('error')): ?>
            <div>
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        
        <?php if($errors->any()): ?>
            <div>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p><?php echo e($error); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        
        <?php if(($overdueCount ?? 0) > 0): ?>
            <div class="error-message">
                【警告】返却期限を過ぎている資産があります
                （<?php echo e($overdueCount); ?>件）。
            </div>
        <?php endif; ?>

        
        <div class="box">
            <form action="/assets" method="get">
                <h2>検索条件</h2>

                <div>
                    <label for="keyword">資産名</label>

                    <input type="text" id="keyword" name="keyword" value="<?php echo e(request('keyword')); ?>" placeholder="例:PC">
                </div>

                <div>
                    <label for="asset_type">資産種別</label>

                    <select id="asset_type" name="asset_type">
                        <option value="">すべて</option>

                        <option value="loan" <?php if(request('asset_type') === 'loan'): echo 'selected'; endif; ?>>
                            貸出資産
                        </option>

                        <option value="consumable" <?php if(request('asset_type') === 'consumable'): echo 'selected'; endif; ?>>
                            消耗品
                        </option>
                    </select>
                </div>

                <button type="submit" id="searchBtn">
                    検索
                </button>
            </form>
        </div>

        
        <h2>貸出資産一覧</h2>

        <table border="1">
            <thead>
                <tr>
                    <th>NO.</th>
                    <th>資産名</th>
                    <th>カテゴリ</th>
                    <th>状態</th>
                    <th>最大貸出期間</th>
                    <th>操作</th>
                </tr>
            </thead>

            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $loanAssetData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($asset->asset_id); ?></td>
                        <td><?php echo e($asset->asset_name); ?></td>
                        <td><?php echo e($asset->category_name); ?></td>

                        <td>
                            <?php if($asset->is_borrowed): ?>
                                貸出中
                            <?php else: ?>
                                利用可能
                            <?php endif; ?>
                        </td>

                        <td><?php echo e($asset->max_loan_days); ?>日</td>

                        <td>
                            <button type="button"
                                onclick="document.getElementById('borrowModal<?php echo e($asset->asset_id); ?>').showModal()"
                                <?php if($asset->is_borrowed || $isLocked): echo 'disabled'; endif; ?>>

                                <?php if($asset->is_borrowed): ?>
                                    貸出中
                                <?php else: ?>
                                    貸出
                                <?php endif; ?>
                            </button>

                            <dialog id="borrowModal<?php echo e($asset->asset_id); ?>">
                                <h2>貸出確認</h2>

                                <p>
                                    「<?php echo e($asset->asset_name); ?>」を貸し出しますか？
                                </p>

                                <form method="post" action="/assets/borrow">
                                    <?php echo csrf_field(); ?>

                                    <input type="hidden" name="asset_id" value="<?php echo e($asset->asset_id); ?>">

                                    <button type="submit">はい</button>

                                    <button type="button"
                                        onclick="document.getElementById('borrowModal<?php echo e($asset->asset_id); ?>').close()">
                                        いいえ
                                    </button>
                                </form>
                            </dialog>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6">貸出資産はありません。</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="pagination-wrapper">
            <?php echo e($loanAssetData->links('pagination::bootstrap-4')); ?>

        </div>

        
        <h2>消耗品一覧</h2>

        <table border="1">
            <thead>
                <tr>
                    <th>NO.</th>
                    <th>品名</th>
                    <th>カテゴリ</th>
                    <th>在庫数</th>
                    <th>状態</th>
                    <th>操作</th>
                </tr>
            </thead>

            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $consumableAssetData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($asset->asset_id); ?></td>
                        <td><?php echo e($asset->asset_name); ?></td>
                        <td><?php echo e($asset->category_name); ?></td>
                        <td><?php echo e($asset->stock); ?></td>

                        <td>
                            <?php if($asset->stock < $asset->min_stock): ?>
                                <strong>要発注</strong>
                            <?php else: ?>
                                在庫あり
                            <?php endif; ?>
                        </td>

                        <td>
                            <button type="button"
                                onclick="document.getElementById('acquireModal<?php echo e($asset->asset_id); ?>').showModal()"
                                <?php if($asset->stock <= 0): echo 'disabled'; endif; ?>>
                                取得
                            </button>

                            <dialog id="acquireModal<?php echo e($asset->asset_id); ?>">
                                <h2>取得数量入力</h2>

                                <p>
                                    「<?php echo e($asset->asset_name); ?>」の取得数量を入力してください。
                                </p>

                                <form method="post" action="/assets/acquire">
                                    <?php echo csrf_field(); ?>

                                    <input type="hidden" name="asset_id" value="<?php echo e($asset->asset_id); ?>">

                                    <div>
                                        <label for="quantity<?php echo e($asset->asset_id); ?>">
                                            数量
                                        </label>

                                        <input type="number" id="quantity<?php echo e($asset->asset_id); ?>" name="quantity" min="1"
                                            max="<?php echo e($asset->stock); ?>" value="1" required>
                                    </div>

                                    <button type="submit">はい</button>

                                    <button type="button"
                                        onclick="document.getElementById('acquireModal<?php echo e($asset->asset_id); ?>').close()">
                                        いいえ
                                    </button>
                                </form>
                            </dialog>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6">消耗品はありません。</td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>

        <div class="pagination-wrapper">
            <?php echo e($consumableAssetData->links('pagination::bootstrap-4')); ?>

        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/assets/index.blade.php ENDPATH**/ ?>