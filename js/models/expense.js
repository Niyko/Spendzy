class ExpenseModel extends Model {
    config() {
        return {
            table: "expense"
        };
    }

    globalFilter(item){
        return true
    }
}