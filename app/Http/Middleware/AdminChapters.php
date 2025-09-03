<?php

namespace App\Http\Middleware;

use App\Models\Chapter;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class AdminChapters
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return Redirect::route('admin.auth.login')->with('error', 'YOU ARE NOT ALLOWED TO ACCESS THIS PAGE');
        } else {
            $user = Auth::user();

            $chapter = $request->query->get('chapter');
            if (!$chapter) {
                abort(404, 'PAGE REQUESTED NOT FOUND');

            }
            $chapter = Chapter::where('name', '=', e($chapter))->first();
            if (!$user->hasAnyRole('admin', 'team-lead', 'lead_assist', 'unit_head') && !$chapter && $user->chapter_id != $chapter?->id) {
                abort(404, 'PAGE REQUESTED NOT FOUND');
            }

        }
        return $next($request);
    }
}
