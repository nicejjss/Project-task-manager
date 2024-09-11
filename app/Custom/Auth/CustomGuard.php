<?php
namespace App\Custom\Auth;

use App\Custom\Traits\JsonResponseTrait;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Throwable;


/**
 * @param Model $user
 * @param CustomProvider $provider
 */
class CustomGuard implements Guard
{
    use GuardHelpers, JsonResponseTrait;

    protected $user;
    protected $provider;

public function __construct(UserProvider $provider)
{
    $this->provider = $provider;
}

    /**
     * @throws Throwable
     */
    public function attempt(string $token): bool
    {
        if ($token) {
            try {
                $credential = json_decode(Crypt::decrypt($token), true);
                $this->user = $this->provider->retrieveByCredentials($credential);
                if ($this->user) {
                    return true;
                }
            } catch (Throwable $e) {
                Log::error($e->getMessage());
                return false;
            }
        }

        return false;
    }

    public function user(): Model
    {
        return $this->user;
    }

    public function validate(array $credentials = [])
    {
        // TODO: Implement validate() method.
    }

    public function attemptByCredentials(array $credentials): bool
    {
        $credentials = $this->formatCredentials($credentials);
        if ($this->user = $this->provider->retrieveByCredentials($credentials)) {
            return true;
        }

        return false;
    }

    public function existEmail(string $email): bool
    {
        return $this->provider->existEmail($email);
    }

    private function formatCredentials(array $credentials): array
    {
        if (array_key_exists('password', $credentials)) {
            $credentials['password'] = md5($credentials['password']);
        }

        return $credentials;
    }

    public function userToken(Model $user = null): string
    {
        if ($user) {
            $userMail = $user->email;
            $userPass = $user->password;
        } else {
            $userMail = auth()->user()->email;
            $userPass = auth()->user()->password;
        }

        return Crypt::encrypt("{\"email\":\"$userMail\",\"password\":\"$userPass\"}");
    }
}
