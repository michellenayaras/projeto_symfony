<?php

namespace App\Repository;

use App\Entity\Animal;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @extends ServiceEntityRepository<Animal>
 *
 * @method Animal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Animal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Animal[]    findAll()
 * @method Animal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnimalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Animal::class);
    }

    public function save(Animal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function remove(Animal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findAnimaisParaAbate()
    {
        $now = new DateTime('now');
        $sub5Years = $now->sub(new \DateInterval('P5Y'));
        $date5YearsAgo = $sub5Years->format('Y-m-d');

        return $this->createQueryBuilder('c')
            ->where('c.abate = :abate')
            ->andWhere(
                '(c.nascimento < :nascimento5YearsAgo)
            OR (c.leite < 40)
            OR (c.leite < 70 AND c.racao > 50)
            OR (c.peso > 18 * 32.5)' // 1 arroba = 32,5 kg
            )
            ->setParameter('nascimento5YearsAgo', $date5YearsAgo)
            ->setParameter('abate', false)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getAnimaisParaAbate()
    {
       
        $date = new DateTime();
        $limitDate = (new DateTime())->sub(new DateInterval('P5Y')); // subtrai 5 anos da data atual

        return $this->createQueryBuilder('a')
            ->where('a.abate = :abate')
            ->andWhere(
                '(a.nascimento <= :limitDate OR 
          a.leite < 40 OR 
          (a.leite >= 40 AND a.leite < 70 AND a.racao > 50) OR 
          a.peso > 18)'
            )
            ->setParameters([
                'abate' => false,
                'limitDate' => $limitDate->format('Y-m-d'),
            ])
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getAnimaisAbatidos()
    {
        return $this->createQueryBuilder('a')
            ->where('a.abate = :abate')
            ->setParameter('abate', true)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function calcularProducaoTotalLeite(): string
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('SUM(a.leite)')
            ->where('a.abate = :abate')
            ->setParameter('abate', false);

        $totalLitros = $qb->getQuery()->getSingleScalarResult();

        return number_format($totalLitros, 2, '.', '');
    }

    public function calcularRacao(): string
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('SUM(a.racao)')
            ->where('a.abate = :abate')
            ->setParameter('abate', false);

        $totalRacao = $qb->getQuery()->getSingleScalarResult();

        return number_format($totalRacao, 2, '.', '');
    }

    public function findAnimaisJovensConsumoAlto(): int
    {
        $now = new DateTime('now');
        $oneYearAgo = $now->sub(new \DateInterval('P1Y'))->format('Y-m-d');

        $qb = $this->createQueryBuilder('a');
        $qb->select('COUNT(a.id)')
            ->where('a.racao > 500')
            ->andWhere('a.nascimento >= :nascimento')
            ->andWhere('a.abate = :abate')
            ->setParameter('nascimento', $oneYearAgo)
            ->setParameter('abate', false);

        $totalAnimais = $qb->getQuery()->getSingleScalarResult();

        return $totalAnimais;
    }







    //    /**
    //     * @return Animal[] Returns an array of Animal objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Animal
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
