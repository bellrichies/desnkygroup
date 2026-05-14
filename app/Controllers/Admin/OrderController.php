<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\OrderRepository;
use App\Services\MailService;
use App\Services\OrderService;
use App\Support\DatabaseFactory;
use Throwable;

/**
 * Admin order listing, status workflow and CSV export.
 */
class OrderController extends BaseController
{
    private OrderService $orders;

    public function __construct()
    {
        $this->orders = new OrderService(new OrderRepository(DatabaseFactory::make()));
    }

    public function index(): string
    {
        return $this->view('admin/orders/index', [
            'title' => 'Orders',
            'user' => $this->user(),
            'orders' => $this->orders->all($_GET),
            'statuses' => $this->orders->statuses(),
            'filters' => $_GET,
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [['label' => 'Orders']],
        ]);
    }

    public function show(string $id): string
    {
        $order = $this->orders->findWithItems((int) $id);
        if ($order === null) {
            $this->abort(404, 'Order not found.');
        }

        return $this->view('admin/orders/show', [
            'title' => 'Order ' . $order['order_number'],
            'user' => $this->user(),
            'order' => $order,
            'statuses' => $this->orders->statuses(),
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [['label' => 'Orders', 'url' => '/admin/orders'], ['label' => $order['order_number']]],
        ]);
    }

    public function status(string $id): void
    {
        try {
            $this->orders->updateStatus((int) $id, (string) ($_POST['status'] ?? 'pending'));
            $order = $this->orders->findWithItems((int) $id);
            if ($order !== null) {
                (new MailService())->sendOrderStatusNotification($order);
            }
            $this->flash('success', 'Order status updated.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/orders/' . (int) $id);
    }

    public function refund(string $id): void
    {
        try {
            $this->orders->refund((int) $id, (string) ($_POST['refund_notes'] ?? ''));
            $this->flash('success', 'Refund recorded and order cancelled.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/orders/' . (int) $id);
    }

    public function export(): void
    {
        $orders = $this->orders->all($_GET);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="orders-' . date('Y-m-d') . '.csv"');

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Order Number', 'Customer', 'Email', 'Status', 'Payment', 'Total', 'Created']);

        foreach ($orders as $order) {
            fputcsv($handle, [
                $order['order_number'],
                $order['customer_name'],
                $order['customer_email'],
                $order['order_status'],
                $order['payment_status'],
                $order['total'],
                $order['created_at'],
            ]);
        }

        fclose($handle);
        exit;
    }
}
