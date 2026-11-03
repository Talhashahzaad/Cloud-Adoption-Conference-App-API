<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Custom verification email template
       VerifyEmail::toMailUsing(function ($notifiable, string $url) {
      return (new MailMessage)
          ->subject('Verify your email address')
          ->view('emails.verify-email', [
              'url' => $url,
              'name' => $notifiable->name,
          ]);
  });

  ResetPassword::toMailUsing(function ($notifiable, string $token) {
      $url = url(route('password.reset', [
          'token' => $token,
          'email' => $notifiable->getEmailForPasswordReset(),
      ], false));

      return (new MailMessage)
          ->subject('Reset your password')
          ->view('emails.reset-password', [
              'url' => $url,
              'name' => $notifiable->name,
          ]);
  });

    }
}