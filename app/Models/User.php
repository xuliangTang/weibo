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

    // 用户粉丝列表
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    // 用户关注人列表
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    public function feed()
    {
        return $this->statuses()->orderBy('created_at', 'desc');
    }

    // 关注
    public function follow($user_ids)
    {
        if(!is_array($user_ids))
            $user_ids = compact('user_ids');
        $this->followings()->sync($user_ids, false);
    }

    // 取消关注
    public function unfollow($user_ids)
    {
        if(!is_array($user_ids))
            $user_ids = compact('user_ids');
        $this->followings()->detach($user_ids);
    }

    // 是否关注了某用户
    public function isFollowings($user_id)
    {
        return $this->followings->contains($user_id);
        // $this->followings()->get()
    }


    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }
}
