<?php

// src/OC/PlatformBundle/Advert_purger/OCPurger.php

namespace OC\PlatformBundle\Advert_purger;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Repository;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\Null;

class OCPurger

{
    private $em;

    public function __construct($doctrine)
    {
        $this->em = $doctrine->getEntityManager();
    }

    public function purge($days)
    {
        $advertswithapplication = $this->em
            ->getRepository('OCPlatformBundle:Advert')
            ->getAdvertWithApplications();


        foreach ($advertswithapplication as $advert) {

            if (!(($advert->getApplications()) === null)) // Si il n'y a pas de candidatures associées
            {

                if (!($advert->getUpdatedAt() === null)) //Si l'annonce a été mise à jour
                {
                    $timestampdate = $advert->getUpdatedAt()->getTimestamp(); // Date de derniere mise à jour de datetime à timestamp
                } else //Sinon on prend la date de parution
                {
                    $timestampdate = $advert->getDate()->getTimestamp(); // Date de partution de datetime à timestamp
                }
                if (($timestampdate + ($days * 24 * 60 * 60)) < time()) // Si la date de derniere mise à jour est antérieure à X jours
                {
                    $this->em->remove($advert);
                }
            }
        }
        $this->em->flush();
    }
}