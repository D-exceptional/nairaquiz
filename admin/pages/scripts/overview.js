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
                                           <i class="fas fa-university"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3 style='text-align: left !important;'>\u20a6${content.totalGamePayIns}</h3>
                                            <p>Total Game Pay-ins</p>
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
                                           <i class="fas fa-chart-line"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3 style='text-align: left !important;'>\u20a6${content.totalGamePayOuts}</h3>
                                            <p>Total Game Pay-outs</p>
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
                                           <i class="fas fa-hourglass"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3 style='text-align: left !important;'>${content.pendingGamePayIns}</h3>
                                            <p>Pending Game Pay-ins</p>
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
                                           <i class="fas fa-hourglass"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3 style='text-align: left !important;'>${content.pendingGamePayOuts}</h3>
                                            <p>Pending Game Pay-outs</p>
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
                                            <h3 style='text-align: left !important;'>\u20a6${content.totalInvestmentPayIns}</h3>
                                            <p>Total Investment Pay-ins</p>
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
                                           <i class="fas fa-chart-line"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3 style='text-align: left !important;'>\u20a6${content.totalInvestmentPayOuts}</h3>
                                            <p>Total Investment Pay-outs</p>
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
                                           <i class="fas fa-hourglass"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3 style='text-align: left !important;'>${content.pendingInvestmentPayIns}</h3>
                                            <p>Pending Investment Pay-ins</p>
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
                                           <i class="fas fa-hourglass"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3 style='text-align: left !important;'>${content.pendingInvestmentPayOuts}</h3>
                                            <p>Pending Investment Pay-outs</p>
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
                                            <h3 style='text-align: left !important;'>\u20a6${content.walletBalance}</h3>
                                            <p>Wallet Balance</p>
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
                                            <i class="fas fa-layer-group"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3>${content.questionCount}</h3>
                                                <p>Total Questions</p>
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
                                                        <p>Registered Users</p>
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
                                                        <i class="fas fa-user-graduate"></i>
                                                    </div>
                                                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                        <h3>${content.ambassadorCount}</h3>
                                                        <p>Total Ambassadors</p>
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
                                                        <i class="fas fa-user-cog"></i>
                                                    </div>
                                                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                        <h3>${content.workerCount}</h3>
                                                        <p>Total Workers</p>
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
                                                        <i class="fas fa-user-shield"></i>
                                                    </div>
                                                    <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                                        <h3>${content.investorCount}</h3>
                                                        <p>Total Investors</p>
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
                                            <i class="fas fa-medal"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3>${content.trialCount}</h3>
                                            <p>Total Trials</p>
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
                                            <i class="fas fa-hourglass-half"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3>${content.gameCount}</h3>
                                            <p>Session Games</p>
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
                                            <i class="fas fa-random"></i>
                                        </div>
                                        <div class="info-div" style='width: 80% !important;display: flex;flex-direction: column;align-items: flex-end;justify-content: flex-end;flex-wrap: wrap !important;padding: 20px 2px 0px 5px !important;'>
                                            <h3>${content.playsCount}</h3>
                                            <p>Session Plays</p>
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
