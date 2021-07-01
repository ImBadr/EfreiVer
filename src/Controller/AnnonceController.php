<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/annonces/{id}/view", name="view_annonce")
     * @param Annonce $annonce
     * @return Response
     */
    public function show(Annonce $annonce): Response
    {
        return $this->render('annonce/annonce.html.twig', [
            'annonce' => $annonce
        ]);
    }
}
