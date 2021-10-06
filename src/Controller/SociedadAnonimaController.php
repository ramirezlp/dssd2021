<?php

namespace App\Controller;

use App\Entity\PaisEstado;
use App\Entity\SociedadAnonima;
use App\Form\SociedadAnonimaType;
use App\Entity\SociedadAnonimaSocio;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Resources\Access;
use App\Resources\Process;

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
            }
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

            foreach($socios as $socio){
                
                $saSocio = new SociedadAnonimaSocio();
                $saSocio->setSocio($socio);
                $saSocio->setSociedadAnonima($sociedadAnonima);
                $saSocio->setPorcentajeAporte($socio->getPorcentaje());
                $saSocio->setEsRepresentanteLegal($socio->getEsRepresentanteLegal());

                $em->persist($saSocio);
                $em->persist($socio);
                array_push($sociosNew, $saSocio);
            }

            $sociedadAnonima->setSocios($sociosNew);

            $em->persist($sociedadAnonima);
            $em->flush();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($sociedadAnonima);
            // $entityManager->flush();

            $sociedadAnonima = new SociedadAnonima();

            $this->addFlash(
                'success',
                'Se guardó correctamente la Sociedad Anónima.'
            );
            $this->completar_solicitud($sociedadAnonima->getNombre(),$sociedadAnonima->getDomicilioReal(),$sociedadAnonima->getDomicilioLegal(),$sociedadAnonima->getMail(),
            $sociedadAnonima->getArchivo()); //falta paises, estados y socios

            return $this->redirectToRoute('sociedad_anonima');
        }else{
            if($form->isSubmitted() && ! $form->isValid()){
                $this->addFlash(
                    'danger',
                    'Ocurrió un error en la carga de la SA.'
                );
            }
        }

        return $this->render('sociedad_anonima/index.html.twig', [
            'controller_name' => 'SociedadAnonimaController',
            'form' => $form->createView()
        ]);
    }

    function completar_solicitud($nombre, $domicilioReal, $domicilioLegal, $mail, $estatuto){
        Access::login();
        Process::setVariable($_SESSION['taskId'], 'nombre', $nombre, 'String');
        Process::setVariable($_SESSION['taskId'], 'domicilioReal', $domicilioReal, 'String');
        Process::setVariable($_SESSION['taskId'], 'domicilioLegal', $domicilioLegal, 'String');
        Process::setVariable($_SESSION['taskId'], 'mail', $mail, 'String');
        Process::setVariable($_SESSION['taskId'], 'estatuto', $estatuto, 'String');
        // Process::setVariable($_SESSION['taskId'], 'nombre', $nombre, 'String');
        // Process::setVariable($_SESSION['taskId'], 'nombre', $nombre, 'String');
        // Process::setVariable($_SESSION['taskId'], 'nombre', $nombre, 'String');
        return Process::completeActivity($_SESSION['taskId']);
    }
}
