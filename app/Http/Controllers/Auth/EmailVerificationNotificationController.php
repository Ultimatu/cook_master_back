<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OTPVerif;
use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/auth/send-verification-email",
     *     summary="Envoyer l'email de vérification",
     *     description="Envoyer un code OTP pour la vérification de l'adresse email de l'utilisateur.",
     *     tags={"Authentification"},
     *       @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="email"),
     *         description="L'adresse email de l'utilisateur"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Un email de vérification a été envoyé à votre adresse email"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="L'email est déjà vérifié",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Votre adresse email est déjà vérifiée"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=422),
     *             @OA\Property(property="message", type="string", example="Une erreur s'est produite lors de l'envoi de l'email de vérification")
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
     *     ),
     * )
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $user = User::where('email', $request->email)->first();
        if ($user->hasVerifiedEmail()) {
            return $this->sendSuccessResponse([], 'Votre adresse email est déjà vérifiée', 200);
        }

        //check if user has already an otp
        $otp = OTPVerif::where('identifier', $user->email)->where('is_verified', false)->where('type', 'email')->first();

        // if send time is more than 30 seconds, send another otp
        if ($otp && now()->diffInSeconds($otp->created_at) < 30) {
            return $this->sendSuccessResponse([], 'Un email de vérification a déjà été envoyé à votre adresse email, veuillez attendre 30 secondes avant de renvoyer un autre email', 200);
        }
        if ($otp) {
            $otp->delete();
        }
        $this->sendOtp($user);
        return $this->sendSuccessResponse([], 'Un email de vérification a été envoyé à votre adresse email', 200);
    }
}
