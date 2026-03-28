<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OAuthProvider;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Two\User as SocialiteUser;

class OAuthService
{
    /**
     * Handle OAuth callback: find or create user, link provider.
     *
     * @return array{action: 'login'|'needs_email', user: ?User, pendingData: ?array<string, mixed>}
     */
    public function handleCallback(string $provider, SocialiteUser $socialiteUser): array
    {
        $oauthRecord = OAuthProvider::query()
            ->with('user')
            ->where('provider', $provider)
            ->where('provider_id', $socialiteUser->getId())
            ->first();

        if ($oauthRecord) {
            /** @var User|null $linkedUser */
            $linkedUser = $oauthRecord->user;

            return ['action' => 'login', 'user' => $linkedUser, 'pendingData' => null];
        }

        $email = $socialiteUser->getEmail();

        if (! $email) {
            return [
                'action' => 'needs_email',
                'user' => null,
                'pendingData' => [
                    'provider' => $provider,
                    'provider_id' => $socialiteUser->getId(),
                    'token' => $socialiteUser->token,
                    'refresh_token' => $socialiteUser->refreshToken,
                    'name' => $socialiteUser->getName(),
                ],
            ];
        }

        /** @var User $user */
        $user = DB::transaction(function () use ($provider, $socialiteUser, $email): User {
            $user = User::query()->where('email', $email)->first();

            if (! $user) {
                $user = User::create([
                    'name' => $socialiteUser->getName() ?: $email,
                    'email' => $email,
                    'password' => null,
                ]);
                $user->markEmailAsVerified();
            }

            OAuthProvider::firstOrCreate(
                ['provider' => $provider, 'provider_id' => $socialiteUser->getId()],
                [
                    'user_id' => $user->id,
                    'token' => $socialiteUser->token,
                    'refresh_token' => $socialiteUser->refreshToken,
                ],
            );

            return $user;
        });

        return ['action' => 'login', 'user' => $user, 'pendingData' => null];
    }

    /**
     * Complete registration for OAuth user without email.
     *
     * @param  array<string, mixed>  $pendingData
     */
    public function completeRegistration(string $email, array $pendingData): User
    {
        return DB::transaction(function () use ($email, $pendingData): User {
            $user = User::query()->where('email', $email)->first();

            if (! $user) {
                $user = User::create([
                    'name' => $pendingData['name'] ?? $email,
                    'email' => $email,
                    'password' => null,
                ]);

                $user->sendEmailVerificationNotification();
            }

            OAuthProvider::firstOrCreate(
                ['provider' => $pendingData['provider'], 'provider_id' => $pendingData['provider_id']],
                [
                    'user_id' => $user->id,
                    'token' => $pendingData['token'],
                    'refresh_token' => $pendingData['refresh_token'],
                ],
            );

            return $user;
        });
    }
}
