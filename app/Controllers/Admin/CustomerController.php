<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\CustomerRepository;
use App\Services\CustomerService;
use App\Support\DatabaseFactory;

/**
 * Admin customer and lead overview.
 */
class CustomerController extends BaseController
{
    private CustomerService $customers;

    public function __construct()
    {
        $this->customers = new CustomerService(new CustomerRepository(DatabaseFactory::make()));
    }

    public function index(): string
    {
        $customers = $this->customers->all($_GET);

        return $this->view('admin/customers/index', [
            'title' => 'Customers',
            'user' => $this->user(),
            'customers' => $customers,
            'metrics' => $this->customers->metrics($customers),
            'filters' => $_GET,
            'breadcrumbs' => [['label' => 'Customers']],
        ]);
    }

    public function export(): void
    {
        $customers = $this->customers->all($_GET);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="customers-' . date('Y-m-d') . '.csv"');

        $handle = fopen('php://output', 'w');
        fputcsv($handle, [
            'Name',
            'Email',
            'Phone',
            'Company',
            'Orders',
            'Total Spent',
            'Inquiries',
            'Newsletter',
            'Last Activity',
        ]);

        foreach ($customers as $customer) {
            fputcsv($handle, [
                $customer['name'],
                $customer['email'],
                $customer['phone'],
                $customer['company'],
                $customer['order_count'],
                number_format((float) $customer['total_spent'], 2, '.', ''),
                $customer['inquiry_count'],
                $customer['newsletter_status'],
                $customer['last_activity_at'],
            ]);
        }

        fclose($handle);
        exit;
    }
}
