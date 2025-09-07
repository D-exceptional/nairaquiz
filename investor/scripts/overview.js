//Fetch Settings Data
let overviewCard = "";

function fetchSiteOverview() {
  $.ajax({
    type: "GET",
    url: "server/overview.php",
    dataType: "json",
    success: function (response) {
      for (const key in response) {
        if (Object.hasOwnProperty.call(response, key)) {
          const content = response[key];
          
           overviewCard = `   <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                    <div class="inner" style='display: flex;flex-direction: row;'>
                                        <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                           <i class="fas fa-chart-line"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3 style='text-align: left !important;'>${content.totalInvestments}</h3>
                                            <p>Total Investments</p>
                                        </div>
                                    </div>
                                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                                </div>
                            </div>
                        `;

          $("#content-overview").append(overviewCard);
          
           overviewCard = `   <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                    <div class="inner" style='display: flex;flex-direction: row;'>
                                        <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                           <i class="fas fa-university"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3 style='text-align: left !important;'>${content.totalPayOuts}</h3>
                                            <p>Total Withdrawals</p>
                                        </div>
                                    </div>
                                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                                </div>
                            </div>
                        `;

          $("#content-overview").append(overviewCard);
          
          overviewCard = `   <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                                    <div class="inner" style='display: flex;flex-direction: row;'>
                                        <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                                           <i class="fas fa-wallet"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3 style='text-align: left !important;'>${content.walletOne}</h3>
                                            <p>Referral Balance</p>
                                        </div>
                                    </div>
                                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"></a>
                                </div>
                            </div>
                        `;

            $("#content-overview").append(overviewCard);
            
            overviewCard = `   <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info" style="background: transparent !important;border: 2px solid #181d38 !important;color: #181d38 !important;">
                <div class="inner" style='display: flex;flex-direction: row;'>
                    <div class="icon-div" style='width: 20% !important;display: flex;flex-direction: row;align-items: center;justify-content: center;font-size: 3.5em;padding: 0px 5px 0px 0px !important;'>
                       <i class="fas fa-wallet"></i>
                    </div>
                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                        <h3 style='text-align: left !important;'>${content.walletTwo}</h3>
                        <p>Downline Balance</p>
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
                                            <h3>${content.investmentCount}</h3>
                                            <p>Total Plans</p>
                                        </div>
                                    </div>
                                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
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
                                                        <i class="fas fa-university"></i>
                                                    </div>
                                                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                        <h3>${content.withdrawalCount}</h3>
                                                        <p>Total Payouts</p>
                                                    </div>
                                                </div>
                                                <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
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
                                                        <i class="fas fa-users"></i>
                                                    </div>
                                                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                        <h3>${content.userCount}</h3>
                                                        <p>Total Downlines</p>
                                                    </div>
                                                </div>
                                                <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
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
                                                        <h3>${content.mailCount}</h3>
                                                        <p>Incoming Mails</p>
                                                    </div>
                                                </div>
                                                <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
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
                                            <h3>${content.notificationCount}</h3>
                                            <p>Total Notification</p>
                                        </div>
                                    </div>
                                    <a href="#" class="small-box-footer" style="background: #181d38 !important;color: #181d38 !important;color: #f8f9fa !important;"> </a>
                                </div>
                            </div>
                        `;

              $("#content-overview").append(overviewCard);

        }
      }
    },
    error: function (e) {
      console.log(e.responseText);
    },
  });
}

fetchSiteOverview();

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
