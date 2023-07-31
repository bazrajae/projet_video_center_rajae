<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\SearchData;

/**
 * @extends ServiceEntityRepository<Video>
 *
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    public function paginationQuery()
   {
       return $this->createQueryBuilder('v')
         
           ->orderBy('v.id', 'ASC')
         
           ->getQuery()
        
       ;
   }

   public function findBySearch(SearchData $searchData)
    {
       $data = $this->createQueryBuilder('p')
       ->addOrderBy('p.createdAt','DESC');
       if(!empty($searchData->q)){
         $data=$data
         ->andWhere("p.title LIKE :q OR p.description LIKE :q")
         
         ->setParameter('q',"%{$searchData->q}%");
       }
       $data=$data
       ->getQuery();
     //   ->getResult();
 
     //   $voitures =$this-> $paginator->paginate($data,$searchData->page,5);
 
       return $data;
    }

//    /**
//     * @return Video[] Returns an array of Video objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Video
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
