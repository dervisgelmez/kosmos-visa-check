<?php

namespace App\Type;

use DateTime;

class AppointmentResponseType
{
    public int $count = 0;

    public DateTime $dateTime;

    public array $hours = [];

    public function __construct(array $response, DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
        foreach ($response as $data) {
            if ($timeResponse = $data['appointmentHour']) {
                $this->hours[] = $timeResponse['name'];
                $this->count++;
            }
        }
    }
}