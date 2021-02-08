<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Spendzy</title>
        <?php require('widgets/header.php'); ?>
        <?php add_css("css/income.css"); ?>
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
                <p class="nav-bar-title">Incomes</p>
            </div>
            <div class="uk-width-expand"></div>
            <div class="uk-width-auto uk-flex uk-flex-middle">
                <input name="sort-date" class="nav-bar-select" onchange="loadIncome()" type="text" data-toggle="monthpicker" value="<?php echo date('M Y'); ?>">
            </div>
            <div class="uk-width-auto uk-flex uk-flex-middle" id="nav-bar-spinner">
                <div class="spinner-container uk-flex uk-flex-middle uk-flex-center">
                    <div class="spinner">
                        <svg width="1em" height="1em"><circle cx="0.5em" cy="0.5em" r="0.45em"/></svg>
                    </div>
                </div>
            </div>
            <div class="uk-width-auto uk-flex uk-flex-middle uk-visible@m">
                <p class="nav-bar-stat-text" id="current-stat"></p>
            </div>
            <?php require('widgets/isolate-nav-button.php'); ?>
        </div>
        <div class="uk-flex uk-flex-center">
            <div class="half-page uk-width-1-1 hide">
                <form id="create-form" onsubmit="addIncome(); return false;">
                    <div class="checklist-create-card uk-width-1-1">
                        <div uk-grid>
                            <div class="uk-width-expand uk-flex uk-flex-middle">
                                <input name="income-title" autocomplete="off" required type="text" placeholder="Title">
                            </div>
                            <div class="uk-width-expand uk-flex uk-flex-middle">
                                <input name="income-date" autocomplete="off" required type="text" data-toggle="datepicker" placeholder="Date">
                            </div>
                            <div class="uk-width-expand uk-flex uk-flex-middle">
                                <input name="income-amount" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required type="tel" placeholder="Amount">
                            </div>
                            <div class="uk-width-auto uk-flex uk-flex-middle">
                                <button class="checklist-create-card-btn ripple-effect" type="submit" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">arrow_forward</span></button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="list-container">
                    <p class="list-title">To be given</p>
                    <div id="not-given-list"><!--- List render from JS ---></div>
                    <p class="list-title">Already given</p>
                    <div id="given-list"><!--- List render from JS ---></div>
                    <div class="divider"></div>
                    <div class="uk-flex uk-flex-right">
                        <span class="imagine-checkbox" onclick="toggleImagineMode()"><span class="material-icons">toys</span></span>
                    </div>
                </div>
            </div>
        </div>

        <!----- Templates ----->
        <template hidden id="spinner-template">
            <div class="uk-flex uk-flex-center uk-width-1-1">
                <div class="spinner">
                    <svg width="1em" height="1em"><circle cx="0.5em" cy="0.5em" r="0.45em"/></svg>
                </div>
            </div>
        </template>

        <template hidden id="list-item-template">
            {{#each incomes}}
                <div class="checklist-item">
                    <div uk-grid class="uk-grid-small">
                        <div class="uk-width-auto uk-flex uk-flex-middle">
                            <button onclick="chechGiven(!{{this.is_given}}, this, '{{this.table_id}}')" class="checklist-checkbox {{#if this.is_given}}active{{/if}}"></button>
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
                                    {{#if is_given}}
                                        <li><a onclick="addToLog(this, '{{this.table_id}}')"><span class="material-icons">addchart</span> Add to log</a></li>
                                    {{/if}}
                                    <li><a ondblclick="deleteIncome(this, '{{this.table_id}}')"><span class="material-icons">delete</span> Delete</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            {{/each}}
        </template>

        <template hidden id="stat-template">
            <span>{{total_given}}</span> / {{total_income}}
        </template>

        <?php require('widgets/side-bar.php'); ?>
        <?php require('widgets/scripts.php'); ?>
        <script>
            let tableName = "income";
            let checkAudio = new Audio("sounds/check.mp3");
            let uncheckAudio = new Audio("sounds/uncheck.mp3");
            let isImagineMode = false;
            let progressRequestCode = "";
            let listItemTemplate = Handlebars.compile($("#list-item-template").html());
            let statTemplate = Handlebars.compile($("#stat-template").html());
            $(function() {onload()});

            function onload(){
                $.ripple(".ripple-effect", {
                    multi: true, 
                });

                loadIncome();
                updateStat();
            }

            async function loadIncome(){
                let rows = await new IncomeModel().get();
                let isGivenRows = rows.filter(doc => {
                    let rowMonth = moment(doc.date, "DD MMM YYYY").format("MMM YYYY");
                    let sortMonth = $("[name=sort-date]").val();
                    return (rowMonth==sortMonth);
                });
                isGivenRows = isGivenRows.filter(doc => doc.is_given==true);
                let notGivenRows = rows.filter(doc => doc.is_given==false);
                $("#given-list").html(listItemTemplate({ incomes: isGivenRows }));
                $("#not-given-list").html(listItemTemplate({ incomes: notGivenRows }));
                $(".half-page").fadeIn();
                toggleNavProgress(false);
                updateStat();
            }

            async function addIncome(){
                toggleNavProgress(true);
                let newIncome = {
                    amount: $("input[name=income-amount]").val(),
                    title: $("input[name=income-title]").val(),
                    is_given: false,
                    date: $("input[name=income-date]").val(),
                };
                let row = await new IncomeModel().insert(newIncome);
                newIncome["table_id"] = row.id
                $("#create-form").trigger("reset");
                $("#not-given-list").prepend(listItemTemplate({ incomes: [newIncome] }));
                $("#not-given-list .checklist-item:first-child").animateCSS("fadeInDown");
                toggleNavProgress(false);
                updateStat();
            }

            async function chechGiven(isGiven, e, id){
                if(isGiven) checkAudio.play(0);
                else uncheckAudio.play(0);
                if(!isImagineMode){
                    toggleNavProgress(true, true, id);
                    let row = await new IncomeModel().update(id, "is_given", isGiven);
                    toggleNavProgress(false, true, id);
                    updateStat();
                }
                $(e).toggleClass("active");
                $(e).closest(".checklist-item").prependTo((isGiven?"#given-list":"#not-given-list"));
                $(e).closest(".checklist-item").animateCSS("fadeInDown");
                $(e).attr("onclick", `chechGiven(!${isGiven}, this, '${id}')`);
                if(isImagineMode) updateStat();
            }

            async function onOtherDataChange(dataKey, e, id){
                toggleNavProgress(true, true, id);
                let row = await new IncomeModel().update(id, dataKey, $(e).val());
                toggleNavProgress(false, true, id);
                updateStat();
                $(e).blur(); 
            }

            async function addToLog(e, id){
                let newLog = {
                    amount: $(e).closest(".checklist-item").find(".checklist-amount").val(),
                    title: $(e).closest(".checklist-item").find(".checklist-title").val(),
                    type: "income",
                    date: $(e).closest(".checklist-item").find(".checklist-date").val(),
                };
                toggleNavProgress(true, true, id);
                let row = await new LogbackModel().insert(newLog);
                toggleNavProgress(false, true, id);
                updateStat();
                $(e).closest(".checklist-item").addClass("checklist-item-deleted");
            }

            async function deleteIncome(e, id){
                toggleNavProgress(true, true, id);
                let status = await new IncomeModel().delete(id);
                toggleNavProgress(false, true, id);
                updateStat();
                $(e).closest(".checklist-item").addClass("checklist-item-deleted");
            }

            async function updateStat(){
                let statistics = await new IncomeModel().getStatistics();
                if(isImagineMode){
                    statistics.totalGiven = 0;
                    $("#given-list .checklist-amount").each(function(index) {
                        statistics.totalGiven += parseInt($(this).val());
                    });
                }
                $("#current-stat").html(statTemplate({ 
                    total_given: nFormatter(statistics.totalGiven, 1),
                    total_income: nFormatter(statistics.totalIncome, 1),
                }));
            }

            function toggleImagineMode(){
                $(".imagine-checkbox").toggleClass("active");
                if(isImagineMode){
                    loadIncome();
                    updateStat();
                }
                isImagineMode = !isImagineMode;
            }
        </script>
    </body>
</html>