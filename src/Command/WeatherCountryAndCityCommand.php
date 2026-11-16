<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:countryAndCity',
    description: 'Add a short description for your command',
)]
class WeatherCountryAndCityCommand{
    private $locationRepository;
    private $weatherUtil;

    public function __construct(LocationRepository $locationRepository, WeatherUtil $weatherUtil)
    {
        $this->locationRepository = $locationRepository;
        $this->weatherUtil = $weatherUtil;
    }
    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Country')] string $country,
        #[Argument('Name')] string $name,
    ): int {
        $foundLocation = $this->locationRepository->findOneBy([
            'country' => $country,
            'name' => $name,
        ]);
        $measurements = $this->weatherUtil->getWeatherForCountryAndCity($country, $name);
        $io->writeln(sprintf('Location: %s', $foundLocation->getName()));
        $io->table(['Date', 'Temperature', 'Cloudiness', 'Humidity', 'Precipitation'], array_map(fn($m) => [
            $m->getDate()->format('Y-m-d'),
            $m->getTemperature(),
            $m->getCloudiness(),
            $m->getHumidity(),
            $m->getPrecipitation(),
        ], $measurements));

        return Command::SUCCESS;
    }
}
