<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
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
     */
    public function verify(string $secret, string $code): bool
    {
        return $this->engine->verifyKey($secret, $code, 1);
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
