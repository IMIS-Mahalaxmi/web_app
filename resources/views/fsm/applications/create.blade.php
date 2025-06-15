<!-- Last Modified Date: 18-04-2024
Developed By: Innovative Solution Pvt. Ltd. (ISPL)   -->
{{--Extend the main layout--}}
@extends('layouts.dashboard')
{{--Add sections for the main layout--}}
@section('title',  __('Add Application'))
{{--Add sections for the index layout--}}

{{--Include the layout inside the main content section--}}
@section('content')
    @include('layouts.components.error-list')
    @include('layouts.components.success-alert')
    @include('layouts.components.error-alert')
    {!! Form::open(['url' => route('application.store'), 'class' => 'form-horizontal', 'id' => 'create_application_form']) !!}
    @include('layouts.partial-form', ['submitButtonText' => __('Save'), 'cardForm' => true])
    {!! Form::close() !!}
@endsection

@push('scripts')
<script>
    function autoFillDetails() {
        $(document).ready(function() {
            if ($("input[name='autofill']:checked").val() === 'on') {
                $("input[name='applicant_name']").val($("input[name=customer_name]").val());
                $("#applicant_gender").val($("#customer_gender").val());
                $("input[name='applicant_contact']").val($("input[name=customer_contact]").val());
            } else {
                $("input[name='applicant_name']").val('');
                $("#applicant_gender").val('');
                $("input[name='applicant_contact']").val('');
            }
        });
    }

    function emptyAutoFields() {
        $('#containment_id').val('');
        $('#ward').val('');
        $('#customer_name').val('');
        $('#customer_gender').val('');
        $('#customer_contact').val('');
        $("input[name='applicant_name']").val('');
        $("#applicant_gender").val('');
        $("input[name='applicant_contact']").val('');
        $("input[name='applicant_name']").removeAttr('disabled');
        $("#applicant_gender").removeAttr('disabled');
        $("input[name='applicant_contact']").removeAttr('disabled');
        $("input[name='autofill']").prop('checked', false);
    }

    function onAddressChange() {
        emptyAutoFields();
        if ($('#bin').find(":selected").text() === 'Address Not Found') {
            $('#building-if-address').hide();
            $("#building-if-address :input").each(function () {
                $(this).attr("disabled", true);
            });
            $('#building-if-not-address').show();
            $("#building-if-not-address :input").each(function () {
                $(this).attr("disabled", false);
            });
            $("input[type='submit']").removeAttr('disabled');
        } else {
            $('#building-if-not-address').hide();
            $("#building-if-not-address :input").each(function () {
                $(this).attr("disabled", true);
            });
            $('#building-if-address').show();
            $("#building-if-address :input").each(function () {
                $(this).attr("disabled", false);
            });

            if ($('#bin').val() != '') {
                displayAjaxLoader();
                $.ajax({
                    url: "{{ route('application.get-building-details') }}",
                    data: {
                        "bin": $('#bin').val()
                    },
                    success: function (res) {
                        if (res.status === true) {
                            let containmentOptions = '';
                            res.containments.forEach(function (containment) {
                                containmentOptions += `<option value="${containment}">${containment}</option>`;
                            });

                            $('#customer_name').val(res.customer_name).attr('disabled', true);
                            $('#customer_gender').val(res.customer_gender).attr('disabled', true);
                            $('#customer_contact').val(res.customer_contact).attr('disabled', true);
                            $('#household_served').val(res.household_served).attr('disabled', true);
                            $('#population_served').val(res.population_served).attr('disabled', true);
                            $('#toilet_count').val(res.toilet_count).attr('disabled', true);
                            $('#ward').val(res.ward);


                            localStorage.setItem("selectedOwnerName", res.customer_name);
                            localStorage.setItem("selectedOwnerGender", res.customer_gender);
                            localStorage.setItem("selectedOwnerContact", res.customer_contact);
                            localStorage.setItem("selectedHouseholdServed", res.household_served);
                            localStorage.setItem("selectedPopulationServed", res.population_served);
                            localStorage.setItem("selectedToiletCount", res.toilet_count);


                            if (res.containments.length === 1) {
                                $('#containment_id').replaceWith(`
                                    <input id="containment_id" name="containment_id" class="form-control" value="${res.containments[0]}" readonly>
                                `);
                                localStorage.setItem("containment_id", res.containments[0]);
                            } else {
                                $('#containment_id').replaceWith(`
                                    <select id="containment_id" name="containment_id" class="form-control">
                                        ${containmentOptions}
                                    </select>
                                `);
                                let selectedContainment = localStorage.getItem("containment_id");
                            }

                            $("input[type='submit']").removeAttr('disabled');
                        } else if (res.status === false) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: "There is an ongoing application for this address!",
                            });
                            emptyAutoFields();
                            $("input[type='submit']").attr('disabled', 'disabled');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: "Error!",
                            });
                            emptyAutoFields();
                        }
                        removeAjaxLoader();
                    },
                    error: function (err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: err.responseJSON.error,
                        });
                        emptyAutoFields();
                        $("input[type='submit']").attr('disabled', 'disabled');
                    }
                });
            }
        }
    }

    $(document).ready(function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('proposed_emptying_date').setAttribute('min', today);

        $('#bin').prepend('<option selected=""></option>').select2({
            ajax: {
                url: "{{ route('building.get-house-numbers-containments') }}",
                data: function (params) {
                    return {
                        search: params.term,
                        road_code: $('#road_code').val(),
                        page: params.page || 1
                    };
                },
            },
            placeholder: '{{ __('House Number / BIN') }}',
            allowClear: true,
            closeOnSelect: true,
            width: '100%'
        });

        $('#road_code').prepend('<option selected=""></option>').select2({
            ajax: {
                url: "{{ route('roadlines.get-road-names') }}",
                data: function (params) {
                    return {
                        search: params.term,
                        bin: $('#bin').val(),
                        page: params.page || 1
                    };
                },
            },
            placeholder: '{{ __('Street Name / Street Code') }}',
            allowClear: true,
            closeOnSelect: true,
            width: '100%'
        });

        if ('{{ old('address') }}' !== '') {
            $('#address').select2().val('{{ old('address') }}').trigger('change');
            onAddressChange();
        }

        $('#bin').on('change', onAddressChange);

        $('#create_application_form').on('submit', function (e) {
            $('#containment_id').removeAttr('disabled'); // Ensure the field is enabled for submission
        });


    var serviceProviderId = {{ Auth::user()->service_provider_id ?? 'null' }};
    
    // Determine the URL based on the service provider ID
    var url = serviceProviderId 
        ? '{!! url("fsm/service-provider") !!}/' + serviceProviderId 
        : '{!! url("fsm/service-provider") !!}/0';

    // Make the AJAX request to fetch the service provider data
    $.ajax({
        url: url,
        method: 'GET',
        success: function (response) {
            // Clear existing options
            $('#service_provider_id').empty();
            
            // Add default option
            $('#service_provider_id').append('<option value="">Service Provider Name</option>');
            
            // Populate the dropdown with options from the response
            $.each(response, function (id, name) {
                $('#service_provider_id').append('<option value="' + id + '">' + name + '</option>');
            });

            // Check if there is a previously selected service provider in localStorage
            const selectedServiceProviderValue = localStorage.getItem("selectedServiceProviderValue");

            // If a value is found in localStorage, set it as the selected option
            if (selectedServiceProviderValue) {
                $('#service_provider_id').val(selectedServiceProviderValue).trigger('change');
            }
        },
        error: function (error) {
            console.error('Error fetching service provider data:', error);
        }
    });

    // Save selected service provider value to localStorage on change
    $('#service_provider_id').on('change', function() {
        var selectedServiceProviderValue = $(this).val();
        localStorage.setItem("selectedServiceProviderValue", selectedServiceProviderValue);
    });


        if ($('.alert.alert-danger.alert-dismissible').length == 0) {
            localStorage.removeItem("selectedRoadCode");
            localStorage.removeItem("selectedOwnerName");
            localStorage.removeItem("selectedOwnerGender");
            localStorage.removeItem("selectedOwnerContact");
            localStorage.removeItem("selectedBINValue");
            localStorage.removeItem("selectedBINText");
            localStorage.removeItem("containment_id");
            localStorage.removeItem("selectedHouseholdServed");
            localStorage.removeItem("selectedPopulationServed");
            localStorage.removeItem("selectedServiceProviderText");
            localStorage.removeItem("selectedServiceProviderValue");
            localStorage.removeItem("selectedToiletCount");
        } else { 
            // Retrieve values from localStorage and populate the form
            const selectedRoadCode = localStorage.getItem("selectedRoadCode");
            const selectedBINValue = localStorage.getItem("selectedBINValue");
            
            const selectedBINText = localStorage.getItem("selectedBINText");
            const selectedOwnerName = localStorage.getItem("selectedOwnerName");
            const selectedOwnerGender = localStorage.getItem("selectedOwnerGender");
            const selectedOwnerContact = localStorage.getItem("selectedOwnerContact");
            const selectedHouseholdServed = localStorage.getItem("selectedHouseholdServed");
            const selectedPopulationServed = localStorage.getItem("selectedPopulationServed");
            const selectedToiletCount = localStorage.getItem("selectedToiletCount");

            if (selectedRoadCode) {
                var roadCode = selectedRoadCode.split(" - ")[0];
                $('#road_code').val(selectedRoadCode); // Set road code from localStorage
            }

            $('#containment_id').prop('disabled', true);

            // Populate form fields with localStorage data
            if (selectedRoadCode) $('#road_code').val(selectedRoadCode);
            if (selectedBINValue) $('#bin').val(selectedBINValue);
            if (selectedOwnerName) $('#customer_name').val(selectedOwnerName).prop('disabled', true);
            if (selectedOwnerGender) $('#customer_gender').val(selectedOwnerGender).prop('disabled', true);
            if (selectedOwnerContact) $('#customer_contact').val(selectedOwnerContact).prop('disabled', true);
            if (selectedHouseholdServed) $('#household_served').val(selectedHouseholdServed).prop('disabled', true);
            if (selectedPopulationServed) $('#population_served').val(selectedPopulationServed).prop('disabled', true);
            if (selectedToiletCount) $('#toilet_count').val(selectedToiletCount).prop('disabled', true);

            // Update road code select2 with stored value
            optionHtmlRoadCode = selectedRoadCode 
                ? `<option value="${roadCode}" selected="selected">${selectedRoadCode}</option>` 
                : `<option selected=""></option>`;
            $('#road_code').prepend(optionHtmlRoadCode).select2({
                ajax: {
                    url: "{{ route('roadlines.get-road-names') }}",
                    data: function (params) {
                        return {
                            search: params.term,
                        bin: $('#bin').val(),
                        page: params.page || 1
                        };
                    },
                },
                placeholder: 'Street Name / Street Code',
                allowClear: true,
                closeOnSelect: true,
                width: '100%',
            });

            // Update bin select2 with stored value
            optionHtmlBIN = selectedBINValue 
                ? `<option value="${selectedBINValue}" selected="selected">${selectedBINText}</option>` 
                : `<option selected=""></option>`;

             

            $('#bin').prepend(optionHtmlBIN).select2({
                ajax: {
                    url: "{{ route('building.get-house-numbers-containments') }}",
                    data: function (params) {
                        return {
                            search: params.term,
                            road_code: $('#road_code').val(),
                            page: params.page || 1
                        };
                    },
                },
                placeholder: 'House Number / BIN',
                allowClear: true,
                closeOnSelect: true,
                width: '100%',
            });
        }

        // Store selected values in localStorage
        $('#road_code').on('change', function() {
            var selectedRoadCode = $(this).find('option:selected').text();
            localStorage.setItem("selectedRoadCode", selectedRoadCode);
        });

        $('#bin').on('change', function() {
            var selectedBINValue = $(this).find('option:selected').attr('value');
            var selectedBINText = $(this).find('option:selected').text();
            localStorage.setItem("selectedBINValue", selectedBINValue);
            localStorage.setItem("selectedBINText", selectedBINText);
        });

        checkDetailsAndUpdateCheckbox();
        // Function to check if the Owner and Applicant details are the same
    function checkDetailsAndUpdateCheckbox() {
        // Get the values of the Owner and Applicant Details
        const ownerDetails = {
            name: document.getElementById('customer_name').value,
            gender: document.getElementById('customer_gender').value,
            contact: document.getElementById('customer_contact').value
        };

        const applicantDetails = {
            name: document.getElementById('applicant_name').value,
            gender: document.getElementById('applicant_gender').value,
            contact: document.getElementById('applicant_contact').value
        };

        // Get the checkbox element
        const sameAsOwnerCheckbox = document.getElementById('autofill');
        const areOwnerDetailsValid = ownerDetails.name !== "" && ownerDetails.gender !== "" && ownerDetails.contact !== "";
        const areApplicantDetailsValid = applicantDetails.name !== "" && applicantDetails.gender !== "" && applicantDetails.contact !== "";


        // Compare Owner and Applicant details
        const isSame = areOwnerDetailsValid && areApplicantDetailsValid &&
                   ownerDetails.name === applicantDetails.name &&
                   ownerDetails.gender === applicantDetails.gender &&
                   ownerDetails.contact === applicantDetails.contact;

        // Update checkbox state based on comparison
        sameAsOwnerCheckbox.checked = isSame;
    }
        // Attach event listeners to Applicant fields to check when any field changes
        document.getElementById('applicant_name').addEventListener('input', checkDetailsAndUpdateCheckbox);
        document.getElementById('applicant_gender').addEventListener('input', checkDetailsAndUpdateCheckbox);
        document.getElementById('applicant_contact').addEventListener('input', checkDetailsAndUpdateCheckbox);
   

    });
</script>

@endpush

