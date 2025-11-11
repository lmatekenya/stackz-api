<?php

namespace App\Security;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class JWTManager
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager
    ) {}

    public function createToken(User $user): string
    {
        return $this->jwtManager->create($user);
    }

    public function decodeToken(string $token): array
    {
        return $this->jwtManager->parse($token);
    }
}
