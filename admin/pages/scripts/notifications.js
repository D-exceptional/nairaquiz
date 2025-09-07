import { displayInfo } from './export.js';

$('#notification-link').on('click', function () {
  if ($(this).find('span').css('display') === 'block') {
    $(this).find('span').hide().text('');
    //Update status
    $.ajax({
      type: 'POST',
      url: '../server/notification.php',
      dataType: 'json',
      success: function (response) {
        for (const key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response.Info;
            if (content === 'Status updated successfully' ) {
                $('.dropdown-menu dropdown-menu-lg dropdown-menu-right')
                  .empty()
                  .html(
                    `<span class='dropdown-item dropdown-header'>No new notifications</span>
                    <a href='../views/timeline.php' class='dropdown-item dropdown-footer'>View all</a>
                  `
                  );
            } else {
              displayInfo(content);
            }
          }
        }
      },
      error: function () {
        displayInfo('Error connecting to server');
      },
    });
  }
  else {
    $(this).css({ opacity: '1' });
  }
});

$("#open-overlay").on("click", function () {
  $("#details-overlay").css({ display: "flex" });
});

$("#close-view").on("click", function () {
  $("#details-overlay").css({ display: "none" });
});
