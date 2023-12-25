<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

use Illuminate\Config\Repository;
use LaravelVault\LoadEnvironmentVariablesVault;

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);
$app->afterLoadingEnvironment(function () use ($app) {
  // checking is the feature enabled
  if (env('VAULT_LOAD_ENV', false)) {
    try {
      $tenantId = env('TENANT_ID');

      // resolving tenant_id by headers - make sure proxy override this header for security reason
      if (!$tenantId) {
        $headers = collect(getallheaders());
        $tenantIdHeader = env('TENANT_ID_HEADER', 'tenant-id');
        $tenantId = $headers
          ->first(fn($value, $key) => $key === $tenantIdHeader
            || strtolower($key) === $tenantIdHeader);
      }

      if (!$tenantId) {
        throw new Exception('Missed Tenant_id ');
      }

      $envRepository = Env::getRepository();
      $vaultDefaultPrefix = $envRepository->get('VAULT_KEY_PREFIX');
      $envRepository->set('VAULT_KEY_PREFIX', $vaultDefaultPrefix.'/'.$tenantId);

      (new LoadEnvironmentVariablesVault)->bootstrap($app);
    } catch (Throwable $e) {
      // preparing the logs for exception
      $app->instance('config', $config = new Repository([]));

      throw $e;
    }
  }
});

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
