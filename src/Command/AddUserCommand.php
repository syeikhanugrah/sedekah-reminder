<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AddUserCommand extends Command
{
    protected static $defaultName = 'app:add-user';

    /**
     * @var SymfonyStyle
     */
    private $io;

    private $entityManager;
    private $passwordEncoder;
    private $validator;
    private $users;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, Validator $validator, UserRepository $users)
    {
        parent::__construct();

        $this->entityManager = $em;
        $this->passwordEncoder = $encoder;
        $this->validator = $validator;
        $this->users = $users;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Membuat user baru dan menyimpannya di database')
            ->setHelp($this->getCommandHelp())
            ->addArgument('username', InputArgument::OPTIONAL, 'Username untuk user baru')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password untuk user baru')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email untuk user baru')
            ->addArgument('nama-lengkap', InputArgument::OPTIONAL, 'Nama lengkap untuk user baru')
            ->addArgument('nomor-ponsel', InputArgument::OPTIONAL, 'Nomor ponsel untuk user baru')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Jika di set, user dibuat sebagai administrator')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('username') && null !== $input->getArgument('password')
            && null !== $input->getArgument('email') && null !== $input->getArgument('nama-lengkap')) {
            return;
        }

        $this->io->title('Panduan interaktif membuat user baru');
        $this->io->text([
            'Jika Anda memilih untuk tidak menggunakan panduan interaktif ini, berikan argumen',
            'yang diperlukan oleh perintah ini sebagai berikut:',
            '',
            '$ php bin/console app:add-user username password email@example.com',
        ]);

        $username = $input->getArgument('username');
        if (null !== $username) {
            $this->io->text(' > <info>Username</info>: '.$username);
        } else {
            $username = $this->io->ask('Username', null, [$this->validator, 'validateUsername']);
            $input->setArgument('username', $username);
        }

        $password = $input->getArgument('password');
        if (null !== $password) {
            $this->io->text(' > <info>Password</info>: '.str_repeat('*', mb_strlen($password)));
        } else {
            $password = $this->io->askHidden('Password', [$this->validator, 'validatePassword']);
            $input->setArgument('password', $password);
        }

        $email = $input->getArgument('email');
        if (null !== $email) {
            $this->io->text(' > <info>Email</info>: '.$email);
        } else {
            $email = $this->io->ask('Email', null, [$this->validator, 'validateEmail']);
            $input->setArgument('email', $email);
        }

        $namaLengkap = $input->getArgument('nama-lengkap');
        if (null !== $namaLengkap) {
            $this->io->text(' > <info>Full Name</info>: '.$namaLengkap);
        } else {
            $namaLengkap = $this->io->ask('Nama Lengkap', null, [$this->validator, 'validateNamaLengkap']);
            $input->setArgument('nama-lengkap', $namaLengkap);
        }

        $nomorPonsel = $input->getArgument('nomor-ponsel');
        if (null !== $nomorPonsel) {
            $this->io->text(' > <info>Nomor Ponsel</info>: '.$nomorPonsel);
        } else {
            $nomorPonsel = $this->io->ask('Nomor Ponsel', null, [$this->validator, 'validateNomorPonsel']);
            $input->setArgument('nomor-ponsel', $nomorPonsel);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $plainPassword = $input->getArgument('password');
        $email = $input->getArgument('email');
        $namaLengkap = $input->getArgument('nama-lengkap');
        $nomorPonsel = $input->getArgument('nomor-ponsel');
        $isAdmin = $input->getOption('admin');

        $this->validateUserData($username, $plainPassword, $email, $namaLengkap, $nomorPonsel);

        $user = new User();
        $user->setNamaLengkap($namaLengkap);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setNomorHp($nomorPonsel);
        $user->setRoles([$isAdmin ? 'ROLE_ADMIN' : 'ROLE_USER']);

        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);

        $em = $this->entityManager;
        $em->persist($user);
        $em->flush();

        $this->io->success(sprintf('%s berhasil dibuat: %s (%s)', $isAdmin ? 'Administrator user' : 'User', $user->getUsername(), $user->getEmail()));
    }

    private function validateUserData($username, $plainPassword, $email, $namaLengkap, $nomorPonsel)
    {
        $existingUser = $this->users->findOneBy(['username' => $username]);

        if (null !== $existingUser) {
            throw new RuntimeException(sprintf('Sudah ada user yang terdaftar dengan username "%s".', $username));
        }

        $this->validator->validatePassword($plainPassword);
        $this->validator->validateEmail($email);
        $this->validator->validateNamaLengkap($namaLengkap);
        $this->validator->validateNomorPonsel($nomorPonsel);

        $existingEmail = $this->users->findOneBy(['email' => $email]);

        if (null !== $existingEmail) {
            throw new RuntimeException(sprintf('Sudah ada user yang terdaftar dengan email "%s".', $email));
        }
    }

    private function getCommandHelp(): string
    {
        return <<<'HELP'
Perintah <info>%command.name%</info> Membuat user baru dan menyimpannya di database:

  <info>php %command.full_name%</info> username password email

Untuk membuat user dengan role admin, tambah option <comment>--admin</comment>:

  <info>php %command.full_name%</info> username password email <comment>--admin</comment>

HELP;
    }
}
