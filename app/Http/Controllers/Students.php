<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Job;
use App\Helpers\Api;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\JobApplication;
use App\Models\StudentProfileView;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Exceptions\AuthenticationError;
use App\Exceptions\InvalidFormDataError;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StudentApplicationSubmitted;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Students extends Controller
{
    /**
     * Get profile overview for a student.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function profileOverview(Request $request): array
    {
        // Statistical data.
        $noJobsAppliedFor = Job::whereHas('applications', function (Builder $applications) use($request) {
            $applications->where('student_id', $request->user()->id);
        })->count();

        $noJobsAppliedDone = Job::whereHas('applications', function (Builder $applications) use($request) {
            $applications->where('student_id', $request->user()->id)->where('status', 'accepted');
        })->count();


        $now = Carbon::now();

        // Total views for the year.
        $totalViews = $request->user()->userable->views()
            ->where(DB::raw('YEAR(student_profile_views.created_at)'), $now->year)
            ->count();

        // View data for the month.
        $monthData = [
            'total' => $request->user()->userable->views()
                ->where(DB::raw('YEAR(student_profile_views.created_at)'), $now->year)
                ->where(DB::raw('MONTH(student_profile_views.created_at)'), $now->month)
                ->count()
        ];
        $weekData = [];

        foreach (range(1, 12) as $i) {
            $monthData[$i] = $request->user()->userable->views()
                ->where(DB::raw('YEAR(student_profile_views.created_at)'), $now->year)
                ->where(DB::raw('MONTH(student_profile_views.created_at)'), $i)
                ->count();

            if ($i == $now->month) {
                foreach (range(1, 4) as $j) {
                    $weekData[$j] = $request->user()->userable->views()
                        ->where(DB::raw('YEAR(student_profile_views.created_at)'), $now->year)
                        ->where(DB::raw('MONTH(student_profile_views.created_at)'), $i)
                        ->where(
                            DB::raw('WEEK(student_profile_views.created_at,5)-WEEK(DATE_SUB(student_profile_views.created_at, INTERVAL DAYOFMONTH(student_profile_views.created_at)-1 DAY),5)+1'),
                            $j
                        )
                        ->count();
                }
            }
        }



        // Response
        return [
            'success' => true,
            'payload' => [
                'data' => [
                    'jobs_applied_for' => $noJobsAppliedFor,
                    'jobs_done' => $noJobsAppliedDone,
                    'visits' => [
                        'total' => $totalViews,
                        'weekly' => $weekData,
                        'monthly' => $monthData
                    ],
                ]
            ]
        ];
    }

    /**
     * update profile visits for a student.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $student
     *
     * @return void
     */
    public function updateVisits(Request $request, User $student): array
    {
        if ($student->userable_type != Student::class) {
            throw new NotFoundHttpException('The resource you requested for was not found.');
        }

        // Add a viewed record to the database.
        $view = new StudentProfileView();
        $view->organization_id = $request->user()->id;
        $view->student_id = $student->id;
        $view->save();

        // Response
        return [
            'success' => true,
            'payload' => []
        ];
    }

    /**
     * Apply for a job.
     *
     * @param \Illuminate\Http\Request $request
     * @param Job $job
     *
     * @return array
     */
    public function applyForJob(Request $request, Job $job): array
    {
        // Check if the user can apply for the job.
        if (!$request->user()->can('apply', $job)) {
            throw new AuthenticationError('You can not apply for the job.');
        }

        // Validate input.
        $validator = Validator::make($request->json()->all(), [
            'proposal' => 'required',
            'previous_experience' => 'required',
            'cover_letter' => 'required',
        ]);

        if ($validator->fails()) { // Validation fails.
            throw new InvalidFormDataError(Api::getFirstValidationError($validator));
        }

        // Apply for the job.
        $application = new JobApplication();
        $application->proposal = $request->json('proposal');
        $application->previous_experience = $request->json('previous_experience');
        $application->cover_letter = $request->json('cover_letter');
        $application->student_id = $request->user()->id;
        $job->applications()->save($application);

        // Send notifications to the organization and student.
        Notification::send([$request->user(), $job->organization], new StudentApplicationSubmitted($application));

        // Response.
        return [
            'success' => true,
            'payload' => [
                'data' => $application->refresh()->load(['job', 'job.organization', 'job.skills'])
            ]
        ];
    }
}
