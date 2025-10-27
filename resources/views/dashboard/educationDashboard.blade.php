@extends('layouts.dashboard')
@section('title', 'Education Dashboard')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Types of Schools:</h1>
        </div>
    </div>


    <div class="row">
        <div class="col-md-4">
            @include('dashboard.education._schoolCountCard')
        </div>
        <div class="col-md-4">
            @include('dashboard.education._schoolCountPrimary')
        </div>
        <div class="col-md-4">
            @include('dashboard.education._primarySecondarySchool')
        </div>

    </div>

    <div class="row">
        <div class="col-lg-3 col-md-12 col-xs-12  d-flex">
            {{-- Scholl with pre primary included --}}
            @include('dashboard.education._schoolWithPrePrimarySchool')
        </div>
        <div class="col-lg-9 col-md-12 col-xs-12  extra-padding">
            <div class="row">
                @include('dashboard.education._educationPrePrimaryCard')
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-12 col-xs-12  d-flex">
            {{-- PRIMARY AND SECONDARY --}}
            @include('dashboard.education._primarySecondary')
        </div>
        <div class="col-lg-9 col-md-12 col-xs-12  extra-padding">
            <div class="row">
                <div class="col-lg-4 d-flex">
                    {{-- basic 1 - 5 --}}
                    @include('dashboard.education._basic')
                </div>
                <div class="col-lg-4 d-flex">
                    {{-- basic 6-8 --}}
                    @include('dashboard.education._basic_6-8')
                </div>
                <div class="col-lg-4 d-flex">
                    {{-- Seconday --}}
                    @include('dashboard.education.secondary9_10')
                </div>
                <div class="col-lg-4 d-flex">
                    {{-- Higher Secondary --}}
                    @include('dashboard.education.high_secondary')
                </div>

            </div>
        </div>

    </div>
@endsection
