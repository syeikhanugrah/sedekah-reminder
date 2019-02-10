<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Events;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordSubscriber implements EventSubscriberInterface
{
    private $entityManager;
    private $mailer;
    private $urlGenerator;
    private $twig;
    private $encoder;
    private $sender;

    public function __construct(
        EntityManagerInterface $entityManager,
        \Swift_Mailer $mailer,
        UrlGeneratorInterface $urlGenerator,
        \Twig_Environment $twig,
        UserPasswordEncoderInterface $encoder,
        $sender
    ) {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->encoder = $encoder;
        $this->sender = $sender;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::RESET_PASSWORD_INIT => 'onResetPasswordInit',
            Events::RESET_PASSWORD_SUCCESS => 'onResetPasswordSuccess',
        ];
    }

    public function onResetPasswordInit(GenericEvent $event): void
    {
        /** @var User $user */
        $user = $event->getSubject();

        $confirmationToken = sha1(random_bytes(10));
        $user->setConfirmationToken($confirmationToken);

        $em = $this->entityManager;
        $em->persist($user);
        $em->flush();

        $confirmationUrl = $this->urlGenerator->generate('reset_password_reset', [
            'confirmationToken' => $user->getConfirmationToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $template = $this->renderTemplate([
            'user' => $user,
            'confirmationUrl' => $confirmationUrl,
        ]);

        $message = (new \Swift_Message())
            ->setSubject('Permintaan mengatur ulang password')
            ->setTo($user->getEmail())
            ->setFrom($this->sender)
            ->setBody($template, 'text/html')
        ;

        $this->mailer->send($message);
    }

    public function onResetPasswordSuccess(GenericEvent $event): void
    {
        /** @var User $user */
        $user = $event->getSubject();

        $encodedPassword = $this->encoder->encodePassword($user, $event->getArgument('password'));
        $user->setPassword($encodedPassword);
        $user->setConfirmationToken(null);

        $em = $this->entityManager;
        $em->persist($user);
        $em->flush();
    }

    private function renderTemplate(array $options)
    {
        return $this->twig->render('reset_password/confirm_reset_password_email.html.twig', [
            'user' => $options['user'],
            'confirmationUrl' => $options['confirmationUrl'],
        ]);
    }
}
