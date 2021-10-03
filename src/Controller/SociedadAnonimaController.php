<?php

namespace App\Controller;

use App\Entity\SociedadAnonima;
use App\Form\SociedadAnonimaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SociedadAnonimaController extends AbstractController
{
    /**
     * @Route("/sociedad/anonima", name="sociedad_anonima")
     */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $sociedadAnonima = new SociedadAnonima();

        $form = $this->createForm(SociedadAnonimaType::class, $sociedadAnonima);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$sociedadAnonima` variable has also been updated
            $em->persist($sociedadAnonima);
            $em->flush();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($sociedadAnonima);
            // $entityManager->flush();

            $sociedadAnonima = new SociedadAnonima();

            $form = $this->createForm(SociedadAnonimaType::class, $sociedadAnonima);

            return $this->render('sociedad_anonima/index.html.twig', [
                'controller_name' => 'SociedadAnonimaController',
                'form' => $form->createView()
            ]);
        }

        return $this->render('sociedad_anonima/index.html.twig', [
            'controller_name' => 'SociedadAnonimaController',
            'form' => $form->createView()
        ]);
    }
}
