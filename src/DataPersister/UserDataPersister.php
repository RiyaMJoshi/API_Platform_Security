<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Psr\Log\LoggerInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private $decoratedDataPersister;

    private $userPasswordHasher;

    private $logger;

    public function __construct(DataPersisterInterface $decoratedDataPersister, UserPasswordHasherInterface $userPasswordHasher, LoggerInterface $logger)
    {
        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->logger = $logger;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     */
    public function persist($data, array $context = [])
    {
        //dump($context);
        if (($context['item_operation_name'] ?? null) === 'put') {
            $this->logger->info(sprintf('User %s is being updated', $data->getId()));
        }
        if (!$data->getId()) {
            // take any actions needed for a new user
            // send registration email
            // integrate into some CRM or payment system
            $this->logger->info(sprintf('User %s jsust registered! Eureka!!', $data->getEmail()));
        }
        if ($data->getPlainPassword()) {
            $data->setPassword(
                $this->userPasswordHasher->hashPassword($data, $data->getPlainPassword())
            );
            $data->eraseCredentials();
        }
        $this->decoratedDataPersister->persist($data);
    }
    public function remove($data, array $context = [])
    {
        $this->decoratedDataPersister->remove($data);
    }
}
?>