<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Report;
use App\Form\ReportType;
use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    /**
     * @Route("/annonce", name="annonce")
     */
    public function index(AnnonceRepository $repository): Response
    {

        $annonces = $repository->findAll();

        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces
        ]);
    }

    /**
     * @Route("/annonce/{id}/view", name="view_annonce")
     * @param Request $request
     * @param Annonce $annonce
     * @return Response
     */
    public function show(Request $request, Annonce $annonce): Response
    {
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $report->setReason(
                $form->get('reason')->getData()
            );
            $report->setAnnonce(
                $annonce
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($report);
            $entityManager->flush();

            return $this->redirectToRoute('view_annonce', [
                'id' => $annonce->getId(),
                'message' => 'Thanks for your report'
            ]);
        }

        return $this->render('annonce/annonce.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
            'message' => ''
        ]);
    }

}
