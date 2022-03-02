<?php

namespace App\Command;

use App\Entity\EnumEntity;
use App\Enum\DataInitializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:data:init',
    description: 'Generate Enums',
)]
class DataInitCommand extends Command
{
    public function __construct(private iterable $enums, private EntityManagerInterface $manager, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            /** @var DataInitializerInterface $enum */
            foreach ($this->enums as $enum) {
                $class = $enum->getEnum();

                if (get_parent_class($enum->getEnum()) !== EnumEntity::class) {
                    throw new Exception(sprintf("La classe %s doit hérité de EnumEntity", $class));
                }

                $io->info("Generating $class ...");

                $this->clearTable($class);

                foreach ($enum->getData() as $dataValue) {
                    $this->manager->persist($dataValue);
                }

                $this->manager->flush();
            }
        }catch (Exception $exception) {
            $io->error($exception->getMessage());

            return  Command::FAILURE;
        }

        $io->success('All enums generated.');

        return Command::SUCCESS;
    }


    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function clearTable(string $class) {
        $values = $this->manager->getRepository($class)->findAll();

        foreach ($values as $value) {
            $this->manager->remove($value);
        }

        $this->manager->flush();

         /** Table always starting to id 1 */

        $tableName= $this->manager->getClassMetadata($class)->getTableName();
        $connection = $this->manager->getConnection();
        $connection->executeStatement("ALTER TABLE " . $tableName . " AUTO_INCREMENT = 1;");
    }
}
