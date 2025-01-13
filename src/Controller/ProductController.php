<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    #[Route('/api/v1/products/{id}', methods: ['GET'])]
    public function index(ProductRepository $productRepository, int $id): JsonResponse
    {
        $product = $productRepository->findByProductId($id);

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $result = $product->getResult();

        $response = [
            'id' => $product->getId(),
            'name' => $result['name'],
            'price' => $result['price'],
            'description' => $result['description'],
            'image' => $result['image'],
        ];

        return new JsonResponse($response);
    }

    #[Route('/api/v1/products', methods: ['GET'])]
    public function list(ProductRepository $productRepository, Request $request): JsonResponse
    {
        $page = (int) $request->query->get('page', 1);
        $itemsPerPage = (int) $request->query->get('itemsPerPage', 5);

        $offset = ($page - 1) * $itemsPerPage;

        $qb = $productRepository->createQueryBuilder('p');
        $qb->andWhere('p.enabled = :enabled')
            ->setParameter('enabled', true);
        $qb->setFirstResult($offset)
            ->setMaxResults($itemsPerPage);

        $paginator = new Paginator($qb, true);
        $totalItems = count($paginator);

        $products = $paginator->getIterator()->getArrayCopy();

        $response = [
            'data' => $this->serializeProducts($products),
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
            'totalItems' => $totalItems,
            'totalPages' => ceil($totalItems / $itemsPerPage),
        ];

        return $this->json($response);
    }

    private function serializeProducts(array $products): array
    {
        return array_map(function ($product) {
            $result = $product->getResult();
            return [
                'id' => $product->getId(),
                'name' => $result['name'],
                'price' => $result['price'],
                'image' => $result['image'],
            ];
        }, $products);
    }
}