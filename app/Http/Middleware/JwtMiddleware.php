<?php

    namespace App\Http\Middleware;

    use Closure;
    use JWTAuth;
    use Exception;
    use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

    class JwtMiddleware extends BaseMiddleware
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
            $response = ['status' => false , 'message' => "Something went wrong"];
            try {
                $user = JWTAuth::parseToken()->authenticate();
                if(!$user){
                    return response()->json($response);
                }
            } catch (Exception $e) {
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                    $response['message'] = 'Token is Invalid';
                }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                    $response['message'] = 'Token is Expired';
                }else{
                    $response['message'] = 'Authorization Token not found';
                }

                return response()->json($response);
            }
            return $next($request);
        }
    }