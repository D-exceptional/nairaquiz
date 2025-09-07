  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #181d38 !important;">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="../../assets/img/nairaQuiz.jpg" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light"><b>NairaQuiz</b></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <?php
                $image = $profile === 'null' ? "<img src='../../assets/img/user.png' class='img-circle elevation-2' width='50px' height='50px' alt='User Image'>" : "<img src='../../uploads/$profile' class='img-circle elevation-2' style='width: 50px !important;height: 50px !important;' alt='User Image'>";
                echo $image;
            ?>
        </div>
        <div class="info">
          <?php
            echo "<a href='#' class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;' id='$userID'>$fullname</a>";
          ?>
        </div>
      </div>
     
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="../index.php" class="nav-link">
              <i class="nav-icon fas fa-home"></i>
              <p>Dashboard</p>
            </a>
          </li>
           <li class="nav-item">
            <a href="../views/wallet.php" class="nav-link">
              <i class="nav-icon fas fa-wallet"></i>
              <p>Wallet</p>
            </a>
          </li>
          <li class='nav-item'>
            <a href='../views/game.php' class='nav-link'>
              <i class='nav-icon fas fa-users'></i>
              <p>Multiplayer</p>
            </a>
          </li>
          <?php 
              if(in_array($userID, [3, 7])){
                  echo 
                  '<li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="nav-icon fas fa-medal"></i>
                      <p>
                        Challenge
                        <i class="fas fa-angle-left right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="../views/quiz.php?type=5" class="nav-link">
                          <i class="far fa-circle nav-icon"></i>
                          <p>5 Questions</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="../views/quiz.php?type=7" class="nav-link">
                          <i class="far fa-circle nav-icon"></i>
                          <p>7 Questions</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="../views/quiz.php?type=10" class="nav-link">
                          <i class="far fa-circle nav-icon"></i>
                          <p>10 Questions</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="../views/quiz.php?type=14" class="nav-link">
                          <i class="far fa-circle nav-icon"></i>
                          <p>14 Questions</p>
                        </a>
                      </li>
                    </ul>
                </li>
              ';
              }
          ?>
          <li class="nav-item">
            <a href="../views/trials.php" class="nav-link">
              <i class="fas fa-retweet nav-icon"></i>
              <p>Trials</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../views/withdrawal.php" class="nav-link">
              <i class="fas fa-download nav-icon"></i>
              <p>Withdrawal</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-hand-holding-usd"></i>
              <p>
                Payouts
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="../views/pending-payout.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Pending</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../views/payout-history.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>History</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-globe"></i>
              <p>
                Community
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="https://www.facebook.com/profile.php?id=61567789027719" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Facebook</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://instagram.com/nairaquiz" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Instagram</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://whatsapp.com/channel/0029VbAiVSKKAwEfwLILhT2J" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>WhatsApp</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://x.com/nairaquiz24965?t=58F-AImMCqisk_xQrpKpDg&s=09" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Twitter</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://www.tiktok.com/@www.nairaquiz.com?_t=ZS-8yUdJ1TqY9C&_r=1" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tiktok</p>
                </a>
              </li>
                <li class="nav-item">
                <a href="https://youtube.com/@nairaquiz?si=IJw1InTdVhp-5a3H" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>YouTube</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="../views/settings.php" class="nav-link">
              <i class="fas fa-cog nav-icon"></i>
              <p>Settings</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../server/logout.php" class="nav-link">
              <i class="nav-icon fas fa-arrow-left"></i>
              <p>Logout</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>