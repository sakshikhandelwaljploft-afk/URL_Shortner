<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $mode = config('shortener.mode', 'literal');

        $stats = [
            'totalCompanies' => Company::count(),
            'totalUsers' => User::count(),
            'totalUrls' => ShortUrl::count(),
            'myUrlsCount' => $user->company_id ? ShortUrl::where('company_id', $user->company_id)->count() : 0,
        ];

        return view('dashboard', compact('user', 'mode', 'stats'));
    }
}

