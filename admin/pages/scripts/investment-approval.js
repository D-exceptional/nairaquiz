import { displayInfo, displaySuccess } from "../scripts/export.js";

/**
 * Handle Approval or Deletion for Each Button
 */
$(".action button").on("click", function () {
  const $btn = $(this);
  const $row = $btn.closest("tr");

  const data = {
    id: $row.attr("id"),
    name: $row.find(".name").text(),
    plan: $row.find(".plan").text(),
    email: $row.find(".email").text(),
    reference: $row.find(".ref").text(),
    currency: $row.find(".currency").text(),
    amountText: $row.find(".amount").text(),
  };

  const amountNumeric = data.amountText.replace(/[^\d.-]/g, "").split(".")[0];

  if ($btn.text() === "Approve") {
    if (!confirm(`Are you sure to approve ${data.name}'s payment?`)) return;

    if (isNaN(amountNumeric)) {
      displayInfo(`Invalid amount ${amountNumeric}`);
      return;
    }

    $btn.text("Processing...");

    if (data.id && data.name !== "null" && data.email && data.reference) {
      $.ajax({
        type: "POST",
        url: "../server/investment-approval.php",
        data: {
          ...data,
          amount: amountNumeric,
        },
        dataType: "json",
        success: (response) => handleApprovalResponse(response, data.id),
        error: (e) => {
          displayInfo(e.responseText);
          console.error(e.responseText);
        },
      });
    } else {
      displayInfo("Missing fields");
    }
  } else {
    if (!confirm(`Are you sure to delete ${data.name}'s payment?`)) return;

    $.ajax({
      type: "POST",
      url: "../server/delete-payment.php",
      data: {
        reference: data.reference,
        payment: "Investment",
      },
      dataType: "json",
      success: (response) => handleDeletionResponse(response, data.id),
      error: (e) => {
        displayInfo(e.responseText);
        console.error(e.responseText);
      },
    });
  }
});

/**
 * Handle Approval Success Response
 */
function handleApprovalResponse(response, id) {
  const message = response.Info;
  if (message === "Payment approved successfully") {
    const $row = $(`.rows#${id}`);
    $row
      .find(".status button")
      .removeClass("btn-danger")
      .addClass("btn-success")
      .text("Completed");

    $row
      .find(".action .btn.btn-info")
      .removeClass("btn-info")
      .addClass("btn-success")
      .html(
        "<i class='fas fa-check' style='padding-right: 5px;'></i>  Approved"
      )
      .attr("disabled", true)
      .css({ background: "green", width: "120px" });

    $row.find(".action .btn.btn-danger").hide();

    setTimeout(() => { 
        $row.remove()
        $(".col-sm-6 h1 b").html(`Investment Approvals (${response.data.total})`);
    }, 1500);
    displaySuccess(message);
  } else {
    displayInfo(message);
  }
}

/**
 * Handle Deletion Success Response
 */
function handleDeletionResponse(response, id) {
  const message = response.Info;
  if (message === "Payment deleted successfully") {
    const $row = $(`.rows#${id}`);
    $row.remove();
    setTimeout(() => window.location.reload(), 1500);
    displaySuccess(message);
  } else {
    displayInfo(message);
  }
}

/**
 * Receipt Image Viewer
 */
$(".receipt img").on("click", function () {
  const source = $(this).attr("src");
  $("#details-overlay")
    .css({ display: "flex" })
    .find("img")
    .attr("src", source);
});

$("#close-view").on("click", function () {
  $("#details-overlay").hide().find("img").attr("src", "");
});

/**
 * Search Filter for Table Rows
 */
$("#page-search").on("keyup", function () {
  const search = $(this).val().toLowerCase();
  $(".rows").each(function () {
    const match = $(this).text().toLowerCase().includes(search);
    $(this).toggle(match);
  });
});
