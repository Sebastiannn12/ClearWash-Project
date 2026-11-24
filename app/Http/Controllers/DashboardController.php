<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use App\TransactionStatus;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'totalMembers' => Member::count(),
            'activeMembers' => Member::where('is_active', true)->count(),
            'pendingTransactions' => MembershipTransaction::where('status', TransactionStatus::Pending->value)->count(),
            'totalCollected' => Payment::sum('amount'),
            'recentTransactions' => MembershipTransaction::with('member')->latest()->orderByDesc('id')->limit(5)->get(),
        ]);
    }
}
