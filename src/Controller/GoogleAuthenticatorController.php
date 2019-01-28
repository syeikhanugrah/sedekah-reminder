<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Security\SocialAuthenticator\GoogleAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class GoogleAuthenticatorController extends AbstractController
{
    /**
     * @Route("/connect/google", name="connect_google")
     */
    public function connectAction(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect();
    }

    /**
     * @Route("/connect/google/check", name="connect_google_check")
     */
    public function connectCheckAction(): Response
    {
        if (!$this->getUser()) {
            return new JsonResponse(array('status' => false, 'message' => 'User not found!'));
        } else {
            return $this->redirectToRoute('default_index');
        }
    }

    /**
     * @Route("/connect/google/registration", name="connect_google_registration")
     */
    public function finishRegistrationAction(
        Request $request,
        GoogleAuthenticator $googleAuthenticator,
        SessionInterface $session,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $authenticatorHandler
    ): Response {
        /** @var GoogleUser $googleUser */
        $googleUser = $googleAuthenticator->getUserInfoFromSession($request);
        if (!$googleUser) {
            throw $this->createAccessDeniedException('Akses ditolak!');
        }

        $user = new User();
        $user->setNamaLengkap($googleUser->getName());
        $user->setEmail($googleUser->getEmail());

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $password = $form['password']->getData();
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($encodedPassword);
            $user->setEnabled(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $session->remove('guard.finish_registration.user_information');
            $authenticatorHandler->authenticateUserAndHandleSuccess($user, $request, $googleAuthenticator, 'main');
            $this->addFlash('success', sprintf('Berhasil terdaftar, selamat datang %s!', $user->getNamaLengkap()));

            return $this->redirectToRoute('default_index');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
