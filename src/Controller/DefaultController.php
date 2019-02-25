<?php

namespace App\Controller;

use App\Entity\Pengingat;
use App\Entity\User;
use App\Form\PengingatType;
use App\Repository\PengingatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="pengingat_index", methods={"GET"})
     */
    public function index(PengingatRepository $pengingatRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        return $this->render('pengingat/index.html.twig', [
            'entities' => $pengingatRepository->findAllEntities([
                'isAdmin' => $authorizationChecker->isGranted('ROLE_ADMIN'),
                'user' => $this->getUser(),
            ]),
        ]);
    }

    /**
     * @Route("/new", name="pengingat_new", methods={"GET","POST"})
     */
    public function new(Request $request, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        $pengingat = new Pengingat();
        $form = $this->createForm(PengingatType::class, $pengingat, [
            'isAdmin' => $authorizationChecker->isGranted('ROLE_ADMIN'),
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
                $pengingat->setUser($this->getUser());
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pengingat);
            $entityManager->flush();

            return $this->redirectToRoute('pengingat_index');
        }

        return $this->render('pengingat/new.html.twig', [
            'pengingat' => $pengingat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pengingat_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(Pengingat $pengingat): Response
    {
        $this->denyAccessUnlessGranted('show', $pengingat, 'Akses ditolak!');

        return $this->render('pengingat/show.html.twig', [
            'pengingat' => $pengingat,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pengingat_edit", methods={"GET","POST"}, requirements={"id":"\d+"})
     */
    public function edit(Request $request, Pengingat $pengingat, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        $this->denyAccessUnlessGranted('edit', $pengingat, 'Akses ditolak!');

        $isPemilikPengingat = $pengingat->getUser() instanceof User;
        $namaPenerima = $isPemilikPengingat ? $pengingat->getUser()->getNamaLengkap() : $pengingat->getNamaPenerima();
        $nomorHpPenerima = $isPemilikPengingat ? $pengingat->getUser()->getNomorHp() : $pengingat->getNomorHpPenerima();

        $form = $this->createForm(PengingatType::class, $pengingat, [
            'isAdmin' => $authorizationChecker->isGranted('ROLE_ADMIN'),
            'isPemilikPengingat' => $isPemilikPengingat,
            'namaPenerima' => $namaPenerima,
            'nomorHpPenerima' => $nomorHpPenerima,
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pengingat_show', [
                'id' => $pengingat->getId(),
            ]);
        }

        return $this->render('pengingat/edit.html.twig', [
            'pengingat' => $pengingat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pengingat_delete", methods={"DELETE"}, requirements={"id":"\d+"})
     */
    public function delete(Request $request, Pengingat $pengingat): Response
    {
        $this->denyAccessUnlessGranted('delete', $pengingat, 'Akses ditolak!');

        if ($this->isCsrfTokenValid('delete'.$pengingat->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pengingat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('pengingat_index');
    }
}
