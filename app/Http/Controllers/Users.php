<?php
namespace App\Http\Controllers;

use App\Helpers\Api;
use App\Models\User;
use App\Models\Student;
use App\Models\Organization;
use App\Models\UserApiToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\UserRegistered;
use App\Models\UserVerificationToken;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

/**
 * Resource controller for users.
 */
class Users extends Controller
{

    /**
     * Get the current logged in user.
     *
     * @param Request $request
     * @return array
     */
    public function index(Request $request): array
    {
        return [
            'success' => true,
            'payload' => [
                'data' => $request->user(),
            ]
        ];
    }

    /**
     * Log a user in.
     *
     * @param Request $request
     * @return array
     */
    public function login(Request $request): array
    {
        $validator = Validator::make($request->json()->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.email' => 'Email field must be a valid email.',
        ]);

        if ($validator->fails()) { // Validation fails.
            return Api::generateErrorResponse(401, 'AuthenticationError', Api::getFirstValidationError($validator));
        }

        // Authenticate user by email, password and status.
        if (is_null($user = User::where('email', $request->json('email'))->first())) {
            return Api::generateErrorResponse(401, 'AuthenticationError', 'User email and password do not match.');
        } else if (!password_verify($request->json('password'), $user->password)) {
            return Api::generateErrorResponse(401, 'AuthenticationError', 'User email and password do not match.');
        } else if ($user->status == 'banned') { // User is banned
            return Api::generateErrorResponse(401, 'AuthenticationError', 'User is unable to log in.');
        }

        // Generate and save api token.
        $hex = bin2hex(random_bytes(64));
        $apiToken = new UserApiToken();
        $apiToken->token = '$' . '.' . $user->id . '.' . password_hash($hex, PASSWORD_DEFAULT) . '.' . $hex;
        $user->tokens()->save($apiToken);

        // Response.
        return [
            'success' => true,
            'payload' => [
                'data' => $user,
                'token' => $apiToken->token,
            ]
        ];
    }

    /**
     * Sign a user up.
     *
     * @param Request $request
     * @return array
     */
    public function signup(Request $request): array
    {
        // Validate input.
        $validator = Validator::make($request->json()->all(), [
            'email' => 'required|email|unique:App\Models\User,email',
            'password' => 'required|min:6',
            'address' => 'required',
            'phone_number' => 'required|phone_number',
            'user_type' => 'required|in:student,organization',
            'full_name' => 'required_if:user_type,student',
            'availability' => 'required_if:user_type,student|in:freelance,part_time',
            'hourly_rate' => 'required_if:user_type,student|numeric',
            'name' => 'required_if:user_type,organization|unique:App\Models\Organization,name',
            'description' => 'required_if:user_type,organization',
            'category_id' => 'required_if:user_type,organization|exists:organization_categories,id',
        ], [
            'email.email' => 'Email field must be a valid email.',
            'email.unique' => 'This email has been taken.',
            'password.min' => 'Password must be at least 6 characters long.',
            'phone_number.phone_number' => 'Phone number field must be a valid phone number.',
            'user_type.in' => 'Invalid user type.',
            'availability.in' => 'Invalid value for availability.',
            'hourly_rate.numeric' => 'Hourly rate must be numeric.',
            'name.unique' => 'This name has been taken.',
            'category_id' => 'This category does not exist.',
        ]);

        if ($validator->fails()) { // Validation fails.
            return Api::generateErrorResponse(105, 'InvalidFormDataError', Api::getFirstValidationError($validator));
        }


        // Create user account.
        switch ($request->json('user_type')) {
            case 'student':
                $userable = new Student;
                $userable->full_name = $request->json('full_name');
                $userable->availability = $request->json('availability');
                $userable->hourly_rate = $request->json('hourly_rate');
            break;

            case 'organization':
                $userable = new Organization;
                $userable->name = $request->json('name');
                $userable->description = $request->json('description');
                $userable->category_id = $request->json('category_id');
            break;
        }


        $user = new User;
        $user->email = $request->json('email');
        $user->password = password_hash($request->json('password'), PASSWORD_DEFAULT);
        $user->address = $request->json('address');
        $user->phone_number = $request->json('phone_number');


        $userable->save();
        $user->userable()->associate($userable);
        $user->save();


        // Generate and save verification token.
        $hex = bin2hex(random_bytes(64));
        $verificationToken = new UserVerificationToken();
        $verificationToken->token = '$' . '.' . $user->id . '.' . password_hash($hex, PASSWORD_DEFAULT) . '.' . $hex;
        $user->verificationTokens()->save($verificationToken);


        // Send activation email.
        Mail::to($user)->send(new UserRegistered($user, $verificationToken));


        // Response
        return [
            'success' => true,
            'payload' => [
                'data' => $user,
            ]
        ];
    }
}
