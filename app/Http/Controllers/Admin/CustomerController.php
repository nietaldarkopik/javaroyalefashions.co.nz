<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
    ) {}

    public function index(Request $request): View
    {
        return view('admin.customers.index', [
            'customers' => $this->customers->paginateForAdmin($request->only('search')),
        ]);
    }

    public function show(Customer $customer): View
    {
        return view('admin.customers.show', [
            'customer' => $this->customers->findById($customer->id),
        ]);
    }
}
