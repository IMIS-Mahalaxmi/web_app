<?php

namespace App\Models\EducationInfo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    /* use HasFactory; */
    use SoftDeletes;

    protected $table = 'educationinfo.schools';
    protected $primaryKey = 'custom_school_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'custom_school_id',
        'name',
        'headteacher_name',
        'contact_person_name',
        'contact_person_number',
        'ward_no',
        'bin',
        'location_name',
        'main_building_structure_type',
        'main_building_floors',
        'associate_buildings_count',
        'school_type_pre_primary',
        'school_type_basic_1_5',
        'school_type_basic_6_8',
        'school_type_secondary_9_10',
        'school_type_secondary_9_12',
        'pre_primary_girls_count',
        'pre_primary_boys_count',
        'pre_primary_other_count',
        'pre_primary_total_count',
        'basic_1_5_girls_count',
        'basic_1_5_boys_count',
        'basic_1_5_other_count',
        'basic_1_5_total_count',
        'basic_6_8_girls_count',
        'basic_6_8_boys_count',
        'basic_6_8_other_count',
        'basic_6_8_total_count',
        'secondary_9_10_girls_count',
        'secondary_9_10_boys_count',
        'secondary_9_10_other_count',
        'secondary_9_10_total_count',
        'total_girls',
        'total_boys',
        'total_other',
        'total_students',
        'teachers_male',
        'teachers_female',
        'teachers_other',
        'teachers_total',
        'support_staff_male',
        'support_staff_female',
        'support_staff_other',
        'support_staff_total',
        'toilet_teacher_male',
        'toilet_teacher_female',
        'toilet_teacher_other',
        'toilet_teacher_total',
        'toilet_student_male',
        'toilet_student_female',
        'toilet_student_other',
        'toilet_student_total',
        'urinal_teacher_male',
        'urinal_teacher_female',
        'urinal_teacher_other',
        'urinal_teacher_total',
        'urinal_student_male',
        'urinal_student_female',
        'urinal_student_other',
        'urinal_student_total',
        'handwash_teacher_male',
        'handwash_teacher_female',
        'handwash_teacher_other',
        'handwash_teacher_total',
        'handwash_student_male',
        'handwash_student_female',
        'handwash_student_other',
        'handwash_student_total',
        'universal_design_toilet_count',
        'main_toilet_type',
        'toilet_connection',
        'septic_outlet',
        'pit_outlet',
        'soap_and_water_available',
        'main_drinking_water_source',
        'last_registration_renewal_date',
        'certificate_picture_url',
        'remarks',
        'geom'
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'ward_no' => 'integer',
        'main_building_floors' => 'integer',
        'associate_buildings_count' => 'integer',
        'school_type_pre_primary' => 'boolean',
        'school_type_basic_1_5' => 'boolean',
        'school_type_basic_6_8' => 'boolean',
        'school_type_secondary_9_10' => 'boolean',
        'school_type_secondary_9_12' => 'boolean',
        'soap_and_water_available' => 'boolean',
        'last_registration_renewal_date' => 'date',
    ];

    public function building()
    {
        return $this->belongsTo('App\Models\BuildingInfo\Building','bin','bin');

    }
}
