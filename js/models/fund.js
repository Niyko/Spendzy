class FundModel extends Model {
    config() {
        return {
            table: "fund"
        };
    }

    globalFilter(item){
        return true
    }

    async getTotal(){
        let rows = await this.get();
        let totalFund = 0;
        rows.forEach(function (row) {
            totalFund += parseInt(row.amount);
        });
        return totalFund;
    }
}