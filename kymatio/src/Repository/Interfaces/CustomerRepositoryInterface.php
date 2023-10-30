<?php

namespace App\Repository\Interfaces;

use App\Entity\Customer;

interface CustomerRepositoryInterface
{
    public function getAllCustomers(): Array;
    public function getCustomer(int $id): ?Customer;
    public function save(Customer $customer): void;
    public function delete(int $id): void;
}