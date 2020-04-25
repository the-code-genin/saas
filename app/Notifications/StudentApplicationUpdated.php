<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use App\Models\JobApplication;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StudentApplicationUpdated extends Notification
{
    use Queueable;

    private $application;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(JobApplication $application)
    {
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)->subject('Job Application' . ucfirst($this->application->status));

        if ($notifiable->userable_type == Student::class) {
            $message->greeting("Hello {$notifiable->userable->full_name}!")
                ->line("The job application you submitted for \"{$this->application->job->title}\" has been {$this->application->status}.");
        } else {
            $message->greeting("Hello {$notifiable->userable->name}!")
                ->line("You have {$this->application->status} a job application for \"{$this->application->job->title}\".");
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        if ($notifiable->userable_type == Student::class) {
            $message = "The job application you submitted for \"{$this->application->job->title}\" has been {$this->application->status}.";
        } else {
            $message = "You have {$this->application->status} a job application for \"{$this->application->job->title}\".";
        }

        return [
            'type' => 'JobApplication' . ucfirst($this->application->status),
            'message' => $message
        ];
    }
}
