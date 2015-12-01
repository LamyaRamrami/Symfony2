<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;




use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        $listAdverts= $this->getAll();
        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
            'listAdverts' =>  $listAdverts
        ));
    }




      private function getAll(){
        $em=$this->getDoctrine()
            ->getManager();
        $repository = $em->getRepository('OCPlatformBundle:Advert');
        $listAdverts= $repository->findAll();
        return $listAdverts;
    }
    private function getAdvert($id){
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('OCPlatformBundle:Advert')
        ;
        $advert = $repository->find($id);
    return $advert;
    }




  public function viewAction($id)
    {
        $advert=$this->getAdvert($id);
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }
        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
            'advert' => $advert
        ));
    }










  public function addAction(Request $request)
  {
    // Création de l'entité
    $advert = new Advert();
    $advert->setTitle('HelpX : La nouvelle tendance chez les backpakers !.');
    $advert->setAuthor('Alexandra');
    $advert->setContent("Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…");

    $formBuilder = $this->get('form.factory')->createBuilder('form', $advert);
    $formBuilder
                ->add('date','date')
                ->add('title','text')
                ->add('content','textarea')
                ->add('author','text')
                ->add('published','checkbox')
                ->add('save','submit');
      $form = $formBuilder->getForm();
      $formBuilder->add('published', 'checkbox', array('required' => false));
      $form->handleRequest($request);
    //
     //$advert->setDate("Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…");
    // On récupère l'EntityManager
    $em = $this->getDoctrine()->getManager();
     if ($form->isValid()) {
             
                $em->persist($advert);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
                return $this->redirect($this->generateUrl('oc_platform_view', array('id' => $advert->getId())));
      }
            return $this->render('OCPlatformBundle:Advert:add.html.twig', array('form' => $form->createView()));
  }



       public function editAction($id, Request $request)
    {
        $advert =$this->getAdvert($id);
            $formBuilder=$this->get('form.factory')->createBuilder('form', $advert);
        $formBuilder
            ->add('title','text')
            ->add('content','textarea')
            ->add('published','checkbox')
           
            ->add('save','submit');
        $form = $formBuilder->getForm();
        $formBuilder->add('published', 'checkbox', array('required' => false));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em=$this->getDoctrine()
                ->getManager();
            
            $em->persist($advert);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Le post a bien été enregistrée.');
            return $this->redirect($this->generateUrl('oc_platform_view', array('id' => $advert->getId())));
        }
        return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
            'advert' => $advert,'form' => $form->createView()
        ));
    }



  public function deleteAction($id)
  {
     $advert=$this->getAdvert($id);
        $em=$this->getDoctrine()
            ->getManager();
      
        $em->flush();

    return $this->render('OCPlatformBundle:Advert:delete.html.twig');
  }

    public function menuAction($limit = 5 )
  {
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
      ->findBy(
        array(),                 // Pas de critère
        array('date' => 'desc'), // On trie par date décroissante
        $limit,                  // On sélectionne $limit annonces
        0                        // À partir du premier
    );
    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
      'listAdverts' => $listAdverts
    ));
  }




}