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
        let totalNonFD = 0;
        let totalFD = 0;
        rows.forEach(function (row) {
            if(row.is_fd) totalFD += parseInt(row.amount);
            else totalNonFD += parseInt(row.amount);
        });
        return [totalFD, totalNonFD];
    }
}