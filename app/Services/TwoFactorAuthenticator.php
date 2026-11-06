<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Cache;
use PragmaRX\Google2FA\Google2FA;

/**
 * Thin wrapper around pragmarx/google2fa for the authenticator-app (TOTP)
 * factor: generating secrets, rendering the enrollment QR, and verifying codes.
 */
class TwoFactorAuthenticator
{
    public function __construct(private Google2FA $engine = new Google2FA) {}

    /**
     * A fresh base32 TOTP secret.
     */
    public function generateSecret(): string
    {
        return $this->engine->generateSecretKey();
    }

    /**
     * Verify a 6-digit code against the secret, allowing a ±1 time-step window
     * to tolerate clock drift between the server and the user's device.
     *
     * With a $replayKey (the user id) a code is accepted only if its time-step
     * is newer than the last one accepted for that key, so an observed code
     * cannot be replayed within its window. The last step is kept in the cache
     * (no column needed) for longer than the window lasts.
     */
    public function verify(string $secret, string $code, int|string|null $replayKey = null): bool
    {
        if ($replayKey === null) {
            return $this->engine->verifyKey($secret, $code, 1);
        }

        $cacheKey = 'totp-last-timestep:'.$replayKey;

        $timestep = $this->engine->verifyKeyNewer($secret, $code, (int) Cache::get($cacheKey, 0), 1);

        if ($timestep === false) {
            return false;
        }

        Cache::put($cacheKey, $timestep, now()->addMinutes(5));

        return true;
    }

    /**
     * The otpauth:// provisioning URI encoded by the enrollment QR code.
     */
    public function provisioningUri(string $account, string $secret): string
    {
        return $this->engine->getQRCodeUrl(
            config('app.name'),
            $account,
            $secret,
        );
    }

    /**
     * Render the provisioning URI as an inline SVG QR code. Inline SVG avoids
     * loading an external image and stays within the portal's strict CSP.
     */
    public function qrCodeSvg(string $account, string $secret, int $size = 200): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, 1),
            new SvgImageBackEnd,
        );

        return (new Writer($renderer))->writeString(
            $this->provisioningUri($account, $secret),
        );
    }
}
