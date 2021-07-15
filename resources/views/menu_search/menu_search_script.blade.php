{{-- <script>

// $('.search-button').on('click', function(e) {
//     $('.menu-search-input').val('');
//     menu_search_result_list.html('');
//     menu_search_result_container.attr('hidden', true);
// });

$('.menu-search-input').on('keyup', function(e) {
    let aa = $('.menu-search-input').val();
    console.log(aa);

    let text = $('.menu-search-input').val();

    let result = '';
    if(text.length) {
        menu_search_result_container.attr('hidden', false);
        menu_list_members.each(function(e) {
            if($(this).text().toLowerCase().includes(text.toLowerCase())) {
                result += '<li class="list-group-item">' + $(this).prop("outerHTML") + '</li>';
            }
        });

        if(!result.length) {
            menu_search_result_msg.text('No Menu Found.');
        }

        menu_search_result_list.html(result);
    } else {
        menu_search_result_container.attr('hidden', true);
        menu_search_result_list.html('');
        menu_search_result_msg.html('');
    }
});
</script> --}}
