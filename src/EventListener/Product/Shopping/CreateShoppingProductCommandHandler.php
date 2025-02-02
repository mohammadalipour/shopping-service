<?php

namespace App\EventListener\Product\Shopping;

use App\Entity\Product;
use App\Entity\ProductStatus;
use Doctrine\ORM\EntityManagerInterface;
use Interop\Queue\Processor;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateShoppingProductCommandHandler
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(CreateShoppingProductCommand $command)
    {
        try {
            $payload = json_decode($command->content(), true);

            $data = $payload['data'] ?? null;
            if (!$data || !isset($data['id']) || !isset($data['enabled'])) {
                throw new \InvalidArgumentException('Invalid message payload: missing "data" or "product_id".');
            }
            $productId = $data['id'];
            $enabled = $data['enabled'];

            $product = new Product();
            $product->setProductId($productId)
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