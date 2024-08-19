<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;



/**
 * @OA\Tag(
 *     name="Authentification",
 *     description="Endpoints pour l'authentification des utilisateurs"
 * )
 *
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/authenticate",
     *     tags={"Authentification"},
     *     summary="Authentifier un utilisateur",
     *     description="Authentifie un utilisateur et génère un jeton d'accès.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret"),
     *             @OA\Property(property="device_name", type="string", example="my-device"),
     *             required={"email", "password", "device_name"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Jeton d'accès généré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="your-access-token-here"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *                 @OA\Property(property="phone_number", type="string", example="+1234567890")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Informations d'identification incorrectes",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=401),
     *             @OA\Property(property="message", type="string", example="Les informations d'identification fournies sont incorrectes.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendErrorResponse('Les informations d\'identification fournies sont incorrectes.', 401);
        }
        // Generate token
        $accessToken = $user->createToken($request->device_name, ['access:api'],$this->getExpirationTime())->plainTextToken;

        return $this->sendAuthResponse($accessToken,$this->getExpirationTime(), $user);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Authentification"},
     *     summary="Déconnecter l'utilisateur",
     *     description="Déconnecte l'utilisateur et supprime le jeton d'accès actuel.",
     *     @OA\Response(
     *         response=200,
     *         description="Session terminée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Session terminée avec succès")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendSuccessResponse([], 'Session terminée avec succès');
    }
}
