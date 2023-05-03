<?php

namespace App\Command;

use App\Service\ApiService;
use App\Service\TelegramService;
use App\Type\AppointmentResponseType;
use DateInterval;
use DatePeriod;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'check:kosmos')]
class CheckDateCommand extends Command
{
    private ApiService $apiService;
    private TelegramService $telegramService;

    public function __construct(
        ApiService $apiService,
        TelegramService $telegramService
    )
    {
        parent::__construct();
        $this->apiService = $apiService;
        $this->telegramService = $telegramService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $response = $this->apiService->getFirstAppointmentTime(new DateTime());
        if ($response instanceof AppointmentResponseType) {
            $this->telegramService->sendAppointmentInformation($response);
        }

        return Command::SUCCESS;
    }
}