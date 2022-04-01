<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    // /**
    //  * @return Game[] Returns an array of Game objects
    //  */
    
    
    public function findBySearch($value)
    {
        return $this->createQueryBuilder('g') // fait le SELECT * FROM game et va renvoyer ts les champs (objets) de game // $this represente l'objet courant de la classe
            ->andWhere('g.title LIKE :val')
            ->setParameter('val', '%' . $value . '%') // setParameter est l'équivalent du BindValue:  // on passe la valeur qu'on veut donner au bindvalue en argt de la fonction
            ->orderBy('g.id', 'ASC')
            // ->setMaxResults(10) // equivalent à LIMIT
            ->getQuery() // doit tjrs apparaître ds la fonction, équivalent à prepare()
            ->getResult() // doit tjrs apparaître ds la fonction, équivalent à FetchAll()
        ;
    }
    

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Game $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Game $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    

    /*
    public function findOneBySomeField($value): ?Game
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
