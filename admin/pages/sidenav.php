<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #181d38 !important;">
  <!-- Brand Logo -->
  <a href="#" class="brand-link">
      <img src="../../../assets/img/nairaQuiz.jpg" alt="Logo" class="brand-image img-circle elevation-3" style="width: 40px;height: 40px;border-radius: 50%;opacity: .9;">
    <span class="brand-text font-weight-light"><b>NairaQuiz</b></span>
  </a>
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
      <?php
          $image = $profile === 'null' ? 
          "<img src='../../../assets/img/user.png' class='img-circle elevation-2' style='width: 50px !important;height: 50px !important;' alt='User Image'>"
          : "<img src='../../../uploads/$profile' class='img-circle elevation-2' style='width: 50px !important;height: 50px !important;' alt='User Image'>";
          echo $image;
        ?>
      </div>
      <div class="info">
      <?php
          echo "<a href='../views/profile.php' class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;'>$fullname</a>";
        ?>
      </div>
    </div>
    <!-- Sidebar Menu -->
    <nav class="mt-2" style="overfolw-x: hidden;overflow-y: visible !imporatnt;">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="../" class="nav-link">
            <i class="nav-icon fas fa-home"></i>
              <p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-hourglass-end"></i>
            <p>
              Approvals
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="../views/game-approval.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Gaming</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../views/investment-approval.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Investment</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>
              Pay-ins
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="../views/game-payin.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Gaming</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../views/investment-payin.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Investment</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>
              Pay-outs
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="../views/game-payout.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Gaming</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../views/investment-payout.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Investment</p>
              </a>
            </li>
          </ul>
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
              <a href="../views/mailbox.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Inbox</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../views/compose-mail.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Compose</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="../views/questions.php" class="nav-link">
            <i class="nav-icon fas fa-layer-group"></i>
            <p>
              Questions
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="../views/multiplayer.php" class="nav-link">
            <i class="nav-icon fas fa-retweet"></i>
            <p>
              Multiplayer
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="../views/trials.php" class="nav-link">
            <i class="nav-icon fas fa-medal"></i>
            <p>
              Trials
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="../views/users.php" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>
              Users
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="../views/investors.php" class="nav-link">
            <i class="nav-icon fas fa-user-cog"></i>
            <p>
              Investors
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="../views/workers.php" class="nav-link">
            <i class="nav-icon fas fa-user-tag"></i>
            <p>
              Workers
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="../views/ambassadors.php" class="nav-link">
            <i class="nav-icon fas fa-user-graduate"></i>
            <p>
              Ambassadors
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="../views/profile.php" class="nav-link">
            <i class="fas fa-cog nav-icon"></i>
            <p>Settings</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./server/logout.php" class="nav-link">
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