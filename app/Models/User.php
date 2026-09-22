<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Enquiry;
use App\Models\Auto\Auto;
use App\Models\UserInformation;
use App\Models\Masters\AutoAreas;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use App\Models\spareparts\SparepartOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'f_name',
        'l_name',
        'email',
        'phone_number',
        'password',
        'auto_area_id',
        'role_id',
        'otp',  
        'device_token',
        'selected_mode',
        'avatar',
        'provider_name',
        'provider_id',
        'token',
        'refresh_token',
        'device_id',
        'referal_code',
        'last_login',
        'is_guest',
        'seller',
        'vendor',
        'mechanic',
        'status',
        'is_online',
        'is_available',
        'current_location',
        'latitude',
        'longitude',
        'fare_price_enabled',
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
            'password'          => 'hashed',
            'latitude' => 'float',
            'longitude' => 'float',
            'fare_price_enabled' => 'boolean',
        ];
    }

    # role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function userInfo()
    {
        return $this->hasOne(UserInformation::class);
    }

    public function auto(){
        return $this->hasOne(Auto::class);
    }

    public function enquiries(){
        return $this->hasMany(Enquiry::class);
    }
  
    public function autoAreas()
    {
        return $this->belongsTo(AutoAreas::class, 'auto_area_id', 'id');
    }
    public function driverRequests()
    {
        return $this->hasMany(DriverRequest::class);
    }
    public function driverRequest()
    {
        return $this->hasOne(DriverRequest::class, 'user_id', 'id');
    }

    public function sparepartOrders()
    {
        return $this->hasMany(SparepartOrder::class, 'user_id');
    }

    public function targets()
    {
        return $this->hasMany(Target::class);
    }

    public function driverRides()
    {
        return $this->hasMany(RideRequest::class,'driver_id');
    }
    /**
     * Cheap pre-filter for "drivers near a point": a lat/lng square that fully
     * contains the search circle, so the indexed coordinates narrow the rows
     * before the exact distance formula runs. It never removes a driver the
     * distance check would have kept.
     */
    public function scopeNearBox($query, $lat, $lng, $radiusKm)
    {
        $lat = (float) $lat;
        $lng = (float) $lng;
        $km  = max((float) $radiusKm, 0.1) * 1.15; // 15% safety margin

        $latDelta = $km / 111.0;
        $lngDelta = $km / (111.0 * max(cos(deg2rad($lat)), 0.01));

        return $query
            ->whereBetween('latitude', [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta]);
    }
    /** Per-request answer to isStaff(); not a database column. */
    protected ?bool $staffMemo = null;

    /** role_id values that mean "customer / app user", not staff. */
    public const CUSTOMER_ROLE_IDS = [null, 0, 1000];

    /**
     * Staff = anyone who may use the admin panel. Customers and staff share this
     * table and the same login session, so both the admin guard (EnsureStaff) and
     * the website guard (CustomerOnly) ask this one question.
     */
    public function isStaff(): bool
    {
        // Asked by the header on every page, so answer from memory after the first time.
        return $this->staffMemo ??= (
            ! in_array($this->role_id, self::CUSTOMER_ROLE_IDS, false)
            || $this->roles()->where('name', '!=', 'user')->exists()
        );
    }
}
