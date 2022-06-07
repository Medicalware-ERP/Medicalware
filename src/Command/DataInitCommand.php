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

                $this->resetAutoIncrement($class);

                //Recreate All
                /** @var EnumEntity $dataValue */
                foreach ($enum->getData() as $dataValue) {

                  $alreadyExist = $this->manager->getRepository($class)->findOneBy(['slug' => $dataValue->getSlug()]);

                  if ($alreadyExist instanceof EnumEntity) {
                      $alreadyExist->setName($dataValue->getName());
                      $alreadyExist->setDescription($dataValue->getDescription());
                      $alreadyExist->setColor($dataValue->getColor());

                      $this->manager->persist($alreadyExist);

                  } else {
                      $this->manager->persist($dataValue);
                  }

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
    private function resetAutoIncrement(string $class) {

         /** Table always starting to id 1 */
        $tableName= $this->manager->getClassMetadata($class)->getTableName();
        $connection = $this->manager->getConnection();
        $connection->executeStatement("ALTER TABLE " . $tableName . " AUTO_INCREMENT = 1;");
    }
}
