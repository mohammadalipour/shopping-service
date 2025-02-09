<?php

namespace App\EventListener\Category\Shopping;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Interop\Queue\Processor;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateShoppingCategoryCommandHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(CreateShoppingCategoryCommand $command)
    {
        try {
            $payload = json_decode($command->content(), true);

            $data = $payload['data'] ?? null;
            if (!$data || !isset($data['id']) || !isset($data['enabled'])) {
                throw new \InvalidArgumentException('Invalid message payload: missing "data" or "category_id".');
            }
            $productId = $data['id'];
            $enabled = $data['enabled'];

            $product = new Category();
            $product->setCategoryId($productId)
                ->setResult($data)
                ->setEnabled($enabled);

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return Processor::ACK;
        } catch (\Exception $e) {
            return Processor::REJECT;
        }
    }
}