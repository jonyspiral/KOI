protected $middlewareGroups = [
    'web' => [
        // ...
        \App\Http\Middleware\KoiSessionMiddleware::class,
    ],
];
protected $routeMiddleware = [
    // ...
    'app' => \App\Http\Middleware\AplicacionMiddleware::class,
];
