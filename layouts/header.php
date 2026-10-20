
<header class="main_header wide_header roma-header">
    <div class="header_container">
        <div class="menu_wrapper menu_sticky">
            <div class="container p_relative h86">

                <div id="navigation" class="of-drop-down of-main-menu" role="navigation">

                    <ul class="menu roma-menu">

                        <!-- Logo -->
                        <li class="roma-logo">
                            <a href="index.php">
                                <img src="assets/Img/logo-main.png" alt="روما">
                            </a>
                        </li>

                        <!-- Menu -->
                        <li>
                            <a href="index.php">صفحه اصلی</a>
                        </li>

                        <li>
                            <a href="articles.php">مقالات</a>
                        </li>

                        <li>
                            <a href="about.php">درباره ما</a>
                        </li>

                        <li>
                            <a href="contact.php">ارتباط با روما</a>
                        </li>

                    </ul>

                </div>

                <!-- Search Icon -->
                <div class="m_search roma-search-icon">
                    <i class="fal fa-search"></i>
                </div>

                <!-- Search Box -->
                <form class="search_wrap roma-search"
                      id="ajax-form-search"
                      method="get"
                      action="search.php">

                    <input
                        type="text"
                        id="search-form-text"
                        class="search-field"
                        name="s"
                        placeholder="جستجوی مقاله..."
                    >

                    <button type="submit">
                        <i class="fal fa-search"></i>
                    </button>

                    <div id="ajax-search-result"></div>

                </form>

            </div>
        </div>
    </div>
</header>

