<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Spendzy</title>
        <?php require('widgets/header.php'); ?>
        <?php add_css("css/analytics.css"); ?>
    </head>
    <body>
        <?php require('widgets/lockscreen.php'); ?>
        <div class="error-container">
            <div uk-grid class="error-inner">
                <div class="uk-width-expand uk-flex uk-flex-middle">
                    <p>Something went wrong, please try again.</p>
                </div>
                <div class="uk-width-auto uk-flex uk-flex-middle">
                    <button class="error-close ripple-effect" onclick="location.reload();" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">close</span></button>
                </div>
            </div>
        </div>
        <div uk-grid class="nav-bar uk-grid-small">
            <div class="uk-width-auto uk-flex uk-flex-middle">
                <button class="nav-bar-icon-btn ripple-effect" onclick="toggleSideBar()" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">menu</span></button>
            </div>
            <div class="uk-width-auto uk-flex uk-flex-middle">
                <p class="nav-bar-title">Analytics</p>
            </div>
            <div class="uk-width-expand"></div>
            <div class="uk-width-auto uk-flex uk-flex-middle" id="nav-bar-spinner">
                <div class="spinner-container uk-flex uk-flex-middle uk-flex-center">
                    <div class="spinner">
                        <svg width="1em" height="1em"><circle cx="0.5em" cy="0.5em" r="0.45em"/></svg>
                    </div>
                </div>
            </div>
            <?php require('widgets/isolate-nav-button.php'); ?>
        </div>
        <div class="uk-flex uk-flex-center">
            <div class="half-page uk-width-1-1 uk-margin-top hide">
                <div id="graphs-container" uk-grid class="uk-grid-small uk-child-width-1-1 uk-child-width-1-2@m"></div>
            </div>
        </div>

        <!----- Templates ----->
        <template hidden id="small-graph-card-template">
            <div>
                <div class="analytics-value-card">
                    <div uk-grid>
                        <div class="uk-width-expand">
                            <p class="analytics-value-card-title">{{title}}</p>
                            <p class="analytics-value-card-value isolated-value">{{value}}</p>
                        </div>
                        <div class="uk-width-auto uk-flex uk-flex-middle">
                            {{#if is_graph}}
                                <div class="analytics-value-card-donut" 
                                    data-donutty 
                                    data-min=0
                                    data-max={{graph_max}}
                                    data-value={{graph_value}}
                                    data-color="#fec007"
                                    data-bg="#f0f0f0"
                                    data-radius=55>
                                </div>
                            {{/if}}
                        </div>
                    </div>
                </div>
            </div>
        </template>
        
        <?php require('widgets/side-bar.php'); ?>
        <?php require('widgets/scripts.php'); ?>
        <?php add_script("js/packages/donutty.js"); ?>
        <script>
            let smallGraphCardTemplate = Handlebars.compile($("#small-graph-card-template").html());
            let incomeData, expenseData, safekeepingData, configData;
            $(function() {onload()});

            function onload(){
                $.ripple(".ripple-effect", {
                    multi: true, 
                });
                loadData();
            }

            async function loadData(){
                incomeData = await new IncomeModel().get();
                expenseData = await new ExpenseModel().get();
                safekeepingData = await new SafekeepingModel().get();
                configData = await new ConfigModel().getConfigs();
                loadGraph();
            }

            async function loadGraph(){
                $("#graphs-container").html("");
                let totalGiven = 0, totalNotGiven = 0, totalIncome = 0, totalExpense = 0, totalSafekepping = 0;
                let goalValue = parseInt(configData["goal-amount"]);
                for (var i = 0; i < incomeData.length; i++) {
                    totalIncome += parseInt(incomeData[i]["amount"]);
                    if(incomeData[i]["is_given"]) totalGiven += parseInt(incomeData[i]["amount"]);
                    else totalNotGiven += parseInt(incomeData[i]["amount"]);
                }
                for (var i = 0; i < expenseData.length; i++) {
                    totalExpense += parseInt(expenseData[i]["amount"]);
                }
                for (var i = 0; i < safekeepingData.length; i++) {
                    totalSafekepping += parseInt(safekeepingData[i]["amount"]);
                }
                let totalFund = await new FundModel().getTotal();
                $("#graphs-container").append(getGraph("Bank balance", numberWithCommas((totalGiven-totalExpense)+totalSafekepping), true, goalValue, (totalGiven-totalExpense)));
                $("#graphs-container").append(getGraph("Mine", numberWithCommas(totalGiven-totalExpense), true, goalValue, (totalGiven-totalExpense)));
                $("#graphs-container").append(getGraph("Goal reached", (((totalGiven-totalExpense)/goalValue)*100).toFixed(0)+"%", true, goalValue, (totalGiven-totalExpense)));
                $("#graphs-container").append(getGraph("Total income", numberWithCommas(totalIncome), true, (totalIncome+totalExpense), totalIncome));
                $("#graphs-container").append(getGraph("Total expense", numberWithCommas(totalExpense), true, (totalIncome+totalExpense), totalExpense));
                $("#graphs-container").append(getGraph("Total given", numberWithCommas(totalGiven), true, totalIncome, totalGiven));
                $("#graphs-container").append(getGraph("To be given", numberWithCommas(totalNotGiven), true, totalIncome, totalNotGiven));
                $("#graphs-container").append(getGraph("Safekeeping", numberWithCommas(totalSafekepping), false));
                $("#graphs-container").append(getGraph("Hedge fund", numberWithCommas(totalFund), false));
                initGraphs();
                toggleNavProgress(false);
                $(".half-page").fadeIn();
            }
            

            function initGraphs(){
                $(".analytics-value-card-donut").each(function(index) {
                    $(this).donutty();
                });
            }

            function getGraph(title, value, is_graph, graph_max, graph_value){
                return smallGraphCardTemplate({
                    title: title,
                    value: value,
                    is_graph: is_graph,
                    graph_max: graph_max,
                    graph_value: graph_value
                });
            }
        </script>
    </body>
</html>