<?php

namespace App\Providers;

use App\Support\EncryptedFilesystemAdapter;
use Hashids\Hashids;
use Illuminate\Filesystem\LocalFilesystemAdapter;
use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Salt falls back to APP_KEY for existing URLs; set HASHIDS_SALT to
        // decouple URL ids from key rotation (see config/hashids.php).
        $this->app->singleton('hashids', fn () => new Hashids(
            config('hashids.salt') ?: config('app.key'),
            (int) config('hashids.min_length', 8),
        ));

        // `artisan serve` (the Sail container's web server) strips every
        // non-allow-listed env var from its PHP workers whenever a .env file
        // exists. compose.yaml sets ADMIN_STORAGE_PATH in `environment:`, so
        // without this the workers fell back to the sibling path and every
        // admin-side file download (report PDFs) 404'd.
        ServeCommand::$passthroughVariables[] = 'ADMIN_STORAGE_PATH';
    }

    public function boot(): void
    {
        $this->registerLocalMountDriver();
    }

    /**
     * A `local` driver that does NOT create its root directory on resolution.
     *
     * Flysystem's LocalFilesystemAdapter creates its root eagerly in the
     * constructor, and Laravel's built-in local driver gives no way to opt out.
     * For the `admin_local` mount that means merely calling Storage::disk()
     * throws UnableToCreateDirectory whenever the mount is absent — which is the
     * normal case in Docker, where the sibling-path default cannot resolve.
     *
     * With lazy root creation the disk resolves, `exists()` simply returns
     * false, and a missing mount degrades to the intended 404 on a report
     * download instead of a 500 — and nothing is created in the wrong place.
     *
     * Keep identical to the same method in nrh-admin.
     */
    private function registerLocalMountDriver(): void
    {
        Storage::extend('local_mount', function ($app, array $config) {
            $visibility = PortableVisibilityConverter::fromArray(
                $config['permissions'] ?? [],
                $config['directory_visibility'] ?? $config['visibility'] ?? Visibility::PRIVATE
            );

            $links = ($config['links'] ?? null) === 'skip'
                ? LocalAdapter::SKIP_LINKS
                : LocalAdapter::DISALLOW_LINKS;

            $adapter = new LocalAdapter(
                $config['root'],
                $visibility,
                $config['lock'] ?? LOCK_EX,
                $links,
                null,
                lazyRootCreation: true,
            );

            // 'encrypt' => true seals files at rest (see EncryptedFilesystemAdapter).
            if ($config['encrypt'] ?? false) {
                $adapter = new EncryptedFilesystemAdapter($adapter);
            }

            return new LocalFilesystemAdapter(
                new Flysystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }
}
