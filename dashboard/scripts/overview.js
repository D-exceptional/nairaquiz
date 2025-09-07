let overviewCard = "";

$(document).ready(function () {
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
  //Show withdraw button on the dashboard only on Thursday
  let button = ``;
  let withdraw = ``;

  function fetchDetails() {
    $.ajax({
      type: "GET",
      url: "server/overview.php",
      dataType: "json",
      success: function (response) {
        //for (const key in response) {
          //if (Object.hasOwnProperty.call(response, key)) {
            const content = response;

            if (content.fixedWalletAmount === 0) {
              button = 
              `<button type='button' class='btn btn-danger btn-sm' style='position: absolute;bottom: 0;right: 0;margin-bottom: 5px;height: 25px;font-size: 12px;z-index: 20000;' id='fund'>
                Fund
              </button>
              `;
            }

            if (content.fixedWithdrawalAmount >= 5000) {
              withdraw = 
              `<button type='button' class='btn btn-info btn-sm' style='position: absolute;bottom: 0;right: 0;margin-bottom: 5px;height: 25px;font-size: 12px;z-index: 20000;' id='withdraw'>
                Withdraw
              </button>
              `;
            }
            /*
            overviewCard = `
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                    <div class="inner" style='display: flex;flex-direction: row;'>
                      <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                        <i class="fas fa-university"></i>
                      </div>
                      <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                        <h3 style='text-align: left !important;'>\u20A6${content.totalPayment}</h3>
                        <p>Total Payment</p>
                      </div>
                    </div>
                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                </div>
              </div>
              `;

            $("#content-overview").append(overviewCard);
            */

            overviewCard = `
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                    <div class="inner" style='display: flex;flex-direction: row;'>
                      <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                        <i class="fas fa-wallet"></i>
                      </div>
                      <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                        <h3 style='text-align: left !important;'>\u20A6${content.walletAmount}</h3>
                        <p>Game Wallet Balance</p>
                      </div>
                    </div>
                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                    ${button}
                </div>
              </div>
              `;

            $("#content-overview").append(overviewCard);

            overviewCard = `
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                  <div class="inner" style='display: flex;flex-direction: row;'>
                    <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                      <i class="fas fa-wallet"></i>
                    </div>
                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                      <h3 style='text-align: left !important;'>\u20A6${content.withdrawalAmount}</h3>
                        <p>Withdrawal Wallet Balance</p>
                    </div>
                  </div>
                  <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                  ${withdraw}
                </div>
              </div>
              `;

            $("#content-overview").append(overviewCard);

            overviewCard = `
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                  <div class="inner" style='display: flex;flex-direction: row;'>
                    <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                      <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                      <h3 style='text-align: left !important;'>\u20A6${content.pendingPayment}</h3>
                      <p>Pending Payout</p>
                    </div>
                  </div>
                  <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                </div>
              </div>
              `;

            $("#content-overview").append(overviewCard);

            overviewCard = `
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                  <div class="inner" style='display: flex;flex-direction: row;'>
                    <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                      <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                      <h3 style='text-align: left !important;'>\u20A6${content.payoutAmount}</h3>
                      <p>Total Payout</p>
                    </div>
                  </div>
                  <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                </div>
              </div>
              `;

            $("#content-overview").append(overviewCard);

            overviewCard = ` 
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                  <div class="inner" style='display: flex;flex-direction: row;'>
                    <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                      <i class="fas fa-list"></i>
                    </div>
                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                      <h3 style='text-align: left !important;'>${content.trialsCount}</h3>
                      <p>Total Trial</p>
                    </div>
                  </div>
                  <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                </div>
              </div>
              `;

            $("#content-overview").append(overviewCard);

            overviewCard = ` 
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                  <div class="inner" style='display: flex;flex-direction: row;'>
                    <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                      <i class="fas fa-random"></i>
                    </div>
                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                      <h3 style='text-align: left !important;'>${content.playsCount}</h3>
                      <p>Session Play</p>
                    </div>
                  </div>
                  <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                </div>
              </div>
              `;

            $("#content-overview").append(overviewCard);

            overviewCard = `  
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                  <div class="inner" style='display: flex;flex-direction: row;'>
                    <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                      <i class="fas fa-medal"></i>
                    </div>
                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                      <h3 style='text-align: left !important;'>${content.winsCount}</h3>
                      <p>Total Win</p>
                    </div>
                  </div>
                  <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                </div>
              </div>
              `;

            $("#content-overview").append(overviewCard);

            overviewCard = `  
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                  <div class="inner" style='display: flex;flex-direction: row;'>
                    <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                      <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                      <h3 style='text-align: left !important;'>${content.mailCount}</h3>
                      <p>Incoming Mail</p>
                    </div>
                  </div>
                  <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                </div>
              </div>
            `;

            $("#content-overview").append(overviewCard);

            overviewCard = `  
              <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                  <div class="inner" style='display: flex;flex-direction: row;'>
                    <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                      <i class="fas fa-bell"></i>
                    </div>
                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                      <h3 style='text-align: left !important;'>${content.notificationCount}</h3>
                      <p>Pending Notification</p>
                    </div>
                  </div>
                  <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                </div>
              </div>
            `;

            $("#content-overview").append(overviewCard);
          //}
        //}
        //Open Fund
        $("#fund").on("click", function () {
          window.location = "views/wallet.php";
        });

        //Open Wihdrawal
        $("#withdraw").on("click", function () {
          window.location = "views/withdrawal.php";
        });
      },
      error: function (e) {
        console.log(e.responseText);
      },
    });
  }

  fetchDetails();

  //Indent all inner child navs
  $(".nav-sidebar").addClass("nav-child-indent");

  //Search function
  $("#page-search").on("keyup", function () {
    let searchValue = $(this).val();

    if (searchValue !== "") {
      $(".col-lg-3.col-6").each(function (index, el) {
        if ($(el).find("p").text().toLowerCase().includes(searchValue)) {
          $(el).css({ display: "block" });
        } else {
          $(el).css({ display: "none" });
        }
      });
    } else {
      $(".col-lg-3.col-6").each(function (index, el) {
        if ($(el).css("display") === "none") {
          $(el).css({ display: "block" });
        }
      });
    }
  });

  const parentDiv = document.getElementById("content-overview");
  const items = parentDiv.querySelectorAll(".col-lg-3.col-6");
  const lastItem = items[items.length - 1];

  parentDiv.addEventListener("scroll", () => {
    const parentScrollTop = parentDiv.scrollTop;
    const parentScrollHeight = parentDiv.scrollHeight;
    const parentClientHeight = parentDiv.clientHeight;

    // Check if the last item is in view
    if (
      parentScrollTop + parentClientHeight >=
      parentScrollHeight - lastItem.clientHeight
    ) {
      // Prevent further scroll
      parentDiv.scrollTop = parentScrollHeight - parentClientHeight;
    }
  });
});

document.addEventListener("contextmenu", function (event) {
  event.preventDefault(); // Prevent the context menu from opening
});
