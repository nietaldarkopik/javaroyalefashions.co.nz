<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class OrderService
{
    /**
     * Store an uploaded payment proof and move the order out of
     * "pending_payment" into the admin's review queue. Re-uploads are
     * allowed (wrong file, blurry scan) — every attempt is kept for the
     * admin verifying payment, the newest one is what they act on.
     */
    public function attachPaymentProof(Order $order, UploadedFile $file): PaymentProof
    {
        $path = $file->store('payment-proofs', 'public');

        $proof = $order->paymentProofs()->create([
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        if ($order->status === OrderStatus::PendingPayment) {
            $order->update(['status' => OrderStatus::WaitingVerification]);
        }

        return $proof;
    }

    /**
     * Admin confirms the bank transfer actually landed: marks the latest
     * proof verified and moves the order to Paid.
     */
    public function verifyPayment(Order $order, User $admin): Order
    {
        $proof = $order->latestPaymentProof();

        if ($proof) {
            $proof->update([
                'is_verified' => true,
                'verified_at' => now(),
                'verified_by' => $admin->id,
            ]);
        }

        $order->update([
            'status' => OrderStatus::Paid,
            'verified_at' => now(),
            'verified_by' => $admin->id,
        ]);

        return $order->fresh();
    }

    /**
     * Move an order to a new status, refusing transitions that don't make
     * sense (e.g. Completed straight back to Pending Payment) so the order
     * timeline stays trustworthy.
     */
    public function updateStatus(Order $order, OrderStatus $next, ?string $adminNotes = null): Order
    {
        if ($order->status !== $next && ! $order->status->canTransitionTo($next)) {
            throw new InvalidArgumentException(
                "Cannot move an order from {$order->status->label()} to {$next->label()}."
            );
        }

        $order->update(array_filter([
            'status' => $next,
            'admin_notes' => $adminNotes,
        ], fn ($value) => $value !== null));

        return $order->fresh();
    }
}
