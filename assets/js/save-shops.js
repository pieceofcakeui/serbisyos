function openImageModal(imageSrc) {
    document.getElementById('imageModal').style.display = 'block';
    document.getElementById('modalImage').src = imageSrc;
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
}

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});

$(document).ready(function () {
    $(".save-shop").click(function () {
        let shopId = $(this).data("shop-id");

        $.ajax({
            url: "../account/save-shops.php",
            type: "POST",
            data: { shop_id: shopId },
            success: function (response) {
                alert(response);
                location.reload();
            },
            error: function () {
                alert("Error saving shop.");
            }
        });
    });

    function showNotification(message) {
        let notification = $("#notification");
        notification.text(message).fadeIn(300);
        setTimeout(() => {
            notification.fadeOut(300);
        }, 3000);
    }

    $(document).on('click', '.remove-shop', function () {
        let shopId = $(this).data("shop-id");
        let modal = $('#removeShopModal');
        modal.find('.confirm-remove').off('click');

        modal.find('.confirm-remove').on('click', function () {
            $.ajax({
                url: "../account/backend/delete_save_shop.php",
                type: "POST",
                data: { shop_id: shopId },
                dataType: "json",
                success: function (response) {
                    showNotification(response.message);
                    modal.modal('hide');
                    location.reload();
                },
                error: function () {
                    showNotification("Error removing shop.");
                    modal.modal('hide');
                }
            });
        });

        modal.modal('show');
    });

    $('#removeShopModal').on('hidden.bs.modal', function () {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    });
});