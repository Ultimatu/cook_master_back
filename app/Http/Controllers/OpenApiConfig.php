<?php

namespace App\Http\Controllers;


/**
 * @OA\Info(
 *     title="API DE O BI COOK",
 *     version="1.0",
 *     description="Documentation des API de O BI COOK",
 *     @OA\Contact(
 *         email="tondesoulco@gmail.com",
 *         name="Souleymane TONDE"
 *     ),
 * ),
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local development server"
 * ),
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Utilisez le token Bearer pour accéder aux API protégées"
 * ),
 *
 */
class OpenApiConfig extends Controller
{
    //
}

