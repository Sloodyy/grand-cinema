<?php

namespace App\Command;

use App\Entity\Seat;
use App\Repository\SeatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-seats',
    description: 'Génère les 1700 sièges des 10 salles du cinéma.'
)]
class GenerateSeatsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SeatRepository $seatRepository
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        $rooms = [
            1 => ['rows' => 20, 'seats' => 15],
            2 => ['rows' => 20, 'seats' => 15],
            3 => ['rows' => 12, 'seats' => 15],
            4 => ['rows' => 12, 'seats' => 15],
            5 => ['rows' => 12, 'seats' => 15],
            6 => ['rows' => 12, 'seats' => 15],
            7 => ['rows' => 8, 'seats' => 15],
            8 => ['rows' => 8, 'seats' => 15],
            9 => ['rows' => 5, 'seats' => 14],
            10 => ['rows' => 5, 'seats' => 14],
        ];

        $existingSeats = [];

        foreach ($this->seatRepository->findAll() as $seat) {
            $key = $seat->getRoomNumber()
                . '-' . $seat->getRowLabel()
                . '-' . $seat->getSeatNumber();

            $existingSeats[$key] = true;
        }

        $created = 0;

        foreach ($rooms as $roomNumber => $configuration) {
            for ($row = 0; $row < $configuration['rows']; $row++) {
                $rowLabel = chr(65 + $row);

                for ($number = 1; $number <= $configuration['seats']; $number++) {
                    $key = $roomNumber . '-' . $rowLabel . '-' . $number;

                    if (isset($existingSeats[$key])) {
                        continue;
                    }

                    $seat = new Seat();
                    $seat->setRoomNumber($roomNumber);
                    $seat->setRowLabel($rowLabel);
                    $seat->setSeatNumber($number);

                    $this->entityManager->persist($seat);

                    $created++;
                }
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf(
            '%d siège(s) créé(s). Les sièges déjà présents ont été conservés.',
            $created
        ));

        return Command::SUCCESS;
    }
}
