<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>setup the admin  </title>
<link rel="stylesheet" href="/public/assets/style.css">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">setup Admin</h1>

        <form action="/setup-admin" method="POST" class="space-y-4">
            <div>
                <label class="block mb-1 font-medium">name</label>
                <input type="text" name="name" class="w-full border border-gray-300 rounded p-2" required>
            </div>
            <div>
                <label class="block mb-1 font-medium"> Email</label>
                <input type="email" name="email" class="w-full border border-gray-300 rounded p-2" required>
            </div>
            <div>
                <label class="block mb-1 font-medium"> password</label>
                <input type="password" name="password" class="w-full border border-gray-300 rounded p-2" required>
            </div>
             <div>
                <label class="block mb-1 font-medium">Confirm password </label>
                <input type="confirm_password" name="confirm_password" class="w-full border border-gray-300 rounded p-2" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">setup </button>
        </form>
    </div>
</body>
</html>
