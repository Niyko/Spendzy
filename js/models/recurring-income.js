class RecurringIncomeModel extends Model {
    config() {
        return {
            table: "recurring-income"
        };
    }

    globalFilter(item){
        return true
    }
}