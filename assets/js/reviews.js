$(document).ready(function () {
    $(".edit-review").click(function () {
        let reviewId = $(this).data("review-id");
        let rating = $(this).data("rating");
        let comment = $(this).data("comment");

        $("#editReviewId").val(reviewId);
        $("#editComment").val(comment);
        setStarRating(rating); 
        $("#editReviewModal").modal("show");
    });

    $("#editStarRating i").click(function () {
        let selectedRating = $(this).data("value");
        $("#editRating").val(selectedRating);
        setStarRating(selectedRating);
    });

    function setStarRating(rating) {
        $("#editStarRating i").each(function () {
            let starValue = $(this).data("value");
            if (starValue <= rating) {
                $(this).removeClass("far").addClass("fas");
            } else {
                $(this).removeClass("fas").addClass("far");
            }
        });
    }

    $("#saveEditReview").click(function () {
        let reviewId = $("#editReviewId").val();
        let rating = $("#editRating").val();
        let comment = $("#editComment").val();

        $.post("../account/review_actions.php", { action: "edit", review_id: reviewId, rating: rating, comment: comment }, function (response) {
            let data = JSON.parse(response);
            alert(data.message);
            if (data.status === "success") location.reload();
        });
    });

    $(".delete-review").click(function () {
        if (!confirm("Are you sure you want to delete this review?")) return;

        let reviewId = $(this).data("review-id");

        $.post("../account/review_actions.php", { action: "delete", review_id: reviewId }, function (response) {
            let data = JSON.parse(response);
            alert(data.message);
            if (data.status === "success") location.reload();
        });
    });
});