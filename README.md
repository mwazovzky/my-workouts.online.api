# Laravel Vue SAP Authentication with Sanctum

## Install API

```
php artisan install:api
```

## CORS

```
php artisan config:publish cors
```

```
// ./config/cors.php
// ...
'supports_credentials' => true,
// ...
```

```
//.env
SESSION_DOMAIN=localhost
```
