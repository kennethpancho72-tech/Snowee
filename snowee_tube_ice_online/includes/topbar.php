<header class="topbar">
  <div><button class="menu-toggle" type="button" onclick="document.body.classList.toggle('sidebar-open')">☰</button><span class="topbar-label"><?= e($page_title ?? 'Dashboard') ?></span></div>
  <div class="user-area"><span class="avatar"><?= e(strtoupper(substr(current_user(),0,1))) ?></span><span><?= e(current_user()) ?></span><a class="logout" href="logout.php">Log out ↗</a></div>
</header>
