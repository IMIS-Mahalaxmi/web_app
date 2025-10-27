@extends('layouts.dashboard')
@section('title', 'School Details')

@section('content')

<div class="card card-info p-3">
    <h3>{{ $school->name }}</h3>
    <hr>

    <p><strong>Headteacher Name:</strong> {{ $school->headteacher_name }}</p>
    <p><strong>Contact Person:</strong> {{ $school->contact_person_name }}</p>
    <p><strong>Contact Number:</strong> {{ $school->contact_person_number }}</p>
    <p><strong>Ward Number:</strong> {{ $school->ward_no }}</p>
    <p><strong>Location:</strong> {{ $school->location_name }}</p>
    <p><strong>Main Building Structure Type:</strong> {{ $school->main_building_structure_type }}</p>
    <p><strong>Main Building Floors:</strong> {{ $school->main_building_floors }}</p>
    <p><strong>Number of Associated Buildings:</strong> {{ $school->associate_buildings_count }}</p>

    <hr>
    <h5>School Types</h5>
    <ul>
        @if($school->school_type_pre_primary)<li>Pre-Primary</li>@endif
        @if($school->school_type_basic_1_5)<li>Basic (1-5)</li>@endif
        @if($school->school_type_basic_6_8)<li>Basic (6-8)</li>@endif
        @if($school->school_type_secondary_9_10)<li>Secondary (9-10)</li>@endif
        @if($school->school_type_secondary_9_12)<li>Secondary (9-12)</li>@endif
    </ul>

    <hr>
    <h5>Students</h5>
    @php
        $levels = [
            'pre_primary' => 'Pre-primary',
            'basic_1_5' => 'Basic (1-5)',
            'basic_6_8' => 'Basic (6-8)',
            'secondary_9_10' => 'Secondary (9-10)',
            'secondary_9_12' => 'Secondary (9-12)'
        ];
    @endphp
    @foreach($levels as $level => $label)
        <strong>{{ $label }}</strong>
        <p>Girls: {{ $school->{$level.'_girls_count'} }},
        Boys: {{ $school->{$level.'_boys_count'} }},
        Other: {{ $school->{$level.'_other_count'} }},
        Total: {{ $school->{$level.'_total_count'} }}</p>
    @endforeach

    <p><strong>Total Students:</strong> Girls: {{ $school->total_girls }},
        Boys: {{ $school->total_boys }},
        Other: {{ $school->total_other }},
        Grand Total: {{ $school->total_students }}</p>

    <hr>
    <h5>Staff</h5>
    <p>Male: {{ $school->teachers_male }},
       Female: {{ $school->teachers_female }},
       Other: {{ $school->teachers_other }},
       Total: {{ $school->teachers_total }}</p>

    @php
        $groups = ['toilet' => 'Toilets', 'urinal' => 'Urinals', 'handwash' => 'Handwashing Units'];
    @endphp
    @foreach($groups as $group => $label)
        <hr>
        <h5>{{ $label }}</h5>
        <p><strong>Teacher Staff:</strong>
            Male: {{ $school->{$group.'_teacher_male'} }},
            Female: {{ $school->{$group.'_teacher_female'} }},
            Other: {{ $school->{$group.'_teacher_other'} }},
            Total: {{ $school->{$group.'_teacher_total'} }}
        </p>
        <p><strong>Students:</strong>
            Male: {{ $school->{$group.'_student_male'} }},
            Female: {{ $school->{$group.'_student_female'} }},
            Other: {{ $school->{$group.'_student_other'} }},
            Total: {{ $school->{$group.'_student_total'} }}
        </p>
    @endforeach

    <hr>
    <h5>Other Facilities</h5>
    <p><strong>Universal Design Toilets:</strong> {{ $school->universal_design_toilet_count }}</p>
    <p><strong>Main Toilet Type:</strong> {{ $school->main_toilet_type }}</p>
    <p><strong>Toilet Connection:</strong> {{ $school->toilet_connection }}</p>
    <p><strong>Septic Outlet:</strong> {{ $school->septic_outlet }}</p>
    <p><strong>Pit Outlet:</strong> {{ $school->pit_outlet }}</p>
    <p><strong>Soap & Water Available:</strong> {{ $school->soap_and_water_available ? 'Yes' : 'No' }}</p>
    <p><strong>Main Drinking Water Source:</strong> {{ $school->main_drinking_water_source }}</p>
    <p><strong>Last Registration Renewal Date:</strong> {{ $school->last_registration_renewal_date }}</p>

    @if($school->certificate_picture_url)
        <hr>
        <h5>Certificate Picture</h5>
        <p><a href="{{ asset('storage/'.$school->certificate_picture_url) }}" target="_blank">View Certificate</a></p>
    @endif

    <hr>
    <a href="{{ route('education.school.edit', $school->id) }}" class="btn btn-warning">Edit</a>
    <a href="{{ route('education.school.index') }}" class="btn btn-secondary">Back to List</a>
</div>

@stop
