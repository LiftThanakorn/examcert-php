<?php
$pageTitle = $pageTitle ?? APP_NAME;
$bodyClass = $bodyClass ?? 'bg-[#F9F8F6] font-sans text-gray-900';
$bodyAttrs = $bodyAttrs ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> - <?= e(APP_NAME) ?></title>
  <meta name="base-url" content="<?= e(BASE_URL) ?>">
  <meta name="csrf-token" content="<?= e(generateCsrfToken()) ?>">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50: '#FFF3E8',
              100: '#FAEEDA',
              200: '#FAC775',
              300: '#EF9F27',
              400: '#E87722',
              500: '#C4601A',
              600: '#9E4A12',
              700: '#7A360C',
              800: '#633806',
              900: '#412402'
            },
            sidebar: '#1A1A1A'
          },
          fontFamily: {
            sans: ['Sarabun', 'Noto Sans Thai', 'Outfit', 'sans-serif'],
            outfit: ['Outfit', 'sans-serif'],
            sarabun: ['Sarabun', 'sans-serif']
          },
          fontSize: {
            xxs: '0.65rem'
          },
          boxShadow: {
            card: '0 1px 4px rgba(0,0,0,0.07)',
            'card-hover': '0 4px 16px rgba(0,0,0,0.10)',
            orange: '0 0 0 3px rgba(232,119,34,0.18)',
            'soft': '0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05)',
            'premium': '0 20px 50px -12px rgba(0, 0, 0, 0.1)'
          }
        }
      }
    };
    window.CSRF_TOKEN = '<?= generateCsrfToken() ?>';
    window.BASE_URL = '<?= BASE_URL ?>';
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <style>
    @keyframes fadeUp { 
      from { opacity: 0; transform: translateY(10px); } 
      to { opacity: 1; transform: translateY(0); } 
    }
    .fade-up { animation: fadeUp 0.4s ease-out both; }
    .fade-up-1 { animation-delay: 0.05s; }
    .fade-up-2 { animation-delay: 0.10s; }
    .fade-up-3 { animation-delay: 0.15s; }
    .fade-up-4 { animation-delay: 0.20s; }
    .fade-up-5 { animation-delay: 0.25s; }
    .bg-mesh {
        background-color: #ffffff;
        background-image: 
            radial-gradient(at 0% 0%, hsla(25,100%,93%,1) 0, transparent 50%), 
            radial-gradient(at 100% 0%, hsla(25,100%,93%,1) 0, transparent 50%);
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/app.css?v=<?= time() ?>">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="<?= e($bodyClass) ?>" <?= $bodyAttrs ?>>
