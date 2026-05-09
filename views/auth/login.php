<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#FAEEDA', 100: '#FFF3E8', 600: '#E87722', 700: '#C4601A' },
                        gray: { 50: '#F9F8F6', 100: '#F1EFE8', 200: '#D3D1C7', 600: '#5F5E5A', 900: '#1A1A1A' }
                    },
                    fontFamily: { sans: ['Sarabun', 'Noto Sans Thai', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 font-sans">
    <main class="min-h-screen grid place-items-center px-4">
        <section class="w-full max-w-md bg-white border border-gray-200 rounded-lg shadow-sm p-8">
            <div class="mb-6">
                <p class="text-sm font-medium text-primary-600">ระบบสอบออนไลน์</p>
                <h1 class="text-2xl font-semibold mt-1">เข้าสู่ระบบผู้ดูแล</h1>
            </div>

            <?php if ($error): ?>
                <div class="mb-5 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-5" autocomplete="off">
                <?= csrfField() ?>
                <div>
                    <label for="username" class="block text-sm font-medium mb-2">ชื่อผู้ใช้</label>
                    <input id="username" name="username" type="text" required
                           class="w-full rounded border border-gray-200 px-3 py-2 focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-50">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium mb-2">รหัสผ่าน</label>
                    <input id="password" name="password" type="password" required
                           class="w-full rounded border border-gray-200 px-3 py-2 focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-50">
                </div>
                <button type="submit"
                        class="w-full rounded bg-primary-600 px-4 py-2.5 font-medium text-white transition-colors hover:bg-primary-700">
                    เข้าสู่ระบบ
                </button>
            </form>
        </section>

        <!-- Developer Credits -->
        <div class="mt-8 text-center text-xs text-gray-400">
            <p class="font-semibold text-gray-500 mb-1">พัฒนาระบบโดย นายธนากร อินทพันธ์</p>
            <p>ตำแหน่งบุคลากร สังกัดงานบริหารทรัพยากรบุคคลและนิติการ</p>
            <p>มหาวิทยาลัยราชภัฏร้อยเอ็ด</p>
        </div>
    </main>
</body>
</html>
