<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserDown extends Notification
{

    /**
     * Create a new notification instance.
     *
     * @return void
     */
	public $user_down;
    public function __construct($id)
    {
        //
		$this->user_down=$id;
		//$this->status_padObj=\App\AllNotification::find($this->status_payout);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
				->view('vendor.notifications.user_down')->subject('Вы давно к нам не заходили');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->status_payout;
    }
}
