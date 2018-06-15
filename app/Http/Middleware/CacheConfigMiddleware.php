<?php

namespace App\Http\Middleware;

use Closure;

class CacheConfigMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!session()->has('cache_keys'))
        {
            $cache_keys = \App\Argo\DynamicConfig::where('cache_enabled', '=', 1)->pluck('key')->all();
            session(['cache_keys' => $cache_keys]);
        }

        if(session()->has('cache_keys'))
        {
            $query_configs = [];
            foreach (session()->get('cache_keys') as $key) {
                if(!session()->has($key))
                {
                    if(count($query_configs) == 0)
                    {
                        $query_configs = \App\Argo\DynamicConfig::whereIn('key', session()->get('cache_keys'))->get();

                        $cache_configs = [];
                        foreach ($query_configs as $query_config) {
                            $cache_configs[$query_config->key] = $query_config->value;
                        }
                    }

                    session([$key => $cache_configs[$key]]);
                }
            }
        }

        return $next($request);
    }
}
