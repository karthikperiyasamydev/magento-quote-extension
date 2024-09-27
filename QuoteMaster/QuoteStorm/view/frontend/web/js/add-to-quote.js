define([
    'jquery',
], function($) {
    'use strict';

    return function(config, element) {
        let form = $(element);
        let button = form.find('#product-addtoquote-button');

        form.on('submit', function(event) {
            event.preventDefault();
            button.prop('disabled', true).find('span').text('Adding...');
            let formData = new FormData(form[0]);
            $.ajax({
                url: form.prop('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(res) {
                    button.find('span').text('Added');
                    setTimeout(function () {
                        button.prop('disabled', false).find('span').text('Add to Quote');
                    }, 1000);
                },
                error: function(error) {
                    console.error('AJAX Error:', error);
                    button.prop('disabled', false).find('span').text('Add to Quote');
                }
            });
        });
    }
});
