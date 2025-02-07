<?php
// Last Modified Date: 18-04-2024
// Developed By: Innovative Solution Pvt. Ltd. (ISPL)
namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Fsm\HelpDesk;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use \App\Models\Fsm\TreatmentPlant;
use App\Models\Fsm\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Yadahan\AuthenticationLog\AuthenticationLogable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use HasApiTokens;
    use SoftDeletes;
    use AuthenticationLogable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'auth.users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'username', 'password', 'status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function wards()
    {
        return $this->belongsTo('App\Ward');
    }

    public function treatment_plant()
    {
        return $this->belongsTo(TreatmentPlant::class, 'treatment_plant_id');
    }

    public function help_desk()
    {
        return $this->belongsTo(HelpDesk::class, 'help_desk_id');
    }

    public function service_provider()
    {
        return $this->belongsTo('App\Models\Fsm\ServiceProvider', 'service_provider_id');
    }
    public function emptyingServices()
    {
        return $this->hasMany('App\Models\Fsm\EmptyingService');
    }

    public function hasanyPermissionInGroup(array $permgroupNames)
    {
        if (!is_array($permgroupNames)) {
            $permgroupNames = [$permgroupNames]; // Convert to array if a single group name is passed
        }

        $user = Auth::user();
        $roleIds = $user->roles->pluck('id')->unique()->toArray();  // Get unique role IDs
        $permissions = DB::table('auth.role_has_permissions')
            ->whereIn('role_id', array_values($roleIds))
            ->pluck('permission_id')
            ->unique()->toArray();

        // Now query the permissions table and filter by group using whereIn
        $filteredPermissions = DB::table('auth.permissions')
                        ->whereIn('id', $permissions)  // Use whereIn to filter by group
                        ->pluck('group')->unique()->toArray();  // Get the results

        // Compare if there is any common group between $filteredPermissions and $groupNames
        $isInGroup = !empty(array_intersect($filteredPermissions, $permgroupNames));

        return $isInGroup;  // Will return true if any group name matches, otherwise false
    }

    public function buildingSurveys()
    {
        return $this->hasMany('App\Models\BuildingInfo\BuildingSurvey','user_id','id');
    }

    public function buildings()
    {
        return $this->hasMany('App\Models\BuildingInfo\Building','user_id','id');
    }

    public function applications()
    {
        return $this->hasMany('App\Models\Fsm\Application','user_id','id');
    }
    public function containments()
    {
        return $this->hasMany('App\Models\Fsm\Containment','user_id','id');
    }
    public function employees()
    {
        return $this->hasMany('App\Models\Fsm\EmployeeInfo','user_id','id');
    }
    public function emptyings()
    {
        return $this->hasMany('App\Models\Fsm\Emptying','user_id','id');
    }
    public function feedbacks()
    {
        return $this->hasMany('App\Models\Fsm\Feedback','user_id','id');
    }
    public function serviceProviders()
    {
        return $this->hasMany('App\Models\Fsm\ServiceProvider','user_id','id');
    }
    public function sludgeCollections()
    {
        return $this->hasMany('App\Models\Fsm\SludgeCollection','user_id','id');
    }
    public function treatmetnPlantTests()
    {
        return $this->hasMany('App\Models\Fsm\TreatmentPlantTest','user_id','id');
    }
    public function lics()
    {
        return $this->hasMany('App\Models\LayerInfo\LowIncomeCommunity','user_id','id');
    }
    public function waterSamples()
    {
        return $this->hasMany('App\Models\PublicHealth\WaterSamples','user_id','id');
    }
    public function hotSpots()
    {
        return $this->hasMany('App\Models\PublicHealth\Hotspots','user_id','id');
    }
    public function yearlyWaterborneCases()
    {
        return $this->hasMany('App\Models\PublicHealth\YearlyWaterborne','user_id','id');
    }
    public function sewerConnections()
    {
        return $this->hasMany('App\Models\SewerConnection\SewerConnection','user_id','id');
    }
    public function drains()
    {
        return $this->hasMany('App\Models\UtilityInfo\Drain','user_id','id');
    }
    public function sewers()
    {
        return $this->hasMany('App\Models\UtilityInfo\SewerLine','user_id','id');
    }
    public function watersupplys()
    {
        return $this->hasMany('App\Models\UtilityInfo\WaterSupplys','user_id','id');
    }
    public function roads()
    {
        return $this->hasMany('App\Models\UtilityInfo\Roadline','user_id','id');
    }



}

