<?php

namespace App\Controller;

use App\Entity\SociedadAnonima;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Constants\ConstanteEstadoFormulario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{

    /** 
    * @Route("/", name="homepage")
    */
    public function indexAction() { 

        $parameters = [
            'controller_name' => 'LoginController',
        ];

        $em = $this->getDoctrine()->getManager();

        if($this->isGranted('ROLE_MESA_ENTRADA')){
            
            
            $parameters['pendientes'] = $em->getRepository(SociedadAnonima::class)->findBy(array('estado' => ConstanteEstadoFormulario::PENDIENTE));
            $parameters['aprobados'] = $em->getRepository(SociedadAnonima::class)->findBy(array('estado' => ConstanteEstadoFormulario::APROBADO));
            $parameters['rechazados'] = $em->getRepository(SociedadAnonima::class)->findBy(array('estado' => ConstanteEstadoFormulario::RECHAZADO));
        }else if($this->isGranted('ROLE_APODERADO')){
            $parameters['pendientes'] = $em->getRepository(SociedadAnonima::class)->findBy(array('estado' => ConstanteEstadoFormulario::PENDIENTE, 
                                                                                                  'solicitante' => $this->getUser()));
            $parameters['aprobados'] = $em->getRepository(SociedadAnonima::class)->findBy(array('estado' => ConstanteEstadoFormulario::APROBADO, 
                                                                                                  'solicitante' => $this->getUser()));
            $parameters['rechazados'] = $em->getRepository(SociedadAnonima::class)->findBy(array('estado' => ConstanteEstadoFormulario::RECHAZADO, 
                                                                                                  'solicitante' => $this->getUser()));
        }

        return $this->render('index.html.twig', $parameters);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
        $this->redirectToRoute('login');
    }

    /**
     * @Route("/login", name="login")
     */
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
                 // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        
                 // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
         return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
}
