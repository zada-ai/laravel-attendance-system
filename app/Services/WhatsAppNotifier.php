<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class WhatsAppNotifier
{
    public function sendWhatsApp(?string $phone, string $message): bool
    {
        if (! $phone) {
            Log::warning('WhatsApp notification skipped: missing phone number', ['message' => $message]);
            return false;
        }

        $recipient = $this->formatWhatsAppNumber($phone);

        if (! $recipient) {
            Log::warning('WhatsApp notification skipped: invalid phone number', ['phone' => $phone, 'message' => $message]);
            return false;
        }

        if (! config('whatsapp.enabled')) {
            Log::warning('WhatsApp notification skipped: disabled in configuration', ['to' => $recipient, 'message' => $message]);
            return false;
        }

        if (! config('whatsapp.twilio_sid') || ! config('whatsapp.twilio_token') || ! config('whatsapp.twilio_whatsapp_from')) {
            Log::warning('WhatsApp notification skipped: missing Twilio configuration', ['to' => $recipient, 'message' => $message]);
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

            return true;
        } catch (\Throwable $e) {
            Log::warning('WhatsApp notify failed', ['error' => $e->getMessage(), 'to' => $recipient, 'message' => $message]);
            return false;
        }
    }

    public function sendUser(User $user, string $message): bool
    {
        return $this->sendWhatsApp($user->whatsapp_number, $message);
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
