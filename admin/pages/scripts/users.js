import { displayInfo } from "../scripts/export.js";

$(document).ready(function () {
    let offset = 0;
    const limit = 50;

    function fetchUsers(offsetVal = 0) {
        $.ajax({
            type: "GET", // use GET to match the PHP version
            url: "../server/fetch-users.php",
            data: { offset: offsetVal, limit: limit },
            dataType: "json",
            success: function (response) {
                if (response.Info === "No record found") {
                    displayInfo("No more users to load.");
                    $("#load-more").hide();
                    return;
                }

                for (const key in response) {
                    if (Object.hasOwnProperty.call(response, key)) {
                        const content = response[key];

                        let status = '';
                        if (content.email !== 'obaloluwaemma2005@gmail.com') {
                            status = `<button class='btn btn-success btn-sm'>Active</button>`;
                        } else {
                            status = `<button class='btn btn-danger btn-sm'>Pending</button>`;
                        }

                        let userCard = `
                            <tr class='rows' id="${content.userID}">
                                <td>#</td>
                                <td class='fullname'>${content.fullname}</td>
                                <td>
                                    <ul class="list-inline">
                                        <li class="list-inline-item">
                                            <img alt="Avatar" class="table-avatar" src="../../../assets/img/user.png" style="width: 50px;height: 50px;border-radius: 50%;">
                                        </li>
                                    </ul>
                                </td>
                                <td class='email'>${content.email}</td>
                                <td>${content.contact}</td>
                                <td>${content.country}</td>
                                <td>${content.created_on}</td>
                                <td>${status}</td>
                                <td>${content.account_number}</td>
                                <td>${content.bank}</td>
                                <td>${content.wallet}</td>
                                <td>${content.withdraw}</td>
                                <td>
                                    <div style='display: flex; gap: 10px;'>
                                        <button class='btn btn-info btn-sm'>View</button>
                                        <button class='btn btn-danger btn-sm'>Delete</button>
                                    </div>
                                </td>
                            </tr> 
                        `;

                        $('tbody').append(userCard);
                    }
                }
                
                sortUsersAlphabetically();

                const counter = $("tbody tr.rows").length;
                $(".col-sm-6 h1").html(`<b>Users (${counter})</b>`);
            },
            error: function (e) {
                console.error("Error fetching users:", e.responseText);
            }
        });
        $('#load-more').text("Load More");
    }

    // First load
    fetchUsers(offset);
    
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
            const nameA = $(a).find(".fullname").text().trim().toLowerCase();
            const nameB = $(b).find(".fullname").text().trim().toLowerCase();
            return nameA.localeCompare(nameB);
        });
    
        // Clear and re-append
        $userList.empty().append(uniqueUsers);
    }

    // Indent all inner child navs
    $('.nav-sidebar').addClass('nav-child-indent');

    // Search function
    $('#page-search').on('keyup', function () {
        const searchValue = $(this).val().toLowerCase();

        $('.rows').each(function () {
            const nameMatch = $(this).find('.fullname').text().toLowerCase().includes(searchValue);
            const emailMatch = $(this).find('.email').text().toLowerCase().includes(searchValue);

            $(this).toggle(nameMatch || emailMatch);
        });
    });

    // Load more users on button click
    $(document).on('click', '#load-more', function () {
        $(this).text("Loading...");
        offset += limit;
        fetchUsers(offset);
    });
});
