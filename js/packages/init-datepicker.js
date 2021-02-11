function initDatepicker(){
    $("[data-toggle='datepicker']").pickadate({
        today: '',
        clear: '',
        close: '',
        format: 'mmm yyyy',
        format: 'dd mmm yyyy',
        formatSubmit: 'dd mmm yyyy',
        onClose: function() {
            document.activeElement.blur();
        }
    });

    $("[data-toggle='monthpicker']").pickadate({
        today: '',
        clear: 'View all',
        close: '',
        format: 'mmm yyyy',
        formatSubmit: 'mmm yyyy',
        onClose: function() {
            document.activeElement.blur();
        }
    });
}