<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content'];
    /**
     * この投稿を所有するユーザ。（ Userモデルとの関係を定義）
     */
    //
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
     /**
     * このユーザーのお気に入りの一覧を取得する。
     */
    public function favorite_users()
    {
        return $this->belongsToMany(User::class, 'favorites', 'micropost_id', 'user_id')->withTimestamps();
    }
    
    /**
     * $postIdで指定された投稿をお気に入りにする。
     *
     * @param  int  $postId
     * @return bool
     */
    public function favorite($postId)/////投稿ＩＤ？
    {
        // すでにお気に入りしているかの確認
        $exist = $this->is_favoriting($postId);
        // 自分の投稿かどうかの確認
        $its_me = $this->id == $postId;

        if ($exist || $its_me) {
            // すでにお気に入りしていれば何もしない
            return false;
        } else {
            //お気に入りしていなければお気に入りにする
            $this->favorites()->attach($postId);
            return true;
        }
    }
    
    /**
     * $userIdで指定された投稿のお気に入りを解除する。
     *
     * @param  int  $postId
     * @return bool
     */
    public function unfavorite($postId)
    {
        // すでにお気に入りしているかの確認
        $exist = $this->is_favoriting($postId);/////投稿ＩＤ」？
        // 自分の投稿かどうかの確認
        $its_me = $this->id == $postId;

        if ($exist && !$its_me) {
            // すでにお気に入りしていればお気に入りを解除
            $this->favorites()->detach($postId);
            return true;
        } else {
            // お気に入りしていなければ何もしない
            return false;
        }
    }
    
    
    /**
     * 指定された $userIdのユーザをこのユーザがフォロー中であるか調べる。フォロー中ならtrueを返す。
     *
     * @param  int  $userId
     * @return bool
     */
    public function is_favoriting($postId)
    {
        // お気に入り中の投稿の中に $postrIdのものが存在するか
        return $this->favorites()->where('micropost_id', $postId)->exists();
    }
}
