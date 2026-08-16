<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Services\BillingService;
use Illuminate\View\View;

class BillingController extends Controller
{
    use HasOrganizationScoping;

    public function index(BillingService $billing): View
    {
        $overview = $billing->overview($this->organization());

        return view('billing.index', compact('overview'));
    }
}
