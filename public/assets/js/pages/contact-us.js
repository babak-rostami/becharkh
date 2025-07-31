$(document).ready(function () {
    var textarea = $('#cu-ta');

    textarea.on('input', function () {
        this.style.overflow = 'hidden';
        this.style.height = 0;
        this.style.height = this.scrollHeight + 'px';
    });

    // Force trigger the input event after setting the value of the textarea programmatically
    textarea.trigger('input');
});