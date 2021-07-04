<?php

namespace App\Controller;

use App\Repository\ReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    /**
     * @Route("/admin/report", name="admin_report")
     */
    public function index(ReportRepository $repository): Response
    {

        $reports = $repository->findAll();

        return $this->render('report/index.html.twig', [
            'reports' => $reports,
        ]);
    }
}
