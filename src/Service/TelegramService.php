<?php

namespace App\Service;

use App\Type\AppointmentResponseType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TelegramService
{
    private HttpClientInterface $client;

    private string $apiKey;

    private string $chatId;

    public function __construct(
        HttpClientInterface $client,
        ParameterBagInterface $bag
    )
    {
        $this->client = $client;

        $this->apiKey = $bag->get('telegram_api_key');
        $this->chatId = $bag->get('telegram_chat_id');
    }

    private function sendMessage(string $message)
    {
        $message = urlencode($message);
        $url = "https://api.telegram.org/bot{$this->apiKey}/sendMessage?chat_id={$this->chatId}&parse_mode=HTML&text={$message}";
        $this->client->request('GET', $url);
    }

    public function sendAppointmentInformation(AppointmentResponseType $response)
    {
        $message = "🇬🇷 En yakın vize tarihi";
        $message .= "\n\n";
        $message .= "🗓️ <b>{$response->dateTime->format('Y-m-d')}</b> \n\n";

        foreach ($response->hours as $hour) {
            $message .= "🕐 {$hour} \n";
        }

        $this->sendMessage($message);
    }
}