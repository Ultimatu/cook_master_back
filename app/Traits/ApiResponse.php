<?php

namespace App\Traits;
use App\Models\OTPVerif;
use App\Notifications\OtpNotification;

trait ApiResponse
{
   public function getExpirationTime()
    {
        return now()->addDays(1);
    }

    /**
     * @OA\Response(
     *     response="SuccessResponse",
     *     description="Réponse de succès générique",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="status",
     *             type="string",
     *             example="success"
     *         ),
     *         @OA\Property(
     *             property="code",
     *             type="integer",
     *             example=200
     *         ),
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             example="Opération réussie"
     *         ),
     *         @OA\Property(
     *             property="data",
     *             type="object",
     *             description="Données retournées",
     *             example={}
     *         )
     *     )
     * )
     */
    public function sendSuccessResponse($data, $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * @OA\Response(
     *     response="ErrorResponse",
     *     description="Réponse d'erreur générique",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="status",
     *             type="string",
     *             example="error"
     *         ),
     *         @OA\Property(
     *             property="code",
     *             type="integer",
     *             example=400
     *         ),
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             example="Une erreur est survenue"
     *         )
     *     )
     * )
     */
    public function sendErrorResponse($message, $code)
    {
        return response()->json([
            'status' => 'error',
            'code' => $code,
            'message' => $message,
        ], $code);
    }

    /**
     * @OA\Response(
     *     response="UnauthorizedResponse",
     *     description="Réponse pour une requête non autorisée",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="status",
     *             type="string",
     *             example="error"
     *         ),
     *         @OA\Property(
     *             property="code",
     *             type="integer",
     *             example=401
     *         ),
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             example="Non autorisé"
     *         )
     *     )
     * )
     */
    public function sendUnauthorizedResponse($message = 'Non autorisé')
    {
        return $this->sendErrorResponse($message, 401);
    }

    /**
     * @OA\Response(
     *     response="NotFoundResponse",
     *     description="Réponse pour une ressource non trouvée",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="status",
     *             type="string",
     *             example="error"
     *         ),
     *         @OA\Property(
     *             property="code",
     *             type="integer",
     *             example=404
     *         ),
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             example="Ressource non trouvée"
     *         )
     *     )
     * )
     */
    public function sendNotFoundResponse($message = 'Ressource non trouvée')
    {
        return $this->sendErrorResponse($message, 404);
    }

    /**
     * @OA\Response(
     *     response="ValidationErrorResponse",
     *     description="Réponse pour une erreur de validation",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="status",
     *             type="string",
     *             example="error"
     *         ),
     *         @OA\Property(
     *             property="code",
     *             type="integer",
     *             example=422
     *         ),
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             example="Erreur de validation"
     *         ),
     *         @OA\Property(
     *             property="errors",
     *             type="object",
     *             description="Erreurs de validation spécifiques",
     *             example={}
     *         )
     *     )
     * )
     */
    public function sendValidationError($message = 'Erreur de validation', $errors = [])
    {
        return response()->json([
            'status' => 'error',
            'code' => 422,
            'message' => $message,
            'errors' => $errors
        ], 422);
    }

    /**
     * @OA\Response(
     *     response="ForbiddenResponse",
     *     description="Réponse pour une action interdite",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="status",
     *             type="string",
     *             example="error"
     *         ),
     *         @OA\Property(
     *             property="code",
     *             type="integer",
     *             example=403
     *         ),
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             example="Action interdite"
     *         )
     *     )
     * )
     */
    public function sendForbiddenResponse($message = 'Action interdite')
    {
        return $this->sendErrorResponse($message, 403);
    }

    /**
     * @OA\Response(
     *     response="AuthResponse",
     *     description="Réponse pour une authentification réussie",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="status",
     *             type="string",
     *             example="success"
     *         ),
     *         @OA\Property(
     *             property="code",
     *             type="integer",
     *             example=200
     *         ),
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             example="Authentification réussie"
     *         ),
     *         @OA\Property(
     *             property="data",
     *             type="object",
     *             description="Données retournées",
     *             @OA\Property(
     *                 property="access_token",
     *                 type="string",
     *                 example="eyJ0eXAiOiekc1NiJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ"
     *             ),
     *             @OA\Property(
     *                 property="token_type",
     *                 type="string",
     *                 example="Bearer"
     *             ),
     *             @OA\Property(
     *                 property="expires_in",
     *                 type="integer",
     *                 example=3600
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 description="Utilisateur authentifié",
     *                 example={}
     *             )
     *         )
     *     )
     * )
     */

    public function sendAuthResponse($access_token, $expires_in, $user, $message = "Authentification réussie")
    {
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => $message,
            'data' => [
                'access_token' => $access_token,
                'token_type' => "Bearer",
                'expires_in' => $expires_in,
                'user' => $user
            ]
        ], 200);
    }



    public function sendInternalError($message = 'Erreur interne du serveur')
    {
        return $this->sendErrorResponse($message, 500);
    }


    public function sendNoContentResponse($message = 'Pas de contenu')
    {
        return response()->json([
            'status' => 'success',
            'code' => 204,
            'message' => $message,
        ], 204);
    }



    private function sendOtp($user, $type = 'email')
    {
        // Génère un OTP pour la vérification de l'email
        $otp = OTPVerif::generateOTP();
        OTPVerif::create([
            'identifier' => $user->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
            'is_verified' => false,
            'type' => $type,
        ]);

        // Envoie une notification avec l'OTP à l'utilisateur
        $user->notify(new OtpNotification($otp, $user->first_name . ' ' . $user->last_name, $user->email));

    }
}
