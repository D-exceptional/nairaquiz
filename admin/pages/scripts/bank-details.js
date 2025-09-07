import { displaySuccess, displayInfo } from "../scripts/export.js";

let bankCode = "";

function verifyAccount(account, code) {
    $("#accountNameDiv").show();
    $("#accountName").val("Verifying account number...");
    $.ajax({
      type: "GET",
      url: "../server/verify-account-number.php",
      data: { account: account, code: code },
      dataType: "json",
      success: function (response) {
        for (const key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response.Info;
            if (content === "Account number verified successfully") {
              displaySuccess(content);
              $("#accountName").val(response.details.name);
              $("#add-details").attr("disabled", false);
            }else if (content === "Account number verification failed") {
              $("#accountName").val("Account name not found");
              displayInfo(response.details.error);
            } 
            else if (content === "Error connecting to Paystack gateway") {
              $("#accountName").val("Account name not found");
              displayInfo('Error connecting to Paystack API');
            } 
            else {
              $("#accountNameDiv").hide();
              $("#accountName").val("");
              displayInfo(response.details.error);
            }
          }
        }
      },
      error: function (e) {
        //displayInfo(e.responseText);
        displayInfo("Error resolving account name");
      },
    });
}

$("#accountNumber").on("keyup", function () {
  const account = $(this).val();
  const bank = $("#bankName").val();
  if (account === "") {
    displayInfo("Enter a valid account number");
    $("#add-details").attr("disabled", true);
  } else if (account.length === 10) {
    $(this).blur();
    $("#add-details").attr("disabled", false);
    //Get bank code
    $.each(jsonData, function (index, obj) {
      if (obj.name === bank) {
        bankCode = obj.code;
        setTimeout(() => {
          //verifyAccount(account, bankCode);
          $('body').css({ opacity: 1 });
        }, 100);
      }
    });
  }
});

$("#bankName").on("change", function () {
  $("#add-details").attr("disabled", true);
  const name = $(this).val();
  const account = $("#accountNumber").val();
  if (account === "null" || account === 0) {
    displayInfo("Enter a valid account number");
    $("#add-details").attr("disabled", true);
  } else if (account === "") {
    displayInfo("Account number field is empty");
    $("#add-details").attr("disabled", true);
  } else if (account.length < 10) {
    displayInfo("Account number must be ten digits");
    $("#add-details").attr("disabled", true);
  } else if (account.length > 10) {
    displayInfo("Account number must not exceed ten digits");
    $("#add-details").attr("disabled", true);
  } else {
    //Get bank code
    $("#add-details").attr("disabled", false);
    $.each(jsonData, function (index, obj) {
      if (obj.name === name) {
        bankCode = obj.code;
        setTimeout(() => {
          //verifyAccount(account, bankCode);
          $('body').css({ opacity: 1 });
        }, 100);
      }
    });
  }
});
$("#bank-form").on("submit", function (event) {
  event.preventDefault();
  const account = $("#accountNumber").val();
  const bank = $("#bankName").val();
  const code = bankCode;
  const currency = $("#Currency").val();
  //const name = $("#accountName").val();
  const name = $("#accountHolder").val();
  //const recipient = $("#uniqueCode").val();
  const recipient = 'Not available';
 
  if (account === "") {
    displayInfo("Enter account number");
    return;
  } else if (account === 0) {
    displayInfo("Enter a valid ten-digit account number");
    return;
  } /*else if (name === "") {
    displayInfo("Verify your account number to proceed");
    return;
  } else if (name === "Account name not found") {
    displayInfo("Enter a valid account number");
    return;
  }*/ else {
    $.ajax({
      type: "POST",
      url: "../server/bank-details.php",
      data: { 
        account: account, 
        bank: bank,
        code: code,
        currency: currency,
        recipient: recipient
      },
      dataType: "json",
      success: function (response) {
        for (const key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response.Info;
            if (["Details added successfully", "Details updated successfully"].includes(content)) {
              displaySuccess(content);
              setTimeout(function () {
                window.location.reload();
              }, 1500);
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
  }
});
