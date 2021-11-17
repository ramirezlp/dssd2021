<?php

namespace App\Controller;

use CURLFile;
use Exception;
use App\Resources\Access;
use App\Entity\PaisEstado;
use App\Resources\Process;
use App\Entity\SociedadAnonima;
use App\Form\SociedadAnonimaType;
use App\Resources\ApiEstampillado;
use App\Entity\SociedadAnonimaSocio;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Constants\ConstanteEstadoFormulario;
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
                $sociosList .= $socio->getSocio()->getNombre() . ' - ' . $socio->getSocio()->getApellido() . ', '; 
                $em->persist($socio);
            }
            $sociosList = rtrim($sociosList, ", ");
            $sociosList .= ']';


            $sociedadAnonima->setSolicitante($this->getUser());

            $this->completar_solicitud($sociedadAnonima->getNombre(),
            $sociedadAnonima->getDomicilioReal(),
            $sociedadAnonima->getDomicilioLegal(),$sociedadAnonima->getMail(),
            $sociedadAnonima->getArchivo(), 
            $paisesList, 
            $sociosList); 
            $sociedadAnonima->setPlazoCorreccion(0);

            $sociedadAnonima->setCaseId($_SESSION['caseId']);
            $this->getUser()->setBonitaUserId($_SESSION['userId']);

            $em->persist($sociedadAnonima);
            $em->flush();


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

            return $this->redirectToRoute('homepage');

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
            'form' => $form->createView(),
            'edit' => false
        ]);
    }


    /**
     * @Route("/editSA", name="sociedad_anonima_edit")
     */
    public function editSA(Request $request, SluggerInterface $slugger): Response
    {
        $em = $this->getDoctrine()->getManager();

        $sociedadAnonima = $em->getRepository(SociedadAnonima::class)->find($_GET['id']);
        $archivo = $sociedadAnonima->getArchivo();
        /*
        $socios = [];
        foreach($sociedadAnonima->getSocios() as $socio){
            echo $socio->getId();
        }*/

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
            }else{
                $sociedadAnonima->setArchivo($archivo);
            }

            $paises = $sociedadAnonima->getPaisesEstados();

            $sociedadAnonima->setEstado(ConstanteEstadoFormulario::PENDIENTE);
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

            $sociosList = "[";
            foreach($socios as $socio){
                $sociosList .= $socio->getSocio()->getNombre() . ' - ' . $socio->getSocio()->getApellido() . ', '; 
                
            }
            $sociosList = rtrim($sociosList, ", ");
            $sociosList .= ']';

            $sociedadAnonima->setSolicitante($this->getUser());

            $this->actualizarDatosSA($sociedadAnonima,$sociedadAnonima->getNombre(),
            $sociedadAnonima->getDomicilioReal(),
            $sociedadAnonima->getDomicilioLegal(),$sociedadAnonima->getMail(),
            $sociedadAnonima->getArchivo(), 
            $paisesList, 
            $sociosList); 
            $sociedadAnonima->setPlazoCorreccion(0);

            $em->flush();

            $this->addFlash(
                'success',
                'Se editó correctamente la Sociedad Anónima.'
            );

            return $this->redirectToRoute('homepage');

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
            'form' => $form->createView(),
            'edit' => true
        ]);
    }

    /**
     * @Route("/rechazarSA", name="rechazar_sa")
     */
    public function rechazarSA(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $formulario = $em->getRepository(SociedadAnonima::class)->find($_GET['id']);

        if($this->isGranted('ROLE_LEGALES')){
            $username = $this->getUser()->getBonitaUser();
            $password = $this->getUser()->getBonitaPass();
            Access::login($username, $password);
            $taskId = Process::getHumanTaskByCase($formulario->getCaseId(), "Determinar validez trámite");
            sleep(2);
            Process::assignTask($taskId, $this->getUser()->getBonitaUserId());
            Process::setVariableByCase($formulario->getCaseId(), 'tramiteValido', "false", 'java.lang.Boolean');
            Process::completeActivity($taskId);
            unset($_SESSION['cookie']);
            unset($_SESSION['client']);

            $this->addFlash(
                'success',
                'Se rechazó correctamente el trámite. Queda pendiente de validación.'
            );
        }else{
            if(isset($_GET['motivo']) && $_GET['motivo'] != ''){
                $formulario->setMotivoRechazo($_GET['motivo']);
            }
            if(isset($_GET['correccion']) && $_GET['correccion'] != ''){
                $formulario->setPlazoCorreccion($_GET['correccion']);
            }
    
            $formulario->setEstado(ConstanteEstadoFormulario::RECHAZADO);
    
            $username = $this->getUser()->getBonitaUser();
            $password = $this->getUser()->getBonitaPass();
            Access::login($username, $password);
            $taskId = Process::getHumanTaskByCase($formulario->getCaseId(), "Validar informacion");
            sleep(2);
            Process::assignTask($taskId, $this->getUser()->getBonitaUserId());
            Process::setVariableByCase($formulario->getCaseId(), 'formularioValido', "false", 'java.lang.Boolean');
            sleep(2);
            Process::completeActivity($taskId);
            unset($_SESSION['cookie']);
            unset($_SESSION['client']);
    
            $em->flush();
    
            $this->addFlash(
                'success',
                'Se rechazó correctamente la Sociedad Anónima.'
            );
        }



        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/aprobarSA", name="aprobar_sa")
     */
    public function aprobarSA(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $formulario = $em->getRepository(SociedadAnonima::class)->find($_GET['id']);
        
        if($this->isGranted('ROLE_LEGALES')){
            $username = $this->getUser()->getBonitaUser();
            $password = $this->getUser()->getBonitaPass();
            $caseId = $formulario->getCaseId();
            Access::login($username, $password);
            $taskId = Process::getHumanTaskByCase($formulario->getCaseId(), "Determinar validez trámite");
            sleep(2);
    
            $token = ApiEstampillado::login($username, $password);
            $hash = ApiEstampillado::estampillar($token, $formulario->getNumeroExpediente(), $formulario->getArchivo());
            $qr = ApiEstampillado::getQr($hash);

            $fp = fopen("uploads/qr-" . $hash .".png",'w');
            fwrite($fp, $qr);
            fclose($fp);

            $formulario->setHash($hash);
            $formulario->setQr("qr-" . $hash .".png");
            $formulario->setEstado(ConstanteEstadoFormulario::PENDIENTE_GENERACION_CARPETAS);
            

            Process::assignTask($taskId, $this->getUser()->getBonitaUserId());
            Process::setVariableByCase($formulario->getCaseId(), 'tramiteValido', "true", 'java.lang.Boolean');
            Process::completeActivity($taskId);

            $em->flush();

            unset($_SESSION['cookie']);
            unset($_SESSION['client']);

            $this->addFlash(
                'success',
                'Se validó correctamente la Sociedad Anónima.'
            );
        }else{
            $qb = $em->getRepository(SociedadAnonima::class)->createQueryBuilder('sa')
            ->select('count(sa.id)')
            ->where('sa.estado = :estado or sa.estado = :estado2 or sa.estado = :estado3 or sa.estado = :estado4 or sa.estado = :estado5')
            ->setParameter('estado', ConstanteEstadoFormulario::PENDIENTE_LEGALES)
            ->setParameter('estado2', ConstanteEstadoFormulario::APROBADO)
            ->setParameter('estado3', ConstanteEstadoFormulario::PENDIENTE_GENERACION_CARPETAS)
            ->setParameter('estado4', ConstanteEstadoFormulario::PENDIENTE_RETIRO_DOCUMENTACION)
            ->setParameter('estado5', ConstanteEstadoFormulario::PENDIENTE)
            ->getQuery()
            ->getSingleScalarResult();
    
            $formulario->setEstado(ConstanteEstadoFormulario::PENDIENTE_LEGALES);
            $formulario->setNumeroExpediente($qb + 1);
    
            $username = $this->getUser()->getBonitaUser();
            $password = $this->getUser()->getBonitaPass();
            $caseId = $formulario->getCaseId();
            Access::login($username, $password);
            $taskId = Process::getHumanTaskByCase($caseId, "Validar informacion");
            sleep(2);
            Process::assignTask($taskId, $this->getUser()->getBonitaUserId());
            Process::setVariableByCase($caseId, 'formularioValido', "true", 'java.lang.Boolean');
            Process::completeActivity($taskId);
            unset($_SESSION['cookie']);
            unset($_SESSION['client']);
    
            $em->flush();
    
            $this->addFlash(
                'success',
                'Se aprobó correctamente la Sociedad Anónima.'
            );
        }



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

    /**
     * @Route("/verSAQR", name="ver_sa_qr")
     */
    public function verSAQR(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $formulario = $em->getRepository(SociedadAnonima::class)->findOneBy(array('hash' => $_GET['hash']));


        return $this->render('sociedad_anonima/detalleQR.html.twig', [
            'controller_name' => 'SociedadAnonimaController',
            'formulario' => $formulario
        ]);
    }

    /**
     * @Route("/generar_carpetas_sa", name="generar_carpetas_sa")
     */
    public function generarCarpetasSa(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $formulario = $em->getRepository(SociedadAnonima::class)->findOneBy(array('id' => $_GET['id']));

        $formulario->setEstado(ConstanteEstadoFormulario::PENDIENTE_RETIRO_DOCUMENTACION);

        $username = $this->getUser()->getBonitaUser();
        $password = $this->getUser()->getBonitaPass();
        $caseId = $formulario->getCaseId();
        Access::login($username, $password);
        $taskId = Process::getHumanTaskByCase($caseId, "Generar carpetas");
        sleep(2);
        Process::assignTask($taskId, $this->getUser()->getBonitaUserId());
        Process::completeActivity($taskId);
        unset($_SESSION['cookie']);
        unset($_SESSION['client']);

        $em->flush();

        $this->addFlash(
            'success',
            'Se generaron correctamente las carpetas de la SA.'
        );

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/retiro_documentacion_sa", name="retiro_documentacion_sa")
     */
    public function retiroDocumentacionSa(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $formulario = $em->getRepository(SociedadAnonima::class)->findOneBy(array('id' => $_GET['id']));

        $formulario->setEstado(ConstanteEstadoFormulario::APROBADO);

        $username = $this->getUser()->getBonitaUser();
        $password = $this->getUser()->getBonitaPass();
        $caseId = $formulario->getCaseId();
        Access::login($username, $password);
        $taskId = Process::getHumanTaskByCase($caseId, "Retiro de documentación");
        sleep(2);
        Process::assignTask($taskId, $this->getUser()->getBonitaUserId());
        Process::completeActivity($taskId);
        unset($_SESSION['cookie']);
        unset($_SESSION['client']);

        $em->flush();

        $this->addFlash(
            'success',
            'Se confirmó correctamente el retiro de la documentación de la SA. Proceso de alta finalizado.'
        );

        return $this->redirectToRoute('homepage');
    }

    
    

    function completar_solicitud($nombre, $domicilioReal, $domicilioLegal, $mail, $estatuto, $paisesestados, $socios){
        
        $access = Access::login($this->getUser()->getBonitaUser(), $this->getUser()->getBonitaPass());
        $client = $access['client'];
        $token = $access['token'];
      
        $_SESSION['processId'] = Process::getProcessId($client, "Proceso de registro de sociedad anonima");
        $_SESSION['caseId'] = Process::initializeProcess($token, $_SESSION['processId']);
        $_SESSION['userId'] = Process::getIdByUsername($_SESSION['user_bonita']);

        sleep(2);
        $_SESSION['taskId'] = Process::getHumanTaskByCase($_SESSION['caseId'], "Registro de SA");

        Process::setVariableByCase($_SESSION['caseId'], 'nombre', $nombre, 'java.lang.String');
        Process::setVariableByCase($_SESSION['caseId'], 'domicilioReal', $domicilioReal, 'java.lang.String');
        Process::setVariableByCase($_SESSION['caseId'], 'domicilioLegal', $domicilioLegal, 'java.lang.String');
        Process::setVariableByCase($_SESSION['caseId'], 'mail', $mail, 'java.lang.String');
        Process::setVariableByCase($_SESSION['caseId'], 'archivo', $estatuto, 'java.lang.String');
        Process::setVariableByCase($_SESSION['caseId'], 'paisesestados', json_encode($paisesestados), 'java.lang.String');
        Process::setVariableByCase($_SESSION['caseId'], 'socios', json_encode($socios), 'java.lang.String');

        Process::assignTask($_SESSION['taskId'], $_SESSION['userId']);

        Process::completeActivity($_SESSION['taskId']);
        //return Process::completeActivity($_SESSION['taskId']);
        

        unset($_SESSION['cookie']);
        unset($_SESSION['client']);

        return $_SESSION['caseId'];
    }

    function actualizarDatosSA($sociedadAnonima, $nombre, $domicilioReal, $domicilioLegal, $mail, $estatuto, $paisesestados, $socios){

        Access::login($this->getUser()->getBonitaUser(), $this->getUser()->getBonitaPass());

        Process::setVariableByCase($sociedadAnonima->getCaseId(), 'nombre', $nombre, 'java.lang.String');
        Process::setVariableByCase($sociedadAnonima->getCaseId(), 'domicilioReal', $domicilioReal, 'java.lang.String');
        Process::setVariableByCase($sociedadAnonima->getCaseId(), 'domicilioLegal', $domicilioLegal, 'java.lang.String');
        Process::setVariableByCase($sociedadAnonima->getCaseId(), 'mail', $mail, 'java.lang.String');
        Process::setVariableByCase($sociedadAnonima->getCaseId(), 'archivo', $estatuto, 'java.lang.String');
        Process::setVariableByCase($sociedadAnonima->getCaseId(), 'paisesestados', json_encode($paisesestados), 'java.lang.String');
        Process::setVariableByCase($sociedadAnonima->getCaseId(), 'socios', json_encode($socios), 'java.lang.String');

        $taskId = Process::getHumanTaskByCase($sociedadAnonima->getCaseId(), "Corrección de datos");
        sleep(2);
        Process::assignTask($taskId, $this->getUser()->getBonitaUserId());
        Process::completeActivity($taskId);
        sleep(2);
        $taskId = Process::getHumanTaskByCase($sociedadAnonima->getCaseId(), "Registro de SA");
        sleep(2);
        Process::assignTask($taskId, $this->getUser()->getBonitaUserId());
        Process::completeActivity($taskId);

        unset($_SESSION['cookie']);
        unset($_SESSION['client']);

        return true;

    }
}
