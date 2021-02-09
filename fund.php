<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Spendzy</title>
        <?php require('widgets/header.php'); ?>
        <?php add_css("css/fund.css"); ?>
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
                <p class="nav-bar-title">Hedge fund</p>
            </div>
            <div class="uk-width-expand"></div>
            <div class="uk-width-auto uk-flex uk-flex-middle" id="nav-bar-spinner">
                <div class="spinner-container uk-flex uk-flex-middle uk-flex-center">
                    <div class="spinner">
                        <svg width="1em" height="1em"><circle cx="0.5em" cy="0.5em" r="0.45em"/></svg>
                    </div>
                </div>
            </div>
            <div class="uk-width-auto uk-flex uk-flex-middle">
                <p class="nav-bar-stat-text isolated-value" id="current-stat"></p>
            </div>
            <?php require('widgets/isolate-nav-button.php'); ?>
        </div>
        <div class="uk-flex uk-flex-center">
            <div class="half-page uk-width-1-1 hide">
                <form id="create-form" onsubmit="addFund(); return false;">
                    <div class="checklist-create-card uk-width-1-1">
                        <div uk-grid>
                            <div class="uk-width-expand uk-flex uk-flex-middle">
                                <input name="fund-title" autocomplete="off" required type="text" placeholder="Title">
                            </div>
                            <div class="uk-width-expand uk-flex uk-flex-middle">
                                <input name="fund-date" autocomplete="off" required type="text" data-toggle="datepicker" placeholder="Date">
                            </div>
                            <div class="uk-width-expand uk-flex uk-flex-middle">
                                <input name="fund-amount" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required type="tel" placeholder="Amount">
                            </div>
                            <div class="uk-width-auto uk-flex uk-flex-middle">
                                <button class="checklist-create-card-btn ripple-effect" type="submit" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">arrow_forward</span></button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="list-container">
                    <p class="list-title">Current goal</p>
                    <div id="goal-list">
                        <div class="checklist-item">
                            <div uk-grid class="uk-grid-small">
                                <div class="uk-width-auto uk-flex uk-flex-middle">
                                    <button class="checklist-delete ripple-effect"><span class="material-icons">flag</span></button>
                                </div>
                                <div class="uk-width-expand uk-flex uk-flex-middle">
                                    <input id="goal-name" type="text" onchange="onGoalDataChange(this, 'goal-name')" class="checklist-title" value="" placeholder="Goal Name">
                                </div>
                                <div class="uk-width-auto uk-flex uk-flex-middle">
                                    <input id="goal-amount" type="text" onchange="onGoalDataChange(this, 'goal-amount')" class="checklist-amount isolated-value" value="" placeholder="Amount"><span class="checklist-amount-currency">/-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="list-title">All Fund</p>
                    <div id="fund-list"><!--- List render from JS ---></div>
                </div>
            </div>
        </div>

        <!----- Templates ----->
        <template hidden id="list-item-template">
            {{#each funds}}
                <div class="checklist-item">
                    <div uk-grid class="uk-grid-small">
                        <div class="uk-width-auto uk-flex uk-flex-middle">
                            <button class="checklist-delete ripple-effect" onclick="toggleInfo(this)" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">expand_more</span></button>
                        </div>
                        <div class="uk-width-expand uk-flex uk-flex-middle">
                            <input type="text" onchange="onOtherDataChange('title', this, '{{this.table_id}}')" class="checklist-title" value="{{this.title}}">
                        </div>
                        <div class="uk-width-auto uk-flex uk-flex-middle uk-visible@m">
                            <input data-toggle="datepicker" onchange="onOtherDataChange('date', this, '{{this.table_id}}')" class="checklist-date" value="{{this.date}}">
                        </div>
                        <div class="uk-width-auto uk-flex uk-flex-middle">
                            <input type="text" onchange="onOtherDataChange('amount', this, '{{this.table_id}}')" class="checklist-amount isolated-value" value="{{this.amount}}"><span class="checklist-amount-currency">/-</span>
                        </div>
                        <div class="uk-width-auto uk-flex uk-flex-middle">
                        <button class="checklist-delete ripple-effect" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">more_vert</span></button>
                            <div class="dropdown" uk-dropdown="mode: click">
                                <ul class="uk-nav uk-dropdown-nav">
                                    <li><a ondblclick="deleteFund(this, '{{this.table_id}}')"><span class="material-icons">delete</span> Delete</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="checklist-item-description-container">
                        <textarea class="checklist-item-description" onchange="onOtherDataChange('description', this, '{{this.table_id}}')" placeholder="More info">{{this.description}}</textarea>
                    </div>
                </div>
            {{/each}}
        </template>

        <template hidden id="stat-template">
            {{total_fund}}
        </template>

        <?php require('widgets/side-bar.php'); ?>
        <?php require('widgets/scripts.php'); ?>
        <script>
            let listItemTemplate = Handlebars.compile($("#list-item-template").html());
            let statTemplate = Handlebars.compile($("#stat-template").html());
            $(function() {onload()});

            function onload(){
                $.ripple(".ripple-effect", {
                    multi: true, 
                });
                loadGoal();
                loadFund();
            }

            async function loadGoal(){
                let configData = await new ConfigModel().getConfigs();
                $("#goal-name").val(configData["goal-name"]);
                $("#goal-amount").val(configData["goal-amount"]);
            }

            async function loadFund(){
                let rows = await new FundModel().get();
                $("#fund-list").html(listItemTemplate({ funds: rows }));
                $(".half-page").fadeIn();
                toggleNavProgress(false);
                updateStat();
            }

            async function addFund(){
                toggleNavProgress(true);
                let newFund = {
                    amount: $("input[name=fund-amount]").val(),
                    title: $("input[name=fund-title]").val(),
                    description: "",
                    date: $("input[name=fund-date]").val(),
                };
                let row = await new FundModel().insert(newFund);
                newFund["table_id"] = row.id
                $("#create-form").trigger("reset");
                $("#fund-list").prepend(listItemTemplate({ funds: [newFund] }));             
                $("#fund-list .checklist-item:first-child").animateCSS("fadeInDown");
                toggleNavProgress(false);
                updateStat();
                $("input[name=fund-amount]").blur(); 
            }

            async function onOtherDataChange(dataKey, e, id){
                toggleNavProgress(true, true, id);
                let row = await new FundModel().update(id, dataKey, $(e).val());
                toggleNavProgress(false, true, id);
                updateStat();
                $(e).blur(); 
            }

            async function onGoalDataChange(e, dataKey){
                toggleNavProgress(true, false);
                let row = await new ConfigModel().updateConfig(dataKey, $(e).val());
                toggleNavProgress(false, false);
                $(e).blur(); 
            }

            async function deleteFund(e, id){
                toggleNavProgress(true, true, id);
                let status = await new FundModel().delete(id);
                toggleNavProgress(false, true, id);
                updateStat();
                $(e).closest(".checklist-item").addClass("checklist-item-deleted");
            }

            async function updateStat(){
                let totalFund = await new FundModel().getTotal();
                $("#current-stat").html(statTemplate({
                    total_fund: nFormatter(totalFund, 1),
                }));
            }

            async function addToLog(e, id){
                let newLog = {
                    amount: $(e).closest(".checklist-item").find(".checklist-amount").val(),
                    title: $(e).closest(".checklist-item").find(".checklist-title").val(),
                    type: "fund",
                    date: $(e).closest(".checklist-item").find(".checklist-date").val(),
                };
                toggleNavProgress(true, true, id);
                let row = await new LogbackModel().insert(newLog);
                toggleNavProgress(false, true, id);
                updateStat();
                $(e).closest(".checklist-item").addClass("checklist-item-deleted");
            }

            function toggleInfo(e){
                $(e).closest(".checklist-item").find(".checklist-item-description-container").toggleClass('active');
            }
        </script>
    </body>
</html>