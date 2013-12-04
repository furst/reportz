var pathname = window.location.pathname;
var url = pathname.split("/")[6];

$(document).ready(function () {
    (function () {

        $('.reportrow').on('mouseover', function(e) {
            $(this).css({background: '#f1f1f1', cursor: 'pointer' });
        });

        $('.reportrow').on('mouseout', function(e) {
            $(this).css({background: 'none'});
        });

        $('a.comment-show').on('click', function(e) {
            $(this).prev('.comment-con').toggle();
            e.preventDefault();
        });

        $('.testcase-form').submit(function(event) {

            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

            if ($('input[name="username"]').val() === '') {
                $('p.testcase-error').text('användarnamn får inte vara tomt');
                event.preventDefault();
            }
            else if ($('input[name="email"]').val() === '') {
                $('p.testcase-error').text('epost får inte vara tomt');
                event.preventDefault();
            }
            else if (!re.test($('input[name="email"]').val())) {
                $('p.testcase-error').text('epost är i fel format');
                event.preventDefault();
            }
        });

        $('.comment-form').wrap('<form class="comment" method="POST"></form>');

        // variable to hold request
        var request;
        // bind to the submit event of our form
        $(".comment").submit(function(event){
            // abort any pending request
            if (request) {
                request.abort();
            }
            // setup some local variables
            var $form = $(this);
            // let's select and cache all the fields
            var $inputs = $form.find("input, select, button, textarea");
            // serialize the data in the form
            var serializedData = $form.serialize();

            // let's disable the inputs for the duration of the ajax request
            $inputs.prop("disabled", true);

            // fire off the request
            request = $.ajax({
                url: url,
                type: "post",
                data: serializedData
            });

            // callback handler that will be called on success
            request.done(function (response, textStatus, jqXHR){
                // TODO: show message?
            });

            // callback handler that will be called on failure
            request.fail(function (jqXHR, textStatus, errorThrown){
                // log the error to the console
                console.error(
                    "The following error occured: "+
                    textStatus, errorThrown
                );
            });

            // callback handler that will be called regardless
            // if the request failed or succeeded
            request.always(function () {
                // reenable the inputs
                $inputs.prop("disabled", false);
            });

            if ($(this).find('input.name').val() !== "" && $(this).find('textarea.content').val() !== "") {
                console.log('test');

                $(this).prev('.comments').append('<p><strong>' + $(this).find('input.name').val() + ':</strong> ' + $(this).find('textarea.content').val() + '</p>');

                $(this).find('input.name').val('');
                $(this).find('textarea.content').val('');

                $(this).next('p.error').text('');
            } else {
                $('p.error').text('');
                $(this).next('p.error').text('Fälten får inte vara tomma');
            }

            // prevent default posting of form
            event.preventDefault();
        });
    })();
});

