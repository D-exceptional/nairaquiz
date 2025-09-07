$(document).ready(function () {
   // Use event delegation for dynamic content
    $(document).on("click", ".rows button", function () {
      const id = $(this).closest("tr").attr("id");
      
      if (id) {
        $("#trials-overlay").css("display", "block");
        $("#trials-overlay iframe").attr("src", `../views/details.php?trialID=${id}`);
      } else {
        console.warn("No trial ID found on row");
      }
    });

    $("#close-trials-overlay").on("click", function () {
      $("#trials-overlay").css({ display: "none" });
      $("#trials-overlay iframe").attr("src", ``);
    });
    
    // Function to sort users alphabetically
    function sortUsersAlphabetically() {
        const $userList = $("tbody"); // Container
        const $users = $userList.find("tr.rows").toArray(); // Get all current user rows
    
        // Use Set to ensure we don’t sort duplicates
        const seenIDs = new Set();
        const uniqueUsers = $users.filter(row => {
            const id = $(row).attr("id");
            if (seenIDs.has(id)) return false;
            seenIDs.add(id);
            return true;
        });
    
        // Sort alphabetically by `.fullname` text
        uniqueUsers.sort((a, b) => {
            const nameA = $(a).find(".name").text().trim().toLowerCase();
            const nameB = $(b).find(".name").text().trim().toLowerCase();
            return nameA.localeCompare(nameB);
        });
    
        // Clear and re-append
        $userList.empty().append(uniqueUsers);
    }
    
    sortUsersAlphabetically();
});
