<?php

namespace App\DataPersister;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;

class UserDataPersister implements DataPersisterInterface
{
    private $decoratedDataPersister;

    private $userPasswordHasher;

    public function __construct(DataPersisterInterface $decoratedDataPersister, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function supports($data): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     */
    public function persist($data)
    {
        if ($data->getPlainPassword()) {
            $data->setPassword(
                $this->userPasswordHasher->hashPassword($data, $data->getPlainPassword())
            );
            $data->eraseCredentials();
        }
        $this->decoratedDataPersister->persist($data);
    }
    public function remove($data)
    {
        $this->decoratedDataPersister->remove($data);
    }
}
?>