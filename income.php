<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Spendzy</title>
        <?php require('widgets/header.php'); ?>
        <?php add_css("css/income.css"); ?>
    </head>
    <body>
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
            <div class="uk-width-auto uk-flex uk-flex-middle" id="nav-bar-spinner">
                <div class="spinner-container uk-flex uk-flex-middle uk-flex-center">
                    <div class="spinner">
                        <svg width="1em" height="1em"><circle cx="0.5em" cy="0.5em" r="0.45em"/></svg>
                    </div>
                </div>
            </div>
            <div class="uk-width-auto uk-flex uk-flex-middle">
                <p class="nav-bar-stat-text" id="current-stat"></p>
            </div>
            <div class="uk-width-auto uk-flex uk-flex-middle">
                <button class="nav-bar-icon-btn ripple-effect" onclick="goToAddIncome()" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">add</span></button>
            </div>
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
                        <div class="uk-width-auto uk-flex uk-flex-middle">
                            <input type="text" onchange="onOtherDataChange('amount', this, '{{this.table_id}}')" class="checklist-amount" value="{{this.amount}}"><span class="checklist-amount-currency">/-</span>
                        </div>
                        <div class="uk-width-auto uk-flex uk-flex-middle">
                            <button class="checklist-delete ripple-effect" ondblclick="deleteIncome(this, '{{this.table_id}}')" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">close</span></button>
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
            var tableName = "income";
            var checkAudio = new Audio("sounds/check.mp3");
            var uncheckAudio = new Audio("sounds/uncheck.mp3");
            var isImagineMode = false;
            var progressRequestCode = "";
            var listItemTemplate = Handlebars.compile($("#list-item-template").html());
            var statTemplate = Handlebars.compile($("#stat-template").html());
            $(function() {onload()});

            function onload(){
                $.ripple(".ripple-effect", {
                    multi: true, 
                });

                loadIncome();
                updateStat();
            }

            function goToAddIncome(){
                $("input[name=income-title]").focus();
            }

            function loadIncome(){
                database.collection(tableName).get()
                    .then((tableRows) => {
                        rows = tableRows.docs.map(doc => Object.assign(
                            { table_id: doc.id },
                            doc.data()
                        ));
                        isGivenRows = rows.filter(doc => doc.is_given==true);
                        notGivenRows = rows.filter(doc => doc.is_given==false);
                        $("#given-list").html(listItemTemplate({ incomes: isGivenRows }));
                        $("#not-given-list").html(listItemTemplate({ incomes: notGivenRows }));
                        $(".half-page").fadeIn();
                        toggleNavProgress(false);
                        updateStat();
                    }).catch(function(error) {
                        onIncomeTableError();
                    });
            }

            function addIncome(){
                toggleNavProgress(true);
                var newIncome = {
                    amount: $("input[name=income-amount]").val(),
                    title: $("input[name=income-title]").val(),
                    is_given: false,
                    date: "Turing",
                };
                database.collection(tableName).add(newIncome)
                    .then(function(row) {
                        newIncome["table_id"] = row.id
                        $("#create-form").trigger("reset");
                        $("#not-given-list").prepend(listItemTemplate({ incomes: [newIncome] }));             
                        $("#not-given-list .checklist-item:first-child").addClass("animate__animated animate__fadeInDown");
                        toggleNavProgress(false);
                        updateStat();
                    })
                    .catch(function(error) {
                        onIncomeTableError();
                    });
            }

            function chechGiven(isGiven, e, id){
                if(isGiven) checkAudio.play(0);
                else uncheckAudio.play(0);
                if(!isImagineMode){
                    toggleNavProgress(true, true, id);
                    database.collection(tableName).doc(id).update({is_given: isGiven}).then(function() {
                            toggleNavProgress(false, true, id);
                            updateStat();
                        }).catch(function(error) {
                            onIncomeTableError();
                        });
                }
                $(e).toggleClass("active");
                $(e).closest(".checklist-item").prependTo((isGiven?"#given-list":"#not-given-list"));
                $(e).closest(".checklist-item").addClass("animate__animated animate__fadeInDown");
                $(e).attr("onclick", `chechGiven(!${isGiven}, this, '${id}')`);
                if(isImagineMode) updateStat();
            }

            function onOtherDataChange(dataKey, e, id){
                toggleNavProgress(true, true, id);
                database.collection(tableName).doc(id).update({[dataKey]: $(e).val()}).then(function() {
                        toggleNavProgress(false, true, id);
                        updateStat();
                    }).catch(function(error) {
                        onIncomeTableError();
                    });
                $(e).blur(); 
            }

            function deleteIncome(e, id){
                toggleNavProgress(true, true, id);
                database.collection(tableName).doc(id).delete().then(function() {
                        toggleNavProgress(false, true, id);
                        updateStat();
                    }).catch(function(error) {
                        onIncomeTableError();
                    });
                $(e).closest(".checklist-item").addClass("checklist-item-deleted");
            }

            function updateStat(){
                var totalIncome = 0, totalGiven = 0;
                database.collection(tableName).get()
                    .then((tableRows) => {
                        tableRows.forEach((row) => {
                            totalIncome += parseInt(row.data().amount);
                            if(row.data().is_given) totalGiven += parseInt(row.data().amount);
                        });
                        if(isImagineMode){
                            totalGiven = 0;
                            $("#given-list .checklist-amount").each(function(index) {
                                totalGiven += parseInt($(this).val());
                            });
                        }
                        $("#current-stat").html(statTemplate({ 
                            total_given: nFormatter(totalGiven, 1),
                            total_income: nFormatter(totalIncome, 1),
                        }));
                    }).catch(function(error) {
                        onIncomeTableError();
                    });
            }

            function onIncomeTableError(){
                loadIncome();
                onDatabaseError();
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