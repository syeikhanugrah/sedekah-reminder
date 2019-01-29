<?php

namespace App\Controller;

use App\Entity\User;
use App\Events;
use App\Form\RegistrationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="registration_index")
     */
    public function indexAction(
        Request $request,
        SessionInterface $session,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $password = $form['password']->getData();
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($encodedPassword);
            $user->setConfirmationToken(sha1(random_bytes(10)));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $token = new UsernamePasswordToken($user, $password, 'main', $user->getRoles());
            $tokenStorage->setToken($token);

            $session->set('_security_main', serialize($token));

            $message = 'Selamat datang %s! Anda telah berhasil terdaftar, harap cek email untuk mengkonfirmasi akun anda.';
            $this->addFlash('success', sprintf($message, $user->getNamaLengkap()));

            $event = new GenericEvent($user);
            $eventDispatcher->dispatch(Events::REGISTRATION_SUCCESS, $event);

            return $this->redirectToRoute('default_index');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/confirm/{confirmationToken}", name="registration_confirm")
     * @Entity("user", expr="repository.findByConfirmationToken(confirmationToken)")
     */
    public function confirmAction(User $user)
    {
        if (!$user) {
            return $this->redirectToRoute('security_login');
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $message = 'Konfirmasi email berhasil, sekarang anda bisa masuk dengan email/username dan password anda.';
        $this->addFlash('success', $message);

        return $this->redirectToRoute('security_login');
    }
}
