<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\CategoryRepository;
use App\Repository\OffreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

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

    /**
     * @Route("/offre/add", name="add_offre")
     * @param Request $request
     * @param CategoryRepository $repository
     * @return Response
     */
    public function new(Request $request, CategoryRepository $repository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ){

            $offre->setUser($this->get('security.token_storage')->getToken()->getUser());
            if ($offre->getImages() != null){
                $images = array();
                $files = $offre->getImages();
                foreach($files as $file)
                {
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move($this->getParameter('upload_directory'), $fileName);
                    array_push($images, $fileName);
                }
                $offre->setImages($images);
            }


            $em = $this->getDoctrine()->getManager();
            $em->persist($offre);
            $em->flush();

            return $this->redirectToRoute('offre');
        }

        $categories = $repository->findAll();

        return $this->render('offre/new.html.twig', [
            'form' => $form->createView(),
            "categories" => $categories,
        ]);
    }

    /**
     * @Route("/offre/{id}/edit", name="edit_offre")
     * @param Offre $offre
     * @param Request $request
     * @param CategoryRepository $repository
     * @return Response
     */
    public function edit(Offre $offre, Request $request, CategoryRepository $repository) : Response
    {
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ){

            $offre->setModifiedAt(new \DateTime());
            $offre->setUser($this->get('security.token_storage')->getToken()->getUser());

            if ($offre->getImages() != null){
                $images = array();
                $files = $offre->getImages();
                foreach($files as $file)
                {
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move($this->getParameter('upload_directory'), $fileName);
                    array_push($images, $fileName);
                }
                $offre->setImages($images);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('offre');
        }

        $categories = $repository->findAll();

        return $this->render('offre/edit.html.twig', [
            "form" => $form->createView(),
            "categories" => $categories,
        ]);
    }

    /**
     * @Route("/offre/{id}/view", name="view_offre")
     * @param Offre $offre
     * @return Response
     */
    public function view(Offre $offre): Response
    {
        return $this->render('offre/offre.html.twig', [
            'offre' => $offre
        ]);
    }

    /**
     * @Route("/offre/list", name="my_offres")
     * @param OffreRepository $repository
     * @return Response
     */
    public function getItems(OffreRepository $repository): Response
    {
        $offres = $repository->findUserOffresById($this->getUser()->getId());

        return $this->render('offre/offre_list.html.twig', [
            'offres' => $offres
        ]);
    }

}
