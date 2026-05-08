<?php
$pageTitle = $pageTitle ?? APP_NAME;
?>
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
                        gray: {
                            50: '#F9F8F6', 100: '#F1EFE8', 200: '#D3D1C7',
                            400: '#888780', 600: '#5F5E5A', 900: '#1A1A1A'
                        },
                        success: { 50: '#EAF3DE', 600: '#3B6D11' },
                        danger: { 50: '#FCEBEB', 600: '#A32D2D' },
                        info: { 50: '#E6F1FB', 600: '#185FA5' }
                    },
                    fontFamily: { sans: ['Sarabun', 'Noto Sans Thai', 'sans-serif'] }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-50 font-sans text-base leading-relaxed text-gray-900">

