<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use App\Mail\ResetCodeMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService 
{

    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(array $credentials)
    {
        // Vérifier si l'utilisateur existe par email
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont invalides.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function register(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        
        $user = $this->userRepository->create($data);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function forgotPassword(array $data): void
    {
        $status = Password::sendResetLink(['email' => $data['email']]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new \Exception(__($status));
        }
    }

    public function resetPassword(array $data): void
    {
        $status = Password::reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new \Exception(__($status));
        }
    }

    // public function sendResetCode(string $email): array
    // {
    //     $user = $this->userRepository->findByEmail($email);

    //     if (!$user) {
    //         return [
    //             'success' => false,
    //             'message' => 'Aucun utilisateur trouvé avec cet email.'
    //         ];
    //     }

    //     // Générer un code à 6 chiffres
    //     $code = rand(100000, 999999);

    //     // Sauvegarder le code
    //     $this->userRepository->saveResetCode($user, $code, 15);

    //     // Envoyer le mail
    //     try {
    //         Mail::to($user->email)->send(new ResetCodeMail($code));
    //     } catch (\Exception $e) {
    //         Log::error("Erreur envoi email reset : " . $e->getMessage());
    //         return [
    //             'success' => false,
    //             'message' => 'Impossible d’envoyer l’email.'
    //         ];
    //     }

    //     return [
    //         'success' => true,
    //         'message' => 'Un code de réinitialisation a été envoyé à votre email.'
    //     ];
    // }
}
