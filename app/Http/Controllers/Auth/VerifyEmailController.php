<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OTPVerif;
use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/auth/verify-email",
     *     summary="Vérifier l'adresse email de l'utilisateur",
     *     description="Endpoint pour vérifier l'adresse email de l'utilisateur via un code OTP.",
     *     tags={"Authentification"},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="email"),
     *         description="L'adresse email de l'utilisateur"
     *     ),
     *     @OA\Parameter(
     *         name="otp",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Le code OTP envoyé à l'adresse email"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Votre adresse email a été vérifiée"),
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
    public function verifyEmail(Request $request)
    {
        $rules = [
            'email' => ['required', 'email'],
            'otp' => ['required', 'string'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors(), 422);
        }
        $email = $request->email;
        $user = User::where("email", $email)->first();
        if (!$user) {
            $this->sendErrorResponse('Utilisateur non trouvé', 401);
        }

        $otp = OTPVerif::where('identifier', $email)->where('otp', $request->otp)->where('is_verified', false)->first();
        if (!$otp) {
            $this->sendErrorResponse(['otp' => 'Le code de vérification est invalide'], 422);
        }

        $otp->is_verified = true;
        $otp->save();

        $request->user()->markEmailAsVerified();

        return $this->sendSuccessResponse([], 'Votre adresse email a été vérifiée', 200);
    }
}
