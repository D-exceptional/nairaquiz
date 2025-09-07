import { displaySuccess, displayInfo } from './export.js';

$(function () {
    
   //Get the URL
  const queryString = new URL(window.location);
  // We can then parse the query string’s parameters using URLSearchParams:
  const urlParams = new URLSearchParams(queryString.search);
  //Then we call any of its methods on the result.
  const ref = urlParams.get('ref');
  //Set
  $("#ref").val(ref);
  
  //Store country code here
  let countriesArray = [];
  let dialingCode = '+234';

  //Get country and dialing code details
  function loadCountries() {
    $.getJSON("./countries.json", function (data) {
      for (const key in data) {
        if (Object.hasOwnProperty.call(data, key)) {
          const content = data[key];
          //Prepare object
          const countryObject = {
            name: content.country_name,
            code: content.phone_code,
            currency: content.currency_code,
          };
          //Save in array
          countriesArray.push(countryObject);
          //Get country names
          const countryName = `<option value='${content.country_name}'>${content.country_name}</option>`;
          $("#country").append(countryName);
        }
      }
    });
    //Set info
    const countryName = `<option value=''>Select Your Country</option>`;
    $("#country").append(countryName);
  }

  loadCountries();

  function setDialingCode() {
    const selectedCountry = $("#country").val();
    //Get code associated with country
    $.each(countriesArray, function (index, country) {
      if (country.name === selectedCountry) {
        dialingCode = `+` + country.code;
        $("#code").val(dialingCode);
        $("#currency").val(country.currency);
      }
    });
  }

  //Track dialing code at select change event
  $("#country").change(function () {
    setDialingCode();
  });

  //Check All Inputs
  $(".form-control").each(function (index, el) {
    $(el)
      .on("keyup", function () {
        if ($(el).val() !== "") {
          $(el).css({ border: "none" });
          $("#Signup").attr("disabled", false);
        } else {
          $(el).css({ border: "2px solid red" });
          $("#Signup").attr("disabled", true);
        }
      })
      .on("blur", function () {
        if ($(el).val() !== "") {
          $(el).css({ border: "none" });
          $("#Signup").attr("disabled", false);
        } else {
          $(el).css({ border: "2px solid red" });
          $("#Signup").attr("disabled", true);
        }
      });
  });
  
  // Prevent numbers from being entered in name field
  $("#fullname")
      .on("keypress", function (e) { // When typing
        if (/\d/.test(e.key)) {
          e.preventDefault();
          displayInfo("Numbers not allowed in name field");
        }
      })
      .on("paste", function (e) { // When pasted
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        if (/\d/.test(paste)) {
          e.preventDefault();
          displayInfo("Numbers not allowed in name field");
        }
      })
      
    // Toggle password visibility
   $("#psw-div i").on("click", function(){
        const $icon = $(this);
        const $passwordInput = $("#password");
        const isHidden = $passwordInput.attr('type') === 'password';
            $passwordInput.attr('type', isHidden ? 'text' : 'password');
            $icon.toggleClass('fa-eye fa-eye-slash');
   });

  //Sign up
  $("#signup-form").on("submit", function (event) {
    event.preventDefault();
    const fullname = $("#fullname").val();
    const email = $("#email").val();
    const contact = $("#contact").val();
    const password = $("#password").val();
    //Payload object
    const payload = {
      name: fullname,
      email: email,
      contact: contact,
      password: password,
    };
    //Prevent empty values
    if (
      !payload.name ||
      !payload.email ||
      !payload.contact ||
      !payload.password
    ) {
      displayInfo("Empty input field(s)");
      return;
    } else {
       // Check name 
      if (/\d/.test(fullname)) {
        displayInfo("Numbers not allowed in name field");
        return false;
      } 
      
      $("#Signup").attr("disabled", true).text("Submitting details...");
      const form = document.getElementById("signup-form");
      const data = new FormData(form);
      //Send to server
      $.ajax({
        type: "POST",
        url: "./assets/server/signup.php",
        data: data,
        dataType: "json",
        cache: false,
        processData: false,
        contentType: false,
        success: function (response) {
          const content = response.Info;
          if (content === "You have registered successfully") {
            displaySuccess(content);
            $("#Signup").attr("disabled", true).text("Registration Successful");
            $("#signup-form")[0].reset();
            //Redirect to login page
            setTimeout(() => {
              window.location = "/login";
            }, 1500);
          } else {
            displayInfo(content);
          }
        },
        error: function (e) {
          displayInfo("Error connecting to server");
          console.log(e.responseText);
        },
      });
    }
  });
});

