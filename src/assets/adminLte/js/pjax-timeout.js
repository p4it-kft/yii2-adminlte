$(document).on('pjax:timeout', function(event) {
    // Prevent default timeout redirection behavior
    event.preventDefault()
});
$(document).on('pjax:send', function(xhr, options) {
    $(xhr.target).find('.card').append('<div class="overlay">\n' +
        '  <i class="fas fa-2x fa-sync-alt fa-spin"></i>\n' +
        '</div>');
});