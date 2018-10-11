jQuery(document).ready(function($){
    console.log('ready');


    $('.close-row').on('click', '.remove_row', function(events){
        $(this).parent().parent().remove();
     });

     $('#add-row-cat').click(function(e){
        $('.append-row-cat').append('<tr>\
                        <td>'+$('.dropdown-cat-list').html()+'</td>\
                        <td><input type="number" name="price" id ="cat-price"></td>\
                        <td><a href="javascript:void(0);" class="remove_row">X</a></td>\
                    </tr>')
    });
});