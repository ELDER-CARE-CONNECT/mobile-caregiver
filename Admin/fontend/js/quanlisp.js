$(document).ready(function() {
    // Gắn sự kiện hover cho các dòng có class "hang", kể cả sau này thêm mới
    $(document).on('mouseover', '.hang', function() {
        $(this).addClass('hightlight'); 
    });

    $(document).on('mouseout', '.hang', function() {
        $(this).removeClass('hightlight'); 
    });
});
