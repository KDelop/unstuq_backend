<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
class User extends Authenticatable implements JWTSubject{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','phone','access_code','status','country_code'
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

    protected $primaryKey = 'id';

    public $timestamps = false;

     /**
     *  Setup model event hooks
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->created_at = (string)gmdate('Y-m-d H:i:s');
        });
    }

     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

     /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray()
    {
        $status = '';
        switch($this->status){
            case 1: $status = 'active'; break;
            case 0: $status = 'unverified'; break;
            case 2: $status = 'pending'; break;
        }

        if(isset($this->info)){
            $location_string = isset(json_decode($this->info)[0]->location_string) ? json_decode($this->info)[0]->location_string :'';
            $favourite_icon = isset(json_decode($this->info)[0]->photo) ? json_decode($this->info)[0]->photo :'';
            $rating = $this->rating;
        }
        else{
            $location_string ='';
            $favourite_icon = isset($this->network) ? $this->network :'';
            $rating = $this->vote_average;
        }
        if(isset($this->userCredit->balance)){
            $balance=$this->userCredit->balance;
        }else{
            $balance=0;
        }
        $premiumUser=false;
        if(isset($this->PremiumUser)){
            $premiumUser=true;
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->avatar,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $status,
            'created_at' => $this->created_at,
            'credits' => $balance,
            'premium_user' => $premiumUser,
        ];
    }

    public function groups()
    {
        return $this->belongsToMany(UserGroup::class, UserGroupMember::class)->withPivot('is_admin','created_at');
    }
    public function userCredit(){
        return $this->hasOne(UserCredit::class, 'user_id', 'id');
    }
    public function devices()
    {
        return $this->hasMany(UserDevice::class, 'user_id', 'id');
    }

    public function feedbacks()
    {
        return $this->hasMany(UserFeedbacks::class, 'user_id', 'id');
    }

    public function favorites()
    {
        return $this->hasMany(UserFavorite::class, 'user_id', 'id');
    }
    public function experience()
    {
        return $this->hasMany(Experience::class, 'user_id', 'id');
    }
    public function userExperience()
    {
        return $this->hasMany(UserExperience::class, 'user_id', 'id');
    }
    public function User()
    {
        return $this->hasMany(User::class, 'user_id', 'id');
    }
    public function UserSource(){
        return $this->hasMany(UserSource::class);
    }
    public function PremiumUser(){
        return $this->hasOne(PremiumUsers::class, 'user_id', 'id');
    }
    // public function Source()
    // {
    //     return $this->hasManyThrough(Source::class, UserSource::class);
    // }
    // public function Source()
    // {
    //     return $this->hasOneThrough(
    //         Source::class,
    //         UserSource::class,
    //         'user_id', // Foreign key on the cars table...
    //         'source_id', // Foreign key on the owners table...
    //         'id', // Local key on the mechanics table...
    //         'id' // Local key on the cars table...
    //     );
    // }
}
