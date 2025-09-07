import { displaySuccess, displayInfo } from "./export.js";

const country = $("#country").val();
const currency = $("#currency").val();
let ratesArray = [];

function getExchangeRates() {
  const base = "NGN";
  const endpoint = "https://api.exchangerate-api.com/v4/latest";
  $.ajax({
    type: "GET",
    url: `${endpoint}/${base}`,
    dataType: "json",
    success: function (response) {
     const rates = response.rates;
     for (const [currency, rate] of Object.entries(rates)) {
       ratesArray.push({ currency, rate });
     }
    },
    error: function () {
      displayInfo("Error connecting to server");
    },
  });
}

getExchangeRates();

function formatCurrency(amount, currency) {
  let charge = 0;
   $.each(ratesArray, function (index, rate) {
     if (rate.currency === currency) {
       charge = amount * rate.rate;
     }
   });
  return Intl.NumberFormat("en-US", {
    style: "currency",
    currency,
  }).format(charge);
}

//Preview selected image
function previewFile(input) {
  if (input.files && input.files[0]) {
    let extension = input.files[0].name.split('.').pop().toLowerCase();
    //let sizeCal = input.files[0].size / 1024 / 1024;
    switch (extension) { 
      case 'jpg':
      case 'jpeg':
      case 'png':
        const reader = new FileReader();
        reader.onload = function(e) {
             $("#details-overlay").css({ display: "flex" });
            $('#details-overlay img').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
        displaySuccess(
          "Selected file format supported"
        );
        $("#pay").attr("disabled", false);
      break;
      case 'mp3':
      case 'mp4':
      case 'pdf':
      case 'zip':
      case 'jfif':
        displayInfo('Selected file format not supported. Choose an image with either .jpg, .jpeg or .png extension !');
        $("#pay").attr("disabled", true);
      break;
    }
  }
}

$('#file').on('change', function () {
    previewFile(this);
});

$("#close-view").on("click", function(){
    $("#details-overlay").css({ display: "none" });
    $("#details-overlay img").attr("src", "");
});

const supportedCountries = [
  "Benin",
  "Botswana",
  "Burkina Faso",
  "Burundi",
  "Cameroon",
  "Chad",
  "China", // Asia
  "Congo",
  "Congo (DRC)",
  "C么te d'Ivoire",
  "Ethiopia",
  "Ghana",
  "Guinea",
  "Guinea-Bissau",
  "India", // Asia
  "Ivory Coast",
  "Kenya",
  "Liberia",
  "Madagascar",
  "Malawi",
  "Mali",
  "Mozambique",
  "Niger",
  "Nigeria",
  "Philippines", // Asia
  "Rwanada",
  "Senegal",
  "Seychelles",
  "Sierra Leone",
  "Somalia",
  "South Africa",
  "South Sudan",
  "Swaziland",
  "Tanzania",
  "Togo",
  "Turkey", // Asia
  "Uganda",
  "Zambia",
  "Zimbabwe",
];

function makePayment() {
  const amount = $("#amount").val();
  const nairaAmount = formatCurrency(amount, "NGN");
  const internationalAmount = formatCurrency(amount + 500, currency);
  const receipt = $("#file").val();

  //Start processing
  if (supportedCountries.includes(country)) {
    if(amount === ""){
      displayInfo("Enter an amount to proceed!");
      $('#pay').prop('disabled', false);
      return;
    }
    else if(amount < 1000){
      displayInfo("Enter an amount of 1000 or above to proceed!");
      $('#pay').prop('disabled', false);
      return;
    }
    else if(receipt === ""){
      displayInfo("Attach payment receipt!");
      $('#pay').prop('disabled', false);
      return;
    }
    else {
      $('#pay').prop('disabled', true).text("Submitting payment details...");
      //Prepare params
      const request = new FormData();
      request.append("amount", amount);
      request.append("currency", currency);
      request.append("receipt", document.getElementById("file").files[0]);
      //Send to server
      $.ajax({
        type: "POST",
        url: "../server/pay.php",
        data: request,
        dataType: "json",
        processData: false,
        contentType: false,
        cache: false,
        success: function (response) {
            $("#payment-modal-overlay").css({ display: "none" });
          //for (const key in response) {
            //if (Object.hasOwnProperty.call(response, key)) {
              const content = response.Info;
              if (content === "Payment request received successfully") {
                if (country === "Nigeria") {
                  displaySuccess(
                    `
                      Your payment request of ${nairaAmount} was successfully initiated. 
                      Expect a response from us via email in the next one working hour.
                      If you have any complaint, send a mail to support@nairaquiz.com.
                    `
                  );
                } else {
                  displaySuccess(
                    `
                      Your payment request of ${internationalAmount} was successfully initiated. 
                      Expect a response from us via email in the next one working hour.
                      If you have any complaint, send a mail to support@nairaquiz.com.
                    `
                  );
                }
                setTimeout(function () {
                  window.location.reload();
                }, 2000);
              } else {
                displayInfo(content);
              }
            //}
          //}
        },
        error: function (e) {
          displayInfo("Error connecting to server");
          $('#pay').prop('disabled', false).text("Fund Wallet");
        },
      });
    }
  } else {
    displayInfo("Country not supported");
    return;
  }
}

//Buy course
$("#topup").on("click", function () {
  if (supportedCountries.includes(country)) {
    const amount = $("#amount").val();
    if (amount === "") {
      displayInfo("Enter amount to proceed!");
      return;
    }
    else if(amount <  1000){
      displayInfo("Enter an amount of 1000 or above to proceed!");
      return;
    }
    else {
      $("#payment-modal-overlay").css({ display: "flex" });
      const nairaAmount = formatCurrency(amount, "NGN");
      const internationalAmount = formatCurrency(amount, currency);
      //Show amount based on country
      if (country === "Nigeria") {
        $("#bankLogo").attr("src", "../../assets/img/moniepoint.jpg");
        $("#pay-text")
          .css({ color: "#ffffff" })
          .html(
            `Pay <b>${nairaAmount} </b> to this Moniepoint Bank Account: 
              <br> 
              <br> 
              <center><b>6935562588 | SKYLER DYNAMIC ENTERPRISE </b></center> 
              <br> 
              After making payment, attach your payment receipt and click on the <b>Submit</b> button afterwards to proceed
            `
          );
      } 
      else {
        $("#bankLogo").attr("src", "../../assets/img/usdt.png");
        $("#pay-text")
          .css({ color: "#ffffff" })
          .html(
            `Pay a minimum of <b>1 USDT </b> to this wallet address: 
              <br> 
              <br> 
              <center>
                Wallet Address: <b id='wallet-address-b' style='cursor: pointer;'>0xdc7bf804566bb1dde4955cd9678b8bcc455fe247</b>
                <br>
                Network: <b>Arbitrum one</b>
                <br>
                Currency: <b>USDT</b>
                <br>
              </center>
              <br> 
              After making payment, attach your payment receipt and click on the <b>Submit</b> button afterwards to proceed
            `
          );
      }
    }
  } else {
    displayInfo("Country not supported");
    return;
  }
});

$("#pay").on("click", function () {
  $(this).prop('disabled', true); // Prevent mulitiple submissions
  //$("#payment-modal-overlay").css({ display: "none" });
  makePayment();
});

$("#payment-overlay-close").on("click", function () {
  $("#payment-modal-overlay").css({ display: "none" });
});

$('#wallet-address, wallet-address-b').on("click", function () {
    // Get the button text
    const buttonText = $(this).text();
    const textInput = document.createElement("input");
    textInput.setAttribute("type", "text");
    textInput.setAttribute("value", buttonText);
    textInput.setAttribute("hidden", true);
    textInput.select();
    textInput.setSelectionRange(0, 99999);
    const shareLink = textInput.value;
    navigator.clipboard.writeText(shareLink);
    displaySuccess("Wallet address copied");
  });

 document.addEventListener('contextmenu', function(event) {
    event.preventDefault(); // Prevent the context menu from opening
 });