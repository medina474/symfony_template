<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Création du premier utilisateur avec le rôle ADMIN',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $repository = $this->entityManager->getRepository(User::class);

        /*
         * Vérifie qu'aucun administrateur n'existe déjà
         */
        $connection = $this->entityManager->getConnection();

        $sql = <<<SQL
            SELECT id
            FROM "user"
            WHERE roles @> :role
            LIMIT 1
        SQL;

        $result = $connection->fetchOne($sql, [
            'role' => '["ROLE_ADMIN"]',
        ]);

        if ($result !== false) {
            $io->error('Il existe déja un administrateur.');
            return Command::FAILURE;
        }

        $email = $input->getArgument('email');

        $existingUser = $repository->findOneBy([
            'email' => $email,
        ]);

        if ($existingUser) {
            $io->error('L\'utilisateur existe déja et n\'est pas administrateur.');
            return Command::FAILURE;
        }

        $passwordQuestion = new Question('Password: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);

        $passwordQuestion->setValidator(function (?string $value): string {
            $value = (string) $value;

            if ($value === '') {
                throw new \RuntimeException('Le mot de passe ne peut pas être vide.');
        }

            if (mb_strlen($value) < 8) {
                throw new \RuntimeException('Le mot de passe doit contenir au moins 8 caractères');
        }

            return $value;
        });

        $password = $io->askQuestion($passwordQuestion);


        $user = new User();
        $user->setEmail($email);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );

        $user->setPassword($hashedPassword);

        $user->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Compte administrateur créé avec succès.');

        return Command::SUCCESS;
    }
}
