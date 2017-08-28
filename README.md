```
composer create-project laravel/laravel admin
```

```
cd admin
composer require shopex/luban-admin
```

config/app.config 下增加

```        
//Providers...
        App\Providers\DesktopServiceProvider::class,
        Collective\Html\HtmlServiceProvider::class,
        Shopex\LubanAdmin\Providers\LubanAdminProvider::class,


//Facade...
        'Form' => Collective\Html\FormFacade::class,
        'Html' => Collective\Html\HtmlFacade::class,      
        'Admin' => Shopex\LubanAdmin\Facades\Admin::class,        
```

resources/assets/js/app.js 下增加
```
    require('../vendor/admin/js/ui.js')
```

resources/assets/sass/app.scss 下增加
```
    @import "../vendor/admin/sass/app";
```

php artisan vendor:publish
php artisan make:auth

npm run dev

php artisan migrate
php artisan serve