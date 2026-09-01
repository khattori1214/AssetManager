<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>?v=<?php echo e(filemtime(public_path('css/app.css'))); ?>">
    <title>AssetManager</title>
</head>

<body>

    <!-- 上部ヘッダー -->
    <header class="app-header">

        <h2>AssetManager-社内資産・備品管理システム-</h2>

        <div>
            <?php echo e(Auth::user()->user_name); ?>

        </div>

        <form action="/logout" method="POST" class="logout-form">
            <?php echo csrf_field(); ?>

            <button type="submit" class="logout-button">
                ログアウト
            </button>
        </form>

    </header>

    <div class="app-body">
        <!-- 左メニュー -->
        <aside class="sidebar">

            <ul>

                <li>
                    <a href="/top">
                        トップ画面
                    </a>
                </li>

                <li>
                    <a href="/assets">
                        資産一覧・申請画面
                    </a>
                </li>

                <li>
                    <a href="/histories">
                        利用履歴・返却画面
                    </a>
                </li>

                <?php if(Auth::user()->role_id == 1): ?>

                    <li>
                        <a href="/admin">
                            資産登録・在庫管理<br>【管理者のみ】
                        </a>
                    </li>

                <?php endif; ?>

            </ul>

        </aside>

        <!-- 各画面 -->
        <main class="main-content">

            
            <?php if(session('success')): ?>
                <div class="message success-message">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            
            <?php if(session('error')): ?>
                <div class="message error-message">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            
            <?php if($errors->any()): ?>
                <div class="message error-message">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>


            <?php echo $__env->yieldContent('content'); ?>

        </main>

    </div>

</body>

</html><?php /**PATH /var/www/html/resources/views/layouts/app.blade.php ENDPATH**/ ?>