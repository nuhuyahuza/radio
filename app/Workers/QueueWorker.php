<?php

namespace App\Workers;

use App\Utils\Email\EmailService;
use Predis\Client;

class QueueWorker
{
    public function run(): void
    {
        $redisHost = $_ENV['REDIS_HOST'] ?? 'redis';
        $client = new Client(['host' => $redisHost, 'port' => 6379]);
        $email = new EmailService();

        while (true) {
            try {
                $item = $client->blpop(['zaa_radio_queue'], 10);
                if (!$item) {
                    continue; // timeout, loop again
                }
                $payload = json_decode($item[1], true);
                if (!is_array($payload) || empty($payload['type'])) {
                    continue;
                }

                switch ($payload['type']) {
                    case 'email.booking_confirmation':
                        $data = $payload['data'];
                        $email->sendBookingConfirmation($data['advertiser_email'], $data['advertiser_name'], $data);
                        break;
                    case 'email.booking_approval':
                        $data = $payload['data'];
                        $email->sendBookingApproval($data['advertiser_email'], $data['advertiser_name'], $data);
                        break;
                    case 'email.booking_rejection':
                        $data = $payload['data'];
                        $email->sendBookingRejection($data['advertiser_email'], $data['advertiser_name'], $data, $data['reason'] ?? null);
                        break;
                    case 'email.account_creation':
                        $d = $payload['data'];
                        $email->sendAccountCreation($d['email'], $d['name'], $d['temporary_password']);
                        break;
                    case 'email.slot_reminder':
                        $data = $payload['data'];
                        $email->sendSlotReminder($data['advertiser_email'], $data['advertiser_name'], $data);
                        break;
                    case 'email.payment_reminder':
                        $data = $payload['data'];
                        $email->sendPaymentReminder($data['advertiser_email'], $data['advertiser_name'], $data);
                        break;
                    default:
                        // Unknown job type; ignore
                        break;
                }
            } catch (\Throwable $e) {
                error_log('Queue worker error: ' . $e->getMessage());
                sleep(1);
            }
        }
    }
}

// Bootstrap when executed directly
if (php_sapi_name() === 'cli') {
    require_once __DIR__ . '/../../vendor/autoload.php';
    (new QueueWorker())->run();
}
