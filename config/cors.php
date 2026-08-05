<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],

    /*
     | Este mismo código se despliega en Alba y en OSV. Si la lista queda hardcodeada con los
     | dominios de un solo entorno, al copiar el código de uno al otro se pisa la config y el
     | front del otro entorno queda bloqueado por CORS (el navegador devuelve status 0 y la app
     | lo muestra como "sistema en mantenimiento").
     |
     | Por eso: el default incluye los dominios de AMBOS entornos, y además se puede sobrescribir
     | por entorno con CORS_ALLOWED_ORIGINS en el .env (separado por comas), que no viaja en los
     | deploys. Ejemplo:
     |   CORS_ALLOWED_ORIGINS=https://osvsalud.oridheansoft.com,http://localhost:4200
     */
    'allowed_origins' => array_values(array_filter(array_map(
        'trim',
        explode(',', env('CORS_ALLOWED_ORIGINS', implode(',', [
            'http://localhost:4200',
            'https://sistemasalud.oridheansoft.com',  // front Alba
            'https://osvsalud.oridheansoft.com',      // front OSV
            'https://fatfa.site',
        ])))
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],

    'exposed_headers' => ['Content-Disposition'],

    'max_age' => 3600,

    'supports_credentials' => false
];
