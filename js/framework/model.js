class Model {
    async get(){
        const model = this;
        return await new Promise(function (resolve) { 
            database.collection(model.config()["table"]).get()
                .then((tableRows) => {
                    let rows = tableRows.docs.map(doc => Object.assign(
                        { table_id: doc.id },
                        doc.data()
                    ));
                    rows = rows.filter(doc => model.globalFilter(doc));
                    resolve(rows);
                }).catch(function(error) {
                    resolve([error]);
                });
        });
    }

    async insert(insertData){
        const model = this;
        return await new Promise(function (resolve) { 
            database.collection(model.config()["table"]).add(insertData)
                .then(function(row) {
                    resolve(row);
                })
                .catch(function(error) {
                    resolve([]);
                });
        });
    }

    async delete(id){
        const model = this;
        return await new Promise(function (resolve) { 
            database.collection(model.config()["table"]).doc(id).delete()
                .then(function() {
                    resolve(true);
                }).catch(function(error) {
                    resolve(false);
                });
        });
    }

    async update(id, key, value){
        const model = this;
        return await new Promise(function (resolve) { 
            database.collection(model.config()["table"]).doc(id).update({[key]: value})
                .then(function() {
                    resolve(true);
                }).catch(function(error) {
                    resolve(false);
                });
        });
    }
}