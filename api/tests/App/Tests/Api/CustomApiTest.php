<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\TeacherFactory;
use App\Factory\UserFactory;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Zenstruck\Foundry\Test\Factories;

abstract class CustomApiTest extends ApiTestCase
{
    use Factories;
    protected function setUser(string $typeOfPerson = '', array $userRoles = []): string
    {
        if ($typeOfPerson === 'teacher') {
            $person = TeacherFactory::createOne();
        } else {
            $person = TeacherFactory::createOne();
        }

        $user = UserFactory::createOne([
            'person' => $person,
            'roles' => $userRoles,
        ]);

        return $user->getEmail();
    }

    protected function makeRequest(string $methode = '', string $url = '', array $json = [], array $headers = [], ?string $userIdentifier = null): ResponseInterface
    {
        $defaultHeaders = [
            'Content-Type' => 'PATCH' === $methode ? 'application/merge-patch+json' : 'application/ld+json',
            'Authorization' => $userIdentifier ?? $this->setUser(),
        ];

        $headers = array_merge($defaultHeaders, $headers);

        return static::createClient()->request($methode, $url, [
            'headers' => $headers,
            'json' => $json,
        ]);
    }
}
