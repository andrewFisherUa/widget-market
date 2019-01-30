<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Payout extends Notification
{

    /**
     * Create a new notification instance.
     *
     * @return void
     */
	public $status_payout;
    public function __construct($status_payout)
    {
        //
		$this->status_payout=$status_payout;
		$this->status_padObj=\App\AllNotification::find($this->status_payout);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
				->view('vendor.notifications.partner_payout', ['status_pad' => $this->status_padObj]);
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
