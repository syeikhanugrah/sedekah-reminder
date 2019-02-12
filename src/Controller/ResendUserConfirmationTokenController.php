<?php

namespace App\Controller;

use App\Entity\User;
use App\Events;
use App\Form\CariAkunType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ResendUserConfirmationTokenController extends AbstractController
{
    /**
     * @Route("/kirim-ulang-email-aktivasi", name="resend_user_confirmation_token_cari_akun")
     */
    public function cariAkunAction(
        Request $request,
        UserRepository $repository,
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session
    ): Response {
        $form = $this->createForm(CariAkunType::class);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $identitas = $form->getData()['identitas'];
            $user = $repository->findByUsernameOrEmail($identitas);

            if (!$user instanceof User) {
                $this->addFlash('danger', 'Akun tidak ditemukan');

                return $this->redirectToRoute('resend_user_confirmation_token_cari_akun');
            }

            if ($user->isEnabled()) {
                $this->addFlash('success', 'Akun anda telah aktif. Silahkan login.');

                return $this->redirectToRoute('security_login');
            }

            $user->setConfirmationToken(sha1(random_bytes(10)));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $event = new GenericEvent($user, ['resend' => true]);
            $eventDispatcher->dispatch(Events::REGISTRATION_SUCCESS, $event);

            $email = preg_replace('/(?:^|@).\K|\.[^@]*$(*SKIP)(*F)|.(?=.*?\.)/', '*', $user->getEmail());
            $session->set('resend_user_confirmation_token_user_email', $email);

            return $this->redirectToRoute('resend_user_confirmation_token_cek_email');
        }

        return $this->render('resend_user_confirmation_token/cari_akun.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/kirim-ulang-email-aktivasi/cek-email", name="resend_user_confirmation_token_cek_email")
     */
    public function checkEmailAction(SessionInterface $session): Response
    {
        if (!$session->has('resend_user_confirmation_token_user_email')) {
            return $this->redirectToRoute('security_login');
        }

        $email = $session->get('resend_user_confirmation_token_user_email');
        $session->clear();

        return $this->render('resend_user_confirmation_token/cek_email.html.twig', [
            'email' => $email,
        ]);
    }
}
