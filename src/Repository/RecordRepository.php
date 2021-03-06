<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Record;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Record|null find($id, $lockMode = null, $lockVersion = null)
 * @method Record|null findOneBy(array $criteria, array $orderBy = null)
 * @method Record[]    findAll()
 * @method Record[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecordRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface $manager */
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Record::class);
        $this->manager = $manager;
    }

    public function saveRecord(string $name, string $artist, string $description): void
    {
        $newRecord = (new Record())
            ->setName($name)
            ->setDescription($description)
            ->setArtist($artist)
            ->setCreatedAt(new DateTime('now'));

        $this->manager->persist($newRecord);
        $this->manager->flush();
    }

    public function findByAsArray(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): array
    {
        $recordList = $this->findBy(
            $criteria,
            $orderBy,
            $limit,
            $offset
        );

        $list = [];
        /** @var Record $record */
        foreach ($recordList as $record) {
            $list[] = $record->toArray();
        }

        return $list;
    }

    public function searchRecords(string $query, int $limit = null, int $offset = null): array
    {
        $recordList = $this->createQueryBuilder('r')
            ->orWhere('r.name LIKE :query')
            ->orWhere('r.artist LIKE :query')
            ->orWhere('r.description LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->addOrderBy('r.artist', 'ASC')
            ->addOrderBy('r.name', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->execute();

        $list = [];
        /** @var Record $record */
        foreach ($recordList as $record) {
            $list[] = $record->toArray();
        }

        return $list;
    }

    public function update(Record $record): void
    {
        $this->manager->persist($record);
        $this->manager->flush();
    }
}
