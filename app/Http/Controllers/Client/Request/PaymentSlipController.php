<?php

namespace App\Http\Controllers\Client\Request;

use App\Http\Controllers\Controller;
use App\Models\ScreeningRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        $originalFilename = $validated['payment_slip']->getClientOriginalName();
        $isReplacement = ! empty($screeningRequest->payment_slip_path);

        if ($isReplacement) {
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

        $this->notifyFinance($screeningRequest, $originalFilename, $isReplacement);

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

    private function notifyFinance(ScreeningRequest $screeningRequest, string $filename, bool $isReplacement): void
    {
        $recipient = config('billing.proof_of_payment_email');

        if (empty($recipient)) {
            Log::warning('payment_slip.finance_recipient_missing', [
                'request_id' => $screeningRequest->id,
            ]);

            return;
        }

        $user = Auth::guard('customer_user')->user();
        $reference = $screeningRequest->reference;
        $customerName = $screeningRequest->customer?->name ?? 'Unknown customer';
        $uploaderName = $user?->name ?? 'Unknown user';
        $uploaderEmail = $user?->email;
        $action = $isReplacement ? 'replaced' : 'uploaded';

        $body = "A payment slip has been {$action} and is awaiting verification.\n\n"
            ."Request: {$reference}\n"
            ."Customer: {$customerName}\n"
            ."Uploaded by: {$uploaderName}".($uploaderEmail ? " ({$uploaderEmail})" : '')."\n"
            .'Uploaded at: '.now()->format('d M Y, H:i')."\n"
            ."Filename: {$filename}\n\n"
            .'Log into the admin portal to view the slip and verify payment.';

        $subjectVerb = $isReplacement ? 'replaced' : 'uploaded';

        try {
            Mail::raw($body, function ($message) use ($recipient, $reference, $subjectVerb, $uploaderEmail) {
                $message->to($recipient)
                    ->subject("Payment slip {$subjectVerb} — {$reference}");

                if ($uploaderEmail) {
                    $message->replyTo($uploaderEmail);
                }
            });

            Log::info('payment_slip.finance_notified', [
                'request_id' => $screeningRequest->id,
                'reference' => $reference,
                'to' => $recipient,
                'replacement' => $isReplacement,
            ]);
        } catch (\Throwable $e) {
            Log::error('payment_slip.finance_notification_failed', [
                'request_id' => $screeningRequest->id,
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);
        }
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
