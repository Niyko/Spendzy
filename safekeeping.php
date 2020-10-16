<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Spendzy</title>
        <?php require('widgets/header.php'); ?>
        <?php add_css("css/safekeeping.css"); ?>
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
                <p class="nav-bar-title">Safekeeping</p>
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
                <button class="nav-bar-icon-btn ripple-effect" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">add</span></button>
            </div>
        </div>
        <div class="uk-flex uk-flex-center">
            <div class="half-page uk-width-1-1 hide">
                <form id="create-form" onsubmit="addSafekeeping(); return false;">
                    <div class="checklist-create-card uk-width-1-1">
                        <div uk-grid>
                            <div class="uk-width-expand uk-flex uk-flex-middle">
                                <input name="safekeeping-title" autocomplete="off" required type="text" placeholder="Title">
                            </div>
                            <div class="uk-width-expand uk-flex uk-flex-middle">
                                <input name="safekeeping-amount" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required type="tel" placeholder="Amount">
                            </div>
                            <div class="uk-width-auto uk-flex uk-flex-middle">
                                <button class="checklist-create-card-btn ripple-effect" type="submit" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">arrow_forward</span></button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="list-container">
                    <p class="list-title">All Safekeeping</p>
                    <div id="safekeeping-list"><!--- List render from JS ---></div>
                </div>
            </div>
        </div>

        <!----- Templates ----->
        <template hidden id="list-item-template">
            {{#each safekeepings}}
                <div class="checklist-item">
                    <div uk-grid class="uk-grid-small">
                        <div class="uk-width-auto uk-flex uk-flex-middle">
                            <button class="checklist-delete ripple-effect" onclick="toggleInfo(this)" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">expand_more</span></button>
                        </div>
                        <div class="uk-width-expand uk-flex uk-flex-middle">
                            <input type="text" onchange="onOtherDataChange('title', this, '{{this.table_id}}')" class="checklist-title" value="{{this.title}}">
                        </div>
                        <div class="uk-width-auto uk-flex uk-flex-middle">
                            <input type="text" onchange="onOtherDataChange('amount', this, '{{this.table_id}}')" class="checklist-amount" value="{{this.amount}}"><span class="checklist-amount-currency">/-</span>
                        </div>
                        <div class="uk-width-auto uk-flex uk-flex-middle">
                            <button class="checklist-delete ripple-effect" ondblclick="deleteSafekeeping(this, '{{this.table_id}}')" data-duration="0.5" data-color="auto" data-opacity="0.3"><span class="material-icons">close</span></button>
                        </div>
                    </div>
                    <div class="checklist-item-description-container">
                        <textarea class="checklist-item-description" onchange="onOtherDataChange('description', this, '{{this.table_id}}')" placeholder="More info">{{this.description}}</textarea>
                    </div>
                </div>
            {{/each}}
        </template>

        <template hidden id="stat-template">
            {{total_safekeeping}}
        </template>

        <?php require('widgets/side-bar.php'); ?>
        <?php require('widgets/scripts.php'); ?>
        <script>
            var tableName = "safekeeping";
            var listItemTemplate = Handlebars.compile($("#list-item-template").html());
            var statTemplate = Handlebars.compile($("#stat-template").html());
            $(function() {onload()});

            function onload(){
                $.ripple(".ripple-effect", {
                    multi: true, 
                });
                loadSafekeeping();
            }

            function loadSafekeeping(){
                database.collection(tableName).get()
                    .then((tableRows) => {
                        rows = tableRows.docs.map(doc => Object.assign(
                            { table_id: doc.id },
                            doc.data()
                        ));
                        $("#safekeeping-list").html(listItemTemplate({ safekeepings: rows }));
                        $(".half-page").fadeIn();
                        toggleNavProgress(false);
                        updateStat();
                    }).catch(function(error) {
                        onSafekeepingTableError();
                    });
            }

            function addSafekeeping(){
                toggleNavProgress(true);
                var newSafekeeping = {
                    amount: $("input[name=safekeeping-amount]").val(),
                    title: $("input[name=safekeeping-title]").val(),
                    description: "",
                    date: "Turing",
                };
                database.collection(tableName).add(newSafekeeping)
                    .then(function(row) {
                        newSafekeeping["table_id"] = row.id
                        $("#create-form").trigger("reset");
                        $("#safekeeping-list").prepend(listItemTemplate({ safekeepings: [newSafekeeping] }));             
                        $("#safekeeping-list .checklist-item:first-child").addClass("animate__animated animate__fadeInDown");
                        toggleNavProgress(false);
                        updateStat();
                    })
                    .catch(function(error) {
                        onSafekeepingTableError();
                    });
                $("input[name=safekeeping-amount]").blur(); 
            }

            function onOtherDataChange(dataKey, e, id){
                toggleNavProgress(true, true, id);
                database.collection(tableName).doc(id).update({[dataKey]: $(e).val()}).then(function() {
                        toggleNavProgress(false, true, id);
                        updateStat();
                    }).catch(function(error) {
                        onSafekeepingTableError();
                    });
                $(e).blur(); 
            }

            function deleteSafekeeping(e, id){
                toggleNavProgress(true, true, id);
                database.collection(tableName).doc(id).delete().then(function() {
                        toggleNavProgress(false, true, id);
                        updateStat();
                    }).catch(function(error) {
                        onSafekeepingTableError();
                    });
                $(e).closest(".checklist-item").addClass("checklist-item-deleted");
            }

            function updateStat(){
                var totalSafekeeping = 0;
                database.collection(tableName).get()
                    .then((tableRows) => {
                        tableRows.forEach((row) => {
                            totalSafekeeping += parseInt(row.data().amount);
                        });
                        $("#current-stat").html(statTemplate({
                            total_safekeeping: nFormatter(totalSafekeeping, 1),
                        }));
                    }).catch(function(error) {
                        onSafekeepingTableError();
                    });
            }

            function toggleInfo(e){
                $(e).closest(".checklist-item").find(".checklist-item-description-container").toggleClass('active');
            }

            function onSafekeepingTableError(){
                loadSafekeeping();
                onDatabaseError();
            }
        </script>
    </body>
</html>