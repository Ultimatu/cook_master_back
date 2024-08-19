<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OTPVerif;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class NewPasswordController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/reset-password",
     *     summary="Réinitialiser le mot de passe de l'utilisateur",
     *     description="Réinitialise le mot de passe de l'utilisateur en utilisant un code OTP.",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="otp", type="string", description="Le code OTP envoyé à l'adresse email"),
     *             @OA\Property(property="email", type="string", description="L'adresse email de l'utilisateur"),
     *             @OA\Property(property="password", type="string", description="Le nouveau mot de passe de l'utilisateur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Votre mot de passe a été réinitialisé avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=422),
     *             @OA\Property(property="message", type="string", example="Le code de vérification est invalide"),
     *             @OA\Property(property="errors", type="object", @OA\Property(property="otp", type="string", example="Le code de vérification est invalide"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=401),
     *             @OA\Property(property="message", type="string", example="Non autorisé")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'otp' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors(), 422);
        }

        //check otp
        $otp = $request->otp;
        $email = $request->email;
        $verifOtp = OTPVerif::where('otp', $otp)->where('identifier', $email)->where('is_verified', false)->first();

        if (!$verifOtp) {
            return $this->sendErrorResponse(['otp' => 'Le code de vérification est invalide'], 422);
        }

        $verifOtp->is_verified = true;

        $verifOtp->save();
        //reset password
        $user = User::where('email', $email)->first();

        if (!$user) {
            return $this->sendUnauthorizedResponse('Utilisateur non trouvé');
        }

        $password = $request->password;
        $user->update([
            'password' => $password,
        ]);

        return $this->sendSuccessResponse([], 'Votre mot de passe a été réinitialisé avec succès', 200);
    }
}
