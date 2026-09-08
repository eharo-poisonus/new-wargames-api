<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Domain\Aggregate\AggregateRoot;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Symfony\Component\HttpFoundation\Response;

readonly class DoctrineRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    protected function persist(AggregateRoot $entity): void
    {
        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch (OrmException $e) {
            throw new Exception($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function remove(AggregateRoot $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    protected function repository(string $entityClass): EntityRepository
    {
        return $this->entityManager->getRepository($entityClass);
    }

    protected function entityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }
}
