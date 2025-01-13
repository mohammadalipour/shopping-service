<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Enqueue\Client\Message;
use Interop\Queue\Processor;
use Psr\Log\LoggerInterface;

final class ProductEnabledUpdateService implements Processor
{
    public function __construct(private ProductRepository $productRepository, private LoggerInterface $logger)
    {
    }

    public function process(Message|\Interop\Queue\Message $message, \Interop\Queue\Context $context): string
    {
        try {
            $payload = json_decode($message->getBody(), true);

            $data = $payload['data'] ?? null;
            if (!$data || !isset($data['product_id']) || !isset($data['enabled'])) {
                throw new \InvalidArgumentException('Invalid message payload: missing "data" or "product_id".');
            }

            $productId = $data['product_id'];

            $this->productRepository->enableProduct($productId, $data['enabled']);

            $this->logger->info("Processed product message for product_id: {$productId}.");

            return Processor::ACK;
        } catch (\Exception $e) {
            $this->logger->error('Failed to process message: ' . $e->getMessage());
            return Processor::REJECT;
        }
    }
}