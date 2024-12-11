<?php

namespace Tests\Feature;

use Database\Seeders\AuthorSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthorApiTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorSeeder::class);
    }

    public function test_get_all_authors()
    {
        $response = $this->getJson('/api/v1/authors');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'name',
                            'bio',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'links' => [
                        'first',
                        'prev',
                        'next',
                    ],
                    'meta' => [
                        'current_page',
                        'from',
                        'path',
                        'per_page',
                        'to',
                    ],
                ],
            ])
            ->assertJsonCount(15, 'data.items');
    }

    public function test_get_authors_search()
    {
        $response = $this->getJson('/api/v1/authors?search=Dr.');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'name',
                            'bio',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'links' => [
                        'first',
                        'prev',
                        'next',
                    ],
                    'meta' => [
                        'current_page',
                        'from',
                        'path',
                        'per_page',
                        'to',
                    ],
                ],
            ]);
    }

    public function test_get_authors_search_not_found()
    {
        $response = $this->getJson('/api/v1/authors?search=Not Found');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items' => [],
                    'links' => [
                        'first',
                        'prev',
                        'next',
                    ],
                    'meta' => [
                        'current_page',
                        'from',
                        'path',
                        'per_page',
                        'to',
                    ],
                ],
            ])
            ->assertJsonCount(0, 'data.items');
    }

    public function test_get_authors_paginated()
    {
        $response = $this->getJson('/api/v1/authors?perPage=5&columns[]=id&columns[]=name&page=2');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'name',
                        ],
                    ],
                    'links' => [
                        'first',
                        'prev',
                        'next',
                    ],
                    'meta' => [
                        'current_page',
                        'from',
                        'path',
                        'per_page',
                        'to',
                    ],
                ],
            ])
            ->assertJsonCount(5, 'data.items')
            ->assertJsonPath('data.meta.current_page', 2);
    }

    public function test_store_author()
    {
        $response = $this->postJson('/api/v1/authors', [
            'name' => $this->faker->name,
            'bio' => $this->faker->sentence,
            'birth_date' => $this->faker->date,
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'bio',
                    'birth_date',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ]);
    }

    public function test_store_author_validation_error()
    {
        $response = $this->postJson('/api/v1/authors', [
            'name' => '',
            'bio' => '',
            'birth_date' => '01-01-2000',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'name',
                    'birth_date',
                ],
            ]);
    }

    public function test_show_author()
    {
        $response = $this->getJson('/api/v1/authors/1');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'bio',
                    'birth_date',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_show_author_not_found()
    {
        $response = $this->getJson('/api/v1/authors/1000');

        $response
            ->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_update_author()
    {
        $response = $this->putJson('/api/v1/authors/1', [
            'name' => $this->faker->name,
            'bio' => $this->faker->sentence,
            'birth_date' => $this->faker->date,
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'bio',
                    'birth_date',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ]);
    }

    public function test_update_author_not_found()
    {
        $response = $this->putJson('/api/v1/authors/1000', [
            'name' => $this->faker->name,
            'bio' => $this->faker->sentence,
            'birth_date' => $this->faker->date,
        ]);

        $response
            ->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_update_author_validation_error()
    {
        $response = $this->putJson('/api/v1/authors/2', [
            'name' => '',
            'bio' => '',
            'birth_date' => '01-01-2000',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'name',
                    'birth_date',
                ],
            ]);
    }

    public function test_delete_author()
    {
        $response = $this->deleteJson('/api/v1/authors/3');

        $response
            ->assertStatus(204)
            ->assertNoContent();
    }

    public function test_delete_author_not_found()
    {
        $response = $this->deleteJson('/api/v1/authors/1000');

        $response
            ->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }
}