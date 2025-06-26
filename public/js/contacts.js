var base_url = $("#base_url").val();
var isExportShown = false;

tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                }
            }
        }
    }
}

$(document).ready(function () {

    // get url parameters
    var urlParams = new URLSearchParams(window.location.search);
    var dateRange = urlParams.get('dateRange');

    // split and get start and end date
    if (dateRange) {
        var dates = dateRange.split(' - ');
        if (dates.length === 2) {
            var startDate = moment(dates[0], 'YYYY-MM-DD');
            var endDate = moment(dates[1], 'YYYY-MM-DD');

            $('#created-daterange span').html(startDate.format('MMM D') + ' - ' + endDate.format('MMM D'));
            $("#dateRange").val(startDate.format('YYYY-MM-DD') + ' - ' + endDate.format('YYYY-MM-DD'));
        }
    } else {
        var startDate = moment().subtract(50, 'years');
        var endDate = moment().add(1, 'year');

        $('#created-daterange span').html('All Time');
    }

    $('#created-daterange').daterangepicker({
        showDropdowns: true,
        startDate: startDate,
        endDate: endDate,
        opens: "left",
        ranges: {
            'All Time': [moment().subtract(50, 'years'), moment().add(1, 'year')],
        }
    }, cb);

    $('#created-daterange').on('apply.daterangepicker', function (ev, picker) {
        var startDate = picker.startDate;
        var endDate = picker.endDate;
        $("#dateRange").val(startDate.format('YYYY-MM-DD') + ' - ' + endDate.format('YYYY-MM-DD'));
    });

    $('#created-daterange').on('show.daterangepicker', function (ev, picker) {
        picker.container.addClass('createddaterange');
    });

    $(document).on("click", '.createddaterange .ranges li[data-range-key="Custom Range"]', function () {
        var start_custom = moment().startOf('month');
        var end_custom = moment().endOf('month').subtract(1, 'days');
        $('#created-daterange').data('daterangepicker').setStartDate(start_custom);
        $('#created-daterange').data('daterangepicker').setEndDate(end_custom);
        $('.yearselect').val(start_custom.format('Y')).trigger('change');
        $('.monthselect').val(start_custom.format('M') - 1).trigger('change');
    });

    $("#exportBtn").click(function (e) {
        e.preventDefault();
        $('#exportBtn').prop('disabled', true);
        $("#exportBtnText").html("Exporting...");

        var form_json = {
            'status': $("#status").val(),
            'company': $("#company").val(),
            'dateRange': $('#dateRange').val(),
            'search': $('#search').val(),
            'total_contacts': $('#total_contacts').val()
        };

        $.ajax({
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: base_url + "/contact-export",
            data: form_json,
            success: function (data) {
                $('#exportBtn').prop('disabled', false);
                $("#exportBtnText").html("Export to CSV");
                $("#pending_message").removeClass("hidden");

                if (data?.status == "error") {
                    $("#text-content").html(data.message);
                } else {
                    $("#text-content").html(data.message);
                }

                setTimeout(function () {
                    $("#pending_message").addClass("hidden");
                }, 2000);

                isExportShown = false;

            }, error: (response) => {
                $("#pending_message").removeClass("hidden");
                $("#text-content").html("An error occurred while processing your request. Please try again later.");
                setTimeout(function () {
                    $("#pending_message").addClass("hidden");
                }, 4000);
            }
        });
    });

    setInterval(function () {
        if (!isExportShown)
            checkFileReady();
    }, 5000);

});

function cb(start, end) {
    if (start.format('MMM D, YYYY') == moment().subtract(50, 'years').format('MMM D, YYYY')) {
        $('#created-daterange span').html('All time');
    } else {
        $('#created-daterange span').html(start.format('MMM D') + ' - ' + end.format('MMM D'));
    }
}

function checkFileReady() {
    $.ajax({
        method: "GET",
        url: base_url + "/check-export-ready",
        success: function (response) {
            if (response?.status === "success" && response?.data) {
                $("#exportModal").removeClass('hidden').addClass('flex');
                $("#downloadExportBtn").attr("href", response?.data?.full_file_path);
                isExportShown = true;
            }
        }
    });
}

function closeModal() {
    $("#exportModal").removeClass('flex').addClass('hidden');
}