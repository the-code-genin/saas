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

    /**
     * Update a user's profile.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function update(Request $request): array
    {
        $user = $request->user();
        $input = collect($request->json()->all());

        // Validate input.
        $validator = Validator::make($request->json()->all(), [
            'password' => 'nullable|min:6',
            'phone_number' => 'nullable|phone_number',
            'availability' => 'nullable|in:freelance,part_time',
            'hourly_rate' => 'nullable|numeric',
            'available_for_job' => 'nullable|in:true,false',
            'proficiency' => 'nullable|in:beginner,intern,expert',
            'name' => "nullable|unique:App\Models\Organization,name,{$user->id}",
            'category_id' => 'nullable|exists:organization_categories,id',
        ], [
            'password.min' => 'Password must be at least 6 characters long.',
            'phone_number.phone_number' => 'Phone number field must be a valid phone number.',
            'availability.in' => 'Invalid value for availability.',
            'hourly_rate.numeric' => 'Hourly rate must be numeric.',
            'available_for_jobs.in' => 'Invalid value for availability for jobs.',
            'proficiency.in' => 'Invalid value for proficiency.',
            'name.unique' => 'This name has been taken.',
            'category_id.exists' => 'This category does not exist.',
        ]);

        if ($validator->fails()) { // Validation fails.
            return Api::generateErrorResponse(105, 'InvalidFormDataError', Api::getFirstValidationError($validator));
        }

        // Update user profile.
        switch ($user->user_type) {
            case 'student':
                $user->userable->full_name = $input->retrieve('full_name', $user->userable->full_name);
                $user->userable->availability = $input->retrieve('availability', $user->userable->availability);
                $user->userable->hourly_rate = $input->retrieve('hourly_rate', $user->userable->hourly_rate);
                $user->userable->cv = $input->retrieve('cv', $user->userable->cv);
                $user->userable->available_for_job = $input->retrieve('available_for_job', $user->userable->available_for_job);
                $user->userable->proficiency = $input->retrieve('proficiency', $user->userable->proficiency);
                $user->userable->bio = $input->retrieve('bio', $user->userable->bio);
            break;

            case 'organization':
                $user->userable->name = $input->retrieve('name', $user->userable->name);
                $user->userable->description = $input->retrieve('description', $user->userable->description);
                $user->userable->category_id = $input->retrieve('category_id', $user->userable->category_id);
            break;
        }

        if (!empty($request->json('password'))) $user->password = password_hash($request->json('password'), PASSWORD_DEFAULT);
        $user->address = $input->retrieve('address', $user->address);
        $user->phone_number = $input->retrieve('phone_number', $user->phone_number);
        $user->profile_image = $input->retrieve('profile_image', $user->profile_image);

        $user->userable->save();
        $user->save();

        // Response.
        return [
            'success' => true,
            'payload' => [
                'data' => $user->refresh(),
            ]
        ];
    }
}
