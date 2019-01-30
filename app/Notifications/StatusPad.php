<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StatusPad extends Notification
{

    /**
     * Create a new notification instance.
     *
     * @return void
     */
	public $status_pad;
	public $manager;
    public function __construct($status_pad)
    {
        //
		$this->status_pad=$status_pad;
		$this->status_padObj=\App\PartnerPad::find($this->status_pad);
		$user=\App\UserProfile::where('user_id', $this->status_padObj->user_id)->first();
		if (!$user){
			$this->manager=\App\UserProfile::where('user_id', '16')->first();
		}
		else{
			$this->manager=\App\UserProfile::where('user_id', $user->manager)->first();
		}
		if (!$this->manager){
			$this->manager=\App\UserProfile::where('user_id', '16')->first();
		}
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
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
				->view('vendor.notifications.partner_pads', ['pad' => $this->status_padObj, 'manager'=>$this->manager])->subject('Модерация площадки');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->status_pad;
    }
}
