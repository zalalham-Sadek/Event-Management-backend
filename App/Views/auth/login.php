<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="/public/assets/style.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>

        <?php if(isset($error)): ?>
            <p class="text-red-500 mb-4 text-center"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST" action="/login" class="space-y-4">
            <div>
                <label class="block mb-1 font-medium">Email:</label>
                <input type="email" name="email" required
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block mb-1 font-medium">Password:</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            
            <button type="submit"
                    class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">login</button>
        </form>

        <p class="mt-4 text-center text-sm">
            don't have an account? <a href="/register" class="text-blue-500 hover:underline">register</a>
        </p>
    </div>

</body>
</html>
