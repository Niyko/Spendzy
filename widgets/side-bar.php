<div id="side-bar" uk-offcanvas="overlay: false; mode: push;">
    <div class="uk-offcanvas-bar">
        <div uk-grid class="nav-bar uk-grid-small">
            <div class="uk-width-auto uk-flex uk-flex-middle">
                <button class="nav-bar-icon-btn ripple-effect" onclick="toggleSideBar()" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">arrow_back</span></button>
            </div>
            <div class="uk-width-auto uk-flex uk-flex-middle">
                <p class="nav-bar-title">Spendzy</p>
            </div>
            <div class="uk-width-expand"></div>
            <div class="uk-width-auto uk-flex uk-flex-middle">
                <button class="nav-bar-icon-btn ripple-effect" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">person_outline</span></button>
            </div>
        </div>
        <div class="side-bar-menu-list">
            <button href="index.php" <?php if(basename($_SERVER['REQUEST_URI'])=='index.php' || basename($_SERVER['REQUEST_URI'])=='Spendzy') echo 'class="active" disabled'; ?>><span class="material-icons">donut_large</span> Analytics</button>
            <button href="income.php" <?php if(basename($_SERVER['REQUEST_URI'])=='income.php') echo 'class="active" disabled'; ?>><span class="material-icons">account_balance_wallet</span> Income</button>
            <button href="expense.php" <?php if(basename($_SERVER['REQUEST_URI'])=='expense.php') echo 'class="active" disabled'; ?>><span class="material-icons">assessment</span> Expense</button>
            <button href="safekeeping.php" <?php if(basename($_SERVER['REQUEST_URI'])=='safekeeping.php') echo 'class="active" disabled'; ?>><span class="material-icons">dns</span> Safekeeping</button>
            <button href="fund.php" <?php if(basename($_SERVER['REQUEST_URI'])=='fund.php') echo 'class="active" disabled'; ?>><span class="material-icons">account_balance</span> Hedge fund</button>
        </div>
    </div>
</div>