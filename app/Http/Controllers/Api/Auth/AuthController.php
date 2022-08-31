<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Login as AuthLogin;
use App\Http\Requests\Auth\Logout as AuthLogout;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Respond with structured user data.
     *
     * @param Authenticatable $user
     *
     * @return ApiResponse
     */
    private function respondWithToken(Authenticatable $user): JsonResponse
    {
        return $this->setDefaultSuccessResponse([])->respondWithSuccess([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'access_token' => $user->api_token,
            ]
        ]);
    }

    /**
     * Get access token via given credentials.
     *
     * @param AuthLogin $request
     *
     * @return ApiResponse
     */
    public function login(AuthLogin $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return $this->respondUnAuthenticated();
        }

        $user = auth()
            ->user()
            ->generateAndSaveApiToken();

        return $this->respondWithToken($user);
    }

    /**
     * Get authenticated User.
     *
     * @return ApiResponse
     */
    public function user(): JsonResponse
    {
        $user = auth()->user();

        return $this->respondWithToken($user);
    }

    /**
     * Reset access token.
     *
     * @param AuthLogout $request
     *
     * @return ApiResponse
     */
    public function logout(AuthLogout $request): JsonResponse
    {
        $user = auth()->user();

        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        return $this->respondWithSuccess();
    }
}
