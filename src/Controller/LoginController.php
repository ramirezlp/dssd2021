<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use App\Resources\Access;
use App\Resources\Process;
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


        if($this->isGranted('ROLE_USER')){
            if(! isset($_SESSION['X-Bonita-API-Token'])){
                Access::login($this->getUser()->getBonitaUser(), $this->getUser()->getBonitaPass());
                if($this->getUser()->getBonitaUserId() == null){
                    $em = $this->getDoctrine()->getManager();
                    $this->getUser()->setBonitaUserId(Process::getIdByUsername($this->getUser()->getUsername()));
                    $em->flush();
                }
                unset($_SESSION['client']);
                unset($_SESSION['cookie']);
            }
        }

        if($this->isGranted('ROLE_MESA_ENTRADA')){
            
            $parameters['pendientes'] = $em->getRepository(SociedadAnonima::class)->createQueryBuilder('sa')
            ->where('sa.estado = :estado or sa.estado = :estado2 OR sa.estado = :estado3 or sa.estado = :estado4')
            ->setParameter('estado', ConstanteEstadoFormulario::PENDIENTE)
            ->setParameter('estado2', ConstanteEstadoFormulario::PENDIENTE_LEGALES)
            ->setParameter('estado3', ConstanteEstadoFormulario::PENDIENTE_GENERACION_CARPETAS)
            ->setParameter('estado4', ConstanteEstadoFormulario::PENDIENTE_RETIRO_DOCUMENTACION)
            ->getQuery()->getResult();
            $parameters['pendientesCorreccion'] = [];
            $parameters['aprobados'] = $em->getRepository(SociedadAnonima::class)->findBy(array('estado' => ConstanteEstadoFormulario::APROBADO));
            $parameters['rechazados'] = $em->getRepository(SociedadAnonima::class)->createQueryBuilder('sa')
            ->where('sa.plazoCorreccion >= 0 and sa.estado = :estado')
            ->setParameter('estado', ConstanteEstadoFormulario::RECHAZADO)
            ->getQuery()->getResult();

            foreach($parameters['rechazados'] as $key => $rechazado){
                
                if($rechazado->getPlazoCorreccion() > 0){
                    $fechaOriginal = clone $rechazado->getFechaCreacion();
                    $fechaOriginal->setTime(0, 0);
                    $fechaActualizada = clone $fechaOriginal;
                    $fechaActualizada = $fechaActualizada->add(new DateInterval('P' . $rechazado->getPlazoCorreccion() . 'D'));

                    $now = new DateTime();
                    $now->setTime(0,0);
                    if($fechaActualizada >= $now){
                        array_push($parameters['pendientesCorreccion'], $rechazado);
                        unset($parameters['rechazados'][$key]);
                    }
                }

            }

        }else if($this->isGranted('ROLE_APODERADO')){
            $parameters['pendientes'] = $em->getRepository(SociedadAnonima::class)->createQueryBuilder('sa')
            ->where('(sa.estado = :estado or sa.estado = :estado2 or sa.estado = :estado3 or sa.estado = :estado4) and sa.solicitante = :solicitante')
            ->setParameter('estado', ConstanteEstadoFormulario::PENDIENTE)
            ->setParameter('estado2', ConstanteEstadoFormulario::PENDIENTE_LEGALES)
            ->setParameter('estado3', ConstanteEstadoFormulario::PENDIENTE_GENERACION_CARPETAS)
            ->setParameter('estado4', ConstanteEstadoFormulario::PENDIENTE_RETIRO_DOCUMENTACION)
            ->setParameter('solicitante', $this->getUser())
            ->getQuery()->getResult();

            $parameters['aprobados'] = $em->getRepository(SociedadAnonima::class)->findBy(array('estado' => ConstanteEstadoFormulario::APROBADO, 
                                                                                                  'solicitante' => $this->getUser()));
            $parameters['rechazados'] = $em->getRepository(SociedadAnonima::class)->createQueryBuilder('sa')
            ->where('sa.plazoCorreccion >= 0 and sa.estado = :estado and sa.solicitante = :solicitante')
            ->setParameter('estado', ConstanteEstadoFormulario::RECHAZADO)
            ->setParameter('solicitante', $this->getUser())
            ->getQuery()->getResult();
            $parameters['pendientesCorreccion'] = [];
            foreach($parameters['rechazados'] as $key => $rechazado){
                
                if($rechazado->getPlazoCorreccion() > 0){
                    $fechaOriginal = clone $rechazado->getFechaCreacion();
                    $fechaOriginal->setTime(0, 0);
                    $fechaActualizada = clone $fechaOriginal;
                    $fechaActualizada = $fechaActualizada->add(new DateInterval('P' . $rechazado->getPlazoCorreccion() . 'D'));

                    $now = new DateTime();
                    $now->setTime(0,0);
                    if($fechaActualizada >= $now){
                        array_push($parameters['pendientesCorreccion'], $rechazado);
                        unset($parameters['rechazados'][$key]);
                    }
                }

            }
        }else if($this->isGranted('ROLE_LEGALES')){
            $parameters['pendientes'] = $em->getRepository(SociedadAnonima::class)->findBy(array('estado' => ConstanteEstadoFormulario::PENDIENTE_LEGALES));
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
