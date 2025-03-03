<?php

namespace App\Controller\API;

use App\Requests\CreateOrderRequest;
use App\Requests\UpdateOrderStatusRequest;
use App\Service\CheckoutService;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;
use App\Requests\GetOrderRequest;


class OrderController extends AbstractController
{
    private CheckoutService $checkoutService;
    private EntityManagerInterface $entityManager;

    private OrderService $orderService;

    public function __construct(CheckoutService $checkoutService, EntityManagerInterface $entityManager, OrderService $orderService)
    {
        $this->checkoutService = $checkoutService;
        $this->orderService = $orderService;
        $this->entityManager = $entityManager;
    }

    /**
     * Creates a new order based on the submitted data.
     *
     * @param Request $request The HTTP request containing order data in JSON format.
     * @param ValidatorInterface $validator The Symfony validator for validating the request object.
     *
     * @return JsonResponse Returns a JSON response with the created order details or validation errors.
     */

    public function createOrder(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Handle case where the request body is empty or invalid JSON
        if ($data === null || is_array($data)) {
            return $this->json(['errors' => 'Invalid JSON format. Expected a JSON object with "items".'], 400);
        }

        $requestDto = new CreateOrderRequest($data);

        $errors = $requestDto->validate($validator);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], 400);
        }

        try {
            $parsedItems = $requestDto->getParsedItems();
            $order = $this->checkoutService->processOrder($parsedItems);
            
            return $this->json([
                'order_id' => $order->getId(),
                'total_price' => $order->getTotalPrice(),
                'status' => 'created'
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Retrieves a list of orders with pagination and search functionality.
     *
     * @param Request $request The HTTP request containing pagination and search parameters.
     *
     * @return JsonResponse Returns a JSON response with the orders matching the criteria.
     */
    public function getOrders(Request $request, OrderService $orderService): JsonResponse
    {
        $filters = (new GetOrderRequest($request))->getFilters();
        $result = $orderService->getData($filters);

        return $this->json([
            "draw" => (int) $request->query->get('draw', 1),
            "recordsTotal" => $result["totalOrders"],
            "recordsFiltered" => $result["filteredOrders"],
            "data" => $result["data"]
        ]);
    }

    /**
     * Retrieves an order by its ID.
     * @param int $id The ID of the order to retrieve.
     * @return JsonResponse Returns a JSON response with the order details or an error if the order is not found.
     */

    public function getOrder(int $id): JsonResponse
    {
        $order = $this->entityManager->getRepository(Order::class)->find($id);
        if (!$order) {
            return $this->json(['error' => 'Order not found'], 404);
        }

        return $this->json([
            'order_id' => $order->getId(),
            'status' => $order->getStatusLabel(),
            'total_price' => $order->getTotalPrice(),
        ]);
    }

    /**
     * Updates the status of an existing order.
     * @param int $id The ID of the order to update.
     * @param Request $request The HTTP request containing the new status in JSON format.
     * @param ValidatorInterface $validator The Symfony validator for validating the request object.
     *
     * @return JsonResponse Returns a JSON response indicating whether the update was successful or errors encountered.
     */

    public function updateOrderStatus(int $id, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $order = $this->entityManager->getRepository(Order::class)->find($id);

        if (!$order === null) {
            return $this->json(['error' => 'Order not found'], 404);
        }
    
        $data = json_decode($request->getContent(), true);
        $requestDto = new UpdateOrderStatusRequest($data);
    
        $errors = $requestDto->validate($validator);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], 400);
        }
    
        $this->entityManager->beginTransaction(); // Start transaction
    
        try {
            $order->updateStatus($requestDto->getStatus());
            $this->entityManager->flush();
            $this->entityManager->commit(); // Commit transaction
    
            return $this->json([
                'message' => 'Order status updated successfully',
                'order_id' => $order->getId(),
                'new_status' => $order->getStatusLabel()
            ], 200);
        } catch (\Exception $e) {
            $this->entityManager->rollback(); // Rollback transaction if error
            return $this->json(['error' => 'Failed to update order status: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Retrieves the items belonging to a specific order.
     *
     * @param int $id The ID of the order whose items will be retrieved.
     *
     * @return JsonResponse Returns a JSON response with the items or an error if not found.
     */

    public function getItems(int $id): JsonResponse
    {
        try {
            $items = $this->orderService->getOrderItems($id);
            return $this->json(['items' => $items],200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Deletes an existing order.
     * @param int $id The ID of the order to delete.
     * @return JsonResponse Returns a JSON response indicating success or failure.
     */
    public function deleteOrder(int $id): JsonResponse
    {
        $order = $this->entityManager->getRepository(Order::class)->find($id);

        if (!$order) {
            return $this->json(['error' => 'Order not found'], 404);
        }

        $this->entityManager->beginTransaction(); // Start transaction

        try {
            $this->entityManager->remove($order);
            $this->entityManager->flush();
            $this->entityManager->commit(); // Commit transaction

            return $this->json([
                'message' => 'Order deleted successfully',
                'order_id' => $id
            ], 200);
        } catch (\Exception $e) {
            $this->entityManager->rollback(); // Rollback transaction if error
            return $this->json(['error' => 'Failed to delete order: ' . $e->getMessage()], 400);
        }
    }

}