<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    
    /**
     * お気に入り登録している投稿を取得
     */
    public function get_favorites() {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    /**
    * お気に入り登録する
    */
    public function set_favorites($micropostId) {
         // 既にお気に入り登録しているかの確認
         $exist = $this->is_favorites($micropostId);
         
        if ($exist) {
            // 既にお気に入り登録していれば何もしない
            return false;
        } else {
            // お気に入り登録していない場合には登録する
            $this->get_favorites()->attach($micropostId);
            return true;
        }
    }
    
    /**
    * お気に入り登録から外す
    */
    public function del_favorites($micropostId) {
         // 既にお気に入り登録しているかの確認
         $exist = $this->is_favorites($micropostId);
         
        if ($exist) {
            // 既にお気に入り登録していればお気に入りを外す
            $this->get_favorites()->detach($micropostId);
            return true;
        } else {
            // お気に入り登録していない場合には何もしない
            return false;
        }
    }
     
    /**
    * お気に入り登録しているかの確認
    */
    public function is_favorites($micropostId) {
         return $this->get_favorites()->where('micropost_id', $micropostId)->exists();
    }
     
    public function microposts() {
        return $this->hasMany(Micropost::class);
    }
    
    /**
     * フォローしているユーザ達を取得
     */
    public function followings() {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps(); 
    }
    
    public function followers() {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    /**
     * フォローする
     */
    public function follow($userId) {
        // 既にフォローしているか確認
        $exist = $this->is_following($userId);
        // 自分自身でないかの確認
        $its_me = $this->id == $userId;
        
        if($exist || $its_me) {
            // 既にフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    /**
     * フォローを外す
     */
    public function unfollow($userId) {
        // 既にフォローしているか確認
        $exist = $this->is_following($userId);
        // 自分自身でないかの確認
        $its_me = $this->id == $userId;
        
        if($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    /**
     * フォローしているか確認
     */
    public function is_following($userId) {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts() {
        $follow_user_ids = $this->followings()->lists('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
}
