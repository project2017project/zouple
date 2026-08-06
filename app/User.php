<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, SoftDeletes, MustVerifyEmailTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'contact', 'user_role', '_token', 'date',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Send the email-verification link.
     *
     * This deliberately does NOT use Laravel's default
     * sendEmailVerificationNotification() / config/mail.php pipeline,
     * because this app's outgoing mail is actually configured per-request
     * from the `mail_settings` DB table (see UsersController::reset_password()).
     * We mirror that same approach here so the email actually sends.
     *
     * @return void
     */
    public function sendCustomVerificationEmail()
    {
        $mail = DB::table('mail_settings')->where('slug', 'password')->first();

        if ($mail) {
            Config::set('mail', [
                'driver'     => $mail->driver,
                'host'       => $mail->host,
                'port'       => $mail->port,
                'from'       => ['address' => $mail->from_address, 'name' => $mail->from_name],
                'encryption' => $mail->encryption,
                'username'   => $mail->username,
                'password'   => $mail->password,
                'sendmail'   => '/usr/sbin/sendmail -bs',
                'pretend'    => false,
            ]);
        }

        // Same style link as reset_password(): built from the WEBSITE_URL
        // constant + a token column lookup, NOT Laravel's signed-route
        // system (which depends on APP_URL matching WEBSITE_URL exactly
        // and was the reason the link wasn't working).
        $url = rtrim(WEBSITE_URL, '/') . '/verify_email?token=' . $this->_token;

        $messageBody = "<!DOCTYPE html>
                <html lang='en'>

                <head>
                    <title>The Zouple</title>
                    <meta charset='utf-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1'>
                </head>

                <body>

                    <section style='width: 60%; min-height: 300px;padding: 15px;margin: 25px auto; background: rgba(255,255,255,.6);  display: block;border-radius: 2px;'>

                        <div style='text-align:center;'>
                            <h1 style='padding-left: 30px; text-align: center; font-size:30px!important; color:#969696;'>VERIFY YOUR EMAIL</h1>
                            <div style=' text-align: center;font-size: 16px; color:#969696; margin-bottom:5px;'>Thanks for registering with The Zouple. Please verify your email address to activate your account.
                            </div>
                            <a class='link-btn' href='{$url}' style='padding: 10px 20px;font-size: 18px;line-height: 24px;background: #000000;margin: 30px auto;display: block; width: 220px;text-align: center;color: #fff;text-transform: uppercase;text-decoration: none;'>VERIFY EMAIL</a>

                            <div style='font-size: 16px;margin-top:30px; margin-bottom: 5px; color:#969696; text-align: center;'>If you did not create this account, you can ignore this email.</div>
                            <div style='font-size: 16px; margin-top:40px; color:#969696; text-align: left;  margin-left:15px;'>
                                Thank You,
                            </div>
                            <div style='font-size: 16px; color:#969696; text-align: left; margin-left:15px;'>
                                The Zouple Team
                            </div>

                        </div>

                    </section>
                </body>

                </html>
                ";

        $subject = 'Verify your The Zouple account email';
        $data['msg'] = $messageBody;
        $data['subject'] = $subject;
        $data['email'] = $this->email;

        Mail::send([], [], function ($message) use ($data) {
            $message->to($data['email'])->subject($data['subject'])
                ->setBody($data['msg'], 'text/html');
        });
    }
}
