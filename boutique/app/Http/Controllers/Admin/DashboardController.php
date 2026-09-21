<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'nbProducts'    => Product::count(),
            'nbCategories'  => Category::count(),
            'nbUsers'       => User::count(),
            'nbOrders'      => Order::count(),
            'chiffre'       => Order::whereIn('status', ['payee', 'expediee', 'livree'])->sum('total'),
            'lastOrders'    => Order::with('user')->latest()->take(8)->get(),
            'ruptures'      => Product::where('stock', '<=', 3)->orderBy('stock')->take(8)->get(),
        ]);
    }
}
