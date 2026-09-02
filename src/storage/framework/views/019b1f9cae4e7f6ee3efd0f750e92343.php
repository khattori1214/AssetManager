<?php $__env->startSection('content'); ?>

    <div class="content-area">

        <a href="/admin" class="<?php echo e(request()->is('admin*') ? 'active' : ''); ?>">
            <h1>資産登録・在庫管理画面</h1>
        </a>
        <button type="button" onclick="document.getElementById('loanRegisterModal').showModal()">
            貸出資産を登録する
        </button>

        <button type="button" onclick="document.getElementById('consumableRegisterModal').showModal()">
            消耗品を登録する
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


            <?php $__currentLoopData = $loanAssetData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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

                        <button type="button" class="cancel-button" onclick="openDeleteModal('<?php echo e($asset->asset_id); ?>', '<?php echo e($asset->asset_name); ?>')">
                            削除する
                        </button>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>

        <div class="pagination-wrapper">
            <?php echo e($loanAssetData->links('pagination::bootstrap-4')); ?>

        </div>


        <!-- 消耗品一覧 -->
        <h2>消耗品一覧</h2>

        <table border="1">
            <tr>
                <th>NO.</th>
                <th>資産名</th>
                <th>在庫数</th>
                <th>状態</th>
                <th>操作</th>
            </tr>

            <?php $__currentLoopData = $consumableAssetData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($asset->asset_id); ?></td>
                    <td><?php echo e($asset->asset_name); ?></td>
                    <td><?php echo e($asset->stock); ?></td>

                    <td>
                        <?php if($asset->stock <= $asset->min_stock): ?>
                            要発注
                        <?php else: ?>
                            在庫あり
                        <?php endif; ?>
                    </td>

                    <td>
                        <button type="button"
                            onclick="document.getElementById('stockModal-<?php echo e($asset->asset_id); ?>').showModal()">
                            在庫を更新する
                        </button>

                        <button type="button" class="cancel-button" onclick="openDeleteModal('<?php echo e($asset->asset_id); ?>', '<?php echo e($asset->asset_name); ?>')">
                            削除する
                        </button>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>

        <div class="pagination-wrapper">
            <?php echo e($consumableAssetData->links('pagination::bootstrap-4')); ?>

        </div>

        <!-- 消耗品在庫更新ダイアログ -->
        <?php $__currentLoopData = $consumableAssetData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <dialog id="stockModal-<?php echo e($asset->asset_id); ?>">
                <h2>消耗品の在庫を更新する</h2>

                <form action="/admin/assets/<?php echo e($asset->asset_id); ?>/stock" method="post">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>

                    <p>
                        資産名：<?php echo e($asset->asset_name); ?>（変更不可）
                    </p>

                    <div>
                        <label>在庫数</label>
                        <input type="number" name="stock" value="<?php echo e(old('stock', $asset->stock)); ?>" min="0" required>
                    </div>

                    <div>
                        <label>最低キープ数</label>
                        <input type="number" name="min_stock" value="<?php echo e(old('min_stock', $asset->min_stock)); ?>" min="0"
                            required>
                    </div>

                    <p>単位：<?php echo e($asset->unit); ?>（変更不可）</p>

                    <p>
                        1回の最大申請数：
                        <?php echo e($asset->max_request_quantity); ?>（変更不可）
                    </p>

                    <p>
                        月間最大申請回数：
                        <?php echo e($asset->monthly_request_limit); ?>（変更不可）
                    </p>


                    <button type="submit">更新する</button>

                    <button type="button" class="cancel-button"
                        onclick="document.getElementById('stockModal-<?php echo e($asset->asset_id); ?>').close()">
                        キャンセル
                    </button>

                </form>
            </dialog>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



        <dialog id="loanRegisterModal">
            <h2>貸出資産を登録する</h2>

            <form action="/admin/assets" method="post">
                <?php echo csrf_field(); ?>

                <input type="hidden" name="asset_type" value="loan">

                <div>
                    <label>資産名</label>
                    <input type="text" name="asset_name" required>
                </div>

                <div>
                    <label>カテゴリ名</label>
                    <!-- プルダウンに -->
                    <select name="category_id">
                        <option value="1">PC</option>
                        <option value="2">BOOK</option>
                    </select>
                </div>

                <div>
                    <label>単位</label>
                    <select name="unit">
                        <option value="台">台</option>
                        <option value="冊">冊</option>
                        <option value="本">本</option>
                        <option value="個">個</option>
                        <option value="枚">枚</option>
                        <option value="箱">箱</option>
                    </select>
                </div>

                <button type="submit">登録する</button>

                <button type="button" class="cancel-button" onclick="document.getElementById('loanRegisterModal').close()">
                    キャンセル
                </button>
            </form>
        </dialog>

        <dialog id="consumableRegisterModal">
            <h2>消耗品を登録する</h2>

            <form action="/admin/assets" method="post">
                <?php echo csrf_field(); ?>

                <input type="hidden" name="asset_type" value="consumable">

                <div>
                    <label>資産名</label>
                    <input type="text" name="asset_name" required>
                </div>

                <div>
                    <label>在庫数</label>
                    <input type="number" name="stock" min="0" required>
                </div>

                <div>
                    <label>最低キープ数</label>
                    <input type="number" name="min_stock" min="0" required>
                </div>

                <div>
                    <label>単位</label>
                    <select name="unit">
                        <option value="台">台</option>
                        <option value="冊">冊</option>
                        <option value="本">本</option>
                        <option value="個">個</option>
                        <option value="枚">枚</option>
                        <option value="箱">箱</option>
                    </select>
                </div>

                <div>
                    <label>1回の最大申請数</label>
                    <input type="number" name="max_request_quantity" min="1">
                </div>

                <div>
                    <label>月間最大申請回数</label>
                    <input type="number" name="monthly_request_limit" min="1">
                </div>

                <button type="submit">登録する</button>

                <button type="button" class="cancel-button"
                    onclick="document.getElementById('consumableRegisterModal').close()">
                    キャンセル
                </button>
            </form>
        </dialog>


        <h2>経理連携用CSVファイル</h2>
        <a href="/admin/csv/download">
            経理連携用CSVをダウンロード
        </a>


        <dialog id="deleteModal">
            <h2>貸出資産・消耗品削除</h2>

            <p>
                「<span id="deleteAssetName"></span>」を削除しますか？
            </p>

            <form id="deleteForm" method="post">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>

                <button type="submit">
                    削除する
                </button>

                <button type="button" class="cancel-button" onclick="document.getElementById('deleteModal').close()">
                    キャンセル
                </button>

            </form>
        </dialog>


        <script>
            function openDeleteModal(id, name) {
                document.getElementById('deleteAssetName').textContent = name;
                document.getElementById('deleteForm').action = '/admin/assets/' + id;
                document.getElementById('deleteModal').showModal();
            }
        </script>


    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/index.blade.php ENDPATH**/ ?>