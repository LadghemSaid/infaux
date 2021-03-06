<?php
// src/Mercure/CookieGenerator.php
namespace App\Mercure;

use App\Entity\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Symfony\Component\HttpFoundation\Cookie;

class CookieGenerator
{
    private $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function generate(User $user)
    {

        $username =$user->getUsername();

        $token = (new Builder())
            //->withClaim('mercure', ['subscribe' => ["/user/{$user->getId()}"]])
            ->withClaim('mercure', ['subscribe' => [sprintf("/%s", $username)]])

            ->getToken(new Sha256(), new Key($this->secret));

        return "mercureAuthorization={$token}; Path=/.well-known/mercure; HttpOnly; ";
        //return Cookie::create('mercureAuthorization', $token, 0, '/.well-known/mercure', '', 'HttpOnly');
    }
    public function generateToken(User $user)
    {
        $username =$user->getUsername();

        $token = (new Builder())
            ->withClaim('mercure', [
                'subscribe' => [
                    sprintf("/%s", $username),
                    "/user/{$user->getId()}"

                ]])

            // ->withClaim('mercure', ['subscribe' => ["/user/{$user->getId()}"]])
            ->getToken(new Sha256(), new Key($this->secret));

        return $token;

    }
}
