import { displayInfo, displaySuccess } from './export.js';;

$(document).ready(function () {

  const userID = $("#userID").val();
  const country = $('#country').val();
  let currency = "NGN";

  // Get currency
  $.getJSON('../../countries-details.json', function (data) {
    for (const key in data) {
      if (Object.hasOwnProperty.call(data, key)) {
        const content = data[key];
        const countryName = content.country_name;
        if (countryName === country) {
          currency = content.currency_code;
          $("#currency").val(content.currency_code);
        }
      }
    }
    if (!currency) {
      displayInfo("Currency not found for the selected country.");
    }
  });

  function recordPayment(request) {
    $.ajax({
      type: "POST",
      url: "../server/fund.php",
      data: request,
      dataType: "json",
      processData: false,
      contentType: false,
      cache: false,
      success: function (response) {
        const content = response.Info;
        if (content === "Payment successful") {
          displaySuccess(response.details.message);
          setTimeout(() => {
            window.location.reload();
          }, 1500);
        } else {
          displayInfo(response.details.error);
        }
      },
      error: function (e) {
        displayInfo("Error connecting to server");
        console.log(e.responseText);
      },
    });
  }

  function verifyPayment(reference) {
    $.ajax({
      type: "POST",
      url: "../server/verify.php",
      data: { reference: reference },
      dataType: "json",
      processData: false,
      contentType: false,
      cache: false,
      success: function (response) {
        const content = response.Info;
        if (content === "Payment verified") {
          displaySuccess(response.details.message);
          setTimeout(() => {
            window.location.reload();
          }, 2000);
        } else {
          displayInfo(response.details.error);
        }
      },
      error: function (e) {
        displayInfo("Error connecting to server");
        console.log(e.responseText);
      },
    });
  }

  function validateAmount(amount) {
    if (amount === '') {
      displayInfo('Enter an amount');
      return false;
    } else if (amount < 1000) {
      displayInfo("Enter atleast 1000 to proceed");
      return false;
    }
    return true;
  }

  $("#amount").on("keyup", function () {
    const amount = $(this).val();
    $('#fund').attr('disabled', !validateAmount(amount));
  });

  $("#fund").on("click", function () {
    const email = $("#email").val();
    const amount = $("#amount").val();
    const amountToPay = Number(amount) * 100;

    if (!validateAmount(amount)) {
      return;
    }

    const popup = new PaystackPop();
    popup.checkout({
      key: "pk_key",
      email: email,
      amount: amountToPay,
      onSuccess: (transaction) => {
        const status = transaction.status;
        const reference = transaction.reference;
        if (status && status === "success") {
          const request = new FormData();
          request.append("id", userID);
          request.append("email", email);
          request.append("amount", amountToPay);
          request.append("currency", currency);
          request.append("reference", reference);
          recordPayment(request);
          //verifyPayment(reference); // Added payment verification
        } else {
          displayInfo("Payment failed");
        }
      },
      onLoad: (response) => {
        console.log("onLoad: ", response);
      },
      onCancel: () => {
        displayInfo("Payment cancelled");
      },
      onError: (error) => {
        displayInfo("Error: ", error.message);
      },
    });
  });
});

document.addEventListener('contextmenu', function(event) {
   event.preventDefault(); // Prevent the context menu from opening
});