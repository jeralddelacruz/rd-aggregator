<style>
  .nav1-color {
    background-color: #ae1e1e !important;
  }

  .navbar {
    padding: 0;
    box-shadow: rgb(0 0 0 / 20%) 0px 0px 0.5rem;
  }

  .navbar-light .navbar-brand {
    color: #ae1e1e;
  }

  .navbar-brand {
    display: inline-block;
    margin-right: 1rem;
    font-size: 1.7rem;
    font-weight: 700;
    line-height: inherit;
    white-space: nowrap;
    padding: 0.8rem 1rem;
    background-color: #ffffff;
  }

  a {
    text-decoration: none !important;
  }

  .navbar-expand-md .navbar-nav .nav-link {
    padding-right: 1rem;
    padding-left: 1rem;
    color: #ffffff !important;
  }

  .nav-item a {
    font-size: 18px;
    font-weight: 600;
  }
</style>

<nav class="navbar navbar-expand-md navbar-light fixed-top nav1-color">
  <div class="container">
    <a class="navbar-brand" href="news.php?campaigns_id=4">Test</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault"
        aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
              <a class="nav-link" href="news.php?campaigns_id=4&category=1">Test Name <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="http://example.com" id="dropdown01" data-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">MORE</a>
              <div class="dropdown-menu" aria-labelledby="dropdown01">
                <a class="dropdown-item" href="news.php?campaigns_id=4&category=1">Test</a>
              </div>
          </li>
        </ul>
        <form class="form-inline my-2 my-lg-0 icons-container">
            <div>
                <a href="#" class=""><i class="fab fa-facebook-f"></i></a>
                <a href="#" class=""><i class="fab fa-twitter"></i></a>
                <a href="#" class=""><i class="fab fa-instagram"></i></a>
                <a href="#" class=""><i class="fab fa-youtube"></i></a>
            </div>
            <div class="search-container">
                <form method="POST">
                    <input class="form-control mr-sm-2 hide" id="campaign_id" type="hidden" name="campaigns_id" value="4" placeholder="Search..." aria-label="Search">
                    <input class="form-control mr-sm-2 hide" id="search-input" type="text" name="search" placeholder="Search..." aria-label="Search">
                </form>
                <i class="fa fa-search"></i>
            </div>
        </form>
    </div>
  </div>
</nav>