<!-- Sidebar — fixed left, dark bg -->
<aside class="fixed top-0 left-0 h-screen w-56 bg-sidebar flex flex-col z-40 overflow-hidden">

  <!-- Logo -->
  <div class="px-4 py-5 border-b border-white/10 flex-shrink-0">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 bg-primary-400 rounded-xl flex items-center justify-center text-white text-lg">
        <i class="fas fa-award"></i>
      </div>
      <div>
        <p class="text-white font-semibold text-[13px] leading-tight">ระบบสอบออนไลน์</p>
        <p class="text-white/40 text-[10px]">ม.ราชภัฏร้อยเอ็ด</p>
      </div>
    </div>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 overflow-y-auto sidebar-nav py-3 px-2">

    <p class="text-white/30 text-xxs uppercase tracking-widest px-2 py-2 mt-1">หลัก</p>

    <?php
    $navItems = [
      ['icon'=>'fa-gauge',         'label'=>'แผงควบคุม',     'route'=>'admin/dashboard.php'],
      ['icon'=>'fa-folder-open',   'label'=>'โครงการสอบ',   'route'=>'admin/projects/'],
      ['icon'=>'fa-users',         'label'=>'ผู้เข้าสอบ',   'route'=>'admin/participants/'],
    ];
    $examItems = [
      ['icon'=>'fa-file-lines',    'label'=>'คลังข้อสอบ',    'route'=>'admin/questions/'],
      ['icon'=>'fa-chart-bar',     'label'=>'ผลการสอบ',      'route'=>'admin/exam-sessions/'],
    ];
    $certItems = [
      ['icon'=>'fa-certificate',   'label'=>'เกียรติบัตร',   'route'=>'admin/certificates/'],
    ];

    if (!function_exists('navItem')) {
        function navItem($item, $current) {
          $active = strpos($_SERVER['REQUEST_URI'] ?? '', $item['route']) !== false;
          $base   = 'flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm mb-0.5 transition-colors duration-150 group';
          $cls    = $active
            ? "$base bg-primary-400 text-white font-medium"
            : "$base text-white/50 hover:text-white hover:bg-white/8";
          echo "<a href='".BASE_URL."/{$item['route']}' class='$cls'>";
          echo "  <i class='fas {$item['icon']} w-4 text-center text-base'></i>";
          echo "  <span>{$item['label']}</span>";
          echo "</a>";
        }
    }

    $current = $_SERVER['REQUEST_URI'] ?? '';
    foreach ($navItems as $item) navItem($item, $current);

    echo "<p class='text-white/30 text-xxs uppercase tracking-widest px-2 py-2 mt-3'>การสอบ</p>";
    foreach ($examItems as $item) navItem($item, $current);

    echo "<p class='text-white/30 text-xxs uppercase tracking-widest px-2 py-2 mt-3'>ใบเกียรติบัตร</p>";
    foreach ($certItems as $item) navItem($item, $current);
    ?>

    <p class="text-white/30 text-xxs uppercase tracking-widest px-2 py-2 mt-3">รายงาน</p>
    <a href="<?= BASE_URL ?>/admin/reports/" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-white/50 hover:text-white hover:bg-white/8 transition-colors">
      <i class="fas fa-chart-pie w-4 text-center"></i>
      <span>สรุปภาพรวม</span>
    </a>

  </nav>

  <!-- Admin Profile -->
  <div class="px-3 py-3 border-t border-white/10 flex-shrink-0">
    <div class="flex items-center gap-2.5">
      <div class="w-8 h-8 rounded-full bg-primary-400 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
        <?php
          $name = (string) ($_SESSION['admin_name'] ?? 'AD');
          $chars = preg_match_all('/./us', $name, $matches) !== false ? $matches[0] : str_split($name);
          echo e(strtoupper(implode('', array_slice($chars, 0, 2))));
        ?>
      </div>
      <div class="min-w-0 flex-1">
        <p class="text-white text-xs font-medium truncate"><?= e($_SESSION['admin_name'] ?? 'Admin') ?></p>
        <p class="text-white/40 text-xxs"><?= e($_SESSION['admin_role'] ?? 'admin') ?></p>
      </div>
      <a href="<?= BASE_URL ?>/admin/logout.php" class="text-white/30 hover:text-red-400 transition-colors text-sm">
        <i class="fas fa-arrow-right-from-bracket"></i>
      </a>
    </div>
  </div>

</aside>
