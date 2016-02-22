<?php
// src/OC/PlatformBundle/DataFixtures/ORM/LoadApplication.php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Application;

class LoadApplication implements FixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        // Liste des candidatures
        $listApplication = array(
            array(
                'author'  => 'Alexandre',
                'content' => 'Je suis un super candidat',
                'date'    => new \Datetime()),
            array(
                'author'  => 'Charlie',
                'content' => 'Je suis le meilleur dans ce domaine',
                'date'    => new \Datetime()),
            array(
                'author'  => 'Camille',
                'content' => 'Prenez moi je veux ce job !',
                'date'    => new \Datetime()),
        );

        foreach ($listApplication as $Application) {
            $application = new Application();
            $application->setAuthor($Application['author']);
            $application->setContent($Application['content']);
            $application->setDate($Application['date']);

            // On la persiste
            $manager->persist($application);
        }

        // On déclenche l'enregistrement de toutes les catégories
        $manager->flush();
    }
}