<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OTPVerif;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
class RegisteredUserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Enregistrer un nouvel utilisateur",
     *     description="Crée un nouvel utilisateur et envoie un email de vérification OTP.",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string", example="Jean", description="Prénom de l'utilisateur"),
     *             @OA\Property(property="last_name", type="string", example="Dupont", description="Nom de famille de l'utilisateur"),
     *             @OA\Property(property="phone_number", type="string", example="+2250102030405", description="Numéro de téléphone de l'utilisateur"),
     *             @OA\Property(property="email", type="string", example="jean.dupont@example.com", description="Adresse email de l'utilisateur"),
     *             @OA\Property(property="password", type="string", example="password123", description="Mot de passe de l'utilisateur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="Un email de vérification a été envoyé à votre adresse email"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=422),
     *             @OA\Property(property="message", type="string", example="Les données de l'utilisateur sont invalides"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors(), 422);
        }

        // Vérifie si un utilisateur avec l'adresse email fournie existe déjà
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            //check if the user is already verified
            if ($existingUser->email_verified_at) {
                return $this->sendErrorResponse(['email' => 'Un utilisateur avec cette adresse email existe déjà'], 422);
            }
            //check if the user has an OTP verification
            $otp = OTPVerif::where('identifier', $request->email)->where('is_verified', false)->where('type', 'email')->first();
            if ($otp) {
                $otp->delete();
            }
            // Génère un OTP pour la vérification de l'email
            $this->sendOtp($existingUser);
        }
        // Crée un nouvel utilisateur dans la base de données en utilisant les données du formulaire d'inscription
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'password' => $request->password,
        ]);
        if (config('mail.service_status') == 'on') {
            $this->sendOtp($user);
            return $this->sendSuccessResponse([], 'Un email de vérification a été envoyé à votre adresse email', 201);
        }
        $user->markEmailAsVerified();
        //exp for 1 days
        $accessToken = $user->createToken('dev_mode', ['access:api'], $this->getExpirationTime())->plainTextToken;
        return $this->sendAuthResponse($accessToken, config('sanctum.expiration'), $user);

    }

}
