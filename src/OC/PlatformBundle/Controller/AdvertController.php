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
    // On ne sait pas combien de pages il y a
    // Mais on sait qu'une page doit être supérieure ou égale à 1
    if ($page < 1) {
      // On déclenche une exception NotFoundHttpException, cela va afficher
      // une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
      throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
    }

    // Ici, on récupérera la liste des annonces, puis on la passera au template

    // Mais pour l'instant, on ne fait qu'appeler le template




    return $this->render('OCPlatformBundle:Advert:index.html.twig', array('listAdverts' => array() ));
   
  }

  public function viewAction($id)
  {
    // On récupère le repository
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
    ;

    // On récupère l'entité correspondante à l'id $id
    $advert = $repository->find($id);

    // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
    // ou null si l'id $id  n'existe pas, d'où ce if :
    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // Le render ne change pas, on passait avant un tableau, maintenant un objet
    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
      'advert' => $advert
    ));
  }

  public function addAction(Request $request)
  {
    // Création de l'entité
    $advert = new Advert();
    $advert->setTitle('HelpX : La nouvelle tendance chez les backpakers !.');
    $advert->setAuthor('Alexandre');
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
                $image = new Image();
                $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
                $image->setAlt('Job de rêve');
                // On lie l'image à l'annonce
                $advert->setImage($image);
                $em->persist($image);
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
    // Ici, on récupérera l'annonce correspondant à $id

    // Ici, on gérera la suppression de l'annonce en question

    return $this->render('OCPlatformBundle:Advert:delete.html.twig');
  }

  public function menuAction($limit)
  {
    // On fixe en dur une liste ici, bien entendu par la suite
    // on la récupérera depuis la BDD !
    $listAdverts = array(
      array('id' => 2, 'title' => 'HelpX : La nouvelle tendance chez les backpakers !'),
      array('id' => 5, 'title' => 'Comment obtenir son billet à moindre prix !'),
      array('id' => 9, 'title' => 'Le paradis à petit prix ! ')
    );

    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
      // Tout l'intérêt est ici : le contrôleur passe
      // les variables nécessaires au template !
      'listAdverts' => $listAdverts
    ));
  }




}