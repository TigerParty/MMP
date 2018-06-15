<?php

namespace App\Http\Middleware;

use Closure;
use Log;

class EncryptAfterMiddleware
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
        $response = $next($request);

        $keyFile = fopen(storage_path(config('argodf.rsa_key.private')), "r");
        $privateKey = fread($keyFile, 8192);

        if (openssl_private_encrypt($response->getContent(), $encrypted, $privateKey) )
        {
            $response->setContent(base64_encode($encrypted));
            return $response;
        }

        Log::error("Encrypt Middleware failed");
        $response->setContent('');

        return $response;
    }
}
