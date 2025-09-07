import { displayInfo, displaySuccess } from "../scripts/export.js";

if (
  $("#active-email").val() !== "okekeebuka928@gmail.com" &&
  $("#active-email").val() !== "izuchukwuokuzu@gmail.com"
) {
  if ($("#pay-all").css("display") === "inline-block") {
    $("#pay-all").css({ display: "none" });
  }
}

//Show or hide backup button
const rowCount = $("tbody tr").length;
if (rowCount === 0) {
  $("#backup, #pay-all").hide();
  $("tbody").empty().html("<center><h3>No sales yet!</h3></center>");
}

//Hide the back up button if all pending payments have not been completed
$("tbody tr").each(function (index, el) {
  if ($(el).find(".status button").text() === "Pending") {
    $("#backup").hide();
  }
});

// With custom settings, forcing a "US" locale to guarantee commas in output
function formatAmount(amount) {
  return parseFloat(
    amount.toLocaleString("en-US", { maximumFractionDigits: 2 })
  );
}

function updateCurrency() {
  $.getJSON("../../../currencies.json", function (data) {
    for (const key in data) {
      if (Object.hasOwnProperty.call(data, key)) {
        const content = data[key];
        const country = content.country;
        const code = content.currency_code;
        //Loop through each row and update the currency by country
        $("tbody tr").each(function (index, el) {
          if ($(el).find(".country").text() === country) {
            $(el).find(".currency").empty().text(code);
          }
        });
      }
    }
  });
}

function updateStatus(count) {
  $.ajax({
    type: "GET",
    url: "../server/get-transfers-status.php",
    data: { count: count },
    dataType: "json",
    success: function (response) {
      for (const key in response) {
        if (Object.hasOwnProperty.call(response, key)) {
          const content = response[key];
          if (content === "No transfers found") {
            //displayInfo(content);
            $("body").css({ opacity: 1 });
          } else {
            //Loop through the table rows
            $("tbody tr").each(function (index, el) {
              if ($(el).find(".email").text() === content.email) {
                //Decide what button to show
                switch (content.status) {
                  case "Completed":
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
                    break;
                  case "Failed":
                    $(el).find(".status button").text("Failed");
                    $(el).find(".action button").text("Retry");
                    break;
                  case "Reversed":
                    $(el).find(".status button").text("Reversed");
                    $(el).find(".action button").text("Retry");
                    break;
                }
              }
            });
          }
        }
      }
    },
    error: function (e) {
      displayInfo(e.responseText);
    },
  });
}

//Sales amount
let salesAmount = 0;

//Get total money made
const totalAmountGenerated = $("#total-amount-value").val();

let amountLeft = parseFloat($("#amount-left-value").val());
//console.log("Old amount left value is " + amountLeft);

//Begin
$("tbody tr").each(function (index, el) {
  const email = $(el).find(".email").text();
  if (email === "jerry@gmail.com" || email === "hassan@gmail.com") {
    const intValue = $(el).find(".amount-row").text().split(" ");
    const currencyAmount = intValue[1].split(",").join("");
    const totalPayoutAmount = parseFloat(currencyAmount);
    //Update payout
    salesAmount = amountLeft += totalPayoutAmount;
    $(el).remove();
  }
});

//Extract company savings from admins commission
let payoutAmount = 0;
let payoutAmountInUSD = 0;
let calcValue = 0;
let companySavings = 0;
let companySavingsInUSD = 0;
let savingsAmountOne = 0;
let savingsAmountTwo = 0;
let savingsAmountThree = 0;
let savingsAmountFour = 0;

$(".admin-rows td.fullname").each(function (index, el) {
  const email = $(el).parent().find(".email").text();
  const amount = $(el).parent().find(".amount-row").text().split(" ");
  const currencyAmount = amount[1].split(",").join("");
  const totalPayoutAmount = parseFloat(currencyAmount);

  switch (email) {
    case "izuchukwuokuzu@gmail.com":
      calcValue = totalPayoutAmount + 0.29 * salesAmount;
      savingsAmountOne = 0.1 * calcValue;
      payoutAmount = formatAmount(calcValue - savingsAmountOne);
      payoutAmountInUSD = formatAmount((calcValue - savingsAmountOne) / 500);
      break;
    case "okekeebuka928@gmail.com":
      calcValue = totalPayoutAmount + 0.29 * salesAmount;
      savingsAmountTwo = 0.1 * calcValue;
      payoutAmount = formatAmount(calcValue - savingsAmountTwo);
      payoutAmountInUSD = formatAmount((calcValue - savingsAmountTwo) / 500);
      break;
    case "jidelwl@gmail.com":
      calcValue = totalPayoutAmount + 0.21 * salesAmount;
      savingsAmountThree = 0.1 * calcValue;
      payoutAmount = formatAmount(calcValue - savingsAmountThree);
      payoutAmountInUSD = formatAmount((calcValue - savingsAmountThree) / 500);
      break;
    case "emmanuelokereke321@gmail.com":
      calcValue = totalPayoutAmount + 0.21 * salesAmount;
      savingsAmountFour = 0.1 * calcValue;
      payoutAmount = formatAmount(calcValue - savingsAmountFour);
      payoutAmountInUSD = formatAmount((calcValue - savingsAmountFour) / 500);
      break;
  }

  //console.log(`Admin ${email}'s pay is ${payoutAmount}`);

  //Prepare company savings
  companySavings = formatAmount(
    savingsAmountOne + savingsAmountTwo + savingsAmountThree + savingsAmountFour
  );
  companySavingsInUSD = formatAmount(
    (savingsAmountOne +
      savingsAmountTwo +
      savingsAmountThree +
      savingsAmountFour) /
      500
  );

  //Update view
  $(el)
    .parent()
    .find(".amount-row")
    .empty()
    .html("&#x20A6 " + payoutAmount + " / " + "$" + payoutAmountInUSD);
});

//Show or hide company savings
if (totalAmountGenerated > 0) {
  const savingsList = ` <tr class='rows' style='margin-bottom: 50px !important;'>
                        <td class='fullname'>Company Savings</td>
                        <td class='email'>admin@chromstack.com</td>
                        <td class='recipient'>RCP_nmu7fer8ykmsj3t</td>
                        <td class='country'>Nigeria</td>
                        <td class='role'>Company</td>
                        <td class='amount-row'>&#x20A6 ${companySavings} / $${companySavingsInUSD}</td>
                        <td class='currency'>NGN</td>
                        <td class='account'>5601160233</td>
                        <td class='bank'>Fidelity Bank</td>
                        <td class='code'>070</td>
                        <td class='status'>
                            <button class='btn btn-danger btn-sm'>Pending</button>
                      </td>
                      <td class='action'>
                        <button class='btn btn-info btn-sm'>Pay</button>
                      </td>
                    </tr>
                  `;
  $("tbody").append(savingsList);
}

//Get currency
updateCurrency();

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
const reason = `Weekly payment from Chromstack`;
const payoutDay = currentDay + ", " + week;
const paymentArray = [];

/*
  SINGLE TRANSFER
*/

$(".action button").each(function (index, el) {
  $(el).on("click", function () {
    $(el).text("Processing...");
    const name = $(el).parent().parent().find(".fullname").text();
    const email = $(el).parent().parent().find(".email").text();
    const recipient = $(el).parent().parent().find(".recipient").text();
    const account = $(el).parent().parent().find(".account").text();
    const bank = $(el).parent().parent().find(".bank").text();
    const code = $(el).parent().parent().find(".code").text();
    const currency = $(el).parent().parent().find(".currency").text();
    const amount = $(el)
      .parent()
      .parent()
      .find(".amount-row")
      .text()
      .split(" ");
    const currencyAmount = amount[1].split(",").join("");
    const parsedAmount = parseFloat(currencyAmount);
    if (isNaN(parsedAmount)) {
      displayInfo(`Invalid amount ${parsedAmount}`);
      return;
    }
    const payoutAmount = parsedAmount * 100;
    const transferAmount = parseFloat(payoutAmount.toFixed(2));
    let buttonText = "";

    if (currentDay !== "Saturday") {
      displayInfo("Payout is only done on Saturdays!");
      return;
    } else {
      //Filter beneficiaries
      if (recipient !== "" && recipient !== "null") {
        //Send transfer data to server
        $.ajax({
          type: "POST",
          url: "../server/make-transfer.php",
          data: {
            name: name,
            email: email,
            account: account,
            bank: bank,
            code: code,
            recipient: recipient,
            amount: transferAmount,
            currency: currency,
            reason: reason,
          },
          dataType: "json",
          success: function (response) {
            for (var key in response) {
              if (Object.hasOwnProperty.call(response, key)) {
                const content = response[key];
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
                } else if (
                  content === "Transfer failed" ||
                  content === "Transfer was reversed"
                ) {
                  //Decide what to show button
                  switch (content) {
                    case "Transfer failed":
                      buttonText = "Failed";
                      break;
                    case "Transfer was reversed":
                      buttonText = "Reversed";
                      break;
                  }
                  //Update UI
                  $("tbody tr").each(function (index, el) {
                    if ($(el).find(".email").text() === email) {
                      $(el).find(".status button").text(buttonText);
                      $(el).find(".action button").text("Retry");
                    }
                  });
                  displayInfo(content);
                } else {
                  displayInfo(content);
                }
              }
            }
          },
          error: function (e) {
            displayInfo(e.responseText);
          },
        });
      } else {
        displayInfo("Recipient code not found");
        return;
      }
    }
  });
});

/*
  BULK TRANSFER
*/

$("#pay-all").on("click", function () {
  if (currentDay !== "Saturday") {
    displayInfo("Payout is only done on Saturdays!");
    return;
  } else if (rowCount === 0) {
    displayInfo("No payout detail available");
    return;
  } else {
    //Loop through table row data
    $("tbody tr").each(function (index, el) {
      const name = $(el).find(".fullname").text();
      const email = $(el).find(".email").text();
      const recipient = $(el).find(".recipient").text();
      const account = $(el).find(".account").text();
      const code = $(el).find(".code").text();
      const currency = $(el).find(".currency").text();
      const amount = $(el).find(".amount-row").text().split(" ");
      const currencyAmount = amount[1].split(",").join("");
      const parsedAmount = parseFloat(currencyAmount);
      if (isNaN(parsedAmount)) {
        displayInfo(`Invalid amount ${parsedAmount}`);
        return;
      }
      const payoutAmount = parsedAmount * 100;
      const transferAmount = parseFloat(payoutAmount.toFixed(2));
      const reason = `Weekly payment from Chromstack`;

      //Filter beneficiaries
      if (recipient !== "" && recipient !== "null") {
        if (email !== "hassan@gmail.com" && email !== "jerry@gmail.com") {
          //Prepare payment object
          const payoutObject = {
            fullname: name,
            email: email,
            account: account,
            bank: code,
            code: recipient,
            amount: transferAmount,
            currency: currency,
            reason: reason,
            payday: payoutDay,
          };
          //Queue up payment objects in array
          paymentArray.push(payoutObject);
        }
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
        //Redirect to status page
        window.location = `../views/transaction-history.php`;
      },
      error: function (e) {
        displayInfo(e.responseText);
      },
    });
  }
});

//Get count
const counter = Number($("tbody").children("tr").length);
$(".col-sm-6 h1")
  .empty()
  .html(
    "<b>Transaction " +
      "(" +
      counter +
      " People, " +
      "(" +
      "&#8358;" +
      totalAmountGenerated +
      ")" +
      ")</b>"
  );

updateStatus(counter);

//Back up data
$("#backup").on("click", function () {
  if (rowCount > 0) {
    $("#backup").text("Processing...").attr("disabled", true);
    $.ajax({
      type: "POST",
      url: "../server/back-up.php",
      dataType: "json",
      success: function (response) {
        for (const key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response[key];
            if (content === "Back up was successful") {
              displaySuccess(content);
              $("#backup").text("Updating...").attr("disabled", true);
              //Loop through the table rows
              $("tbody tr").each(function (index, el) {
                //if ($(el).find(".status button").text() === "Completed") {
                setTimeout(() => {
                  $(el).remove();
                  if (rowCount === 0) {
                    $("#backup").html(
                      "<i class='fas fa-check' style='padding-right: 5px;'></i>  Done"
                    );
                    //Update text
                    setTimeout(() => {
                      $("#backup").html("Back Up").attr("disabled", true);
                    }, 2000);
                    //Hide button
                    setTimeout(() => {
                      $("#backup").attr("disabled", false).hide();
                      $("#pay-all").hide();
                      $("tbody").empty();
                    }, 3000);
                  }
                }, 2000);
                //}
              });
            } else {
              displayInfo(content);
            }
          }
        }
      },
      error: function (e) {
        displayInfo(e.responseText);
      },
    });
  } else {
    displayInfo("No data available for back up");
  }
});

//Search function
$("#page-search").on("keyup", function () {
  const searchValue = $(this).val();
  if (searchValue !== "") {
    $(".rows, .admin-rows").each(function (index, el) {
      if (
        $(el).find(".fullname").text().toLowerCase().includes(searchValue) ||
        $(el).find(".email").text().toLowerCase().includes(searchValue) ||
        $(el).find(".fullname").text().toLowerCase().includes(searchValue) ||
        $(el).find(".email").text().toLowerCase().includes(searchValue) ||
        $(el).find(".role").text().toLowerCase().includes(searchValue)
      ) {
        $(el).css({ display: "table-row" });
      } else {
        $(el).css({ display: "none" });
      }
    });
  } else {
    $(".rows, .admin-rows").each(function (index, el) {
      if ($(el).css("display") === "none") {
        $(el).css({ display: "table-row" });
      }
    });
  }
});
