class IncomeModel extends Model {
    config() {
        return {
            table: "income"
        };
    }

    globalFilter(item){
        return true;
    }

    async getStatistics(){
        let rows = await this.get();
        let totalIncome = 0;
        let totalGiven = 0;
        rows.forEach(function (row) {
            totalIncome += parseInt(row.amount);
            if(row.is_given) totalGiven += parseInt(row.amount);
        });
        return {
            totalIncome,
            totalGiven
        };
    }
}