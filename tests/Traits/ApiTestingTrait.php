<?php

namespace Tests\Traits;

use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;

trait ApiTestingTrait
{
    /**
     * Assert that the response has a successful status code.
     */
    protected function assertResponseSuccessful(TestResponse $response): void
    {
        $response->assertSuccessful();
    }

    /**
     * Assert that the response is a JSON response.
     */
    protected function assertResponseIsJson(TestResponse $response): void
    {
        $response->assertHeader('Content-Type', 'application/json');
    }

    /**
     * Assert that the response has a specific structure.
     */
    protected function assertResponseStructure(TestResponse $response, array $structure): void
    {
        $response->assertJsonStructure($structure);
    }

    /**
     * Assert that the response contains specific data.
     */
    protected function assertResponseContains(TestResponse $response, array $data): void
    {
        $response->assertJson($data);
    }

    /**
     * Assert that the response has a validation error.
     */
    protected function assertValidationError(TestResponse $response, string $field, string $message = null): void
    {
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($field);

        if ($message) {
            $response->assertJsonValidationErrors([$field => $message]);
        }
    }

    /**
     * Assert that the response is unauthorized.
     */
    protected function assertUnauthorized(TestResponse $response): void
    {
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Assert that the response is forbidden.
     */
    protected function assertForbidden(TestResponse $response): void
    {
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Assert that the response is not found.
     */
    protected function assertNotFound(TestResponse $response): void
    {
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * Create API headers for testing.
     */
    protected function getApiHeaders(array $additional = []): array
    {
        return array_merge([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ], $additional);
    }

    /**
     * Create authenticated API headers.
     */
    protected function getAuthenticatedApiHeaders(string $token, array $additional = []): array
    {
        return $this->getApiHeaders(array_merge([
            'Authorization' => "Bearer {$token}",
        ], $additional));
    }
}
