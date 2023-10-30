<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Repository\Interfaces\CustomerRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 *
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository implements CustomerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function getAllCustomers(): Array
    {
        return $this->findAll();
    }

    public function getCustomer(int $id): ?Customer
    {
        return $this->find($id);
    }

    public function save(Customer $customer): void
    {
        $this->_em->persist($customer);
        $this->_em->flush();
    }

    public function delete(int $id): void
    {
        $this->_em->remove($this->find($id));
        $this->_em->flush();
    }
}
