<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="OTPVerif",
 *     type="object",
 *     title="Modèle de Vérification OTP",
 *     description="Modèle pour gérer les processus de vérification OTP",
 *     @OA\Property(
 *         property="identifier",
 *         type="string",
 *         description="L'identifiant (ex: 'tondesouloc@gmail.com', '+225012345678')",
 *         example="user123"
 *     ),
 *     @OA\Property(
 *         property="otp",
 *         type="string",
 *         description="Code OTP généré",
 *         example="123456"
 *     ),
 *     @OA\Property(
 *         property="expires_at",
 *         type="string",
 *         format="date-time",
 *         description="Date et heure d'expiration de l'OTP",
 *         example="2024-08-19T15:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="is_verified",
 *         type="boolean",
 *         description="Indique si l'OTP a été vérifié",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="Le type de vérification (ex: 'email', 'phone_number')",
 *         example="email"
 *     ),
 * )
 */
class OTPVerif extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'otp',
        'expires_at',
        'is_verified',
        'type',
    ];


    public function scopeValid($query, $identifier, $otp, $type)
    {
        return $query->where('identifier', $identifier)
            ->where('otp', $otp)
            ->where('type', $type)
            ->where('expires_at', '>=', now())
            ->where('is_verified', false);
    }

    public function scopeVerified($query, $identifier, $otp, $type)
    {
        return $query->where('identifier', $identifier)
            ->where('otp', $otp)
            ->where('type', $type)
            ->where('expires_at', '>=', now())
            ->where('is_verified', true);
    }


    public function scopeExpired($query, $identifier, $otp, $type)
    {
        return $query->where('identifier', $identifier)
            ->where('otp', $otp)
            ->where('type', $type)
            ->where('expires_at', '<', now());
    }


    public function scopeNotVerified($query, $identifier, $otp, $type)
    {
        return $query->where('identifier', $identifier)
            ->where('otp', $otp)
            ->where('type', $type)
            ->where('is_verified', false);
    }


    public function scopeNotExpired($query, $identifier, $otp, $type)
    {
        return $query->where('identifier', $identifier)
            ->where('otp', $otp)
            ->where('type', $type)
            ->where('expires_at', '>=', now());
    }


    public function scopeNotExpiredAndNotVerified($query, $identifier, $otp, $type)
    {
        return $query->where('identifier', $identifier)
            ->where('otp', $otp)
            ->where('type', $type)
            ->where('expires_at', '>=', now())
            ->where('is_verified', false);
    }




    public function scopeNotExpiredAndVerified($query, $identifier, $otp, $type)
    {
        return $query->where('identifier', $identifier)
            ->where('otp', $otp)
            ->where('type', $type)
            ->where('expires_at', '>=', now())
            ->where('is_verified', true);
    }


    public function scopeExpiredAndNotVerified($query, $identifier, $otp, $type)
    {
        return $query->where('identifier', $identifier)
            ->where('otp', $otp)
            ->where('type', $type)
            ->where('expires_at', '<', now())
            ->where('is_verified', false);
    }


    public function scopeExpiredAndVerified($query, $identifier, $otp, $type)
    {
        return $query->where('identifier', $identifier)
            ->where('otp', $otp)
            ->where('type', $type)
            ->where('expires_at', '<', now())
            ->where('is_verified', true);
    }


    //generate otp
    public static function generateOTP($length = 6)
    {
        $otp = "";
        $characters = "0123456789";
        $charactersLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $otp .= $characters[rand(0, $charactersLength - 1)];
        }
        return $otp;
    }


    public static function isValid($identifier, $otp, $type)
    {
        return self::valid($identifier, $otp, $type)->exists();
    }

    public static function isVerified($identifier, $otp, $type)
    {
        return self::verified($identifier, $otp, $type)->exists();
    }

    public static function isExpired($identifier, $otp, $type)
    {
        return self::expired($identifier, $otp, $type)->exists();
    }

    public static function isNotVerified($identifier, $otp, $type)
    {
        return self::notVerified($identifier, $otp, $type)->exists();
    }
}
