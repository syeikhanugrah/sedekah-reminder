<?php

namespace App\Controller;

use App\Entity\User;
use App\Events;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/lupa-password/cari-akun", methods={"GET", "POST"}, name="reset_password_cari_akun")
     */
    public function cariAkunAction(
        Request $request,
        UserRepository $repository,
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session
    ): Response {
        $form = $this->createForm(ResetPasswordType::class, null, [
            'mode' => ResetPasswordType::MODE_CARI_AKUN,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $identitas = $form->getData()['identitas'];
            $user = $repository->findByUsernameOrEmail($identitas);

            if (!$user instanceof User) {
                $this->addFlash('danger', 'Akun tidak ditemukan');
            }

            $event = new GenericEvent($user);
            $eventDispatcher->dispatch(Events::RESET_PASSWORD_INIT, $event);

            $email = preg_replace('/(?:^|@).\K|\.[^@]*$(*SKIP)(*F)|.(?=.*?\.)/', '*', $user->getEmail());
            $session->set('reset_password_user_email', $email);

            return $this->redirectToRoute('reset_password_cek_email');
        }

        return $this->render('reset_password/cari_akun.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/lupa-password/cek-email", name="reset_password_cek_email")
     */
    public function checkEmailAction(SessionInterface $session): Response
    {
        if (!$session->has('reset_password_user_email')) {
            return $this->redirectToRoute('security_login');
        }

        $email = $session->get('reset_password_user_email');
        $session->clear();

        return $this->render('reset_password/cek_email.html.twig', [
            'email' => $email,
        ]);
    }

    /**
     * @Route("/lupa-password/reset/{confirmationToken}", name="reset_password_reset")
     */
    public function resetAction(
        Request $request,
        $confirmationToken,
        UserRepository $userRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $user = $userRepository->findByConfirmationToken($confirmationToken);
        if (!$user instanceof User) {
            return $this->redirectToRoute('security_login');
        }

        $form = $this->createForm(ResetPasswordType::class, null, [
            'mode' => ResetPasswordType::MODE_RESET_PASSWORD,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $password = $form->getData()['passwordBaru'];

            $event = new GenericEvent($user, ['password' => $password]);
            $eventDispatcher->dispatch(Events::RESET_PASSWORD_SUCCESS, $event);

            $this->addFlash('success', 'Password berhasil diatur ulang. Sekarang Anda bisa masuk dengan password yang baru');

            return $this->redirectToRoute('security_login');
        }

        return $this->render('reset_password/reset.html.twig', [
           'form' => $form->createView(),
        ]);
    }
}
