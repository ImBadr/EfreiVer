<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Report;
use App\Form\AnnonceType;
use App\Form\ReportType;
use App\Repository\AnnonceRepository;
use App\Repository\CategoryRepository;
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
     * @Route("/annonce/add", name="add_annonce")
     * @param Request $request
     * @param CategoryRepository $repository
     * @return Response
     */
    public function new(Request $request, CategoryRepository $repository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ){

            $annonce->setUser($this->get('security.token_storage')->getToken()->getUser());
            if ($annonce->getImages() != null){
                $images = array();
                $files = $annonce->getImages();
                foreach($files as $file)
                {
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move($this->getParameter('upload_directory'), $fileName);
                    array_push($images, $fileName);
                }
                $annonce->setImages($images);
            }


            $em = $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('annonce');
        }

        $categories = $repository->findAll();

        return $this->render('annonce/new.html.twig', [
            'form' => $form->createView(),
            "categories" => $categories,
        ]);
    }

    /**
     * @Route("/annonce/{id}/edit", name="edit_annonce")
     * @param Annonce $annonce
     * @param Request $request
     * @param CategoryRepository $repository
     * @return Response
     */
    public function edit(Annonce $annonce, Request $request, CategoryRepository $repository) : Response
    {
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ){

            $annonce->setModifiedAt(new \DateTime());
            $annonce->setUser($this->get('security.token_storage')->getToken()->getUser());

            if ($annonce->getImages() != null){
                $images = array();
                $files = $annonce->getImages();
                foreach($files as $file)
                {
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move($this->getParameter('upload_directory'), $fileName);
                    array_push($images, $fileName);
                }
                $annonce->setImages($images);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('annonce');
        }

        $categories = $repository->findAll();

        return $this->render('annonce/edit.html.twig', [
            "form" => $form->createView(),
            "categories" => $categories,
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

    /**
     * @Route("/annonces/list", name="my_annonces")
     * @param AnnonceRepository $repository
     * @return Response
     */
    public function getItems(AnnonceRepository $repository): Response
    {
        $annonces = $repository->findUserAnnoncesById($this->getUser()->getId());

        return $this->render('annonce/annonce_list.html.twig', [
            'annonces' => $annonces
        ]);
    }

}
