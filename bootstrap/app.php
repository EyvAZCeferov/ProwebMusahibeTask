<?php

use App\Http\Middleware\AuditLogMiddleware;
use App\Http\Middleware\SetApiLocale;
use App\Services\TranslationService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(append: [
            SetApiLocale::class,
            AuditLogMiddleware::class,
        ]);
        $middleware->alias([
            'localize'                => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect'    => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect'   => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect'    => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath'          => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,

            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->report(function (Throwable $e) {
            try {
                $request = request();
                $currentUrl = $request->fullUrl();
                $userId = $request->user()?->id ?? 'Qonaq';
                Log::channel('mail_errors')->error(
                    'Sistem Xətası Baş Verdi: ' . $e->getMessage(),
                    [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => substr($e->getTraceAsString(), 0, 500),
                        'url' => $currentUrl,
                        'user_id' => $userId,
                    ]
                );
            } catch (Throwable $logException) {
                Log::channel('single')->error(
                    'Xəta loglama kanalında problem yarandı: ' . $logException->getMessage()
                );
            }
        });


        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                $translator = app(TranslationService::class);
                $message = $translator->get('Daxili server xətası baş verdi.');
                $statusCode = 500;

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'message' => $translator->get('Verilən məlumatlar yanlışdır.'),
                        'errors' => $e->errors(),
                    ], 422);
                }

                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json(['message' => $translator->get('Autentifikasiya tələb olunur.')], 401);
                }

                if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return response()->json(['message' => $translator->get('Bu əməliyyatı etmək üçün icazəniz yoxdur.')], 403);
                }

                if ($e instanceof ThrottleRequestsException) {
                    return response()->json(['message' => $translator->get('Api sorğusu həddən artıqdır')], 503);
                }

                if ($e instanceof NotFoundHttpException) {
                    return response()->json(['message' => $translator->get('Axtarılan mənbə tapılmadı.')], 404);
                }

                if (config('app.debug')) {
                    return response()->json([
                        'message' => $e->getMessage(),
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => substr($e->getTraceAsString(), 0, 500),
                    ], $statusCode);
                }

                return response()->json(['message' => $message], $statusCode);
            }
        });
    })->create();
