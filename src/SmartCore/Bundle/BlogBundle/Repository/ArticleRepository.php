<?php

namespace SmartCore\Bundle\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SmartCore\Bundle\BlogBundle\Model\ArticleInterface;
use SmartCore\Bundle\BlogBundle\Model\CategoryInterface;
use SmartCore\Bundle\BlogBundle\Model\TagInterface;

class ArticleRepository extends EntityRepository
{
    /**
     * @param integer $limit
     * @return \SmartCore\Bundle\BlogBundle\Model\Article[]|null // @todo ArticleInterface
     */
    public function findLast($limit = null)
    {
        return $this->findBy([
            'enabled'    => true,
        ], [
            'created_at' => 'DESC',
            'id'         => 'DESC',
        ], $limit);
    }

    /**
     * @param TagInterface $tag
     * @return ArticleInterface[]|null
     */
    public function findByTag(TagInterface $tag)
    {
        $query = $this->_em->createQuery("
            SELECT a
            FROM {$this->_entityName} AS a
            JOIN a.tags AS t
            WHERE t = :tag
            AND a.enabled = true
            ORDER BY a.created_at, a.id DESC
        ")->setParameter('tag', $tag);

        return $query->getResult();
    }

    /**
     * @param CategoryInterface $category
     * @param null $offset
     * @param null $limit
     * @return ArticleInterface[]|null
     */
    public function findByCategory(CategoryInterface $category = null, $limit  = null, $offset = null)
    {
        $query = $this->_em->createQuery("
            SELECT a
            FROM {$this->_entityName} AS a
            WHERE a.enabled = true
            ORDER BY a.created_at, a.id DESC
        ")->setFirstResult($offset)
        ->setMaxResults($limit)
        ;

        return $query->getResult();
    }
    
    /**
     * @param CategoryInterface $category
     * @return integer
     *
     * @todo поддержку категорий.
     */
    public function getCountByCategory(CategoryInterface $category = null)
    {
        $query = $this->_em->createQuery("
            SELECT COUNT(a.id)
            FROM {$this->_entityName} a
            WHERE a.enabled = true
        ");

        return $query->getSingleScalarResult();
    }

    /**
     * @param TagInterface $tag
     * @return integer
     *
     * @todo возможность выбора по нескольким тэгам.
     */
    public function getCountByTag(TagInterface $tag)
    {
        $query = $this->_em->createQuery("
            SELECT COUNT(a.id)
            FROM {$this->_entityName} AS a
            JOIN a.tags AS t
            WHERE t = :tag
            AND a.enabled = true
        ")->setParameter('tag', $tag);

        return $query->getSingleScalarResult();
    }
}