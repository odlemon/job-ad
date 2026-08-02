<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiscoveryController extends Controller
{
    /**
     * In-portal Job Discovery (Bolt /job-seeker discovery module).
     */
    public function index(Request $request): View
    {
        return view('job-seeker.discovery', [
            'initialTab' => $request->get('tab', 'discover') === 'saved' ? 'saved' : 'discover',
            'initialQuery' => (string) $request->get('q', $request->get('keyword', '')),
            'initialLocation' => (string) $request->get('location', ''),
        ]);
    }
}
