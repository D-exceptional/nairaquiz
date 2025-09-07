import { displaySuccess, displayInfo } from './export.js';

$(function () {
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
  

  //Sign up
  $("#signup-form").on("submit", function (event) {
    event.preventDefault();
    const fullname = $("#fullname").val();
    const email = $("#email").val();
    const contact = $("#contact").val();
    const country = $("#country").val();
    //Payload object
    const payload = {
      name: fullname,
      email: email,
      contact: contact,
      country: country
    };
    //Prevent empty values
    if (
      !payload.name ||
      !payload.email ||
      !payload.contact ||
      !payload.country
    ) {
      displayInfo("Empty input field(s)");
      return;
    } else {
      // Check name 
      if (/\d/.test(fullname)) {
        displayInfo("Numbers not allowed in name field");
        return false;
      }
        
      const form = document.getElementById("signup-form");
      const data = new FormData(form);
      //Send to server
      $.ajax({
        type: "POST",
        url: "./assets/server/ambassador.php",
        data: data,
        dataType: "json",
        cache: false,
        processData: false,
        contentType: false,
        success: function (response) {
          const content = response.Info;
          if (content === "Application successful") {
            displaySuccess('Your application was successful. Kindly check your mailbox for further information.');
            $("#signup-form")[0].reset();
          } else {
            displayInfo(content);
          }
        },
        error: function (e) {
          displayInfo("Error connecting to server");
          //console.log(e.responseText);
        },
      });
    }
  });
});

