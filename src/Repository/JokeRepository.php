<?php

namespace App\Repository;

use App\Entity\Joke;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @method Joke|null find($id, $lockMode = null, $lockVersion = null)
 * @method Joke|null findOneBy(array $criteria, array $orderBy = null)
 * @method Joke[]    findAll()
 * @method Joke[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JokeRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    private $paginator;

    /**
     * JokeRepository constructor.
     *
     * @param ManagerRegistry $registry
     * @param EntityManagerInterface $manager
     */
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Joke::class);
        $this->manager = $manager;
        $this->paginator = $paginator;
    }

    public function getAll(int $page)
    {
        $allJokesQuery = $this->createQueryBuilder('j')
                              ->select('j.id, j.Joke')
                              ->getQuery();

        // Paginate the results of the query
        $jokes = $this->paginator->paginate(
            $allJokesQuery,
            $page,
            5
        );

        $data['totalCount'] = $jokes->getTotalItemCount();
        $data['currentPage'] = $jokes->getCurrentPageNumber();
        $data['itemsPerPage'] = $jokes->getItemNumberPerPage();
        $data['jokes'] = $jokes->getItems();

        return $data;
    }

    /**
     * saveJoke
     *
     * @param $joke string
     */
    public function saveJoke($joke)
    {
        $newJoke = new Joke();

        $newJoke->setJoke($joke);

        $this->manager->persist($newJoke);
        $this->manager->flush();
    }

    /**
     * @param $joke
     * @return Joke
     */
    public function updateJoke(Joke $joke)
    {
        $this->manager->persist($joke);
        $this->manager->flush();

        return $joke;
    }

    /**
     * deleteJoke
     *
     * @param $joke Joke
     */
    public function deleteJoke($joke)
    {
        $this->manager->remove($joke);
        $this->manager->flush();
    }

    /**
     * getRandom
     *
     * @return mixed
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getRandom()
    {
        // Get the count
        $qb = $this->createQueryBuilder('j');
        $count = $qb
            ->select('count(j.id)')
            ->getQuery()
            ->getSingleScalarResult();


        $qb = $this->createQueryBuilder('j');
        $joke = $qb->select('j.id, j.Joke')
                   ->setMaxResults(1)// This is like LIMIT
                   ->setFirstResult(rand(0, $count - 1))// This is like OFFSET
                   ->getQuery()
                   ->getSingleResult();

        // This is a bit wonky to me as it returns an array and not a Joke entity
        return $joke;
    }
}
