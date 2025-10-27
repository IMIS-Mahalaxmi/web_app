@extends('layouts.dashboard')
@section('title', 'Edit School')

@section('content')
@include('layouts.components.error-list')

<div class="card card-info">
    {!! Form::model($school, [
        'route' => ['education.school.update', $school->custom_school_id],
        'method' => 'PUT',
        'files' => true
    ]) !!}
    <div class="card-body">

        <div class="form-group">
            {!! Form::label('name', 'School Name') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter School Name']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('headteacher_name', 'Headteacher Name') !!}
            {!! Form::text('headteacher_name', null, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('contact_person_name', 'Contact Person') !!}
            {!! Form::text('contact_person_name', null, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('contact_person_number', 'Contact Number') !!}
            {!! Form::text('contact_person_number', null, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('ward_no', 'Ward Number') !!}
            {!! Form::number('ward_no', null, ['class' => 'form-control', 'min' => 1]) !!}
        </div>

        <div class="form-group">
            {!! Form::label('location_name', 'Location') !!}
            {!! Form::text('location_name', null, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('main_building_structure_type', 'Main Building Structure Type') !!}
            {!! Form::text('main_building_structure_type', null, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('main_building_floors', 'Main Building Floors') !!}
            {!! Form::number('main_building_floors', null, ['class' => 'form-control', 'min' => 0]) !!}
        </div>

        <div class="form-group">
            {!! Form::label('associate_buildings_count', 'Number of Associated Buildings') !!}
            {!! Form::number('associate_buildings_count', null, ['class' => 'form-control', 'min' => 0]) !!}
        </div>

        <hr>
        <h5>School Types</h5>
        <div class="form-check">
            {!! Form::checkbox('school_type_pre_primary', 1, $school->school_type_pre_primary, ['class' => 'form-check-input']) !!}
            {!! Form::label('school_type_pre_primary', 'Pre-Primary') !!}
        </div>
        <div class="form-check">
            {!! Form::checkbox('school_type_basic_1_5', 1, $school->school_type_basic_1_5, ['class' => 'form-check-input']) !!}
            {!! Form::label('school_type_basic_1_5', 'Basic 1-5') !!}
        </div>
        <div class="form-check">
            {!! Form::checkbox('school_type_basic_6_8', 1, $school->school_type_basic_6_8, ['class' => 'form-check-input']) !!}
            {!! Form::label('school_type_basic_6_8', 'Basic 6-8') !!}
        </div>
        <div class="form-check">
            {!! Form::checkbox('school_type_secondary_9_10', 1, $school->school_type_secondary_9_10, ['class' => 'form-check-input']) !!}
            {!! Form::label('school_type_secondary_9_10', 'Secondary 9-10') !!}
        </div>
        <div class="form-check">
            {!! Form::checkbox('school_type_secondary_9_12', 1, $school->school_type_secondary_9_12, ['class' => 'form-check-input']) !!}
            {!! Form::label('school_type_secondary_9_12', 'Secondary 9-12') !!}
        </div>

        @php
            $levels = [
                'pre_primary' => 'Pre-primary',
                'basic_1_5' => 'Basic (1-5)',
                'basic_6_8' => 'Basic (6-8)',
                'secondary_9_10' => 'Secondary (9-10)',
                'secondary_9_12' => 'Secondary (9-12)'
            ];
        @endphp

        <hr>
        <h5>No. of Students</h5>
        @foreach($levels as $level => $label)
            <div class="form-group">
                <label>{{ $label }}</label>
                <div class="form-row">
                    <div class="col">
                        {!! Form::number($level.'_girls_count', null, ['class' => 'form-control', 'placeholder' => 'Girls']) !!}
                    </div>
                    <div class="col">
                        {!! Form::number($level.'_boys_count', null, ['class' => 'form-control', 'placeholder' => 'Boys']) !!}
                    </div>
                    <div class="col">
                        {!! Form::number($level.'_other_count', null, ['class' => 'form-control', 'placeholder' => 'Other']) !!}
                    </div>
                    <div class="col">
                        {!! Form::number($level.'_total_count', null, ['class' => 'form-control', 'placeholder' => 'Total']) !!}
                    </div>
                </div>
            </div>
        @endforeach

        <div class="form-group">
            <label>Total Students</label>
            <div class="form-row">
                <div class="col">
                    {!! Form::number('total_girls', null, ['class' => 'form-control', 'placeholder' => 'Total Girls']) !!}
                </div>
                <div class="col">
                    {!! Form::number('total_boys', null, ['class' => 'form-control', 'placeholder' => 'Total Boys']) !!}
                </div>
                <div class="col">
                    {!! Form::number('total_other', null, ['class' => 'form-control', 'placeholder' => 'Total Other']) !!}
                </div>
                <div class="col">
                    {!! Form::number('total_students', null, ['class' => 'form-control', 'placeholder' => 'Grand Total']) !!}
                </div>
            </div>
        </div>

        <hr>
        <h5>Staff</h5>
        <div class="form-row">
            <div class="form-group col-md-3">
                {!! Form::label('teachers_male', 'Teachers Male') !!}
                {!! Form::number('teachers_male', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group col-md-3">
                {!! Form::label('teachers_female', 'Teachers Female') !!}
                {!! Form::number('teachers_female', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group col-md-3">
                {!! Form::label('teachers_other', 'Teachers Other') !!}
                {!! Form::number('teachers_other', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group col-md-3">
                {!! Form::label('teachers_total', 'Teachers Total') !!}
                {!! Form::number('teachers_total', null, ['class' => 'form-control']) !!}
            </div>
        </div>

        @php
            $groups = ['toilet' => 'Toilets', 'urinal' => 'Urinals', 'handwash' => 'Hand Washing Units'];
        @endphp

        @foreach($groups as $group => $label)
            <hr>
            <h6>{{ $label }}</h6>
            <div class="form-group">
                <label>Teacher Staff</label>
                <div class="form-row">
                    @foreach(['male', 'female', 'other', 'total'] as $gender)
                        <div class="col">
                            {!! Form::number($group.'_teacher_'.$gender, null, ['class' => 'form-control', 'placeholder' => ucfirst($gender)]) !!}
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group">
                <label>Students</label>
                <div class="form-row">
                    @foreach(['male', 'female', 'other', 'total'] as $gender)
                        <div class="col">
                            {!! Form::number($group.'_student_'.$gender, null, ['class' => 'form-control', 'placeholder' => ucfirst($gender)]) !!}
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <hr>
        <h5>No. of Universal Design Toilets</h5>
        <div class="form-group">
            {!! Form::number('universal_design_toilet_count', null, ['class' => 'form-control', 'placeholder' => 'Number of Universal Design Toilets']) !!}
        </div>

        <hr>
        <h5>Main type of toilet</h5>
        <div class="form-group">
            {!! Form::select('main_toilet_type', [
                'Flush / Pour-flush Toilet' => 'Flush / Pour-flush Toilet',
                'Pit Latrine' => 'Pit Latrine',
                'Composting Toilet' => 'Composting Toilet',
                'Other' => 'Other'
            ], null, ['class' => 'form-control', 'placeholder' => 'Select Main Toilet Type']) !!}
        </div>

        <hr>
        <h5>Toilet Connection</h5>
        <div class="form-group">
            {!! Form::select('toilet_connection', [
                'Septic Tank' => 'Septic Tank',
                'Sewer Network' => 'Sewer Network',
                'Drain Network' => 'Drain Network',
                'Onsite Treatment (biogas)' => 'Onsite Treatment (biogas)',
                'Pit / Holding' => 'Pit / Holding'
            ], null, ['class' => 'form-control', 'placeholder' => 'Select Toilet Connection']) !!}
        </div>

        <hr>
        <h5>Septic outlet connection</h5>
        <div class="form-group">
            {!! Form::select('septic_outlet', [
                'Without Outlet Connection' => 'Without Outlet Connection',
                'Connected to Sewer Network' => 'Connected to Sewer Network',
                'Connected to Drain Network' => 'Connected to Drain Network',
                'Connected to Soak Pit' => 'Connected to Soak Pit',
                'Connected to Water Body' => 'Connected to Water Body'
            ], null, ['class' => 'form-control', 'placeholder' => 'Select Septic Outlet']) !!}
        </div>

        <hr>
        <h5>Pit outlet connection</h5>
        <div class="form-group">
            {!! Form::select('pit_outlet', [
                'Without Outlet Connection' => 'Without Outlet Connection',
                'Connected to Sewer Network' => 'Connected to Sewer Network',
                'Connected to Drain Network' => 'Connected to Drain Network',
                'Connected to Soak Pit' => 'Connected to Soak Pit',
                'Connected to Water Body' => 'Connected to Water Body'
            ], null, ['class' => 'form-control', 'placeholder' => 'Select Pit Outlet']) !!}
        </div>

        <hr>
        <h5>Are soap & water available for handwashing?</h5>
        <div class="form-group">
            {!! Form::select('soap_and_water_available', [1 => 'Yes', 0 => 'No'], null, ['class' => 'form-control', 'placeholder' => 'Select']) !!}
        </div>

        <hr>
        <h5>Main Water Source</h5>
        <div class="form-group">
            {!! Form::select('main_drinking_water_source', [
                'Piped / Municipal / Community Water' => 'Piped / Municipal / Community Water',
                'Jar Water' => 'Jar Water',
                'Tanker Water' => 'Tanker Water',
                'Deep Boring' => 'Deep Boring',
                'Protected Dug Well' => 'Protected Dug Well',
                'Other' => 'Other'
            ], null, ['class' => 'form-control', 'placeholder' => 'Select Main Water Source']) !!}
        </div>

        <hr>
        <h5>Last Registration Renewal Date</h5>
        <div class="form-group">
            {!! Form::date('last_registration_renewal_date', null, ['class' => 'form-control']) !!}
        </div>

        <hr>
        <h5>Certificate Picture</h5>
        <div class="form-group">
            @if($school->certificate_picture_url)
                <p>Current: <a href="{{ asset('storage/'.$school->certificate_picture_url) }}" target="_blank">View Certificate</a></p>
            @endif
            {!! Form::file('certificate_picture_url', ['class' => 'form-control']) !!}
        </div>

        <div class="card-footer">
            {!! Form::submit('Update', ['class' => 'btn btn-success']) !!}
        </div>
    </div>
    {!! Form::close() !!}
</div>
@stop
