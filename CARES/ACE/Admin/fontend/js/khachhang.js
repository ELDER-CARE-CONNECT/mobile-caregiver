
$(document).ready(function() {
    $('.hang').mouseover(function() {
        $(this).addClass('hightlight'); 
    });

    $('.hang').mouseout(function() {
        $(this).removeClass('hightlight');
    });
});

$(document).ready(function(){
    $(".show-orders").click(function(){
        var id = $(this).data("id");
        $("#orders-" + id).toggle(); // Ẩn/hiện dòng đơn hàng tương ứng
    });
});
