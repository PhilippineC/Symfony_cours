<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Category;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Form\AdvertType;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        if ($page < 1) {
            throw $this->createNotFoundException("La page ".$page." n'existe pas.");
        }

        $nbPerPage = 3;
        $listAdverts = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('OCPlatformBundle:Advert')
            ->getAdverts($page, $nbPerPage)
        ;

        // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
        $nbPages = ceil(count($listAdverts)/$nbPerPage);

        // Si la page n'existe pas, on retourne une 404
        if ($page > $nbPages) {
            throw $this->createNotFoundException("La page ".$page." n'existe pas.");
        }

        // On donne toutes les informations nécessaires à la vue
        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
            'listAdverts' => $listAdverts,
            'nbPages'     => $nbPages,
            'page'        => $page
        ));
    }

    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        // On récupère l'annonce $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if ($advert === null) {
            throw $this->createNotFoundException("L'annonce d'id ".$id." n'existe pas.");
        }
        $listApplications = $em
            ->getRepository('OCPlatformBundle:Advert')
            ->getAdvertWithApplications()
        ;

        // On récupère maintenant la liste des AdvertSkill
        $listAdvertSkills = $em
            ->getRepository('OCPlatformBundle:AdvertSkill')
            ->findBy(array('advert' => $advert))
        ;

        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
            'advert'           => $advert,
            'listAdvertSkills' => $listAdvertSkills
        ));
    }

    public function addAction(Request $request)
    {
        // On crée un objet Advert
        $advert = new Advert();

        // Ici, on préremplit avec la date d'aujourd'hui, par exemple
        $advert->setDate(new \Datetime());

        // On crée le Form grâce au service form factory
        $form = $this->createForm(new AdvertType(), $advert);

          // On vérifie que les valeurs entrées sont correctes
        // (Nous verrons la validation des objets en détail dans le prochain chapitre)
        if ($form->handleRequest($request)->isValid()) {
            $advert->getImage()->upload();
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            // On redirige vers la page de visualisation de l'annonce nouvellement créée
            return $this->redirect($this->generateUrl('oc_platform_view', array('id' => $advert->getId())));
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        // On récupère l'annonce $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if ($advert == null) {
            throw $this->createNotFoundException("L'annonce d'id ".$id." n'existe pas.");
        }

        // Et on construit le formBuilder avec cette instance de l'annonce, comme précédemment
        $formBuilder = $this->get('form.factory')->createBuilder('form', $advert);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('date',      'date')
            ->add('title',     'text')
            ->add('content',   'textarea')
            ->add('author',    'text')
            ->add('published', 'checkbox', array('required' => false)) //Par défaut les champs son requis, là on souhaite le mettre facultatif
            ->add('save',      'submit')
        ;



        // Ici, on s'occupera de la création et de la gestion du formulaire
        return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
            'advert' => $advert
        ));
    }

    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // On récupère l'annonce $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
        if (null === $advert) {
            throw $this->createNotFoundException("L'annonce d'id " . $id . " n'existe pas.");
        }

        if ($request->isMethod('POST')) {

            // Si la requête est en POST, on deletea l'article
            $request->getSession()->getFlashBag()->add('info', 'Annonce bien supprimée.');

            // Puis on redirige vers l'accueil
            return $this->redirect($this->generateUrl('oc_platform_home'));
        }
    }

    public function menuAction($limit)
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

    public function deletepurgeAction($days)
    {
     /*   $advertswithapplication = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('OCPlatformBundle:Advert')
            ->getAdvertWithApplications()
        ;*/
        $purge = $this->get('oc_platform.advert_purger');
     /*   $old_adverts = $purge->purge($days, $advertswithapplication);*/
        $purge->purge($days);
     /*   foreach($old_adverts as $old_advert)
        {
            $em = $this->getdoctrine()->getManager();
            $em->remove($old_advert);
            $em->flush();
        }*/
        return $this->redirect($this->generateUrl('oc_platform_home'));
    }
}