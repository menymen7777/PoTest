$(document).ready(function () {
    /** Фоновое действие */
    $('body').on('click', '.js__ajax-action', function (e) {
        e.preventDefault();
        var $this = $(this);
        $.ajax({
            url: $this.data('url'),
            method: 'post',
            success: function () {
                $.pjax.reload({container : $this.data('pjax-selector'), async: false});
            },
            error: function (jqXHR) {
                alert(xhr.responseJSON.message);
            }
        });
    });

    /** Отправка формы в модалке */
    $('body').on('click', '.js__accept-modal', function () {
        $(this).attr('disabled', true);
        $(this).closest('form').submit();
    });

    /** Вызов модалки */
    $('body').on('click', '.js__eat', function (e) {
        e.preventDefault();
        var
            $modal = $($(this).data('target')),
            url = $(this).data('url');

        $.ajax({
            url: url,
            method: 'get',
            success: function (data, textStatus, jqXHR) {
                $modal.find('.js__modal-body').html(data);
                $modal.modal('show');
            },
            error: function (jqXHR) {
                alert(xhr.responseJSON.message);
            }
        });
    });
});