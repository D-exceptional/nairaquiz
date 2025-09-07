import { displayInfo, displaySuccess } from "./export.js";

$(document).ready(function () {
    
    function showButton() {
      let hasNoWithdrawals = false;
      const tbodies = document.querySelectorAll('tbody');
    
      tbodies.forEach(tbody => {
        const tr = tbody.querySelector('tr > td[colspan="20"].text-center');
        if (tr && tr.textContent.trim() === 'No withdrawals yet') {
          hasNoWithdrawals = true;
        }
      });
    
      if (hasNoWithdrawals) {
        $("#pay-all").hide();
        return;
      }
    
      // Withdrawals exist; now check for "Pending" status
      let pendingCount = 0;
      $("tbody tr").each(function () {
        const buttonText = $(this).find(".status button").text().trim();
        if (buttonText === "Pending") {
          pendingCount++;
        }
      });
    
      if (pendingCount === 0) {
        $("#pay-all").hide();
      } else {
        $("#pay-all").show();
      }
    }
    
    setTimeout(() => {
      showButton();
    }, 2000);
    
    //Get current day and week
    const currentDate = new Date();
    const weekdays = [
      "Sunday",
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
    ];
    const currentDay = weekdays[currentDate.getDay()];
    const week = currentDate.toLocaleDateString("en-US", {
      month: "long",
      day: "numeric",
      year: "numeric",
    });
    const reason = `NairaQuiz Payout`;
    const payoutDay = currentDay + ", " + week;

    /*
      SINGLE TRANSFER
    */
    
    $(".action button").each(function (index, el) {
      $(el).on("click", function () {
        const row = $(el).closest('tr');
        const name = row.find(".fullname").text();
        const email = row.find(".email").text();
        const reference = row.find(".reference").text();
        const account = row.find(".account").text();
        const bank = row.find(".bank").text();
        const amount = row.find(".amount").text().trim();

      //Filter beneficiaries
      if (account !== "" && bank !== "") {
        $(el).text("Processing...");
        //Send transfer data to server
        $.ajax({
          type: "POST",
          url: "../server/transfer.php",
          data: {
            name: name,
            email: email,
            amount: amount,
            reference: reference,
          },
          dataType: "json",
          success: function (response) {
            for (var key in response) {
              if (Object.hasOwnProperty.call(response, key)) {
                const content = response.Info;
                if (content === "Transfer was successful") {
                  $("tbody tr").each(function (index, el) {
                    if ($(el).find(".email").text() === email) {
                      $(el)
                        .find(".status button")
                        .removeClass("btn btn-danger btn-sm")
                        .addClass("btn btn-success btn-sm")
                        .text("Completed");
                      $(el)
                        .find(".action button")
                        .removeClass("btn btn-info btn-sm")
                        .addClass("btn btn-success btn-sm")
                        .html(
                          "<i class='fas fa-check' style='padding-right: 5px;'></i>  Done"
                        )
                        .attr("disabled", true)
                        .css({ background: "green", width: "80px" });
                    }
                  });
                  displaySuccess(content);
                } else  {
                  //Update UI
                  $("tbody tr").each(function (index, el) {
                    if ($(el).find(".email").text() === email) {
                      $(el).find(".status button").text("Pending");
                      $(el).find(".action button").text("Retry");
                    }
                  });
                  displayInfo(content);
                }
              }
            }
          },
          error: function (e) {
            displayInfo("Error connecting to server");
            console.log(
              `Error response is: ${e.responseText} and error status is: ${e.statusText}`
            );
          },
        });
      } else {
        displayInfo("Bank details not found");
        return;
      }
      });
    });
    
    /*
      BULK TRANSFER
    */
    
    let paymentArray = [];
    
    $("#pay-all").on("click", function () {
        //Loop through table row data
        $("tbody tr").each(function (index, el) {
          const row = $(el);
          const name = row.find(".fullname").text();
          const email = row.find(".email").text();
          const reference = row.find(".reference").text();
          const account = row.find(".account").text();
          const bank = row.find(".bank").text();
          const amount = row.find(".amount").text().trim();

          //Filter beneficiaries
          if (account !== "" && bank !== "") {
              //Prepare payment object
              const payoutObject = {
                name: name,
                email: email,
                amount: amount,
                reference: reference,
              };
              //Queue up payment objects in array
              paymentArray.push(payoutObject);
          }
        });
        //Convert payment array to json string
        const beneficiaries = JSON.stringify(paymentArray);
        //Send payment data to server
        $.ajax({
          type: "POST",
          url: "../server/payment.php",
          data: { beneficiaries: beneficiaries },
          dataType: "json",
          success: function (response) {
            for (const key in response) {
              if (Object.hasOwnProperty.call(response, key)) {
                const content = response.Info;
                if (content === "Transfers queued successfully") {
                  displaySuccess(content);
                  //Check status of transfers
                  setTimeout(() => {
                    window.location.reload();
                  }, 3000);
                } else {
                  displayInfo(content);
                }
              }
            }
          },
          error: function (e) {
            displayInfo("Error connecting to server");
            console.log(
              `Error response is: ${e.responseText} and error status is: ${e.statusText}`
            );
          },
        });
    });
    
    //Search function
    $("#page-search").on("keyup", function () {
      const searchValue = $(this).val().toLowerCase();
      if (searchValue !== "") {
        $(".rows").each(function (index, el) {
          if ($(el).find("td").text().toLowerCase().includes(searchValue)) {
            $(el).css({ display: "table-row" });
          } else {
            $(el).css({ display: "none" });
          }
        });
      } else {
        $(".rows").each(function (index, el) {
          if ($(el).css("display") === "none") {
            $(el).css({ display: "table-row" });
          }
        });
      }
    });
});