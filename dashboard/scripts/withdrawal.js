// With custom settings, forcing a "US" locale to guarantee commas in output
function formatAmount(amount, decimalPrecision = 2) {
  return amount.toLocaleString(undefined, {
    minimumFractionDigits: decimalPrecision,
    maximumFractionDigits: decimalPrecision,
  });
}

function clearError() {
  setTimeout(() => {
    $("#info-span").text(``).css({ color: "gray" });
    $("#request-withdrawal").attr("disabled", false);
  }, 1000);
}

// Get details
const accountNumber = $("#accountNumber").text();
const bank = $("#bankName").text();

$("#withdrawalAmount").on("keyup", function () {
  const availableAmount = Number($("#availableAmount").val());
  const withdrawalAmount = Number($(this).val());
  const amount = formatAmount(availableAmount);

  if (withdrawalAmount === "") {
    $("#info-span").text("Enter an amount").css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
  } else if (withdrawalAmount === 0) {
    $("#info-span").text("Enter a non-zero amount").css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
  } else if (availableAmount === 0) {
    $("#info-span").text("Insufficient balance").css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
  } else if (withdrawalAmount > availableAmount) {
    $("#info-span")
      .html(`Enter an amount not greater than <b>${amount}</b>`)
      .css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
  } else {
    //Do something else
    $("#info-span").text(``);
  }
});

$("#withdraw-form").on("submit", function (event) {
  event.preventDefault();
  const availableAmount = Number($("#availableAmount").val());
  const withdrawalAmount = Number($("#withdrawalAmount").val());
  const amount = formatAmount(availableAmount);
  const withdraw = formatAmount(withdrawalAmount);

  if (bank == "" || bank == "null" || accountNumber == "" || accountNumber == "null") {
    $("#info-span")
      .text("Add your bank details via the settings page to be able to proceed")
      .css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
    return;
  } else if (withdrawalAmount === "") {
    $("#info-span").text("Enter an amount").css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
    return;
  } else if (withdrawalAmount === 0) {
    $("#info-span").text("Enter a non-zero amount").css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
    return;
  } else if (availableAmount === 0) {
    $("#info-span").text("Insufficient balance").css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
    return;
  } else if (withdrawalAmount > availableAmount) {
    $("#info-span")
      .html(`Enter an amount not greater than <b>${amount}</b>`)
      .css({ color: "red" });
    $("#request-withdrawal").attr("disabled", true);
    clearError();
    return;
  } else {
    //Do something else
    $("#info-span").text(``).css({ color: "gray" });
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
    const narration = `Withdrawal request of \u20a6${withdraw} for ${currentDay}, ${week}`;
    let balance = availableAmount - withdrawalAmount;
    if(balance <= 0){
        balance = 0.1;
    }
    //Send details to server
    $.ajax({
      type: "POST",
      url: "../server/withdrawal.php",
      data: {
        amount: withdrawalAmount,
        account: accountNumber,
        bank: bank,
        narration: narration,
        balance: balance,
      },
      dataType: "json",
      success: function (response) {
        for (const key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response.Info;
            if (content === "Withdrawal request placed successfully") {
              $("#info-span")
                .html(
                  `Your withdrawal request for <b>\u20a6${withdraw}</b> was successful. Check your mail for more details`
                )
                .css({ color: "green" });
              setTimeout(function () {
                //window.location.reload();
                $("#withdraw-overlay").css({ display: "none" });
                $("#withdrawalAmount").val("");
                clearError();
              }, 3000);
              window.location.reload();
            } else if (
              content === "Withdrawal request have been previously placed"
            ) {
              $("#info-span").html(`${content}`).css({ color: "red" });
              clearError();
            } else {
              $("#info-span")
                .html(
                  `Your withdrawal request for <b>\u20a6${withdraw}</b> failed. Kindly try again shortly`
                )
                .css({ color: "red" });
              clearError();
            }
          }
        }
      },
      error: function (e) {
        $("#info-span")
          .text(`Error ocurred while placing request`)
          .css({ color: "red" });
        clearError();
      },
    });
  }
});

$("#withdraw").on("click", function () {
  $("#withdraw-overlay").css({ display: "flex" });
  clearError();
});

$("#close-view").on("click", function () {
  $("#withdraw-overlay").css({ display: "none" });
  $("#withdrawalAmount").val("");
  clearError();
});

 document.addEventListener('contextmenu', function(event) {
    event.preventDefault(); // Prevent the context menu from opening
 });
