import { displaySuccess, displayInfo } from "./export.js";

$(document).ready(function () {
  const queryString = new URL(window.location);
  const urlParams = new URLSearchParams(queryString.search);
  const packageName = urlParams.get("package");
  const id = urlParams.get("id");

  // Ensure that package is present in URL
  if (!packageName) {
    displayInfo("Package not found. Redirecting...");
    setTimeout(() => {
      window.location.href = "./invest";
    }, 2000);
    return;
  }

  // Ensure that package is present in URL
  if (!id) {
    displayInfo("ID not found. Redirecting...");
    setTimeout(() => {
      window.location.href = "./";
    }, 2000);
    return;
  }

  let currency = "NGN";
  let dialingCode = "";
  let amount = 0;
  let roi = "";
  let ratesArray = [];
  let countriesArray = [];

  const supportedCountries = [
    "Benin",
    "Botswana",
    "Burkina Faso",
    "Burundi",
    "Cameroon",
    "Chad",
    "China",
    "Congo",
    "Congo (DRC)",
    "C么te d'Ivoire",
    "Ethiopia",
    "Ghana",
    "Guinea",
    "Guinea-Bissau",
    "India",
    "Ivory Coast",
    "Kenya",
    "Liberia",
    "Madagascar",
    "Malawi",
    "Mali",
    "Mozambique",
    "Niger",
    "Nigeria",
    "Philippines",
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
    "Turkey",
    "Uganda",
    "Zambia",
    "Zimbabwe",
  ];

  // Toggle password visibility
  $("#psw-div i").on("click", togglePasswordVisibility);

  // Get details
  getPackageDetails();

  // Load countries and exchange rates
  loadCountries();
  getExchangeRates();

  // Event Handlers
  $("#country").change(updateDetails);
  $("#fullname, #email, #contact, #password").on("keyup blur", validateInputs);
  $("#proceed").on("click", handleProceed);
  $("#show-continue").on("click", showContinueSection);
  $("#receipt").on("change", toggleSubmitButton);
  $("#hide-continue").on("click", handleFormSubmit);
  $("#payment-overlay-close").on("click", closePaymentOverlay);
  $("#makePayment").on("click", handleMakePayment);

  function getPackageDetails() {
    $.ajax({
      type: "GET",
      url: `./assets/server/details.php?package=${packageName}`,
      dataType: "json",
      success: function (response) {
        const message = response.Info;
        if (message === "Details fetched successfully") {
          //displaySuccess("Package details fetched successfully");
          amount = response.data.amount;
          roi = response.data.roi;
        } else {
          displayInfo(message);
        }
      },
      error: function () {
        displayInfo("Error connecting to server");
      },
    });
  }

  function togglePasswordVisibility() {
    const $icon = $(this);
    const $passwordInput = $("#password");
    
    const isHidden = $passwordInput.attr('type') === 'password';
    $passwordInput.attr('type', isHidden ? 'text' : 'password');
    
    $icon.toggleClass('fa-eye fa-eye-slash');
  }

  function loadCountries() {
    $.getJSON("./countries-details.json", function (data) {
      for (const key in data) {
        if (Object.hasOwnProperty.call(data, key)) {
          const { country_name, currency_code, phone_code } = data[key];
          countriesArray.push({
            name: country_name,
            currency: currency_code,
            code: `+${phone_code}`,
          });
          $("#country").append(
            `<option value="${country_name}">${country_name}</option>`
          );
        }
      }
    });
  }

  function getExchangeRates() {
    $.ajax({
      type: "GET",
      url: "https://api.exchangerate-api.com/v4/latest/NGN",
      dataType: "json",
      success: function (response) {
        ratesArray = Object.entries(response.rates).map(([currency, rate]) => ({
          currency,
          rate,
        }));
      },
      error: function () {
        displayInfo("Error connecting to server");
      },
    });
  }

  function updateDetails() {
    const selectedCountry = $("#country").val();
    const country = countriesArray.find((c) => c.name === selectedCountry);

    if (country) {
      currency = country.currency;
      dialingCode = country.code;
    } else {
      displayInfo("Currency not found for the selected country.");
    }
  }

  function validateInputs() {
    const isValid = $(this).val() !== "";
    $(this).css({ border: isValid ? "none" : "2px solid red" });
    $("form button").prop("disabled", !isValid);
  }

  function formatCurrency(amount, currency) {
    const rate = ratesArray.find((rate) => rate.currency === currency)?.rate;
    return rate
      ? Intl.NumberFormat("en-US", { style: "currency", currency }).format(
          amount * rate
        )
      : "";
  }

  function register(receipt) {
    const request = new FormData();
    const formData = {
      name: $("#fullname").val(),
      email: $("#email").val(),
      contact: $("#contact").val(),
      country: $("#country").val(),
      password: $("#password").val(),
      plan: packageName,
      amount: amount,
      roi: roi,
      receipt,
      id,
      currency,
      code: dialingCode,
    };

    if (Object.values(formData).some((value) => value === "")) {
      displayInfo("Some fields are empty");
      $("#hide-continue").prop("disabled", false).text("Submit");
      return;
    }

    if (supportedCountries.includes(formData.country)) {
      Object.entries(formData).forEach(([key, value]) =>
        request.append(key, value)
      );

      // Send to main registration
      $.ajax({
        type: "POST",
        url: "./assets/server/enroll.php",
        data: request,
        dataType: "json",
        processData: false,
        contentType: false,
        cache: false,
        success: handleRegisterResponse,
        error: function () {
          displayInfo("Error connecting to server");
        },
      });
    } else {
      displayInfo("Country not supported");
    }
  }

  function handleRegisterResponse(response) {
    const status = response.status;
    if (message === "success") {
      displaySuccess(response.message);
      setTimeout(() => window.location.reload(), 1500);
    } else {
      displayInfo(message);
    }
  }

  function handleProceed() {
    //const amount = amount;
    const nairaAmount = formatCurrency(amount, "NGN");
    const internationalAmount = formatCurrency(
      parseFloat(amount) + 500,
      currency
    );

    if (supportedCountries.includes($("#country").val())) {
      if (
        Object.values({
          name: $("#fullname").val(),
          email: $("#email").val(),
          contact: $("#contact").val(),
          country: $("#country").val(),
          password: $("#password").val(),
          amount,
        }).some((val) => val === "")
      ) {
        displayInfo("Some fields are empty");
        return;
      }

      const isNigeria = $("#country").val() === "Nigeria";
      const paymentText = isNigeria
        ? `Pay <b>${nairaAmount}</b> to this Moniepoint Bank Account: <center><b>6935562588 | SKYLER DYNAMIC ENTERPRISE</b></center><br>After payment, click on the <b>I have paid</b> button to get started`
        //: `Pay <b>${internationalAmount}</b> to this Mobile Money (MoMo) Bank Account: <center><b>7039408406 | Divine Smart </b></center><br>After payment, click on the <b>I have paid</b> button to get started`;
        : `Pay a minimum of <b>1 USDT </b> to this wallet address: <br><br> <center> Wallet Address: <b id='wallet-address-b' style='cursor: pointer;'>0xdc7bf804566bb1dde4955cd9678b8bcc455fe247</b><br> Network: <b>Arbitrum one</b><br> Currency: <b>USDT</b><br></center><br> After making payment, attach your payment receipt and click on the <b>I have paid</b> button to get started`;

      $("#bankLogo").attr(
        "src",
        isNigeria ? "assets/img/moniepoint.jpg" : "assets/img/usdt.png"
      );
      $("#pay-text").html(paymentText).css({ color: "#ffffff" });
      $("#payment-modal-overlay").css({ display: "flex" });
      $("#paymentDetails").css({ visibility: "visible" });
      $("#show-continue").show();
      $("#hide-continue").hide();
      $("#ref-div").hide();
    } else {
      displayInfo("Country not supported");
    }
  }

  function showContinueSection() {
    $("#ref-div, #hide-continue").show();
    $("#pay-text")
      .html(
        `Attach the payment receipt from your payment app or channel and click on the <b>Submit</b> button afterwards to register`
      )
      .css({ color: "#ffffff" });
    $("#show-continue").hide();
    $("#hide-continue").show();
  }

  function toggleSubmitButton() {
    const input = this;

    if (input.files && input.files[0]) {
      let file = input.files[0];
      let extension = file.name.split(".").pop().toLowerCase();

      if (["jpg", "jpeg", "png", "pdf"].includes(extension)) {
        $(input).css({ border: "none" });
        $("#hide-continue").prop("disabled", false);
        displaySuccess("Selected file is supported");
      } else {
        displayInfo("Selected file not supported!");
        $(input).css({ border: "2px solid red" });
        $("#hide-continue").prop("disabled", true);
      }
    } else {
      displayInfo("Selected file invalid!");
      $(input).css({ border: "2px solid red" });
      $("#hide-continue").prop("disabled", true);
    }
  }

  function handleFormSubmit() {
    const receipt = $("#receipt")[0].files[0];

    if (!receipt) {
      displayInfo("Please select a valid file!");
      return;
    }

    const extension = receipt.name.split(".").pop().toLowerCase();

    if (["jpg", "jpeg", "png", "pdf"].includes(extension)) {
      // Prevent multiple submission
      $("#hide-continue").prop("disabled", true).text("Processing...");
      // Send payment details to server
      register(receipt);
    } else {
      displayInfo("Selected file not supported!");
      return;
    }
  }

  function closePaymentOverlay() {
    $("#payment-modal-overlay").css({ display: "none" });
  }

  function handleMakePayment() {
    $("#page-overlay").css({ height: "0%", padding: "0%" });
  }
});
