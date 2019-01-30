// inspired by http://phppackagechecklist.com/js/scripts.js

$(function() {
    // If is not set, create it
    if (window.localStorage.getItem('books') == null) {
        window.localStorage.setItem('books', JSON.stringify([]));
    }

    var rows = JSON.parse(window.localStorage.getItem('books'));
    var $inputs = $('.checklist__input');

    // Set initial checked state to checkboxes found in localStorage
    if (rows.length) {
        $inputs.each(function() {
            console.log($(this).val());
            if ($.inArray($(this).val(), rows) !== -1) {
                $(this).attr('checked', true);
            }
        });
    }

    // Update localStorage on change
    $inputs.change(function () {
        var rows = [];

        $('.checklist__input:checked').each(function() {
            rows.push($(this).val());
        });

        window.localStorage.setItem('books', JSON.stringify(rows));
    });
});
