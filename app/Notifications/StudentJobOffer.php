<?php

namespace App\Notifications;

use App\Models\Student;
use App\Models\StudentHire;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StudentJobOffer extends Notification
{
    use Queueable;

    private $offer;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(StudentHire $offer)
    {
        $this->offer = $offer;
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
        $message = (new MailMessage)->subject('Job Offer');

        if ($notifiable->userable_type == Student::class) {
            $message->greeting("Hello {$notifiable->userable->full_name}!")
                ->line("{$this->offer->organization->userable->name} has sent you a job offer.");
        } else {
            $message->greeting("Hello {$notifiable->userable->name}!")
                ->line("You have sent a job offer to {$this->offer->student->userable->full_name}.")
                ->line('We will notify you when the student either accepts or rejects the offer.');
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
            $message = "{$this->offer->organization->userable->name} has sent you a job offer.";
        } else {
            $message = "You have sent a job offer to {$this->offer->student->userable->full_name}.";
        }

        return [
            'type' => 'JobOffer',
            'message' => $message
        ];
    }
}
