<?php

namespace App\Http\Controllers\Api;

use Exception;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; 

class AuthenticationController extends BaseController
{

    public function login(Request $request)
    {
        try {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $success['token'] = $user->createToken('MyApp')->accessToken;
                $success['name'] = $user->name;

                return $this->sendResponse($success, 'User login successfully.');
            } else {
                $userExists = User::where('email', $request->email)->exists();
                
                if (!$userExists) {
                    $errorMessage = 'User does not exist in the database.';
                    return $this->sendError($errorMessage, ['error' => $errorMessage]);
                }

                $errorMessage = 'Invalid credentials. Authentication failed.';
                return $this->sendError($errorMessage, ['error' => $errorMessage]);
            }
        } catch (Exception $e) {
            return $this->sendError('An error occurred during login.', ['error' => $e->getMessage()]);
        }
    }

       /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
        public function logout(Request $request)
        {
            if (Auth::user()) {
                $request->user()->token()->revoke();

                return response()->json([
                    'success' => true,
                    'message' => 'Logged out successfully',
                ], 200);
            }
        }
}
