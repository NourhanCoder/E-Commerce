<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
     use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'active',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

      public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function cartItems()
   {
     return $this->hasMany(CartItem::class);
   }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

      public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    //لعرض كل المنتجات اللي ضافها المستخدم للمفضلة
    public function favouriteProducts()
    {
      return $this->belongsToMany(Product::class, 'favourites');
    }


     public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    //to avoid writing image path in blade files
    public function image()
{
    return $this->image ? asset('public/users/' . $this->image) : null;
}

}
