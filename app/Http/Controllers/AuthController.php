<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use DateTimeZone;
use DateTime;
use App\Mail\EmailVerification;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\Http\Controllers\EmailController;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function ubahPassword(Request $request)
    {
        $validateData = $request->validate([
            'id' => 'required',
            'password' => 'required'
        ]);

        $dataUser = User::find($validateData['id']);
        $dataUser->password = bcrypt($validateData['password']);
        $dataUser->save();

        return response()->json(['message' => 'success'],200);
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'no_hp' => 'required|numeric|unique:users',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'no_hp' => $request->input('no_hp'),
            'password' => bcrypt(request('password')),
        ]);

        if ($user) {
            return response()->json(['message' => 'Registration successful'],201);
        } else {
            return response()->json(['message' => 'Registration failed'], 500);
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveToken($id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json($user->active_token,200);
        }
    }

    public function login()
    {
        // $now = Carbon::now(); 
        $user = User::where('email', request('email'))->first();
        
        $credentials = request(['email', 'password']);

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $customClaims = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'level' => $user->level,
            // 'tanggal_berakhir' => $user->tanggal_berakhir,
            'status' => $user->status,
        ];

        
        $tokenWithClaims = JWTAuth::claims($customClaims)->fromUser($user);
        $user->active_token = $tokenWithClaims;
        $user->save();
        return $this->respondWithToken($tokenWithClaims);
    }

    public function listUser()
    {
        $users = User::get();

        return response()->json([
            'data' => $users,
            'message' => 'successfully'
        ]);
    }

    public function resetPassword(Request $request, $id)
    {
        $validateData = $request->validate([
            "name" => "required",
            "email" => "required",
            'no_hp' => 'required',
            'metode_pembayaran' => 'required',
            "password" => "required",
        ]);

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'data not found']);
        }

        $user->name = $validateData['name'];
        $user->email = $validateData['email'];
        $user->no_hp = $validateData['no_hp'];
        $user->password = $validateData['password'];
        $user->metode_pembayaran = $validateData['metode_pembayaran'];

        $user->save();

        return response()->json(['message' => 'update users successfully']);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function deleteUser($id)
    {
        $data = User::find($id);
        if ($data) {
            $data->delete();
            return response()->json([
                'message' => 'success',
            ]);
        }
        return response()->json([
            'message' => 'data not found'
        ]);
    }

    public function deleteUsers(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $deletedCount = User::whereIn('id', $validated['user_ids'])->delete();

        if ($deletedCount > 0) {
            return response()->json(['message' => 'User deleted successfully', 'deleted' => $deletedCount], 200);
        }

        return response()->json(['message' => 'No user were deleted'], 404);
    }

    public function updatePw(Request $request, $id)
    {
        $validateData = $request->validate([
            'password' => 'required'
        ]);

        $data = User::find($id);
        if ($data) {
            $data->password = bcrypt(request('password'));
            $data->save();
            return response()->json([
                'message' => 'success'
            ],200);    
        }
        return response()->json([
            'message' => 'failed'
        ],401);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'sub' => auth()->user()->id,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'level' => auth()->user()->level,
            'status' => auth()->user()->status,
            'iat' => now()->timestamp,
            'token_type' => 'bearer',
            'expires_in' =>  auth()->factory()->getTTL() * 60,
        ]);
    }
    
    

}
