// inspired by http://phppackagechecklist.com/js/scripts.js
// https://stackoverflow.com/questions/35793305/how-do-i-use-localstorage-javascript-to-remember-information-input-values
// https://jsfiddle.net/gurvinder372/g3ueqkzh/1/

function cleaning_checklist_submit()
{
    var project_name = document.getElementById('project_name').value;
    window.localStorage.setItem('project_name', project_name)
}

$(function() {
    var project_name = window.localStorage.getItem('project_name');
    var keyname = project_name + '_cleaning_lady_items';

    document.getElementById('project_name').value = project_name;

    // If is not set, create it
    if (window.localStorage.getItem(keyname) == null) {
        window.localStorage.setItem(keyname, JSON.stringify([]));
    }

    var rows = JSON.parse(window.localStorage.getItem(keyname));
    var $inputs = $('.cleaning_checklist_input');



    // Set initial checked state to checkboxes found in localStorage
    if (rows.length) {
        $inputs.each(function() {
            if ($.inArray($(this).val(), rows) !== -1) {
                $(this).attr('checked', true);
            }
        });
    }

    // Update localStorage on change
    $inputs.change(function () {
        var rows = [];

        $('.cleaning_checklist_input:checked').each(function() {
            rows.push($(this).val());
        });

        window.localStorage.setItem(keyname, JSON.stringify(rows));
    });
});
