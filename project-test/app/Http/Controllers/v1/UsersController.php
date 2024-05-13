<?php

namespace App\Http\Controllers\v1;

use App\Models\Users;
use App\Models\Activities;
use App\Models\LogsActivities;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class UsersController extends Controller
{
    /**
     * Login
     * @author Nanda Firmansyah
     * @return void
     */
    public function login(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string',
                'remember_me' => 'boolean',
            ]);

            if ($validator->fails()) {
                return $this->templateResponse('Bad Request', 400, [], $validator->errors(), 'VALIDATION_ERROR');
            }

            $user = Users::with('role')->where(function ($query) use ($request) {
                $query->where('username', $request->username)
                      ->orWhere('email', $request->username);
            })->first();
            
            if (!$user) {
                return $this->templateResponse('Unauthorized', 401, [], 'User not found', 'UNAUTHORIZED');
            }

            if ($user->status === 'INACTIVE') {
                return $this->templateResponse('Unauthorized', 401, [], 'User inactive', 'UNAUTHORIZED');
            }

            if ($user->status === 'BANNED') {
                return $this->templateResponse('Unauthorized', 401, [], 'User has been ban', 'UNAUTHORIZED');
            }

            if ($user->status === 'DELETED') {
                return $this->templateResponse('Unauthorized', 401, [], 'User has been delete', 'UNAUTHORIZED');
            }

            if ($user->login_attempt >= 3) {
                return $this->templateResponse('Unauthorized', 401, [], 'User locked', 'UNAUTHORIZED');
            }

            if (!password_verify($request->password, $user->password)) {
                $user->login_attempt += 1;
                $user->save();
                return $this->templateResponse('Unauthorized', 401, [], 'Credential incorrect', 'UNAUTHORIZED');
            }

            $user->login_attempt = 0;
            $user->last_ip = $request->ip();
            $user->last_login = now();
            $user->save();

            if(empty(Redis::keys($user->id . ':TOKEN*'))) {
                $token = md5(Hash::make(md5(time() . rand(10000, 99999))));
                $user->access_token = $token;
                if($request->remember_me) {
                    Redis::set($user->id . ':TOKEN:' . $token, json_encode($user));
                } else {
                    Redis::set($user->id . ':TOKEN:' . $token, json_encode($user), 'EX', 86400);
                }
                $user->token_expired_in = 86400;
            } else {
                $raw_key = Redis::keys($user->id . ':TOKEN*')[0];
                $token = explode(':', $raw_key)[2];
                $user->access_token = $token;
                $ttl = Redis::ttl($user->id . ':TOKEN:' . $token);
                $user->token_expired_in = $ttl;
                if($request->remember_me) {
                    Redis::set($user->id . ':TOKEN:' . $token, json_encode($user));
                } else {
                    Redis::set($user->id . ':TOKEN:' . $token, json_encode($user), 'EX', $ttl);
                }
            }

            $log = new LogsActivities();
            $log->user_id = $user->id;
            $log->activity_id = Activities::where('code', 'LOGIN_APP')->first()->id;
            $log->description = 'User login';
            $log->ip_address = $request->ip();
            $log->user_agent = $request->header('User-Agent');
            $log->created_by = $user->username;
            $log->updated_by = $user->username;
            $log->save();

            return $this->templateResponse('Login Success', 200, $user);
        } catch (\Exception $e) {
            return $this->templateResponse('Internal Server Error', 500, [], $e->getMessage(), 'INTERNAL_SERVER_ERROR');
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request) {
        try {
            $profile = $request->get('profile');
            $token = $profile['access_token'];
            $user = Users::find($profile['id']);
            Redis::del($user->id . ':TOKEN:' . $token);

            $log = new LogsActivities();
            $log->user_id = $user->id;
            $log->activity_id = Activities::where('code', 'LOGOUT_APP')->first()->id;
            $log->description = 'User logout';
            $log->ip_address = $request->ip();
            $log->user_agent = $request->header('User-Agent');
            $log->created_by = $user->username;
            $log->updated_by = $user->username;
            $log->save();

            return $this->templateResponse('Logout Success', 200, [], [], 'SUCCESS');
        } catch (\Exception $e) {
            return $this->templateResponse('Internal Server Error', 500, [], $e->getMessage(), 'INTERNAL_SERVER_ERROR');
        }
    }

    /**
     * List Users
     * @author Nanda Firmansyah
     */
    public function index(Request $request) {
        $profile = $request->get('profile');
        if($profile['role']['name'] !== 'ADMIN') {
            return $this->templateResponse('Forbidden', 403, [], 'Forbidden access', 'FORBIDDEN');
        }

        try {
            $validator = Validator::make($request->query(), [
                'username' => 'string',
                'fullname' => 'string',
                'email' => 'string',
                'birthdate' => 'date',
                'phone' => 'string',
                'status' => 'string',
            ]);

            if ($validator->fails()) {
                return $this->templateResponse('Bad Request', 400, [], $validator->errors(), 'VALIDATION_ERROR');
            }

            $users = Users::query();
            if ($request->query()) {
                foreach ($request->query() as $key => $value) {
                    $users = $users->where($key, 'like', '%' . $value . '%');
                }
            }
            $users = $users->orderBy('created_at', 'DESC')->get();
            return $this->templateResponse('List Users', 200, $users);
        } catch (\Exception $e) {
            return $this->templateResponse('Internal Server Error', 500, [], $e->getMessage(), 'INTERNAL_SERVER_ERROR');
        }
    }

    /**
     * Create User
     * @author Nanda Firmansyah
     */
    public function create(Request $request) {
        try {
            $profile = $request->get('profile');
            if($profile['role']['name'] !== 'ADMIN') {
                return $this->templateResponse('Forbidden', 403, [], 'Forbidden access', 'FORBIDDEN');
            }

            $validator = Validator::make($request->all(), [
                'role_id' => 'required|uuid',
                'username' => 'required|string|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|string',
                'fullname' => 'required|string',
                'birthdate' => 'date',
                'phone' => 'string',
                'address' => 'string',
                'avatar' => 'string',
                'last_ip' => 'string',
                'language' => 'string|in:EN,ID',
                'status' => 'string|in:INACTIVE,ACTIVE,BANNED',
                'login_attempt' => 'integer',
                'last_login' => 'date',
                'created_at' => 'date',
                'created_by' => 'string',
                'updated_at' => 'date',
                'updated_by' => 'string',
            ]);

            if ($validator->fails()) {
                return $this->templateResponse('Bad Request', 400, [], $validator->errors(), 'VALIDATION_ERROR');
            }

            $user = new Users();
            $user->role_id = $request->role_id;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->fullname = $request->fullname;
            $user->birthdate = $request->birthdate;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->avatar = $request->avatar;
            $user->last_ip = $request->last_ip;
            $user->language = $request->language;
            $user->status = $request->status;
            $user->login_attempt = $request->login_attempt;
            $user->last_login = $request->last_login;
            $user->created_by = $profile['username'];
            $user->updated_by = $profile['username'];
            $user->save();

            $log = new LogsActivities();
            $log->user_id = $profile['id'];
            $log->activity_id = Activities::where('code', 'CREATE_USER')->first()->id;
            $log->description = 'Create user ' . $user->username;
            $log->ip_address = $request->ip();
            $log->user_agent = $request->header('User-Agent');
            $log->created_by = $profile['username'];
            $log->updated_by = $profile['username'];
            $log->save();

        } catch (\Exception $e) {
            return $this->templateResponse('Internal Server Error', 500, [], $e->getMessage(), 'INTERNAL_SERVER_ERROR');
        }
    }

    /**
     * Update User
     * @author Nanada Firmansyah
     */
    public function update(Request $request) {
        try {
            $profile = $request->get('profile');
            if($profile['role']['name'] !== 'ADMIN') {
                return $this->templateResponse('Forbidden', 403, [], 'Forbidden access', 'FORBIDDEN');
            }

            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'role_id' => 'integer',
                'username' => 'string|max:255',
                'email' => 'string|email|max:255',
                'password' => 'string|min:6',
                'fullname' => 'string|max:255',
                'birthdate' => 'date',
                'phone' => 'string|max:15',
                'address' => 'string',
                'avatar' => 'string',
                'last_ip' => 'string',
                'language' => 'string|in:EN,ID',
                'status' => 'string|in:INACTIVE,ACTIVE,BANNED',
                'login_attempt' => 'integer',
                'last_login' => 'date',
            ]);

            if ($validator->fails()) {
                return $this->templateResponse('Bad Request', 400, [], $validator->errors(), 'VALIDATION_ERROR');
            }

            $user = Users::find($request->id);
            if (!$user) {
                return $this->templateResponse('Not Found', 400, [], 'User not found', 'NOT_FOUND');
            }

            $user->role_id = $request->role_id ?? $user->role_id;
            $user->username = $request->username ?? $user->username;
            $user->email = $request->email ?? $user->email;
            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->fullname = $request->fullname ?? $user->fullname;
            $user->birthdate = $request->birthdate ?? $user->birthdate;
            $user->phone = $request->phone ?? $user->phone;
            $user->address = $request->address ?? $user->address;
            $user->avatar = $request->avatar ?? $user->avatar;
            $user->last_ip = $request->last_ip ?? $user->last_ip;
            $user->language = $request->language ?? $user->language;
            $user->status = $request->status ?? $user->status;
            $user->login_attempt = $request->login_attempt ?? $user->login_attempt;
            $user->last_login = $request->last_login ?? $user->last_login;
            $user->updated_by = $profile['username'];
            $user->save();

            $log = new LogsActivities();
            $log->user_id = $profile['id'];
            $log->activity_id = Activities::where('code', 'UPDATE_USER')->first()->id;
            $log->description = 'Update user ' . $user->username;
            $log->ip_address = $request->ip();
            $log->user_agent = $request->header('User-Agent');
            $log->created_by = $profile['username'];
            $log->updated_by = $profile['username'];
            $log->save();

            return $this->templateResponse('User updated successfully', 200, $user->toArray(), [], 'SUCCESS');
        } catch (\Exception $e) {
            return $this->templateResponse('Internal Server Error', 500, [], $e->getMessage(), 'INTERNAL_SERVER_ERROR');
        }
    }
}
