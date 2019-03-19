<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/profile")
 */
class ProfileController extends Controller
{
    /**
     * @Route("/", name="profile_show", methods={"GET"})
     */
    public function show(): Response
    {
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/edit", name="profile_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, [
            'mode' => UserType::MODE_EDIT_PROFILE,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $passwordEncoder->encodePassword($user, $form->get('password')->getData());
            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profile_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
