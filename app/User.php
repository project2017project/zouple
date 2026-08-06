<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->id, 'hash' => sha1($this->email)]
        );

        $messageBody = "<!DOCTYPE html><html><body>
            <p>Please verify your email: <a href='{$url}'>Verify Email</a></p>
            <p>This link expires in 60 minutes.</p>
            </body></html>";

        $data = [
            'msg'     => $messageBody,
            'subject' => 'Verify your The Zouple account email',
            'email'   => $this->email,
        ];

        Mail::send([], [], function ($message) use ($data) {
            $message->to($data['email'])->subject($data['subject'])
                ->setBody($data['msg'], 'text/html');
        });
    }
}
