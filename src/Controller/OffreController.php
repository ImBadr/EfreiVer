<?php

namespace App\Controller;

use App\Repository\OffreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OffreController extends AbstractController
{
    /**
     * @Route("/offre", name="offre")
     */
    public function index(OffreRepository $repository): Response
    {

        $offres = $repository->findAll();

        return $this->render('offre/index.html.twig', [
            'offres' => $offres,
        ]);
    }
}
