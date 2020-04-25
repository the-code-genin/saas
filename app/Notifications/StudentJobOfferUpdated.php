<?php

namespace App\Notifications;

use App\Models\Student;
use App\Models\StudentHire;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StudentJobOfferUpdated extends Notification
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
        $message = (new MailMessage)->subject('Job Offer ' . ucfirst($this->offer->status));

        if ($notifiable->userable_type == Student::class) {
            $message->greeting("Hello {$notifiable->userable->full_name}!")
                ->line("You have {$this->offer->status} a job offer from \"{$this->offer->organization->userable->name}\".");
        } else {
            $message->greeting("Hello {$notifiable->userable->name}!")
                ->line("The job offer you sent to \"{$this->offer->student->userable->full_name}\" has been {$this->offer->status}.");
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
            $message = "You have {$this->offer->status} a job offer from \"{$this->offer->organization->userable->name}\".";
        } else {
            $message = "The job offer you sent to \"{$this->offer->student->userable->full_name}\" has been {$this->offer->status}.";
        }

        return [
            'type' => 'JobOffer' . ucfirst($this->offer->status),
            'message' => $message
        ];
    }
}
