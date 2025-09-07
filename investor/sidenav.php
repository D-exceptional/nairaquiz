<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #181d38 !important;">
    <!-- Brand Logo -->
    <a href="../index.php" class="brand-link">
      <img src="../../../assets/img/nairaQuiz.jpg" alt="Logo" class="brand-image img-circle elevation-3" style="width: 40px;height: 40px;border-radius: 50%;opacity: .9;">
      <span class="brand-text font-weight-light"><b>NairaQuiz</b></span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <?php
            echo "<img src='$profile' class='img-circle elevation-2' style='width: 50px !important;height: 50px !important;' alt='User Image'>";
          ?>
        </div>
        <div class="info">
          <?php
            echo "<a href='profile.php' class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;'>$fullname</a>";
          ?>
        </div>
      </div>
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="index.php" class="nav-link">
              <i class="nav-icon fas fa-home"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="plans.php" class="nav-link">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>
                Plans
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="investments.php" class="nav-link">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>
                Investments
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="downline.php" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Downlines
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="wallets.php" class="nav-link">
              <i class="nav-icon fas fa-wallet"></i>
              <p>
                Wallets
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="withdrawals.php" class="nav-link">
              <i class="nav-icon fas fa-retweet"></i>
              <p>
                Withdrawals
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-envelope-open"></i>
              <p>
                Mail
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="mailbox.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Inbox</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="compose-mail.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Compose</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="profile.php" class="nav-link">
              <i class="fas fa-cog nav-icon"></i>
              <p>Settings</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="server/logout.php" class="nav-link">
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