<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/favicon.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>App Carga Horaria</title>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/js/app.jsx', 'resources/css/app.css'])
    @else
        <script type="importmap">
        {
            "imports": {
                "react": "https://esm.sh/react@18",
                "react-dom": "https://esm.sh/react-dom@18"
            }
        }
        </script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@3/tailwind.min.css">
    @endif
</head>
<body>
    <div id="root"></div>
    @if (!file_exists(public_path('build/manifest.json')))
        <script type="module">
            import React from 'react';
            import ReactDOM from 'react-dom';
            ReactDOM.createRoot(document.getElementById('root')).render(
                React.createElement('div', null, 'Compila el frontend con: npm run build en la carpeta frontend/')
            );
        </script>
    @endif
</body>
</html>
