$(document).ready(() => {
  const MIN_WALLET_AMOUNT = 1000;

  // Start game button click
  $("#start").on("click", () => {
    const walletAmount = Number($("#walletAmount").val());

    if (walletAmount < MIN_WALLET_AMOUNT) {
      displayInfo("Fund your wallet with a minimum of 1000 to proceed");
      return;
    }

    window.location.href = "../views/multiplayer.php";
  });

  // Fund wallet button click
  $("#fund").on("click", () => {
    window.location.href = "../views/wallet.php";
  });
});
