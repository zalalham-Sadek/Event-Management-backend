<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تعديل المستخدم</title>
</head>
<body>
    <h1>تعديل المستخدم</h1>

    <?php if ($users): ?>
        <form action="/update-user.php" method="POST">
            <!-- نرسل الـ id في حقل مخفي -->
            <input type="hidden" name="id" value="<?= htmlspecialchars($users['id']) ?>">

            <label for="name">الاسم:</label><br>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($users['name']) ?>"><br><br>

            <label for="email">البريد الإلكتروني:</label><br>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($users['email']) ?>"><br><br>

            <button type="submit">حفظ التغييرات</button>
        </form>
    <?php else: ?>
        <p>المستخدم غير موجود.</p>
    <?php endif; ?>

</body>
</html>
