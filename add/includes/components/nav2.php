<style>
  .nav2-color {
    background-color: #ae1e1e !important;
  }

  .navbar {
    padding: 0;
    box-shadow: rgb(0 0 0 / 20%) 0px 0px 0.5rem;
    justify-content: flex-end !important;
  }

  .navbar-light .navbar-brand {
    color: #ae1e1e;
  }

  .custom-navbar-brand {
    display: inline-block;
    font-size: 1.7rem;
    font-weight: 700;
    line-height: inherit;
    white-space: nowrap;
    padding: 0.8rem 1rem;
    position: absolute;
    left: 0;
    right: 0;
    margin-left: auto;
    margin-right: auto;
    text-align: center;
    z-index: 0;
  }

  a {
    text-decoration: none !important;
  }

  .nav-right {
    font-size: 1.7rem;
    padding: 0.8rem 1rem;
    z-index: 1;

    display: flex;
    justify-content: flex-end;
  }
</style>

<nav class="navbar navbar-expand-md navbar-light fixed-top nav2-color">
    <div class="nav-title custom-navbar-brand">
        <a class="" href="news.php?campaigns_id=4">Test</a>
    </div>
    
    <div class="nav-right">
        <button class="btn btn-primary">SUBSCRIBE</button>
    </div>
</nav>