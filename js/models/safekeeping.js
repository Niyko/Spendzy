class SafekeepingModel extends Model {
    config() {
        return {
            table: "safekeeping"
        };
    }

    globalFilter(item){
        return true
    }

    async getTotal(){
        let rows = await this.get();
        let totalSafekeeping = 0;
        rows.forEach(function (row) {
            totalSafekeeping += parseInt(row.amount);
        });
        return totalSafekeeping;
    }
}