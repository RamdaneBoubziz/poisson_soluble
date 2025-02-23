<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Psr\Log\LoggerInterface;
use Doctrine\DBAL\Connection;

#[AsCommand(name: 'app:count-receivers')]
class ImportReceiversCommand extends Command
{
    public function __construct(private Connection $connection, private LoggerInterface $logger)
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->setDescription('Compte les lignes en erreur et en succès d\'un fichier CSV')
            ->addArgument('file', InputArgument::REQUIRED, 'Chemin du CSV');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');
        if (!\file_exists($filePath)) {
            $output->writeln('<error>Fichier introuvable.</error>');

            return Command::FAILURE;
        }
        $handle = \fopen($filePath, 'r');

        $successAndErrors = $this->getSuccessAndErrors($handle);

        \fclose($handle);

        $output->writeln('<info>Import terminé : ' . $successAndErrors['success'] . ' succès, ' . $successAndErrors['errors'] . ' erreurs.</info>');

        return Command::SUCCESS;
    }

    private function getSuccessAndErrors($handle): array
    {
        $success = 0;
        $errors = 0;

        \fgetcsv($handle);

        while (($data = \fgetcsv($handle, 1000, ',')) !== false) {
            [$insee, $telephone] = $data;
            if ($this->validateInsee($insee) && $this->validatePhone($telephone)) {
                $this->connection->insert('destinataires', ['insee' => $insee, 'telephone' => $telephone]);
                $success++;
            } else {
                $errors++;
            }
        }

        return
            [
                'success' => $success,
                'errors' => $errors,
            ];
    }

    private function validateInsee(string $insee): bool
    {
        return \preg_match('/^\d{5}$/', $insee);
    }

    private function validatePhone(string $phone): bool
    {
        return \preg_match('/^\+?\d{10,15}$/', $phone);
    }
}
