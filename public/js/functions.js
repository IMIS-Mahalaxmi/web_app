/*
    Function for delete action
*/
function deleteAction(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
/*
    Function for filtering DataTable
*/
function filterDataTable(dataTable) {
    $('#filter-form').on('submit', function (e) {
        e.preventDefault();
        dataTable.ajax.reload();
    });
}
/*
    Function for resetting filter and DataTable
*/
function resetDataTable(dataTable) {
    $('#reset-filter').on('click', function (e) {
        e.preventDefault();
        localStorage.clear();
        $('#filter-form')[0].reset();
        $('#filter-form').find('select').each(function (index, el) {
            $(el).trigger("change");
        });
        dataTable.ajax.reload();
    });
}

/*
    Function for displaying ajax loader
*/
function displayAjaxLoader(message) {
    $("#loading-content").text(message);
    $(".loading-overlay")[0].classList.toggle('is-active');
}

function removeAjaxLoader() {
    $("#loading-content").text("");
    $(".loading-overlay")[0].classList.toggle('is-active');
}

/*
    Function for export
*/

$.fn.serializeObject = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function exportToCsv(event) {
    event.preventDefault();
    var filterData = $('#filter-form').serializeObject();
    displayAjaxLoader();
    try {
        var a = document.createElement('a');
        a.href = event.target.href + "?" + $.param(filterData);
        a.download = 'report.csv';
        a.click();
        a.remove();
        removeAjaxLoader();
    } catch (e) {
        removeAjaxLoader();
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "Error!",
        });
    }
}
// function to prevent multiple submits in form
function preventMultipleSubmit() {
    $('.prevent-multiple-submits').attr('disabled', 'true');
};

$(document).ready(function () {
    // Check if the create_user checkbox is checked on page load
    if ($('#create_user').is(":checked")) {
        $('#user-password').show();
    } else {
        $('#user-password').hide();
    }
});

// function to check if user is to be created or not
function createUser() {
    $('#create_user').on('change', function () {
        if ($('#create_user').is(":checked")) {
            
            $('#user-password').show();
        }
        else {
            $('#user-password').hide();

        }
    });
}


// building page onload functions for dynamic display according to dropdown

function dynamicBuildingForm() {
    // display/ hide building_associated field
    $('#main_building').on('change', function () {
        var mainBuilding = $("#main_building").val();
        if (mainBuilding == "1") {
            $('#building_associated').hide();
        } else if (mainBuilding == "0") {

            $('#building_associated').show();
        }
    });




    // show lic name when id_lic field is yes
    $('#lic_status').on('change', function () {
        var licStatus = $("#lic_status").val();
        if (licStatus == "1") {
            $('#lic_id').show();
        } else {
            $('#lic_id').hide();
        }
    });


    $('#water-id').on('change', function () {
        if ($("#water-id :selected").text() == "Municipal/Public Water Supply") {
            $('#water-customer-id').show();
            $('#water-pipe-id').show();
        } else {
            $('#water-customer-id').hide();
            $('#water-pipe-id').hide();
        }
    });

    // hide distance from well if well presence is No and sanitaiton technology is containment
    $('#well-presence').on('change', function () {
        var wellPresence = $("#well_presence_status").val();
        if (wellPresence == "1") {
            $('#distance-from-well').show();
        } else {
            $('#distance-from-well').hide();
        }
    });

    // hides if toilet presence No
    // shows if toilet presence Yes
    // hides everything is nothing is selected
    $('#toilet_status').on('change', function () {
    var toiletPresence = $("#toilet_status").val(); 
    if (toiletPresence == "1") {  
        $('#toilet-info').show();
        var useCategory = $("#use_category_id").val();  
        if (useCategory == "Community Toilet" || useCategory == "Public Toilet") {
            $('#shared-toilet-popn').hide();
            $('#shared-toilet').hide();
        }
        else {
            $('#shared-toilet-popn').show();
            $('#shared-toilet').show();
        }
        $('#toilet-connection').show();
        $('#defecation-place').hide();
        $('#ctpt-toilet').hide();
    } 
    else if (toiletPresence == "0") {  
        $('#defecation-place').show();
        $('#toilet-info').hide();
        $('#shared-toilet').hide();
        $('#toilet-connection').hide();
        $('#shared-toilet-popn').hide();
        $('#containment-info').hide();
        $('#containment-id').hide();
        $('#drain-code').hide();
        $('#sewer-code').hide();
    }
    else {
        $('#defecation-place').hide();
        $('#toilet-info').hide();
        $('#shared-toilet').hide();
        $('#toilet-connection').hide();
        $('#shared-toilet-popn').hide();
        $('#containment-info').hide();
        $('#containment-id').hide();
        $('#drain-code').hide();
        $('#sewer-code').hide();
    }
});


    //show ctpt field only when defecation-place is Community Toilet
    $('#defecation-place').on('change', function () {
        if ($("#defecation-place :selected").text() == "Community Toilet") {
            $('#ctpt-toilet').show();
        } else {
            $('#ctpt-toilet').hide();
        }
    });

    $('#toilet-connection').on('change', function () {
        if ($("#toilet-connection :selected").text() === "Septic Tank" || $(
            "#toilet-connection :selected").text() === "Pit/ Holding Tank") {
            $('#containment-info').show();
            $('#containment-id').hide();
            $('#drain-code').hide();
            $('#sewer-code').hide();
            $('#vacutug-accessible').show();

        }
        else if ($("#toilet-connection :selected").text() == "Shared Containment") {
            $('#containment-id').show();
            $('#containment-info').hide();
            $('#drain-code').hide();
            $('#sewer-code').hide();
            $('#vacutug-accessible').hide();
        }
        else if ($("#toilet-connection :selected").text() == "Drain Network") {
            $('#drain-code').show();
            $('#containment-id').hide();
            $('#containment-info').hide();
            $('#sewer-code').hide();
            $('#vacutug-accessible').hide();
        }
        else if ($("#toilet-connection :selected").text() == "Sewer Network") {
            $('#drain-code').hide();
            $('#containment-id').hide();
            $('#containment-info').hide();
            $('#sewer-code').show();
            $('#vacutug-accessible').hide();
        }
        else {
            $('#containment-id').hide();
            $('#containment-info').hide();
            $('#drain-code').hide();
            $('#sewer-code').hide();
            $('#vacutug-accessible').hide();
        }
    });

    $('#containment-type').on('change', function () {

        var selectedText = $("#containment-type option:selected").text();
        var showOptions = [
            "Septic Tank connected to Drain Network",
            "Lined Pit connected to Drain Network"
        ];
        if (showOptions.includes(selectedText)) {
            $('#drain-code').show();
        } else {
            $('#drain-code').hide();
        }
    });

    $('#containment-type').on('change', function () {

        var selectedText = $("#containment-type option:selected").text();
        var showOptions = [
            "Septic Tank connected to Sewer Network",
            "Lined Pit connected to Sewer Network"
        ];
        if (showOptions.includes(selectedText)) {
            $('#sewer-code').show();
        } else {
            $('#sewer-code').hide();
        }
    });


    $('#containment-type, #pit-shape').on('change', function () {

        var selectedText = $("#containment-type option:selected").text();
        var showOptions = [
            "Double Pit",
            "Permeable/ Unlined Pit",
            "Lined Pit connected to a Soak Pit",
            "Lined Pit connected to Water Body",
            "Lined Pit connected to Open Ground",
            "Lined Pit connected to Sewer Network",
            "Lined Pit connected to Drain Network",
            "Lined Pit without Outlet",
            "Lined Pit with Unknown Outlet Connection",
            "Lined Pit with Impermeable Walls and Open Bottom"
        ];
        if (showOptions.includes(selectedText)) {
            $('#pit-shape').show();
            $('#tank-depth').hide();
            $('#tank-width').hide();
            $('#tank-length').hide();
            $('#septic-tank').hide();
        }
        else {
            $('#tank-length').show();
            $('#septic-tank').show();
            $('#pit-shape').hide();
        }

        // Check if the selected text is in the array of showOptions and if the pit shape is "Cylindrical"
        if (showOptions.includes(selectedText) && ($("#pit-shape :selected").text() ==
            "Cylindrical")) {
            $('#pit-depth').show();
            $('#pit-size').show();
            $('#tank-depth').hide();
            $('#tank-width').hide();
            $('#tank-length').hide();
        } else {
            $('#pit-size').hide();
            $('#pit-depth').hide();
            $('#tank-depth').show();
            $('#tank-width').show();
            $('#tank-length').show();

        }


        if (!showOptions.includes(selectedText) || $("#pit-shape :selected").text() !== "Rectangular") {
            $('#tank-length').show();
        } else {
            $('#tank-length').hide();
        }

        if ($("#pit-shape :selected").text() == "Cylindrical") {
            $('#tank-length').hide();
        } else {
            $('#tank-length').show();
        }
        if (!showOptions.includes(selectedText)) {
            $('#tank-length').show();
        }

    });


    $('#functional_use_id').on('change', function () {
        var html = '<option value="">Use Category  of Building</option>';
        var functional_use = $(this).val();
        if (functional_use) {
            $.each(usecatgs[functional_use], function (key, value) {
                html += '<option value="' + key + '">' + value + '</option>';
            });
            if (functional_use == "1" ) {
                $('#office-business').hide();
            } else {
                $('#office-business').show();
            }
        }

        $('#use_category_id').html(html);
    });

    //ajax
    $(document).on('ready', function () {

        // searchable dropdown for building_associated_to
        $('#building_associated_to').prepend('<option selected=""></option>').select2({

            ajax: {
                url: "{{ route('building.get-house-numbers-all') }}",
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
            },
            placeholder: 'BIN of Main Building',
            allowClear: true,
            closeOnSelect: true,
            width: '85%',
        });
        $('#road_code').prepend('<option selected=""></option>').select2({
            ajax: {
                url: "{{ route('roadlines.get-road-names') }}",
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
            },
            placeholder: 'Road Code - Road Name',
            allowClear: true,
            closeOnSelect: true,
            width: '85%',
        });

        $('#watersupply_pipe_code').prepend('<option selected=""></option>').select2({
            ajax: {
                url: "{{ route('watersupply.get-watersupply-code') }}",
                data: function (params) {
                    return {
                        search: params.term,
                        // ward: $('#ward').val(),
                        page: params.page || 1
                    };
                },
            },
            placeholder: 'Water Supply Pipe Line Code',
            allowClear: true,
            closeOnSelect: true,
            width: '85%',
        });
        $('#sewer_code').prepend('<option selected=""></option>').select2({
            ajax: {
                url: "{{ route('sewerlines.get-sewer-names') }}",
                data: function (params) {
                    return {
                        search: params.term,
                        // ward: $('#ward').val(),
                        page: params.page || 1
                    };
                },
            },
            placeholder: 'Sewer Code',
            allowClear: true,
            closeOnSelect: true,
            width: '85%',
        });

        $('#build_contain').prepend('<option selected=""></option>').select2({
            ajax: {
                url: "{{ route('building.get-house-numbers-containments') }}",
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
            },
            placeholder: 'BIN of Pre Connected Building',
            allowClear: true,
            closeOnSelect: true,
            width: '85%',
        });
    });

    //show use category only when functional use is filled

        var functionalUseId = $('#functional_use_id').val();
        if (functionalUseId) {
            $('#use-category').show();
        } else {
            $('#use-category').hide();
        }

        $('#functional_use_id').on('change', function() {
            var functionalUseId = $(this).val();
            if (functionalUseId) {
                $('#use-category').show();
            } else {
                $('#use-category').hide();
            }
        });


    // to hide population , family count , total popn  if functional == Community/Public toilets
    $('#use_category_id').on('change', function () {
        if ($("#use_category_id :selected").text() == "Community Toilet" ||  $("#use_category_id :selected").text() == "Public Toilet") {
            $('#population-info').hide();
            $('#family-count').hide();
            $('#male-population').hide();
            $('#female-population').hide();
            $('#other-population').hide();
           $('#male-diff-population').hide();
           $('#female-diff-population').hide();
           $('#other-diff-population').hide();
           $('#shared-toilet').hide();
           $('#shared-toilet-popn').hide();
        } else {
            $('#male-population').show();
            $('#female-population').show();
            $('#other-population').show();
            $('#population-info').show();
            $('#family-count').show();
            $('#male-diff-population').show();
            $('#female-diff-population').show();
            $('#other-diff-population').show();
            $('#shared-toilet').show();
            $('#shared-toilet-popn').show();


        }
    });

    // To set toilet status == 1 and show related fields(also applied in backend part (Building Structure Service))
    $('#use_category_id').on('change', function () {
        var selectedText = $("#use_category_id option:selected").text();
        if (selectedText == "Community Toilet" ||selectedText == "Public Toilet"  ) {
            $('#toilet_status').val('1'); // Set toilet_status to 'Yes'
            $('#toilet-info').show();
            $('#toilet-connection').show();
            $('#defecation-place').hide();
            $('#ctpt-toilet').hide();
            $('#population-info').hide();
            $('#family-count').hide();
            $('#male-population').hide();
            $('#female-population').hide();
            $('#other-population').hide();
           $('#male-diff-population').hide();
           $('#female-diff-population').hide();
           $('#other-diff-population').hide();
           $('#shared-toilet').hide();
           $('#shared-toilet-popn').hide();
        }
    });
    // change on reload 
    if ($("#use_category_id :selected").text() == "Community Toilet" ||  $("#use_category_id :selected").text() == "Public Toilet") {
        $('#population-info').hide();
        $('#family-count').hide();
        $('#male-population').hide();
        $('#female-population').hide();
        $('#other-population').hide();
       $('#male-diff-population').hide();
       $('#female-diff-population').hide();
       $('#other-diff-population').hide();
       $('#shared-toilet').hide();
       $('#shared-toilet-popn').hide();
    } else {
        $('#male-population').show();
        $('#female-population').show();
        $('#other-population').show();
        $('#population-info').show();
        $('#family-count').show();
        $('#male-diff-population').show();
        $('#female-diff-population').show();
        $('#other-diff-population').show();
        $('#shared-toilet').show();
        $('#shared-toilet-popn').show();
    }

}
document.addEventListener('DOMContentLoaded', function () {
    var populationFields = document.querySelectorAll(
        'input[name="male_population"], input[name="female_population"], input[name="other_population"]'
    );

    populationFields.forEach(function (field) {
        field.addEventListener('input', function () {
            // Retrieve values and check if they're empty; if empty, treat as undefined
            var malePopulation = field.closest('form').querySelector('input[name="male_population"]').value;
            var femalePopulation = field.closest('form').querySelector('input[name="female_population"]').value;
            var otherPopulation = field.closest('form').querySelector('input[name="other_population"]').value;

            // Convert to integers, or leave as 0 if empty
            malePopulation = malePopulation === "" ? 0 : parseInt(malePopulation);
            femalePopulation = femalePopulation === "" ? 0 : parseInt(femalePopulation);
            otherPopulation = otherPopulation === "" ? 0 : parseInt(otherPopulation);

            // Calculate total population
            var totalPopulation = malePopulation + femalePopulation + otherPopulation;

            // Update the 'population_served' field with the new total
            var populationServedField = document.querySelector('input[name="population_served"]');
            if(malePopulation!== 0 || femalePopulation!== 0 || otherPopulation!== 0 )
            {
                populationServedField.value = totalPopulation;
            }
            // Make the 'population_served' field readonly after the value is calculated
            if (malePopulation >= 1 || femalePopulation >= 1 || otherPopulation >= 1 ) {
                populationServedField.readOnly = true; // Set as readonly instead of disabled
            }
            else
            {
                populationServedField.readOnly = false; // Set as readonly instead of disabled
            }
        });
    });
});

function onLoadPopReadOnly()
{
    var populationFields = document.querySelectorAll(
        'input[name="male_population"], input[name="female_population"], input[name="other_population"]'
    );
    populationFields.forEach(function (field) {
        // Retrieve values and check if they're empty; if empty, treat as undefined
     var malePopulation = field.closest('form').querySelector('input[name="male_population"]').value;
     var femalePopulation = field.closest('form').querySelector('input[name="female_population"]').value;
     var otherPopulation = field.closest('form').querySelector('input[name="other_population"]').value;

     // Convert to integers, or leave as 0 if empty
     malePopulation = malePopulation === "" ? 0 : parseInt(malePopulation);
     femalePopulation = femalePopulation === "" ? 0 : parseInt(femalePopulation);
     otherPopulation = otherPopulation === "" ? 0 : parseInt(otherPopulation);

     // Calculate total population
     var totalPopulation = malePopulation + femalePopulation + otherPopulation;

     // Update the 'population_served' field with the new total
     var populationServedField = document.querySelector('input[name="population_served"]');
     if(malePopulation!== 0 || femalePopulation!== 0 || otherPopulation!== 0 )
    {
        populationServedField.value = totalPopulation;
    } 
    // Make the 'population_served' field readonly after the value is calculated
    if (malePopulation >= 1 || femalePopulation >= 1 || otherPopulation >= 1 ) {
        populationServedField.readOnly = true; // Set as readonly instead of disabled
    }
    else
    {
        populationServedField.readOnly = false; // Set as readonly instead of disabled
    }
    });
}



function onloadDynamicContainmentType() {

    $('#containment-info').show();
    var selectedText = $("#containment-type option:selected").text();


    var showOptions = [
        "Septic Tank connected to Drain Network",
        "Lined Pit connected to Drain Network"
    ];
    if (showOptions.includes(selectedText)) {

        $('#drain-code').show();
    } else {
        $('#drain-code').hide();
    }

    var selectedText = $("#containment-type option:selected").text();
    var showOptions = [
        "Septic Tank connected to Sewer Network",
        "Lined Pit connected to Sewer Network"
    ];
    if (showOptions.includes(selectedText)) {

        $('#sewer-code').show();
    } else {
        $('#sewer-code').hide();
    }


    var selectedText = $("#containment-type option:selected").text();
    var showOptions = [
        "Double Pit",
        "Permeable/ Unlined Pit",
        "Lined Pit connected to a Soak Pit",
        "Lined Pit connected to Water Body",
        "Lined Pit connected to Open Ground",
        "Lined Pit connected to Sewer Network",
        "Lined Pit connected to Drain Network",
        "Lined Pit without Outlet",
        "Lined Pit with Unknown Outlet Connection",
        "Lined Pit with Impermeable Walls and Open Bottom"
    ];
    if (showOptions.includes(selectedText)) {
        $('#pit-shape').show();
        $('#tank-depth').hide();
        $('#tank-width').hide();
        $('#tank-length').hide();
        $('#septic-tank').hide();
    }
    else {
        $('#septic-tank').show();
        $('#pit-shape').hide();
    }

    // Check if the selected text is in the array of showOptions and if the pit shape is "Cylindrical"
    if (showOptions.includes(selectedText) && ($("#pit-shape :selected").text() ==
        "Cylindrical")) {
        $('#pit-depth').show();
        $('#pit-size').show();
        $('#tank-depth').hide();
        $('#tank-width').hide();
        $('#tank-length').hide();
    } else {
        $('#pit-size').hide();
        $('#pit-depth').hide();
        $('#tank-depth').show();
        $('#tank-width').show();
        $('#tank-length').show();

    }


    // if functional use is public/community Toilets then hide the fields
    //to set toilet presence to yes when use category is yes

    // const useCategorySelect = document.getElementById('use_category_id');
    //     const toiletStatusSelect = document.getElementById('toilet_status');

    //     useCategorySelect.addEventListener('change', function() {
    //         if (this.value === 'Community Toilet' || this.value === 'Public Toilet') {
    //             toiletStatusSelect.value = false; // Set the value to 'No'
    //             toiletStatusSelect.disabled = true; // Disable the field
    //         } else {
    //             toiletStatusSelect.disabled = false; // Enable the field for other options
    //             toiletStatusSelect.value = null; // Reset the value to allow the user to choose
    //         }
    //     });
}


      // Logic for lic_status field
      function handleLowIncomeChange() {
        if ($("#lic_status select").val() == "1") {
            $('#lic_status').show();
        } else {
            $('#lic_id').hide();
        }
    }

    function handleLicStatusChange() {
            if ($("#lic_status :selected").text() == "Yes") {
                $('#lic_id').show();
            } else {
                $('#lic_id').hide();
            }
    }



    function maindrinkingWaterSource() {
        if ($("#water-id select").val() == "11") {
            $('#water-customer-id').show();
            $('#water-pipe-id').show();
        } else {
            $('#water-customer-id').hide();
            $('#water-pipe-id').hide();
        }
    }

    function wellpresenceStatus() {
        if ($("#well-presence select").val() == "1") {

            $('#distance-from-well').show();
        } else {
            $('#distance-from-well').hide();
        }
    }

    function handleToiletPresenceChange() {
        if ($("#toilet-presence :selected").text() == "Yes") {
            $('#toilet-info').show();
            $('#toilet-connection').show();
            if ($("#use_category_id :selected").text() == "Community Toilet" ||  $("#use_category_id :selected").text() == "Public Toilet") {
                $('#shared-toilet-popn').hide();
                $('#shared-toilet').hide();
            }
            else{
                $('#shared-toilet-popn').show();
                $('#shared-toilet').show();
            }
            $('#defecation-place').hide();
            $('#ctpt-toilet').hide();
        } else {
            $('#vacutug-accessible').hide();
            $('#defecation-place').show();
            $('#toilet-info').hide();
            $('#shared-toilet').hide();
            $('#toilet-connection').hide();
            $('#shared-toilet-popn').hide();
            $('#containment-info').hide();
            $('#containment-id').hide();
            $('#drain-code').hide();
            $('#sewer-code').hide();
        }
    }

    function handleToiletConnectionChange() {
        if ($("#toilet-connection :selected").text() === "Septic Tank" || $("#toilet-connection :selected").text() ===
            "Pit/ Holding Tank") {
            $('#containment-info').show();
            $('#containment-id').hide();
            $('#drain-code').hide();
            $('#sewer-code').hide();
            $('#vacutug-accessible').show();
            var selectedTextDrain = $("#containment-type option:selected").text();
            var showOptions = [
                "Septic Tank connected to Drain Network",
                "Lined Pit connected to Drain Network"
            ];
            if (showOptions.includes(selectedTextDrain)) {
                $('#drain-code').show();
            } else {
                $('#drain-code').hide();
            }
    
            var selectedTextSewer = $("#containment-type option:selected").text();
            var showOptions = [
                "Septic Tank connected to Sewer Network",
                "Lined Pit connected to Sewer Network"
            ];
            if (showOptions.includes(selectedTextSewer)) {
                $('#sewer-code').show();
            } else {
                $('#sewer-code').hide();
            }
        
            var selectedTextOthers = $("#containment-type option:selected").text();
            var showOptions = [
                "Double Pit",
                "Permeable/ Unlined Pit",
                "Lined Pit connected to a Soak Pit",
                "Lined Pit connected to Water Body",
                "Lined Pit connected to Open Ground",
                "Lined Pit connected to Sewer Network",
                "Lined Pit connected to Drain Network",
                "Lined Pit without Outlet",
                "Lined Pit with Unknown Outlet Connection",
                "Lined Pit with Impermeable Walls and Open Bottom"
            ];
            if (showOptions.includes(selectedTextOthers)) {
                $('#pit-shape').show();
                $('#tank-depth').hide();
                $('#tank-width').hide();
                $('#tank-length').hide();
                $('#septic-tank').hide();
            }
            else {
                $('#tank-length').show();
                $('#septic-tank').show();
                $('#pit-shape').hide();
            }
        } else if ($("#toilet-connection :selected").text() === "Shared Containment") {
            $('#containment-id').show();
            $('#containment-info').hide();
            $('#drain-code').hide();
            $('#sewer-code').hide();
            $('#vacutug-accessible').hide();
        } else if ($("#toilet-connection :selected").text() === "Drain Network") {
            $('#drain-code').show();
            $('#containment-id').hide();
            $('#containment-info').hide();
            $('#sewer-code').hide();
            $('#vacutug-accessible').hide();
        } else if ($("#toilet-connection :selected").text() === "Sewer Network") {
            $('#drain-code').hide();
            $('#containment-id').hide();
            $('#containment-info').hide();
            $('#sewer-code').show();
            $('#vacutug-accessible').hide();
        } else {
            $('#containment-id').hide();
            $('#containment-info').hide();
            $('#drain-code').hide();
            $('#sewer-code').hide();
            $('#vacutug-accessible').hide();
        }
    }
    function handleMainBuildingChange() {
        if ($("#main_building :selected").text() == "Yes") {
            $('#building_associated').hide();
        } else if ($("#main_building :selected").text() == "No") {
            $('#building_associated').show();
        }
    }
    function hideoffice() {
        if ($("#functional-use select").val() == 1 || $("#functional-use select").val() == 14) {
            $('#office-business').hide();
        } else {
            $('#office-business').show();
        }
    }

    function defecationStatus() {
        if ($("#defecation-place select").val() == 9) {
            $('#ctpt-toilet').show();
        } else {
            $('#ctpt-toilet').hide();
        }
    }

    function munPublicDrinkingWater() {
    if ($("#water-id :selected").text() == "Municipal/Public Water Supply") {
        $('#water-customer-id').show();
        $('#water-pipe-id').show();
    } else {
        $('#water-customer-id').hide();
        $('#water-pipe-id').hide();
    }
    }


    function handleContainmentTypeChange()
    {
            
        if ($("#toilet-presence :selected").text() == "Yes") {
        $('#ctpt-toilet').hide();
        
        } else {
            $('#vacutug-accessible').hide();
            $('#defecation-place').show();
            $('#toilet-info').hide();
            $('#shared-toilet').hide();
            $('#toilet-connection').hide();
            $('#shared-toilet-popn').hide();
            $('#containment-info').hide();
            $('#containment-id').hide();
            $('#drain-code').hide();
            $('#sewer-code').hide();
        }

    }
    


        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') {
                        event.preventDefault();
                    }
                });
            });
        });

        function validateOwnerContactInput(input) {
            var value = input.value;
            
            // Allow only digits (0-9)
            value = value.replace(/[^0-9]/g, '');
            
            // Set the cleaned value back to the input
            input.value = value;
        }
        function validateAlphanumeric(input) {
            // Allow only alphanumeric characters (letters and numbers)
            input.value = input.value.replace(/[^a-zA-Z0-9]/g, '');
        }
        
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('password_confirmation');
        
        const passwordError = document.getElementById('password-error');
        const confirmPasswordError = document.getElementById('confirm-password-error');
        
        // Focus Events to Show Messages
        passwordField.addEventListener('focus', () => passwordError.style.display = 'block');
        confirmPasswordField.addEventListener('focus', () => confirmPasswordError.style.display = 'block');
        
        // Blur Events to Hide Messages
        passwordField.addEventListener('blur', () => passwordError.style.display = 'none');
        confirmPasswordField.addEventListener('blur', () => confirmPasswordError.style.display = 'none');
        
        // Input Validation for Password
        passwordField.addEventListener('input', validatePassword);
        confirmPasswordField.addEventListener('input', validateConfirmPassword);
        
        // Validation Logic
        function validatePassword() {
            const password = passwordField.value;
        
            // Requirements
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumber = /\d/.test(password);
            const hasSymbol = /[\W_]/.test(password);
            const hasMinLength = password.length >= 8;
        
            // Update Message Colors
            document.getElementById('char-count').style.color = hasMinLength ? 'green' : 'red';
            document.getElementById('uppercase-lowercase').style.color = (hasUpperCase && hasLowerCase) ? 'green' : 'red';
            document.getElementById('symbol').style.color = hasSymbol ? 'green' : 'red';
            document.getElementById('number').style.color = hasNumber ? 'green' : 'red';
        
            // Trigger Confirm Password Validation
            validateConfirmPassword();
        }
        
        function validateConfirmPassword() {
            const confirmPassword = confirmPasswordField.value;
            const password = passwordField.value;
        
            // Requirements
            const hasUpperCase = /[A-Z]/.test(confirmPassword);
            const hasLowerCase = /[a-z]/.test(confirmPassword);
            const hasNumber = /\d/.test(confirmPassword);
            const hasSymbol = /[\W_]/.test(confirmPassword);
            const hasMinLength = confirmPassword.length >= 8;
            const passwordsMatch = confirmPassword === password;
        
            // Update Message Colors
            document.getElementById('confirm-char-count').style.color = hasMinLength ? 'green' : 'red';
            document.getElementById('confirm-uppercase-lowercase').style.color = (hasUpperCase && hasLowerCase) ? 'green' : 'red';
            document.getElementById('confirm-symbol').style.color = hasSymbol ? 'green' : 'red';
            document.getElementById('confirm-number').style.color = hasNumber ? 'green' : 'red';
            document.getElementById('confirm-match').style.color = passwordsMatch ? 'green' : 'red';
        }    

        function handleSizeReadOnlyChange()
        {
            document.addEventListener('DOMContentLoaded', function() {
                var diameterField = document.querySelector('input[name="pit_diameter"]');
                var depthField = document.querySelector('input[name="pit_depth"]');
                var volumeField = document.querySelector('input[name="size"]');
                var calculateVolume = function() {
                    var diameter = parseFloat(diameterField.value) || 0;
                    var depth = parseFloat(depthField.value) || 0;
    
                    // Calculate radius
                    var radius = diameter / 2;
    
                    // Calculate volume
                    var volume = Math.PI * Math.pow(radius, 2) * depth;
                    // Update the volume field with the calculated value
                    volumeField.value = volume.toFixed(2); // Round to 2 decimal places
                    if ( diameterField.value >= 1 || depthField.value >= 1 ) {
                        volumeField.readOnly = true; // Set as readonly to prevent editing
                    }
                    else
                    {
                        volumeField.readOnly = false; // Set as not readonly
                    }  
                };
    
                // Add event listeners to trigger volume calculation on input change
                diameterField.addEventListener('input', calculateVolume);
                depthField.addEventListener('input', calculateVolume);
            });
    
    
            //To calculate volume of recatangle
            document.addEventListener('DOMContentLoaded', function() {
                var lengthField = document.querySelector('input[name="tank_length"]');
                var widthField = document.querySelector('input[name="tank_width"]');
                var depthField = document.querySelector('input[name="depth"]');
                var volumeField = document.querySelector('input[name="size"]');
    
                var calculateVolume = function() {
                    var length = parseFloat(lengthField.value) || 0;
                    var width = parseFloat(widthField.value) || 0;
                    var depth = parseFloat(depthField.value) || 0;
    
                    // Calculate volume
                    var volume = length * width * depth;
    
                    // Update the volume field with the calculated value
                    volumeField.value = volume.toFixed(2); // Round to 2 decimal places
                 // Make the 'size' field readonly after the value is calculated
                 if (lengthField.value >= 1 || widthField.value >= 1 || depthField.value >= 1 ) {
                        volumeField.readOnly = true; // Set as readonly to prevent editing
                    }
                    else
                    {
                        volumeField.readOnly = false; // Set as not readonly
    
                    }
                };
    
                // Add event listeners to trigger volume calculation on input change
                lengthField.addEventListener('input', calculateVolume);
                widthField.addEventListener('input', calculateVolume);
                depthField.addEventListener('input', calculateVolume);
            });
    
        }
    function handleSizeReadOnlyOnLoad()
    {
        var diameterField = document.querySelector('input[name="pit_diameter"]');
        var depthField = document.querySelector('input[name="pit_depth"]');
        var volumeField = document.querySelector('input[name="size"]');
        var lengthField = document.querySelector('input[name="tank_length"]');
        var widthField = document.querySelector('input[name="tank_width"]');
        if ( diameterField.value >= 1 || depthField.value >= 1 || lengthField.value >= 1 
            || widthField.value >= 1 || depthField.value >= 1 ) {
            volumeField.readOnly = true; // Set as readonly to prevent editing
        }
        else
        {
            volumeField.readOnly = false; // Set as not readonly
        }  
    }

    function validateFileSize(input,message_id, file_size) {
        const file = input.files[0];
        const hint = document.getElementById(message_id); // Select the <small> element
        const label = input.nextElementSibling; 
        if (file && file.size > file_size * 1024 * 1024) { // 5 MB in bytes
            input.value = ''; // Clear the input
            // Change the font color to red
            hint.style.color = 'red';
             // Update the label text to default
            if (label && label.classList.contains('custom-file-label')) {
                label.textContent = 'Choose file';
            }
        } else {
            // Reset the font color to its default (if file size is acceptable or input is cleared)
            hint.style.color = 'green'; // Default back to CSS-defined color
        }
    }


    function showContainmentDimensionsOnReload()
    {
        var selectedText = $("#containment-type option:selected").text();
        var showOptions = [
            "Double Pit",
            "Permeable/ Unlined Pit",
            "Lined Pit connected to a Soak Pit",
            "Lined Pit connected to Water Body",
            "Lined Pit connected to Open Ground",
            "Lined Pit connected to Sewer Network",
            "Lined Pit connected to Drain Network",
            "Lined Pit without Outlet",
            "Lined Pit with Unknown Outlet Connection",
            "Lined Pit with Impermeable Walls and Open Bottom"
        ];
        if (showOptions.includes(selectedText)) {
            $('#pit-shape').show();
            $('#tank-depth').hide();
            $('#tank-width').hide();
            $('#tank-length').hide();
            $('#septic-tank').hide();
        }
        else {
            $('#septic-tank').show();
            $('#pit-shape').hide();
        }
    
        // Check if the selected text is in the array of showOptions and if the pit shape is "Cylindrical"
        if (showOptions.includes(selectedText) && ($("#pit-shape :selected").text() ==
            "Cylindrical")) {
            $('#pit-depth').show();
            $('#pit-size').show();
            $('#tank-depth').hide();
            $('#tank-width').hide();
            $('#tank-length').hide();
        } else {
            $('#pit-size').hide();
            $('#pit-depth').hide();
            $('#tank-depth').show();
            $('#tank-width').show();
            $('#tank-length').show();
    
        }
    }


    function handleCTPTUseCat()
    {
           // to hide population , family count , total popn  if functional == Community/Public toilets
    $('#use_category_id').on('change', function () {
        if ($("#use_category_id :selected").text() == "Community Toilet" ||  $("#use_category_id :selected").text() == "Public Toilet") {
            $('#population-info').hide();
            $('#family-count').hide();
            $('#male-population').hide();
            $('#female-population').hide();
            $('#other-population').hide();
           $('#male-diff-population').hide();
           $('#female-diff-population').hide();
           $('#other-diff-population').hide();
           $('#shared-toilet').hide();
           $('#shared-toilet-popn').hide();
        } else {
            $('#male-population').show();
            $('#female-population').show();
            $('#other-population').show();
            $('#population-info').show();
            $('#family-count').show();
            $('#male-diff-population').show();
            $('#female-diff-population').show();
            $('#other-diff-population').show();
            $('#shared-toilet').show();
            $('#shared-toilet-popn').show();


        }
    });

    // To set toilet status == 1 and show related fields(also applied in backend part (Building Structure Service))
    $('#use_category_id').on('change', function () {
        var selectedText = $("#use_category_id option:selected").text();
        if (selectedText == "Community Toilet" ||selectedText == "Public Toilet"  ) {
            $('#toilet_status').val('1'); // Set toilet_status to 'Yes'
            $('#toilet-info').show();
            $('#toilet-connection').show();
            $('#defecation-place').hide();
            $('#ctpt-toilet').hide();
        }
    });

    if ($("#use_category_id :selected").text() == "Community Toilet" ||  $("#use_category_id :selected").text() == "Public Toilet") {
        $('#population-info').hide();
        $('#family-count').hide();
        $('#male-population').hide();
        $('#female-population').hide();
        $('#other-population').hide();
       $('#male-diff-population').hide();
       $('#female-diff-population').hide();
       $('#other-diff-population').hide();
       $('#shared-toilet').hide();
       $('#shared-toilet-popn').hide();
    } else {
        $('#male-population').show();
        $('#female-population').show();
        $('#other-population').show();
        $('#population-info').show();
        $('#family-count').show();
        $('#male-diff-population').show();
        $('#female-diff-population').show();
        $('#other-diff-population').show();
        $('#shared-toilet').show();
        $('#shared-toilet-popn').show();

    }

    var selectedText = $("#use_category_id option:selected").text();
    if (selectedText == "Community Toilet" ||selectedText == "Public Toilet"  ) {
        $('#toilet_status').val('1'); // Set toilet_status to 'Yes'
        $('#toilet-info').show();
        $('#toilet-connection').show();
        $('#defecation-place').hide();
        $('#ctpt-toilet').hide();
    }
    }


    