<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class Localization {

    /**
     * Handle an incoming request.
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $locale = $request->segment(1);

        $languages = config('locale')['languages'];

        $activeLocale = config('appconfig.locale') ?? config('app.fallback_locale');

        if ($activeLocale !== $locale) {
            $request->session()->reflash();

            $urlParts = explode('/', $request->route()->uri());
            $urlParts[0] = $activeLocale;

            return redirect(implode('/', $urlParts));
        }

        if (!array_key_exists($locale, $languages)) {
            return redirect('/'.config('app.fallback_locale'));
        }
        App::setLocale($locale);

        return $next($request);
    }
}
