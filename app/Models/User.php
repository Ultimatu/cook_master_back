<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="Modèle Utilisateur",
 *     description="Modèle représentant un utilisateur du système",
 *     @OA\Property(
 *         property="first_name",
 *         type="string",
 *         description="Prénom de l'utilisateur",
 *         example="Jean"
 *     ),
 *     @OA\Property(
 *         property="last_name",
 *         type="string",
 *         description="Nom de famille de l'utilisateur",
 *         example="Tonde"
 *     ),
 *     @OA\Property(
 *         property="phone_number",
 *         type="string",
 *         description="Numéro de téléphone de l'utilisateur",
 *         example="+225012345678"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Adresse email de l'utilisateur",
 *         example="jean.tonde@example.com"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         description="Mot de passe de l'utilisateur (stocké de manière sécurisée)",
 *         example="password123"
 *     ),
 *     @OA\Property(
 *         property="email_verified_at",
 *         type="string",
 *         format="date-time",
 *         description="Date et heure de la vérification de l'email",
 *         example="2024-08-19T15:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="remember_token",
 *         type="string",
 *         description="Jeton de session pour la fonctionnalité 'se souvenir de moi'",
 *         example="1d92b2b74c8f3405b8b1e3e7cdd9f5d9"
 *     ),
 *     @OA\Property(
 *         property="emailOtpVerifications",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OTPVerif"),
 *         description="Vérifications OTP associées à l'email de l'utilisateur"
 *     ),
 *     @OA\Property(
 *         property="phoneOtpVerifications",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OTPVerif"),
 *         description="Vérifications OTP associées au numéro de téléphone de l'utilisateur"
 *     )
 * )
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Les attributs pouvant être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'password',
    ];

    /**
     * Les attributs à masquer lors de la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Obtenir les attributs à convertir.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Obtenir les vérifications OTP par email associées à l'utilisateur.
     */
    public function emailOtpVerifications()
    {
        return $this->hasMany(OTPVerif::class, 'identifier', 'email');
    }

    /**
     * Obtenir les vérifications OTP par téléphone associées à l'utilisateur.
     */
    public function phoneOtpVerifications()
    {
        return $this->hasMany(OTPVerif::class, 'identifier', 'phone_number');
    }
}
