<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterCommand extends Command
{
    protected static $defaultName = 'app:register';
    private EntityManagerInterface $em;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct();
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this
            ->setDescription('Register new user')
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = new User();

        $user->setEmail($io->ask("Email: "));
        do {
            $pass = $io->askHidden("Password: ");
            $passAgain = $io->askHidden("Password repeat: ");

            if ($pass != $passAgain) {
                $error = true;
                $io->error("Passwords to does not match");
            }

            if (strlen($pass) < 8) {
                $error = true;
                $io->error("Password must have at least 8 characters");
            }
        } while (isset($error));
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setIsVerified(true);
        $user->setFacebookId('');
        $user->setGoogleId('');

        $user->setPassword($this->passwordEncoder->encodePassword($user, $pass));

        $this->em->persist($user);
        $this->em->flush();

        $io->success('Account for email ' . $user->getEmail() . ' created.');

        return 0;
    }
}
