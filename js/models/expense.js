class ExpenseModel extends Model {
    config() {
        return {
            table: "expense"
        };
    }

    globalFilter(item){
        return true
    }

    async getTotal(){
        let rows = await this.get();
        let totalExpense = 0;
        rows.forEach(function (row) {
            totalExpense += parseInt(row.amount);
        });
        return totalExpense;
    }
}