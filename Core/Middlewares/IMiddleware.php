<?php
namespace Core\Middlewares;
use \Closure;
use \Core\Http\Request;
interface IMiddleware
{
    public function handle(Request $request, Closure $next);
}
