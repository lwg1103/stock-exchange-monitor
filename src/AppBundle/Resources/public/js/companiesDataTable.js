var table = $('#companies-table').DataTable();

/**
 * button events
 */
$( document ).ready(function() {
    loadParametersFromURL();
    applyFilters();
});

$('#filter-apply').click( function() {
    applyFilters();
} );

$('#filter-save').click( function() {
    saveParamsInURL();
});

/**
 * actions
 */
function loadParametersFromURL()
{
    var params = decodeURIComponent(window.location.search.substring(1)).split('&');

    if (params) {
        for (i = 1; i < params.length; i++) {
            var parameterParts = params[i].split('=');
            var element = $('#' + parameterParts[0]);

            switch (element.attr('type')) {
                case 'number':
                    element.val(parameterParts[1]);
                    break;
                case 'checkbox':
                    element.attr('checked', true);
                    break;
            }
        }
    }
}

function applyFilters() {
    $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            return (
                checkNumberColumn(data, 'czy', 3) &&
                checkNumberColumn(data, 'cz4q', 4) &&
                checkNumberColumn(data, 'cz7y', 5) &&
                checkNumberColumn(data, 'cwk', 6) &&
                checkNumberColumn(data, 'czcwk', 7) &&
                checkNumberColumn(data, 'div', 8) &&
                checkCheckboxColumn(data, 'noloss', 9)
            )
        }
    );

    table.draw();
}

function checkNumberColumn(data, name, index) {
    var min = ($('#'+name+'-min').val() ? $('#'+name+'-min').val() : -9999);
    var max = ($('#'+name+'-max').val() ? $('#'+name+'-max').val() : 9999);
    var value = parseFloat( data[index] ) || 0;

    if ( min <= value && value <= max )
        return true;
    return false;
}

function checkCheckboxColumn(data, name, index) {
    var enabled = $('#'+name)[0].checked;
    var value = data[index];

    return (!enabled || value === 'OK');
}

function saveParamsInURL()
{
    var url = "";

    $(':input.js-filter-param').each( function () {
        switch ($( this ).attr('type')) {
            case 'number':
                if ($( this ).val()) {
                    url = url + "&" + $(this).attr('id') + "=" + $(this).val();
                };
                break;
            case 'checkbox':
                if ($(this).is(':checked'))
                    url = url + "&" + $(this).attr('id') + "=true";
                break;
        }
    });

    document.location.search = encodeURI(url);
}
