<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Query Builder CRUD API',
    description: 'Laravel API with Query Builder, Passport, and Swagger'
)]
#[OA\Server(
    url: 'http://127.0.0.1:8000',
    description: 'Local Server'
)]
class ApiDocumentation
{
}
