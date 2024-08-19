<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OTPVerif;
use App\Notifications\OtpNotification;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PasswordResetLinkController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/auth/forgot-password",
     *     summary="Demander un lien de réinitialisation de mot de passe",
     *     description="Permet à l'utilisateur de demander un code OTP pour réinitialiser son mot de passe.",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", description="L'adresse email de l'utilisateur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Un email de réinitialisation de mot de passe a été envoyé à votre adresse email"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="code", type="string", example="123456"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=422),
     *             @OA\Property(property="message", type="string", example="Une erreur s'est produite lors de l'envoi de l'email de réinitialisation de mot de passe"),
     *             @OA\Property(property="errors", type="object", @OA\Property(property="email", type="string", example="Aucun utilisateur trouvé avec cette adresse email"))
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
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        //send notification
        if (!$user) {
            return $this->sendErrorResponse('Aucun utilisateur trouvé avec cette adresse email', 422);
        }

        //generate otp
        $otp = OTPVerif::generateOTP();
        OTPVerif::create([
            'identifier' => $user->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
            'is_verified' => false,
            'type' => 'email',
        ]);
        $user->notify(new OtpNotification($otp, $user->first_name . ' ' . $user->last_name, $user->email));

        return $this->sendSuccessResponse(['code' => $otp], 'Un email de réinitialisation de mot de passe a été envoyé à votre adresse email', 200);
    }
}
