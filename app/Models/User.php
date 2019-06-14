<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;


class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users'; // 数据库表名

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [     // 只有包含在该属性中的字段才能够被正常更新
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

    public static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        // 监听模型被创建之前的事件
        static::creating(function ($user){
           $user->activation_token = Str::random(10);   // 生成令牌
        });
    }

    // 用户 -> 微博 一对多关联
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }
}
