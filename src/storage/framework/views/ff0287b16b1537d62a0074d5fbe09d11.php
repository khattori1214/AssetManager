<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン | AssetManager</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>?v=3">
</head>

<body class="login-page">
    <main class="login-card">
        <h1>AssetManager</h1>
        <p class="login-subtitle">
            社内資産・備品管理システム
        </p>

        <form action="/login" method="post" class="login-form">
            <?php echo csrf_field(); ?>

            <?php $__errorArgs = ['login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="error-message">
                    <?php echo e($message); ?>

                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <div class="form-group">
                <label for="employee_no">社員番号ID</label>
                <input type="text" id="employee_no" name="employee_no" placeholder="社員番号を入力">
                <?php $__errorArgs = ['employee_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="field-error">
                        <?php echo e($message); ?>

                    </p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
            
                <label for="password">パスワード</label>
                <div class="password-field">
                <input type="password" id="password" name="password" placeholder="パスワードを入力">
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="field-error">
                        <?php echo e($message); ?>

                    </p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <button type="submit" class="login-button">
                ログイン
            </button>
        </form>
    </main>

</body>

</html><?php /**PATH /var/www/html/resources/views/auth/login.blade.php ENDPATH**/ ?>