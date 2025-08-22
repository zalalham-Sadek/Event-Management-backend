<?php $title = "Users Page"; ?>

<h1 class="text-2xl font-bold mb-4">قائمة المستخدمين</h1>
<ul class="list-disc pl-6 space-y-2">
    <?php foreach ($users as $user): ?>
        <li>
            <span class="font-medium"><?= htmlspecialchars($user['name']) ?></span>
            - <span class="text-gray-600"><?= htmlspecialchars($user['email']) ?></span>
        </li>
    <?php endforeach; ?>
</ul>
