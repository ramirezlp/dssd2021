<?php

namespace App\Controller;

use App\Entity\Constants\ConstanteEstadoFormulario;
use Exception;
use App\Resources\Access;
use App\Entity\PaisEstado;
use App\Resources\Process;
use App\Entity\SociedadAnonima;
use App\Form\SociedadAnonimaType;
use App\Entity\SociedadAnonimaSocio;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class SociedadAnonimaController extends AbstractController
{
    /**
     * @Route("/solicitudSA", name="sociedad_anonima")
     */
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $em = $this->getDoctrine()->getManager();

        $sociedadAnonima = new SociedadAnonima();

        $form = $this->createForm(SociedadAnonimaType::class, $sociedadAnonima);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            // $form->getData() holds the submitted values
            // but, the original `$sociedadAnonima` variable has also been updated

            $brochureFile = $form->get('archivo')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('archivo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $sociedadAnonima->setArchivo($newFilename);
            }

            $paises = $sociedadAnonima->getPaisesEstados();

            $sociedadAnonima->setPaisesEstados(new ArrayCollection());
            $paisesList = "[";
            $i = 0;
            foreach($paises as $pais){
                $paisNew = new PaisEstado();
                if($pais->getPais() != null && $pais->getEstado() != null){
                    $entity = $em->getRepository(PaisEstado::class)->findOneBy(array('pais' => $pais->getPais(), 'estado' => $pais->getEstado()));
                    if($entity != null){
                        $sociedadAnonima->addPaisesEstados($entity);
                    }else{
                        $paisNew->setPais($pais->getPais());
                        $paisNew->setEstado($pais->getEstado());
                        $em->persist($paisNew);
                        $sociedadAnonima->addPaisesEstados($paisNew);
                    }
                    $i = $i + 1;
                }else{
                    if($pais->getPais() != null){
                        $entity = $em->getRepository(PaisEstado::class)->findOneBy(array('pais' => $pais->getPais(), 'estado' => null));
                        if($entity != null){
                            $sociedadAnonima->addPaisesEstados($entity);
                        }else{
                            $paisNew->setPais($pais->getPais());
                            $em->persist($paisNew);
                            $sociedadAnonima->addPaisesEstados($paisNew);
                        }
                        $i = $i + 1;
                    }
                }

                if($pais->getPais() != null){
                    $nombre = $pais->getPais();
                    $paisesList .= $pais->getPais() . (($pais->getEstado() != null) ? ' - ' . $pais->getEstado() : '') .  ', '; 
                }

            }
            $paisesList = rtrim($paisesList, ", ");
            $paisesList .= ']';

            if($i == 0){
                $entity = $em->getRepository(PaisEstado::class)->findOneBy(array('pais' => 'AR', 'estado' => null));
                if($entity != null){
                    $sociedadAnonima->addPaisesEstados($entity);
                }else{
                    $paisNew = new PaisEstado();
                    $paisNew->setPais('AR');
                    $em->persist($paisNew);
                    $sociedadAnonima->addPaisesEstados($paisNew);
                }
            }

            $socios = $sociedadAnonima->getSocios();
            $sociosNew = [];

            $sociosList = "[";
            foreach($socios as $socio){
                
                $saSocio = new SociedadAnonimaSocio();
                $saSocio->setSocio($socio);
                $saSocio->setSociedadAnonima($sociedadAnonima);
                $saSocio->setPorcentajeAporte($socio->getPorcentaje());
                $saSocio->setEsRepresentanteLegal($socio->getEsRepresentanteLegal());

                $em->persist($saSocio);
                $em->persist($socio);
                array_push($sociosNew, $saSocio);

                
                $sociosList .= $socio->getNombre() . ' - ' . $socio->getApellido() . ', '; 
                
            }
            $sociosList = rtrim($sociosList, ", ");
            $sociosList .= ']';

            $sociedadAnonima->setSocios($sociosNew);
            $sociedadAnonima->setSolicitante($this->getUser());

            $em->persist($sociedadAnonima);
            $em->flush();

            $this->completar_solicitud($sociedadAnonima->getNombre(),
            $sociedadAnonima->getDomicilioReal(),
            $sociedadAnonima->getDomicilioLegal(),$sociedadAnonima->getMail(),
            $sociedadAnonima->getArchivo(), 
            $paisesList, 
            $sociosList); 
            //falta paises, estados y socios
            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($sociedadAnonima);
            // $entityManager->flush();

            $sociedadAnonima = new SociedadAnonima();
            $form = $this->createForm(SociedadAnonimaType::class, $sociedadAnonima);

            $this->addFlash(
                'success',
                'Se guardó correctamente la Sociedad Anónima.'
            );

        }else{

            if($form->isSubmitted() && ! $form->isValid()){
                foreach($form->getErrors(true, false) as $error)
                {
                    // Do stuff with:
                    //   $error->getPropertyPath() : the field that caused the error
                    try{
                        $current = $error->current();
                        $message = $error->current()->getMessage();
                        $this->addFlash(
                            'danger',
                            $message
                        );
                    }catch(Exception $e){
                        echo $e;
                    }
                }

                $sociedadAnonima = new SociedadAnonima();
                $form = $this->createForm(SociedadAnonimaType::class, $sociedadAnonima);
            }
        }

        return $this->render('sociedad_anonima/registroSA.html.twig', [
            'controller_name' => 'SociedadAnonimaController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/rechazarSA", name="rechazar_sa")
     */
    public function rechazarSA(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $formulario = $em->getRepository(SociedadAnonima::class)->find($_GET['id']);

        if(isset($_GET['motivo']) && $_GET['motivo'] != ''){
            $formulario->setMotivoRechazo($_GET['motivo']);
        }
        if(isset($_GET['correccion']) && $_GET['correccion'] != ''){
            $formulario->setPlazoCorreccion($_GET['correccion']);
        }

        $formulario->setEstado(ConstanteEstadoFormulario::RECHAZADO);

        $em->flush();

        $this->addFlash(
            'success',
            'Se rechazó correctamente la Sociedad Anónima.'
        );

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/aprobarSA", name="aprobar_sa")
     */
    public function aprobarSA(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $formulario = $em->getRepository(SociedadAnonima::class)->find($_GET['id']);

        $formulario->setEstado(ConstanteEstadoFormulario::APROBADO);
        $formulario->setNumeroExpediente($formulario->getId());

        $em->flush();

        $this->addFlash(
            'success',
            'Se aprobó correctamente la Sociedad Anónima.'
        );

        return $this->redirectToRoute('homepage');
    }
        /**
     * @Route("/verSA", name="ver_sa")
     */
    public function verSA(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $formulario = $em->getRepository(SociedadAnonima::class)->find($_GET['id']);


        return $this->render('sociedad_anonima/detalle.html.twig', [
            'controller_name' => 'SociedadAnonimaController',
            'formulario' => $formulario
        ]);
    }


    function completar_solicitud($nombre, $domicilioReal, $domicilioLegal, $mail, $estatuto, $paisesestados, $socios){
        
        $access = Access::login();
        $client = $access['client'];
        $token = $access['token'];
        
        $_SESSION['userId'] = Process::getIdByUsername($_SESSION['user_bonita']);

        $_SESSION['processId'] = Process::getProcessId($client, "Proceso de registro de sociedad anonima");
        $_SESSION['caseId'] = Process::initializeProcess($token, $_SESSION['processId']);
        //$_SESSION['taskId'] = Process::getHumanTaskByCase($_SESSION['caseId'], "Registro de SA");

        Process::setVariableByCase($_SESSION['caseId'], 'nombre', $nombre, 'java.lang.String');
        Process::setVariableByCase($_SESSION['caseId'], 'domicilioReal', $domicilioReal, 'java.lang.String');
        Process::setVariableByCase($_SESSION['caseId'], 'domicilioLegal', $domicilioLegal, 'java.lang.String');
        Process::setVariableByCase($_SESSION['caseId'], 'mail', $mail, 'java.lang.String');
        Process::setVariableByCase($_SESSION['caseId'], 'archivo', $estatuto, 'java.lang.String');
        Process::setVariableByCase($_SESSION['caseId'], 'paisesestados', json_encode($paisesestados), 'java.lang.String');
        Process::setVariableByCase($_SESSION['caseId'], 'socios', json_encode($socios), 'java.lang.String');

        //return Process::completeActivity($_SESSION['taskId']);
        

        unset($_SESSION['cookie']);
        unset($_SESSION['client']);

        return true;
    }
}
