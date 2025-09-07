import { displayInfo } from "../scripts/export.js";

function formatMessage(text) {
    return text.split(/\s+/).slice(0, 25).join(' ');
}

function fetchMails() {
    $('tbody').empty();
    $.ajax({
        type: "GET",
        url: "../server/mailbox.php",
        dataType: "json",
        success: function(response){
            for (const key in response) {
                if (Object.hasOwnProperty.call(response, key)) {
                    const content = response[key];
                    if (content !== 'No mail found') {
                        // Get media
                        const media = content.mail_filename === 'null' ? '' : '<i class="fas fa-paperclip"></i>';
                        // Get message
                        const message = formatMessage(content.mail_message);
                        
                        // Display mails
                        let mailCard = `   <tr class='mail-list' id='${content.mailID}'>
                                            <td>
                                                <div class="icheck-primary">
                                                    <input type="checkbox" value="">
                                                    <label for="check13"></label>
                                                </div>
                                            </td>
                                            <td class="mailbox-star"><a href="#"><i class="fas fa-star-o text-warning"></i></a></td>
                                            <td class="mailbox-name"><a href="read-mail.php?mailID=${content.mailID}">${content.mail_sender}</a></td>
                                            <td class="mailbox-subject">${message}...</td>
                                            <td class="mailbox-attachment">${media}</td>
                                            <td class="mailbox-date">${content.mail_date}  ${content.mail_time}</td>
                                            <!-- Hidden details -->
                                            <input type="text" value="${content.mail_filename}" class='mail_filename' hidden>
                                            <input type="number" value="${content.mailID}" class='mailID' hidden>
                                        </tr>
                                        `;

                        $('tbody').append(mailCard);
                    }
                    else{
                        displayInfo(content);
                    }
                }
            }

            const counter = Number($("tbody").children("tr").length);
            $(".col-sm-6 h1")
            .empty()
            .html("<b>Inbox " + "(" + counter + ")");
        },
        error: function (e) {
            console.log(e.responseText)
        }
    });
}

fetchMails();

// Read mails
$(document).on('click', 'tbody tr', function(){
    const id = $(this).attr('id');
    window.location.href = `read-mail.php?mailID=${id}`;
});

//Indent all inner child navs
$('.nav-sidebar').addClass('nav-child-indent');

//Search function
$('#page-search').on('keyup', function () { 

let searchValue = $(this).val();
if (searchValue !== "") {
    $('.mail-list').each(function (index, el) {
        if($(el).find('.mailbox-name a').text().toLowerCase().includes(searchValue) || ($(el).find('.mailbox-subject').text().toLowerCase().includes(searchValue))){
            $(el).css({'display':'table-row'});
        }else{
            $(el).css({'display':'none'});
        }
    });
}
else{
    $('.mail-list').each(function (index, el) {
        if($(el).css('display') === 'none'){
            $(el).css({'display':'table-row'});
        }
    });
}
});