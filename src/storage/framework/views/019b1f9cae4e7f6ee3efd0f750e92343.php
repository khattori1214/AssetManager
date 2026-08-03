<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>資産登録・在庫管理画面（仮）</h1>
    <h2>貸出資産新規登録</h2>
        
            <h3>資産名</h3>
       

        <form action="/admin/assets" method="post">
            <?php echo csrf_field(); ?>
            <label>資産名</label><br>
            <input type="text" name="asset_name"><br>
            <button type="submit">新規登録</button>
        </form>
</body>

</html><?php /**PATH /var/www/html/resources/views/admin/index.blade.php ENDPATH**/ ?>