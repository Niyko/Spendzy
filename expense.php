<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Spendzy</title>
        <?php require('widgets/header.php'); ?>
        <?php add_css("css/expense.css"); ?>
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
                <p class="nav-bar-title">Expense</p>
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
                <input name="sort-date" class="nav-bar-select" onchange="if(this.value==''){ this.value = 'View all'; } loadExpense();" type="text" data-toggle="monthpicker" value="<?php echo date('M Y'); ?>">
            </div>
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
                <form id="create-form" onsubmit="addExpense(); return false;">
                    <div class="checklist-create-card uk-width-1-1">
                        <div uk-grid>
                            <div class="uk-width-expand uk-flex uk-flex-middle">
                                <input name="expense-title" autocomplete="off" required type="text" placeholder="Title">
                            </div>
                            <div class="uk-width-expand uk-flex uk-flex-middle">
                                <input name="expense-date" autocomplete="off" required type="text" data-toggle="datepicker" placeholder="Date">
                            </div>
                            <div class="uk-width-expand uk-flex uk-flex-middle">
                                <input name="expense-amount" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required type="tel" placeholder="Amount">
                            </div>
                            <div class="uk-width-auto uk-flex uk-flex-middle">
                                <button class="checklist-create-card-btn ripple-effect" type="submit" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">arrow_forward</span></button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="list-container">
                    <p class="list-title">All Expense</p>
                    <div id="expense-list"><!--- List render from JS ---></div>
                    <p class="list-title">Recurring income</p>
                    <div id="recurring-list"><!--- List render from JS ---></div>
                </div>
            </div>
        </div>

        <!----- Templates ----->
        <template hidden id="list-item-template">
            {{#each rows}}
                <div class="checklist-item">
                    <div uk-grid class="uk-grid-small">
                        <div class="uk-width-expand uk-flex uk-flex-middle">
                            <input type="text" onchange="onOtherDataChange('title', this, '{{this.table_id}}')" class="checklist-title" value="{{this.title}}">
                        </div>
                        <div class="uk-width-auto uk-flex uk-flex-middle uk-visible@m">
                            <input data-toggle="datepicker" onchange="onOtherDataChange('date', this, '{{this.table_id}}')" class="checklist-date" value="{{this.date}}">
                        </div>
                        {{#if is_recurring}}
                            {{#if is_issued}}
                                <div class="uk-width-auto uk-flex uk-flex-middle">
                                    <p><span class="material-icons">done_all</span></p>
                                </div>
                            {{/if}}
                        {{/if}}
                        <div class="uk-width-auto uk-flex uk-flex-middle">
                            <input type="text" onchange="onOtherDataChange('amount', this, '{{this.table_id}}')" class="checklist-amount isolated-value" value="{{this.amount}}"><span class="checklist-amount-currency">/-</span>
                        </div>
                        <div class="uk-width-auto uk-flex uk-flex-middle">
                            {{#if is_recurring}}
                                <button onclick="issueRecuringExpense(this)" class="checklist-btn ripple-effect" data-duration="0.5" data-color="auto" data-opacity="0.3">Issue</button>
                            {{else}}
                                <button class="checklist-delete ripple-effect" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">more_vert</span></button>
                                <div class="dropdown" uk-dropdown="mode: click">
                                    <ul class="uk-nav uk-dropdown-nav">
                                        <li><a onclick="addToLog(this, '{{this.table_id}}')"><span class="material-icons">addchart</span> Add to log</a></li>
                                        <li><a ondblclick="deleteExpense(this, '{{this.table_id}}')"><span class="material-icons">delete</span> Delete</a></li>
                                    </ul>
                                </div>
                            {{/if}}
                        </div>
                    </div>
                </div>
            {{/each}}
        </template>

        <template hidden id="stat-template">
            {{total_expense}}
        </template>

        <?php require('widgets/side-bar.php'); ?>
        <?php require('widgets/scripts.php'); ?>
        <script>
            let expenseTitleList = [];
            let listItemTemplate = Handlebars.compile($("#list-item-template").html());
            let statTemplate = Handlebars.compile($("#stat-template").html());
            $(function() {onload()});

            function onload(){
                $.ripple(".ripple-effect", {
                    multi: true, 
                });
                loadExpense();
                loadRecurringExpense();
            }

            async function loadRecurringExpense(){
                let rows = await new RecurringModel().getRowsByType("expense");
                rows.forEach((row, index) => {
                    if(expenseTitleList.includes(row.title)) rows[index]["is_issued"] = true;
                });
                $("#recurring-list").html(listItemTemplate({ rows }));
            }

            async function loadExpense(){
                toggleNavProgress(true);
                let rows = await new ExpenseModel().get();
                let sortMonth = $("[name=sort-date]").val();
                expenseTitleList = [];
                rows = rows.filter(doc => {
                    if(sortMonth!="View all"){
                        let rowMonth = moment(doc.date, "DD MMM YYYY").format("MMM YYYY");
                        return (rowMonth==sortMonth);
                    }
                    else return true;
                });
                rows.forEach((doc) => {
                    expenseTitleList.push(doc.title);
                });
                $("#expense-list").html(listItemTemplate({ rows }));
                $(".half-page").fadeIn();
                toggleNavProgress(false);
                updateStat();
            }

            async function addExpense(){
                toggleNavProgress(true);
                var newExpense = {
                    amount: $("input[name=expense-amount]").val(),
                    title: $("input[name=expense-title]").val(),
                    date: $("input[name=expense-date]").val(),
                };
                let row = await new ExpenseModel().insert(newExpense);
                newExpense["table_id"] = row.id;
                $("#create-form").trigger("reset");
                $("#expense-list").prepend(listItemTemplate({ rows: [newExpense] }));             
                $("#expense-list .checklist-item:first-child").animateCSS("fadeInDown");
                toggleNavProgress(false);
                updateStat();
                $("input[name=expense-amount]").blur(); 
            }

            async function addToLog(e, id){
                let newLog = {
                    amount: $(e).closest(".checklist-item").find(".checklist-amount").val(),
                    title: $(e).closest(".checklist-item").find(".checklist-title").val(),
                    type: "expense",
                    date: $(e).closest(".checklist-item").find(".checklist-date").val(),
                };
                toggleNavProgress(true);
                let row = await new LogbackModel().insert(newLog);
                let deletedRow = await new ExpenseModel().delete(id);
                toggleNavProgress(false);
                updateStat();
                $(e).closest(".checklist-item").addClass("checklist-item-deleted");
            }

            async function onOtherDataChange(dataKey, e, id){
                toggleNavProgress(true, true, id);
                let row = await new ExpenseModel().update(id, dataKey, $(e).val());
                toggleNavProgress(false, true, id);
                updateStat();
                $(e).blur(); 
            }

            async function issueRecuringExpense(e){
                toggleNavProgress(true);
                let newExpense = {
                    amount: $(e).closest(".checklist-item").find(".checklist-amount").val(),
                    title: $(e).closest(".checklist-item").find(".checklist-title").val(),
                    type: "expense",
                    date: moment().format("DD MMM YYYY"),
                };
                let row = await new ExpenseModel().insert(newExpense);
                $("#expense-list").prepend(listItemTemplate({ rows: [newExpense] }));
                $("#expense-list .checklist-item:first-child").animateCSS("fadeInDown");
                $(e).hide();
                toggleNavProgress(false);
                updateStat();
            }

            async function deleteExpense(e, id){
                toggleNavProgress(true, true, id);
                let status = await new ExpenseModel().delete(id);
                toggleNavProgress(false, true, id);
                updateStat();
                $(e).closest(".checklist-item").addClass("checklist-item-deleted");
            }

            async function updateStat(){
                let totalExpense = await new ExpenseModel().getTotal();
                $("#current-stat").html(statTemplate({
                    total_expense: nFormatter(totalExpense, 1),
                }));
            }
        </script>
    </body>
</html>