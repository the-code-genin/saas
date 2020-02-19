<?php
namespace App\Controllers\Api;

use App\Helpers\Api;
use App\Models\User;
use Cradle\Controller;
use App\Models\Student;
use Valitron\Validator;
use App\Models\Organization;
use App\Models\OrganizationCategory;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Resource controller for users.
 */
class Users extends Controller
{

    /**
     * Get the current logged in user.
     *
     * @param ServerRequestInterface $request
     * @param object $params
     * @return array
     */
    protected function index(ServerRequestInterface $request, object $params): array
    {
        $payload = [
            'data' => $request->getAttribute('user'),
        ];

        return [
            'success' => true,
            'payload' => $payload
        ];
    }

    /**
     * Log a user in.
     *
     * @param ServerRequestInterface $request
     * @param object $params
     * @return array
     */
    protected function login(ServerRequestInterface $request, object $params): array
    {
        $input = $request->getAttribute('body');

        $validator = new Validator((array) $input);
        $validator->rule('required', ['email', 'password']);
        $validator->rule('email', 'email');
        if (!$validator->validate()) { // Validation fails.
            $errors = $validator->errors();
            return Api::generateErrorResponse(401, 'AuthenticationError', array_shift($errors)[0]);
        }

        // Authenticate user by email and password.
        $user = User::where('email', $input->email)->first();
        if (is_null($user)) {
            return Api::generateErrorResponse(401, 'AuthenticationError', 'User email and password do not match.');
        } else if (!password_verify($input->password, $user->password)) {
            return Api::generateErrorResponse(401, 'AuthenticationError', 'User email and password do not match.');
        }

        // Generate and save api token.
        $hex = bin2hex(random_bytes(64));
        $apiToken = '$' . '.' . $user->id . '.' . password_hash($hex, PASSWORD_DEFAULT) . '.' . $hex;

        $this->db->table('user_api_tokens')->insert([
            'user_id' => $user->id,
            'token' => $apiToken,
        ]);

        // Response.
        return [
            'success' => true,
            'payload' => [
                'data' => $user,
                'token' => $apiToken,
            ]
        ];
    }

    /**
     * Sign a user up.
     *
     * @param ServerRequestInterface $request
     * @param object $params
     * @return array
     */
    protected function signup(ServerRequestInterface $request, object $params): array
    {
        $input = $request->getAttribute('body');

        // Validate input.
        $validator = new Validator((array) $input);
        $validator->rule('required', ['email', 'password', 'address', 'phone_number', 'user_type']);
        $validator->rule('email', 'email');
        $validator->rule('lengthMin', 'password', 6);
        $validator->rule('in', 'user_type', ['student', 'organization']);
        $validator->rule('regex', 'phone_number', '/^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/i');
        $validator->rule(function($field, $value, $params, $fields) {
            return User::where('email', $value)->count() == 0;
        }, 'email')->message("This email has been taken.");

        if (!$validator->validate()) { // Validation fails.
            $errors = $validator->errors();
            return Api::generateErrorResponse(105, 'InvalidFormDataError', array_shift($errors)[0]);
        }

        switch ($input->user_type) {
            case 'student':
                $validator->rule('required', ['full_name', 'availability', 'hourly_rate']);
                $validator->rule('in', 'availability', ['freelance', 'part_time']);
                $validator->rule('numeric', 'hourly_rate');
            break;
            
            case 'organization':
                $validator->rule('required', ['name', 'description', 'category_id']);
                $validator->rule(function($field, $value, $params, $fields) {
                    return OrganizationCategory::where('id', $value)->count() == 1;
                }, 'category_id')->message("The chosen category does not exist.");
                $validator->rule(function($field, $value, $params, $fields) {
                    return Organization::where('name', $value)->count() == 0;
                }, 'name')->message("This name has been taken.");
            break;
        }

        if (!$validator->validate()) { // Validation fails.
            $errors = $validator->errors();
            return Api::generateErrorResponse(401, 'InvalidFormDataError', array_shift($errors)[0]);
        }


        // Create user account.
        switch ($input->user_type) {
            case 'student':
                $userable = new Student;
                $userable->full_name = $input->full_name;
                $userable->availability = $input->availability;
                $userable->hourly_rate = $input->hourly_rate;
            break;

            case 'organization':
                $userable = new Organization;
                $userable->name = $input->name;
                $userable->description = $input->description;
                $userable->category_id = $input->category_id;
            break;
        }

        $user = new User;
        $user->email = $input->email;
        $user->password = password_hash($input->password, PASSWORD_DEFAULT);
        $user->address = $input->address;
        $user->phone_number = $input->phone_number;

        $userable->save();
        $user->userable()->associate($userable);
        $user->save();


        // Send activation email.


        // Response
        $payload = [
            'data' => $user,
        ];

        return [
            'success' => true,
            'payload' => $payload
        ];
    }
}
