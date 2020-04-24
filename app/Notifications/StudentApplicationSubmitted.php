<?php

namespace App\Notifications;

use App\Models\JobApplication;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentApplicationSubmitted extends Notification
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
        $message = new MailMessage;

        if ($notifiable->userable_type == Student::class) {
            $message->greeting("Hello {$notifiable->userable->full_name}!")
                ->line('Your job application has been submitted.');
        } else {
            $message->greeting("Hello {$notifiable->userable->name}!")
                ->line('A student has applied for one of the jobs you posted.');
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
            $message = 'Your job application has been submitted.';
        } else {
            $message = 'A student has applied for one of the jobs you posted.';
        }

        return [
            'type' => 'StudentApplicationSubmitted',
            'message' => $message
        ];
    }
}
