class ConfigModel extends Model {
    config() {
        return {
            table: "config"
        };
    }

    globalFilter(item){
        return true
    }

    async getConfigs(){
        let values = [];
        let rows = await this.get();
        rows.forEach(function (row) {
            values[row.key] = row.value;
        });
        return values;
    }

    async getConfig(key){
        let value = "";
        let rows = await this.get();
        rows.forEach(function (row) {
            if(row.key==key) value = row.value;
        });
        return value;
    }

    async updateConfig(key, value){
        let isNew = true;
        let oldId = 0;
        let rows = await this.get();
        rows.forEach(function (row) {
            if(row.key==key){
                isNew = false;
                oldId = row.table_id;
            }
        });
        if(isNew){
            return await this.insert({
                key,
                value
            });
        }
        else {
            console.log(oldId);
            return await this.update(oldId, "value", value);
        }
    }
}