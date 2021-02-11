class RecurringModel extends Model {
    config() {
        return {
            table: "recurring"
        };
    }

    globalFilter(item){
        return true
    }

    async getRowsByType(type){
        let rows = await this.get();
        rows = rows.filter(doc => {
            return doc.type==type;
        });
        return rows;
    }
}