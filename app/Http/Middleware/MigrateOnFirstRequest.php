<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * On hosts without a deploy hook (Vercel's PHP functions) the first request of
 * each fresh instance brings the database schema up to date. A marker file in
 * the temp directory, keyed by the set of migration files, keeps every later
 * request on that instance from touching the database for this check.
 */
class MigrateOnFirstRequest
{
    public function __construct(private Migrator $migrator) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('store.auto_migrate')) {
            $this->migrateIfNeeded();
        }

        return $next($request);
    }

    /**
     * Run pending migrations, at most once per set of migration files and instance.
     * Returns whether anything was migrated.
     */
    public function migrateIfNeeded(): bool
    {
        $files = $this->migrationNames();
        $marker = $this->markerPath();

        if (is_file($marker)) {
            return false;
        }

        try {
            $ran = $this->migrator->repositoryExists() ? $this->migrator->getRepository()->getRan() : [];
            $pending = array_diff($files, $ran);

            if ($pending !== []) {
                // --isolated takes a cache lock, so instances starting together migrate only once.
                Artisan::call('migrate', ['--force' => true, '--isolated' => true]);
            }

            touch($marker);

            return $pending !== [];
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    /**
     * The file that records "this instance already checked this set of migrations".
     */
    public function markerPath(): string
    {
        return sys_get_temp_dir().DIRECTORY_SEPARATOR.'anatolian-schema-'.md5(implode('|', $this->migrationNames()));
    }

    /**
     * @return array<int, string>
     */
    private function migrationNames(): array
    {
        return array_keys($this->migrator->getMigrationFiles(database_path('migrations')));
    }
}
