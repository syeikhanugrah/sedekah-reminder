<?php

namespace App\Controller;

use App\Entity\User;
use App\Events;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="registration_index")
     */
    public function indexAction(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
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

            $message = 'Anda telah berhasil terdaftar, harap cek email untuk mengkonfirmasi akun anda.';
            $this->addFlash('success', $message);

            $event = new GenericEvent($user);
            $eventDispatcher->dispatch(Events::REGISTRATION_SUCCESS, $event);

            return $this->redirectToRoute('security_login');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/confirm/{confirmationToken}", name="registration_confirm")
     */
    public function confirmAction($confirmationToken, UserRepository $repository)
    {
        $user = $repository->findByConfirmationToken($confirmationToken);
        if (!$user instanceof User) {
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
