<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorEnabledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        // يمكن تخصيص بيانات إضافية هنا إن لزم الأمر
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('مصادقة ثنائية مفعلة')
                    ->greeting('مرحبًا ' . $notifiable->name . ',')
                    ->line('لقد قمت بتفعيل المصادقة الثنائية (2FA) على حسابك.')
                    ->line('من الآن فصاعدًا، ستحتاج إلى إدخال رمز التحقق عند تسجيل الدخول.')
                    ->action('إدارة الإعدادات', url('/profile/security'))
                    ->line('إذا لم تقم أنت بتفعيل المصادقة الثنائية، يرجى التواصل معنا فورًا.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'تم تفعيل المصادقة الثنائية على حسابك.',
            'url' => url('/profile/security')
        ];
    }
}
