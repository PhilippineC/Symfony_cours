<?php

namespace OC\PlatformBundle\Repository;

/**
 * AdvertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use OC\PlatformBundle\Entity\Advert;

class AdvertRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAdverts($page, $nbPerPage)
    {
        $query = $this
            ->createQueryBuilder('a')
            ->leftjoin('a.image', 'i')
            ->addSelect('i')
            ->leftJoin('a.categories', 'c')
            ->addSelect('c')
            ->leftJoin('a.advertskills' , 'advsk')
            ->addSelect('advsk')
            ->leftJoin('advsk.skill' , 'sk')
            ->addSelect('sk')
            ->orderBy('a.date', 'DESC')
            ->getQuery()
            ;
        $query
            // On définit l'annonce à partir de laquelle commencer la liste
            ->setFirstResult(($page-1) * $nbPerPage)
            // Ainsi que le nombre d'annonce à afficher sur une page
            ->setMaxResults($nbPerPage)
        ;
        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        return new Paginator($query, true);
    }

    public function getAdvertWithApplications()
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->leftJoin('a.applications', 'app')
            ->addSelect('app')
        ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function getAdvertWithCategories(array $categoryNames)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->join('a.categories', 'c');
        $qb->addSelect('c');
        $qb->where($qb->expr()->in('c.name', $categoryNames));

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function getPublishedQueryBuilder()
    {// attention cette fonction retourne un querybuilder et non une query ou un resulat !
        return $this
            ->createQueryBuilder('a')
            ->where('a.published = :published')
            ->setParameter('published', true)
            ;
    }

}
