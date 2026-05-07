<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use App\Models\ScreeningRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentSlipController extends Controller
{
    public function store(Request $request, int $id): RedirectResponse
    {
        $screeningRequest = $this->resolveCashRequest($id);

        $validated = $request->validate([
            'payment_slip' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if ($screeningRequest->payment_slip_path) {
            Storage::disk('local')->delete($screeningRequest->payment_slip_path);
        }

        $path = $validated['payment_slip']->store(
            "payment-slips/{$screeningRequest->customer_id}",
            'local',
        );

        $screeningRequest->update([
            'payment_slip_path' => $path,
            'payment_slip_uploaded_at' => now(),
        ]);

        return redirect()
            ->route('client.requests.details', $screeningRequest->id)
            ->with('status', 'Payment slip uploaded. Our finance team will verify it shortly.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $screeningRequest = $this->resolveCashRequest($id);

        if ($screeningRequest->payment_slip_path) {
            Storage::disk('local')->delete($screeningRequest->payment_slip_path);
        }

        $screeningRequest->update([
            'payment_slip_path' => null,
            'payment_slip_uploaded_at' => null,
        ]);

        return redirect()
            ->route('client.requests.details', $screeningRequest->id)
            ->with('status', 'Payment slip removed. You can upload a new one.');
    }

    public function download(int $id): StreamedResponse
    {
        $screeningRequest = $this->resolveCashRequest($id);

        abort_unless($screeningRequest->hasPaymentSlip(), 404);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($screeningRequest->payment_slip_path), 404);

        $extension = pathinfo($screeningRequest->payment_slip_path, PATHINFO_EXTENSION);
        $filename = "payment-slip-{$screeningRequest->reference}.{$extension}";

        return $disk->download($screeningRequest->payment_slip_path, $filename);
    }

    private function resolveCashRequest(int $id): ScreeningRequest
    {
        $customerId = session('client_customer_id', 1);

        $screeningRequest = ScreeningRequest::with('customer.agreement')
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        abort_unless($screeningRequest->customer?->isCashBilled(), 403, 'Payment slips are only accepted for cash-billed accounts.');

        return $screeningRequest;
    }
}
