<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AlumniResetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use Queueable;
    /**
     * Get the reset URL for the given notifiable.
     */
    protected function resetUrl(mixed $notifiable): string
    {
        return url(route('alumni.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $resetUrl = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Alumni Portal – Reset Your Password')
            ->line('You are receiving this email because we received a password reset request for your alumni account.')
            ->action('Reset Password', $resetUrl)
            ->line('This link will expire in ' . config('auth.passwords.alumni_forms.expire') . ' minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }
}
