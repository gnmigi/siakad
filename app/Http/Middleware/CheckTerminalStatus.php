<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TerminalService;
use RealRashid\SweetAlert\Facades\Alert;

class CheckTerminalStatus
{
    protected $terminalService;

    public function __construct(TerminalService $terminalService)
    {
        $this->terminalService = $terminalService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$restrictedFeatures)
    {
        // Only check for mahasiswa guard
        if (!Auth::guard('mahasiswa')->check()) {
            return $next($request);
        }

        $mahasiswa = Auth::guard('mahasiswa')->user();

        // Skip terminal check for certain safe routes
        $currentRoute = $request->route() ? $request->route()->getName() : '';
        $safeRoutes = [
            'mahasiswa.dashboard-render',
            'mahasiswa.profile-render',
            'mahasiswa.profile-handle',
            'mahasiswa.payment.',
            'mahasiswa.payment-history',
            'mahasiswa.handle-logout',
            'mahasiswa.handle-logout-get'
        ];

        $isSafeRoute = false;
        foreach ($safeRoutes as $safeRoute) {
            if (strpos($currentRoute, $safeRoute) !== false) {
                $isSafeRoute = true;
                break;
            }
        }

        // Auto-check for terminal status based on unpaid SPP (but don't block safe routes)
        $this->terminalService->checkAutoTerminal($mahasiswa);

        // Reload mahasiswa data after potential terminal update
        $mahasiswa = \App\Models\Mahasantri::find($mahasiswa->id);

        // If student is terminated, only block access to restricted academic features
        if ($mahasiswa->isTerminal() && !$isSafeRoute) {
            $terminalInfo = $mahasiswa->getTerminalInfo();
            
            // Check if current request is for restricted features
            $isRestrictedRoute = $this->isRestrictedRoute($currentRoute, $restrictedFeatures);
            
            if ($isRestrictedRoute) {
                $message = 'Akses fitur akademik tidak tersedia. Anda sedang dalam status terminal.';
                if ($terminalInfo && $terminalInfo['reason']) {
                    $message .= ' Alasan: ' . $terminalInfo['reason'];
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'terminal_info' => $terminalInfo
                    ], 403);
                }

                Alert::warning('Informasi', $message);

                return redirect()->route('mahasiswa.dashboard-render')
                    ->with('error', $message)
                    ->with('alert-type', 'error')
                    ->with('alert-title', 'Akses Fitur Akademik Ditolak');
            }
        }

        return $next($request);
    }

    /**
     * Check if current route is restricted
     */
    protected function isRestrictedRoute($currentRoute, $restrictedFeatures)
    {
        if (empty($restrictedFeatures)) {
            // Default restricted features
            $restrictedFeatures = ['krs', 'khs', 'transkrip'];
        }

        foreach ($restrictedFeatures as $feature) {
            if (strpos($currentRoute, $feature) !== false) {
                return true;
            }
        }

        return false;
    }
}
