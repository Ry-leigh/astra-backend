<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAnnouncement extends Notification
{
    use Queueable;

    protected $announcement;


    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $title = $this->announcement->title;

        if ($this->announcement->event_date) {
            $title .= " on {$this->announcement->event_date}";
            if ($this->announcement->event_time) {
                $title .= " at {$this->announcement->event_time}";
            }
        }

        return [
            'title' => $title,
            'message' => $this->announcement->description,
            'announcement_id' => $this->announcement->id,
            'created_by' => $this->announcement->created_by,
        ];
    }


    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     $title = $this->announcement->title;

    //     // Check if event_date and event_time are not null and build the title accordingly
    //     if ($this->announcement->event_date) {
    //         $title .= " on {$this->announcement->event_date}";

    //         if ($this->announcement->event_time) {
    //             $title .= " at {$this->announcement->event_time}";
    //         }
    //     }
        
    //     return (new \Illuminate\Notifications\Messages\MailMessage)
    //         ->subject('New Announcement Posted')
    //         ->line("{$title}")
    //         ->line("{$this->announcement->description}")
    //         ->action('View Announcement', url("/announcements/{$this->announcement->id}"));
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
