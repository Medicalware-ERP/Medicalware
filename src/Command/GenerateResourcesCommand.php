<?php

namespace App\Command;

use App\Entity\Patient;
use App\Entity\Planning\Resource;
use App\Entity\Room\Room;
use App\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:generate:resources',
    description: 'Add a short description for your command',
)]
class GenerateResourcesCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try
        {
            // Delete all resources (éviter doublon)
            $resourceRepository = $this->manager->getRepository(Resource::class);
            $resourcesToDelete = $resourceRepository->findAll();

            $io->info("Suppression des resources présentes en base ...");

            foreach ($resourcesToDelete as $resource)
            {
                $this->manager->remove($resource);
            }

            $this->manager->flush();

            $this->resetAutoIncrement(Resource::class);

            // Get all rooms, user, patient and doctor and create resource lied to it
            $rooms = $this->manager->getRepository(Room::class)->findAll();
            $users = $this->manager->getRepository(User::class)->findAll();
            $patients = $this->manager->getRepository(Patient::class)->findAll();

            $entities = array_merge($rooms, $users, $patients);

            foreach ($entities as $entity)
            {
                $info = "#".(string)$entity->getId()." ".$entity::class;
                $io->info("Création de la ressource pour l'entité $info");

                $newResource = new Resource();
                $newResource->setResourceId($entity->getId());
                $newResource->setResourceClass($entity::class);

                $this->manager->persist($newResource);
            }

            $this->manager->flush();

            $io->success("Ressources générées avec succès");
            return Command::SUCCESS;
        }
        catch (\Exception $ex)
        {
            $io->error($ex->getMessage());
            return Command::FAILURE;
        }
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
