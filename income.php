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
            <div class="uk-width-expand uk-flex uk-flex-center uk-flex-middle">
                <div class="nav-bar-search-box uk-grid-small" uk-grid>
                    <div class="uk-width-expand">
                        <input class="nav-bar-search-input" oninput="searchOnList()" type="text" value="" placeholder="Search" autocomplete="off">
                    </div>
                    <div class="uk-width-auto">
                        <button class="checklist-create-card-btn ripple-effect" onclick="toggleSearchBox(false)" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">close</span></button>
                    </div>
                </div>
            </div>
            <div class="uk-width-auto uk-flex uk-flex-middle uk-visible@m">
                <button class="nav-bar-icon-btn ripple-effect" onclick="toggleSearchBox(true)" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">search</span></button>
            </div>
            <div class="uk-width-auto uk-flex uk-flex-middle">
                <input name="sort-date" class="nav-bar-select" onchange="if(this.value==''){ this.value = 'View all'; } loadIncome();" type="text" data-toggle="monthpicker" value="<?php echo date('M Y'); ?>">
            </div>
            <div class="uk-width-auto uk-flex uk-flex-middle" id="nav-bar-spinner">
                <div class="spinner-container uk-flex uk-flex-middle uk-flex-center">
                    <div class="spinner">
                        <svg width="1em" height="1em"><circle cx="0.5em" cy="0.5em" r="0.45em"/></svg>
                    </div>
                </div>
            </div>
            <div class="uk-width-auto uk-flex uk-flex-middle uk-visible@m">
                <p class="nav-bar-stat-text isolated-value" id="current-stat"></p>
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
                    <p class="list-title">Recurring income</p>
                    <div id="recurring-list"><!--- List render from JS ---></div>
                    <p class="list-title">Already given</p>
                    <div id="given-list"><!--- List render from JS ---></div>
                    <div class="divider"></div>
                    <div class="uk-flex uk-flex-right">
                        <span class="imagine-checkbox" onclick="toggleImagineMode()"><span class="material-icons">filter_vintage</span></span>
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
            {{#each rows}}
                <div class="checklist-item">
                    <div uk-grid class="uk-grid-small">
                        {{#unless is_recurring}}
                            <div class="uk-width-auto uk-flex uk-flex-middle">
                                <button ondblclick="chechGiven(!{{this.is_given}}, this, '{{this.table_id}}')" class="checklist-checkbox {{#if this.is_given}}active{{/if}}"></button>
                            </div>
                        {{/unless}}
                        <div class="uk-width-expand uk-flex uk-flex-middle">
                            <input type="text" onchange="onOtherDataChange('title', this, '{{this.table_id}}')" class="checklist-title  {{#if is_recurring}} no-pointer-events {{/if}}" value="{{this.title}}">
                        </div>
                        <div class="uk-width-auto uk-flex uk-flex-middle uk-visible@m">
                            <input data-toggle="datepicker" onchange="onOtherDataChange('date', this, '{{this.table_id}}')" class="checklist-date  {{#if is_recurring}} no-pointer-events {{/if}}" value="{{this.date}}">
                        </div>
                        {{#if is_recurring}}
                            {{#if is_issued}}
                                <div class="uk-width-auto uk-flex uk-flex-middle">
                                    <p><span class="material-icons">done_all</span></p>
                                </div>
                            {{/if}}
                        {{/if}}
                        <div class="uk-width-auto uk-flex uk-flex-middle">
                            <input type="text" onchange="onOtherDataChange('amount', this, '{{this.table_id}}')" class="checklist-amount isolated-value  {{#if is_recurring}} no-pointer-events {{/if}}" value="{{this.amount}}"><span class="checklist-amount-currency">/-</span>
                        </div>
                        <div class="uk-width-auto uk-flex uk-flex-middle">
                            {{#if is_recurring}}
                                <button ondblclick="issueRecuringIncome(this)" class="checklist-btn ripple-effect" data-duration="0.5" data-color="auto" data-opacity="0.3">Issue</button>
                            {{else}}
                                <button class="checklist-delete ripple-effect" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">more_vert</span></button>
                                <div class="dropdown" uk-dropdown="mode: click">
                                    <ul class="uk-nav uk-dropdown-nav">
                                        {{#if is_given}}
                                            <li><a ondblclick="addToLog(this, '{{this.table_id}}')"><span class="material-icons">addchart</span> Add to log</a></li>
                                        {{/if}}
                                        <li><a ondblclick="deleteIncome(this, '{{this.table_id}}')"><span class="material-icons">delete</span> Delete</a></li>
                                    </ul>
                                </div>
                            {{/if}}
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
            let checkAudio = new Audio("sounds/check.mp3");
            let uncheckAudio = new Audio("sounds/uncheck.mp3");
            let isImagineMode = false;
            let progressRequestCode = "";
            let incomeTitleList = [];
            let listItemTemplate = Handlebars.compile($("#list-item-template").html());
            let statTemplate = Handlebars.compile($("#stat-template").html());
            $(function() {onload()});

            function onload(){
                $.ripple(".ripple-effect", {
                    multi: true, 
                });
                loadIncome();
                loadRecurringIncome();
                updateStat();
            }

            async function loadRecurringIncome(){
                let rows = await new RecurringModel().getRowsByType("income");
                rows.forEach((row, index) => {
                    if(incomeTitleList.includes(row.title)) rows[index]["is_issued"] = true;
                });
                $("#recurring-list").html(listItemTemplate({ rows }));
            }

            async function loadIncome(){
                toggleNavProgress(true);
                let rows = await new IncomeModel().get();
                let sortMonth = $("[name=sort-date]").val();
                incomeTitleList = [];
                let isGivenRows = rows.filter(doc => {
                    if(sortMonth!="View all"){
                        let rowMonth = moment(doc.date, "DD MMM YYYY").format("MMM YYYY");
                        return (rowMonth==sortMonth);
                    }
                    else return true;
                });
                isGivenRows = isGivenRows.filter(doc => {
                    if(doc.is_given) incomeTitleList.push(doc.title);
                    return doc.is_given==true;
                });
                let notGivenRows = rows.filter(doc => doc.is_given==false);
                $("#given-list").html(listItemTemplate({ rows: isGivenRows }));
                $("#not-given-list").html(listItemTemplate({ rows: notGivenRows }));
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
                $("#not-given-list").prepend(listItemTemplate({ rows: [newIncome] }));
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
                $(e).attr("ondblclick", `chechGiven(!${isGiven}, this, '${id}')`);
                if(isImagineMode) updateStat();
            }

            async function onOtherDataChange(dataKey, e, id){
                toggleNavProgress(true, true, id);
                let row = await new IncomeModel().update(id, dataKey, $(e).val());
                toggleNavProgress(false, true, id);
                updateStat();
                $(e).blur(); 
            }

            async function issueRecuringIncome(e){
                toggleNavProgress(true);
                let newIncome = {
                    amount: $(e).closest(".checklist-item").find(".checklist-amount").val(),
                    title: $(e).closest(".checklist-item").find(".checklist-title").val(),
                    type: "income",
                    is_given: true,
                    date: moment().format("DD MMM YYYY"),
                };
                let row = await new IncomeModel().insert(newIncome);
                $("#given-list").prepend(listItemTemplate({ rows: [newIncome] }));
                $("#given-list .checklist-item:first-child").animateCSS("fadeInDown");
                $(e).hide();
                toggleNavProgress(false);
                updateStat();
            }

            async function addToLog(e, id){
                let newLog = {
                    amount: $(e).closest(".checklist-item").find(".checklist-amount").val(),
                    title: $(e).closest(".checklist-item").find(".checklist-title").val(),
                    type: "income",
                    date: $(e).closest(".checklist-item").find(".checklist-date").val(),
                };
                toggleNavProgress(true);
                let row = await new LogbackModel().insert(newLog);
                let deletedRow = await new IncomeModel().delete(id);
                toggleNavProgress(false);
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