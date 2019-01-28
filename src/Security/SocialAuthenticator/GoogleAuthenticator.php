<?php

namespace App\Security\SocialAuthenticator;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Security\Exception\FinishRegistrationException;
use KnpU\OAuth2ClientBundle\Security\Helper\FinishRegistrationBehavior;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GoogleAuthenticator extends SocialAuthenticator
{
    use FinishRegistrationBehavior;

    private $clientRegistry;
    private $em;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
    }

    public function supports(Request $request)
    {
        return $request->getPathInfo() == '/connect/google/check' && $request->isMethod('GET');
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getGoogleClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $this->getGoogleClient()->fetchUserFromToken($credentials);

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $googleUser->getEmail()]);
        if (!$user) {
            throw new FinishRegistrationException($googleUser);
        }

        return $user;
    }

    /**
     * @return OAuth2Client
     */
    private function getGoogleClient()
    {
        return $this->clientRegistry->getClient('google');
    }

    /**
     * @param Request $request
     * @param AuthenticationException
     *
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('/login');
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($exception instanceof FinishRegistrationException) {
            $this->saveUserInfoToSession($request, $exception);

            return new RedirectResponse($this->router->generate('connect_google_registration'));
        }
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse($this->router->generate('default_index'));
    }
}
