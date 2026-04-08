<?php

namespace App\Services;

use App\Models\User;
use App\Models\WhatsAppNotification;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class WhatsAppNotifier
{
    public function sendWhatsApp(?string $phone, string $message, ?User $user = null): bool
    {
        if (! $phone) {
            Log::warning('WhatsApp notification skipped: missing phone number', ['message' => $message]);
            $this->logNotification($user, null, $message, 'skipped', 'missing phone number');
            return false;
        }

        $recipient = $this->formatWhatsAppNumber($phone);

        if (! $recipient) {
            Log::warning('WhatsApp notification skipped: invalid phone number', ['phone' => $phone, 'message' => $message]);
            $this->logNotification($user, $phone, $message, 'skipped', 'invalid phone number');
            return false;
        }

        if (! config('whatsapp.enabled')) {
            Log::warning('WhatsApp notification skipped: disabled in configuration', ['to' => $recipient, 'message' => $message]);
            $this->logNotification($user, $phone, $message, 'skipped', 'disabled');
            return false;
        }

        if (! config('whatsapp.twilio_sid') || ! config('whatsapp.twilio_token') || ! config('whatsapp.twilio_whatsapp_from')) {
            Log::warning('WhatsApp notification skipped: missing Twilio configuration', ['to' => $recipient, 'message' => $message]);
            $this->logNotification($user, $phone, $message, 'skipped', 'missing twilio configuration');
            return false;
        }

        try {
            $client = new Client(config('whatsapp.twilio_sid'), config('whatsapp.twilio_token'));
            $client->messages->create(
                $recipient,
                [
                    'from' => config('whatsapp.twilio_whatsapp_from'),
                    'body' => $message,
                ]
            );

            Log::info('WhatsApp notification sent', ['to' => $recipient, 'message' => $message]);
            $this->logNotification($user, $phone, $message, 'sent');

            return true;
        } catch (\Throwable $e) {
            Log::warning('WhatsApp notify failed', ['error' => $e->getMessage(), 'to' => $recipient, 'message' => $message]);
            $this->logNotification($user, $phone, $message, 'failed', $e->getMessage());
            return false;
        }
    }

    public function sendUser(User $user, string $message): bool
    {
        return $this->sendWhatsApp($user->whatsapp_number, $message, $user);
    }

    protected function logNotification(?User $user, ?string $phone, string $message, string $status, ?string $failureReason = null): void
    {
        WhatsAppNotification::create([
            'user_id' => $user?->id,
            'whatsapp_number' => $phone,
            'message' => $message,
            'status' => $status,
            'failure_reason' => $failureReason,
            'sent_at' => $status === 'sent' ? now() : null,
        ]);
    }

    protected function formatWhatsAppNumber(string $phone): ?string
    {
        $clean = preg_replace('/[^+\d]/', '', trim($phone));

        if ($clean === '') {
            return null;
        }

        if (str_starts_with($clean, 'whatsapp:')) {
            return $clean;
        }

        return 'whatsapp:' . $clean;
    }
}
