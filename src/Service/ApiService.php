<?php

namespace App\Service;

use App\Type\AppointmentResponseType;
use DateInterval;
use DatePeriod;
use DateTime;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiService
{
    private HttpClientInterface $client;

    /**
     * @var string
     */
    private string $api;

    public function __construct(
        ParameterBagInterface $bag,
        HttpClientInterface $client
    )
    {
        $this->client = $client;
        $this->api = $bag->get('kosmos_api_url');
    }

    /**
     * @param string $endpoint
     * @param array $query
     * @return string
     */
    private function uri(string $endpoint, array $query = []): string
    {
        $params = http_build_query($query);
        return "{$this->api}/{$endpoint}?{$params}";
    }

    /**
     * @param DateTime $date
     * @param int $type
     * @param int $dealer
     * @return AppointmentResponseType
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getAppointmentTimesByDate(DateTime $date, int $type = 16, int $dealer = 1): AppointmentResponseType
    {
        $request = $this->client->request(
            'GET',
            $this->uri('AppointmentTime_Days/Get',
                [
                    'Date' => $date->format('Y-m-d'),
                    'TypeId' => $type,
                    'DealerId' => $dealer
                ]
            )
        );

        return new AppointmentResponseType($request->toArray(), $date);
    }

    /**
     * @param DateTime $startDate
     * @param string $modifier
     * @return AppointmentResponseType|null
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getFirstAppointmentTime(DateTime $startDate, string $modifier = '+2 month'): ?AppointmentResponseType
    {
        $endDate = clone $startDate;
        $endDate->modify($modifier);

        $period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate);

        /** @var DateTime $date */
        foreach ($period as $date) {
            $response = $this->getAppointmentTimesByDate($date);
            if ($response->count) {
                return $response;
            }
        }
        return null;
    }
}