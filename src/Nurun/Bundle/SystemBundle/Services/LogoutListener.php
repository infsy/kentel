<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 15-12-22
 * Time: 21:21
 */

namespace Nurun\Bundle\SystemBundle\Services;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;


class LogoutListener implements  LogoutSuccessHandlerInterface
{
    public function onLogoutSuccess(Request $request)
    {
        return new Response('', 401);
    }
}