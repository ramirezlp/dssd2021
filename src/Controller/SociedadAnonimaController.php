<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SociedadAnonimaController extends AbstractController
{
    /**
     * @Route("/sociedad/anonima", name="sociedad_anonima")
     */
    public function index(): Response
    {
        return $this->render('sociedad_anonima/index.html.twig', [
            'controller_name' => 'SociedadAnonimaController',
        ]);
    }
}
