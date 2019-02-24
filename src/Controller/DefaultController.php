<?php

namespace App\Controller;

use App\Entity\Pengingat;
use App\Form\PengingatType;
use App\Repository\PengingatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="pengingat_index", methods={"GET"})
     */
    public function index(PengingatRepository $pengingatRepository): Response
    {
        return $this->render('pengingat/index.html.twig', [
            'entities' => $pengingatRepository->findByUser($this->getUser()),
        ]);
    }

    /**
     * @Route("/new", name="pengingat_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $pengingat = new Pengingat();
        $form = $this->createForm(PengingatType::class, $pengingat);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $pengingat->setUser($this->getUser());

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
    public function edit(Request $request, Pengingat $pengingat): Response
    {
        $this->denyAccessUnlessGranted('edit', $pengingat, 'Akses ditolak!');

        $form = $this->createForm(PengingatType::class, $pengingat);
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
