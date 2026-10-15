<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pengaturan\WebSetting;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\QueryException;
use Exception;

class PreventMultipleSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Check if WebSetting exists and if there's already a super admin (type 0)
            $webSetting = WebSetting::first();
            $superAdmin = User::where('type', 0)->first();
            
            // If both WebSetting and super admin exist, prevent access to setup
            if ($webSetting && $superAdmin) {
                return redirect()->route('auth.render-signin')
                    ->with('error', 'Sistem sudah dikonfigurasi. Silakan login untuk mengakses sistem.');
            }
        } catch (QueryException $e) {
            // If database error occurs, allow access to setup
            return $next($request);
        } catch (Exception $e) {
            // Handle any other exceptions, allow access to setup
            return $next($request);
        }

        return $next($request);
    }
}
