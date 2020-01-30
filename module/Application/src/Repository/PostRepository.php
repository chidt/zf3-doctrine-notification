<?php
/**
 * Created by PhpStorm.
 * User: CHI-DT
 * Date: 19/07/2018
 * Time: 08:50
 */

namespace Application\Repository;


use Application\Entity\Post;
use Doctrine\ORM\EntityRepository;

// This is the custom repository class for Post entity
class PostRepository extends EntityRepository
{

    // Finds all published posts having any tag.
    public function findPostHavingAnyTag()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Post::class, 'p')
            ->join('p.tags', 't')
            ->where('p.status = ?1')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', Post::STATUS_PUBLISHED);

        $posts = $queryBuilder->getQuery()->getResult();

        return $posts;
    }

    // Finds all published posts having the given tag.
    public function findPostsByTag($tagName)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Post::class,'p')
            ->join('p.tags', 't')
            ->where('p.status = ?1')
            ->andWhere('t.name = ?2')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', Post::STATUS_PUBLISHED)
            ->setParameter('2', $tagName);

        $posts = $queryBuilder->getQuery()->getResult();

        return $posts;
    }
}