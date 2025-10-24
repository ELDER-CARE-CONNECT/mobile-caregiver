$(document).ready(function() {
    $('#chonhet').click(function() {
        var status = this.checked;
        $('input[name=chon]').each(function() {
            this.checked = status;
        });
    });

    $('#xoahet').click(function() {
        var listid = "";
        $('input[name=chon]').each(function() {
            if (this.checked == true) {
                listid += "," + this.value;
            }
        });
        listid = listid.substr(1);
        window.location = "xoa.php?listid=" + listid;
    });
});

//         $(document).ready(function() {
//             $('.hang').mouseover(function() {
//                 $(this).addClass('hightlight'); 
//             });
    
//             $('.hang').mouseout(function() {
//                 $(this).removeClass('hightlight');
//             });
//         });
//     $(document).ready(function() {
//         $('#chonhet').click(function() {
//             var status=this.checked;
//             $('input[name=chon]').each(function() {
//                 this.checked=status;
//             });
//         });
//     });
//     $(document).ready(function() {
//     $('#chonhet').click(function() {
//         var status = this.checked;
//         $('input[name=chon]').each(function() {
//             this.checked = status;
//         });
//     });

//     $('#chinhhet').click(function() {
//         var listid = "";
//         $('input[name=chon]').each(function() {
//             if (this.checked) 
//                 listid = listid + ',' + this.value;
//         });
//         window.location = "chinh.php?listid=" + listid;
//     });
// });
//     $(document).ready(function() {
//         $('#chonhet').click(function() {
//             var status = this.checked;
//             $('input[name=chon]').each(function() {
//                 this.checked = status;
//             });
//         });

//         $('#xoahet').click(function() {
//             var listid = "";
//             $('input[name=chon]').each(function() {
//                 if (this.checked == true) {
//                     listid += "," + this.value;
//                 }
//             });
//             listid = listid.substr(1);
//              window.location = "xoa.php?listid=" + listid;
//         });
//     });