<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use App\Models\ReportVersion;
use App\Models\ScreeningRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportDownloadController extends Controller
{
    public function download(ScreeningRequest $request, ReportVersion $version): BinaryFileResponse|StreamedResponse
    {
        $customerId = (int) Auth::guard('customer_user')->user()?->customer_id;

        if ($request->customer_id !== $customerId) {
            abort(404);
        }

        if ($version->screening_request_id !== $request->id) {
            abort(404);
        }

        $disk = Storage::disk('local');
        $path = $version->file_path;

        if (! $disk->exists($path)) {
            abort(404, 'Report file is no longer available. Please contact support.');
        }

        $filename = sprintf(
            '%s-%s-v%d.pdf',
            $request->reference,
            $version->type,
            $version->version,
        );

        activity('access')
            ->causedBy(Auth::guard('customer_user')->user())
            ->performedOn($version)
            ->withProperties(['ip' => request()->ip()])
            ->event('report.downloaded')
            ->log("Downloaded {$version->type} report v{$version->version} for {$request->reference}");

        return $disk->download($path, $filename);
    }
}
